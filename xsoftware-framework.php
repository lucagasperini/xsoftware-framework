<?php
/*
Plugin Name: XSoftware Framework
Description: Framework for xsoftware wordpress plugins.
Version: 1.0
Author: Luca Gasperini
Author URI: https://xsoftware.it/
Text Domain: xsoftware_products
*/

if(!defined("ABSPATH")) die;

include 'html.php';
include 'languages.php';
include 'browser.php';
include 'style.php';
include 'user.php';

define('XS_CONTENT_DIR', WP_CONTENT_DIR.'/xsoftware/');

class xs_framework
{
        use html;
        use languages;
        use browser;
        use style;
        use user;
        
        static function get_option($selected = NULL) 
        {
                $option = get_option('xs_framework_options', array());
                if(empty($option)) {
                        xs_framework::download_language('en_GB');
                        $default = array(
                                'available_languages' => array('en_GB' => xs_framework::get_lang_property('en_GB')),
                                'default_language' => 'en_GB',
                                'style' => array( 
                                        '.xs_primary' => array(
                                                0 => array( 'color' => 'primary' , 'background-color' => '', 'border-color' => ''), 
                                                'hover' => array( 'color' => '' , 'background-color' => '', 'border-color' => ''), 
                                                'focus' => array( 'color' => '' , 'background-color' => '', 'border-color' => ''),
                                        ),
                                        '.xs_secondary' => array(
                                                0 => array( 'color' => 'secondary' , 'background-color' => '', 'border-color' => ''), 
                                                'hover' => array( 'color' => '' , 'background-color' => '', 'border-color' => ''), 
                                                'focus' => array( 'color' => '' , 'background-color' => '', 'border-color' => ''),
                                        ),
                                        '.xs_body' => array(
                                                0 => array( 'color' => 'text' , 'background-color' => 'background', 'border-color' => ''), 
                                                'hover' => array( 'color' => '' , 'background-color' => '', 'border-color' => ''), 
                                                'focus' => array( 'color' => '' , 'background-color' => '', 'border-color' => ''),
                                        )
                                ),
                                'colors' => array(
                                        'primary' => '#FFB342',
                                        'secondary' => '#999999',
                                        'background' => '#DDDDDD',
                                        'text' => '#222222'
                                )
                        );
                        $option += $default;
                }
                if($selected != NULL)
                        return isset($option[$selected]) ? $option[$selected] : FALSE;
                else
                        return $option;
        }
        
        static function update_option($name, $values)
        {
                $options = get_option('xs_framework_options', array());
                if(isset($options[$name])) {
                        $options[$name] = $values;
                        wp_cache_delete ( 'alloptions', 'options' );
                        return update_option('xs_framework_options', $options);
                }
                return FALSE;
        }
        
        static function init_admin_style()
        {
                wp_enqueue_style('xs_framework_admin_style', plugins_url('style/admin.css', __FILE__));
        }
        
        static function init_admin_script()
        {
                wp_enqueue_script('xs_framework_admin_script', plugins_url('js/admin.js', __FILE__));
        }
        
        static function url_image($image)
        {
                return plugins_url('img/'.$image, __FILE__);
        }
        
        static function code_name($name)
        {
                $offset = $name;
               
                $offset = str_replace(' ', '_', $offset);
                $offset = strtolower($offset);
                
                return $offset;
        }
} 

include 'framework-options.php';

add_action( 'plugins_loaded', 'xs_framework_init', 0 );

function xs_framework_init() 
{
        //session_start(); CANNOT LOAD WORDPRESS IF ENABLED
        $options = xs_framework::get_option();
        //take language from browser setting
        $language = xs_framework::language_browser();

        $language = xs_framework::set_user_language($language);
}
?>
