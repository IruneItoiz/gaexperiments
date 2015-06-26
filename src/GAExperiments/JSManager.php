<?php
namespace GAExperiments;

/** This class includes the GA code and the Reading tracker JS code */

class JSManager {

    protected $plugin_directory;
    protected $settings_page_properties;

    public function __construct( $plugin_directory,  $settings_page_properties ){
        $this->plugin_directory = $plugin_directory;
        $this->settings_page_properties = $settings_page_properties;

    }

    public function run() {
        add_action( 'wp_enqueue_scripts', array( $this, 'add_js_scripts' ));

        add_action( 'wp_head', array( $this, 'add_js_code' ));
    }

    function add_js_code()
    {
        $settings = get_option($this->settings_page_properties['option_name']) ;


        $code = "
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
              m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
              })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

              ga('create', '".$settings['ga_profile']."', 'auto');
              ga('send', 'pageview');";


        $parts = array();

        if ($settings['minHeight'] > 0)
        {
           $parts[] =  "minHeight: ".$settings['minHeight'];
        }

        if ($settings['percentage'] == 0)
        {
            $parts[] =  "percentage: false";
        }

        if ($settings['usertiming'] == 0)
        {
            $parts[] =  "userTiming: false";
        }

        if ($settings['scrolldepth'] == 0)
        {
            $parts[] =  "pixelDepth: false";
        }

        if ($settings['noninteraction'] == 0)
        {
            $parts[] =  "nonInteraction: false";
        }

        if (trim($settings['elements']) != '')
        {

            $ids = explode(",", $settings['elements']);
            print_r($ids);
            foreach ($ids as $id)
            {
                $id_array[] = "'".$id."'";
            }
            $elements = "elements: [".implode(",", $id_array)."]";
            $parts[] = $elements;
        }

        $code .= "
        jQuery(function() {
            jQuery.scrollDepth({\n".implode (",\n", $parts)."\n });
            });
        ";

        echo '<script>'.$code.'</script>';
    }

    function add_js_scripts()
    {
        // Deregister the included library
        wp_deregister_script( 'jquery' );

        // Register the library again from Google's CDN
        wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js', array(), null, false );

        // Register the script like this for a plugin:
        wp_register_script( 'scroll-script', $this->plugin_directory.'js/jquery.scrolldepth.min.js', array( 'jquery' ) );


        // For either a plugin or a theme, you can then enqueue the script:
        wp_enqueue_script( 'scroll-script' );
    }


}