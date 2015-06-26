<?php
/**
 * Created by PhpStorm.
 * User: iruneitoiz
 * Date: 17/06/15
 * Time: 15:43
 */

namespace GAExperiments;

class Experiment {
    public function run() {
        add_action( 'init', array( $this, 'create_posttype' ) );
    }


    public function create_posttype()
    {
        $labels = array(
            'name'               => _x( 'Experiments', 'post type general name', 'experiments-textdomain' ),
            'singular_name'      => _x( 'Experiment', 'post type singular name', 'experiments-textdomain' ),
            'menu_name'          => _x( 'Experiments', 'admin menu', 'experiments-textdomain' ),
            'name_admin_bar'     => _x( 'Experiment', 'add new on admin bar', 'experiments-textdomain' ),
            'add_new'            => _x( 'Add New', 'experiment', 'experiments-textdomain' ),
            'add_new_item'       => __( 'Add New Experiment', 'experiments-textdomain' ),
            'new_item'           => __( 'New Experiment', 'experiments-textdomain' ),
            'edit_item'          => __( 'Edit Experiment', 'experiments-textdomain' ),
            'view_item'          => __( 'View Experiment', 'experiments-textdomain' ),
            'all_items'          => __( 'All Experiments', 'experiments-textdomain' ),
            'search_items'       => __( 'Search Experiments', 'experiments-textdomain' ),
            'parent_item_colon'  => __( 'Parent Experiments:', 'experiments-textdomain' ),
            'not_found'          => __( 'No experiments found.', 'experiments-textdomain' ),
            'not_found_in_trash' => __( 'No experiments found in Trash.', 'experiments-textdomain' )
        );

        $args = array(
            'labels'             => $labels,
            'description'        => 'Google A/B Testing Experiments',
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_admin_bar'  => true,
            'menu_position'      => 12,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'experiment' ),
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => true,
            'supports'           => array( 'title', 'editor',  'custom-fields', 'revisions', 'page-attributes' ),
            'register_meta_box_cb' => array ($this, 'register_metaboxes')
        );

        register_post_type( 'experiment', $args );
        add_action( 'add_meta_boxes', array ($this, 'register_metaboxes'), 10, 1 );
        add_action('save_post', array ($this,'save_experiments_meta'), 1, 2); // save the custom fields
        add_filter( 'template_include', array ($this, 'experiment_template') );
    }

    public function register_metaboxes()
    {

        $args = array(
            //'public'   => true,
            '_builtin' => false
        );

        $output = 'names'; // names or objects, note names is the default
        $operator = 'and'; // 'and' or 'or'

        $post_types = get_post_types( $args, $output, $operator );

        $post_types[] = 'post';
        $post_types[] = 'page';

        foreach ( $post_types  as $page )
        {
            add_meta_box( 'experiment-id', 'Experiments', array ($this, 'experiment_attributes_meta_box'), $page, 'side', 'high');
        }


    }

    function experiment_attributes_meta_box($post) {
        $experiment_id = get_post_meta( $post->ID, '_experiment_id', true );
        $pages = wp_dropdown_pages(array('post_type' => 'experiment', 'selected' => $experiment_id, 'name' => 'experiment_id', 'show_option_none' => __('(No Experiment)'), 'sort_column'=> 'menu_order, post_title', 'echo' => 0));
        if ( ! empty($pages) ) {
            wp_nonce_field( 'save_experiment_id', 'experiment_id_nonce' );

            echo $pages;
        } // end empty pages check
    } // end hierarchical check.

    // Save the Metabox Data

    function save_experiments_meta($post_id, $post) {

        // verify this came from the our screen and with proper authorization,
        // because save_post can be triggered at other times
        if ( !wp_verify_nonce( $_POST['experiment_id_nonce'],'save_experiment_id' )) {
            return $post->ID;
        }

        // Is the user allowed to edit the post or page?
        if ( !current_user_can( 'edit_post', $post->ID ))
            return $post->ID;

        // OK, we're authenticated: we need to find and save the data
        // We'll put it into an array to make it easier to loop though.

        $experiment_id = $_POST['experiment_id'];
        // Add values of $events_meta as custom fields

        if(get_post_meta($post->ID, '_experiment_id', FALSE)) { // If the custom field already has a value
                update_post_meta($post->ID, '_experiment_id',$experiment_id);
        } else { // If the custom field doesn't have a value
                add_post_meta($post->ID, '_experiment_id', $experiment_id);
        }
        if(!$experiment_id) delete_post_meta($post->ID, '_experiment_id'); // Delete if blank
    }



    function experiment_template( $original_template ) {
        global $post;


        //Check if the parameter abver is present
        $variation = false;

        $variation = ( isset($_REQUEST['abver']) ? sanitize_text_field( $_REQUEST['abver'] ) : false );

        if (!$variation)
            $variation = ( isset($_COOKIE['abver']) ? sanitize_text_field( $_COOKIE['abver'] ) : false );

        //Figure out if the page is part of an experiment
        $post_experiment = get_post_meta($post->ID, '_experiment_id', true);

        if ( $variation != false )
        {
            //We set the cookie first
            setcookie('abver', $variation, time()+3600*24*100, COOKIEPATH, COOKIE_DOMAIN, false);

            if ($post_experiment)
            {
                $array_experiments = $this->get_experiment_parameters($post_experiment);
                //Get the template associated with the value on $variation
                $file_template = (isset($array_experiments[$variation]) ? $array_experiments[$variation] : false);
                //Return the correct file

                if ($file_template)
                {
                    return get_template_directory() . '/'.$file_template;
                }
            }
            return $original_template;
        } else {
            return $original_template;
        }
    }

    function get_experiment_parameters($post_experiment)
    {
        $content_post = get_post($post_experiment);
        $content = $content_post->post_content;
        $content = $str = preg_replace('/^\h*\v+/m', '',$content);
        $content = explode("\n", $content);

        foreach ($content as $experiment)
        {
            $values = explode ('=', $experiment);
            if (isset($values[1]))
                $content_array[trim($values[0])] = trim($values[1]);
        }
        return $content_array;
    }

}


