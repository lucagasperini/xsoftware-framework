<?php
/*
Plugin Name: XSoftware Framework
Description: Framework for xsoftware wordpress plugins.
Version: 1.0
Author: Luca Gasperini
Author URI: https://xsoftware.eu/
Text Domain: xsoftware_products
*/

add_action( 'plugins_loaded', 'load_framework', 0 ); //Load it first!

function load_framework()
{
        include 'framework-options.php';
        
        $options = xs_framework::get_option();
        //take language from browser setting
        $language = xs_framework::language_browser();
        
        if(isset($options['available_languages'][$language])) {
                $language = xs_framework::cookie_language($language);
        }
}

add_action( 'init', 'xs_framework_init_meta_boxes', 9999 );

function xs_framework_init_meta_boxes() {
    if( ! class_exists( 'cmb_Meta_Box' ) )
        require_once(plugin_dir_path( __FILE__ ) . 'meta-boxes.php');
}


add_filter('locale', 'xs_framework::set_locale');

include 'html.php';
include 'languages.php';
include 'browser.php';

class xs_framework
{
        use html;
        use languages;
        use browser;
        
        static function get_option($selected = NULL) 
        {
                $default = array(
                        'available_languages' => array('en_GB' => xs_framework::get_lang_property('en_GB')),
                        'frontend_language' => 'en_GB',
                        'backend_language' => 'en_GB'
                );
                
                $option = get_option('xs_framework_options', array());
                $option += $default;
                
                if($selected != NULL)
                        return isset($option[$selected]) ? $option[$selected] : FALSE;
                else
                        return $option;
        }
        
        static function init_admin_style()
        {
                wp_enqueue_style('xs_framework_admin_style', plugins_url('style/admin.css', __FILE__));
        }
        
        static function init_admin_script()
        {
                wp_enqueue_script('xs_framework_admin_script', plugins_url('js/functions.js', __FILE__));
        }
        
        static function user_role($role, $user_id = NULL)
        {      
                if($user_id == NULL)
                        $user_id = get_current_user_id();
                if($user_id < 1)
                        return FALSE;
                
                $standard_roles = array(0 => 'subscriber', 1 => 'contributor', 2 => 'author', 3 => 'editor', 4 => 'administrator');
                $user_roles = get_userdata($user_id)->roles; 
                if(count($user_roles) != 1) //FIXME: Can user have more roles?
                        return FALSE;
                foreach($standard_roles as $key => $value) {
                        if($role == $value)
                                $find_need_role = $key;
                        if($user_roles[0] == $value) {
                                $find_user_role = $key;
                        }
                }
                return $find_user_role >= $find_need_role;
        }
} 
?>
