<?php
/*
Plugin Name: GA Experiments
Description: Enable switching templates dynamically site-wide based on GA Experiments
Version: 1.0.0
License: GPL-2.0+
*/

use GAExperiments\Plugin;
use GAExperiments\SettingsPage;
use GAExperiments\Experiment;

spl_autoload_register( 'gaexperiments_autoloader' ); // Register autoloader

function gaexperiments_autoloader( $class_name ) {

    if ( false !== strpos( $class_name, 'GAExperiments' ) ) {
        $classes_dir = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
        $class_file = str_replace( '\\', DIRECTORY_SEPARATOR, $class_name ) . '.php';

        require_once $classes_dir . $class_file;
    }
}

add_action( 'plugins_loaded', 'gaexperiments_init' ); // Hook initialization function
function gaexperiments_init() {
	$plugin = new Plugin();
	$plugin['path'] = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR;
	$plugin['url'] = plugin_dir_url( __FILE__ );
	$plugin['version'] = '1.0.0';
	$plugin['settings_page_properties'] = array(
		'parent_slug' => 'options-general.php',
		'page_title' =>  'GAExperiments - Settings',
		'menu_title' =>  'GAExperiments',
		'capability' => 'manage_options',
		'menu_slug' => 'gcaexperiments-settings',
		'option_group' => 'gcaexperiments_option_group',
		'option_name' => 'gcaexperiments_option_name'
	);
	
	//$plugin['settings_page'] = function ( $plugin ) {
    //    return new SettingsPage( $plugin['settings_page_properties'] );
    //};

    $plugin['experiment_cpt'] = function ( $plugin ) {
        return new Experiment( );
    };

	$plugin->run();
}
