<?php
/*
Plugin Name: XSoftware Framework
Description: Framework for xsoftware wordpress plugins.
Version: 1.0
Author: Luca Gasperini
Author URI: https://xsoftware.eu/
Text Domain: xsoftware_products
*/

add_action( 'plugins_loaded', 'load_framework', 0 );

function load_framework()
{

}

add_action( 'init', 'xs_framework_init_meta_boxes', 9999 );

function xs_framework_init_meta_boxes() {
    if( ! class_exists( 'cmb_Meta_Box' ) )
        require_once(plugin_dir_path( __FILE__ ) . 'meta-boxes.php');
}


class xs_framework
{
        static function init_admin_style()
        {
                wp_enqueue_style('xs_framework_admin_style', plugins_url('style/admin.css', __FILE__));
        }
        
        static function create_checkbox_input($settings)
        {
                $default_settings = array('options' => array(), 'defaults' => array(), 'name' => '', 'compare' => '', 'field_name' => '' );
                $settings += $default_settings;
                
                $value = '';
                if(isset($settings['defaults'][$settings['name']]))
                        $value = (isset($settings['options'][$settings['name']])) ? $settings['options'][$settings['name']] : $settings['defaults'][$settings['name']];
                        
                $name = (empty($settings['field_name'])) ? $settings['name'] : $settings['field_name'];
                $checked = ($value == $settings['compare']) ? 'checked' : '';
                
                echo "<input type='checkbox' name='".$name."' ".$checked." />";
        }

        static function create_text_input($settings)
        {
        
                $default_settings = array('options' => array(), 'defaults' => array(), 'name' => '', 'readonly' => '', 'type' => 'text', 'field_name' => '' );
                $settings += $default_settings;
                
                $value = '';
                if(isset($settings['defaults'][$settings['name']]))
                        $value = (isset($settings['options'][$settings['name']])) ? $settings['options'][$settings['name']] : $settings['defaults'][$settings['name']];
                        
                $name = (empty($settings['field_name'])) ? $settings['name'] : $settings['field_name'];
                $readonly = (empty($settings['readonly'])) ? '' : 'readonly';
                
                echo "<input type='".$settings['type']."' name='". $name . "' value='".$value."' " . $readonly  . "/>";
        }
} 
?>
