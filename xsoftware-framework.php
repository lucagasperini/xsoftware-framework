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

if (!class_exists("xs_framework")) :

include 'html.php';
include 'html-utils.php';
include 'languages.php';
include 'browser.php';
include 'user.php';
include 'menus.php';
include 'currency.php';

define('XS_CONTENT_DIR', WP_CONTENT_DIR.'/xsoftware/');

class xs_framework
{
        use html;
        use html_utils;
        use languages;
        use browser;
        use user;
        use menus;
        use currency;

        static function get_option($selected = NULL)
        {
                $option = get_option('xs_framework_options', array());
                if(empty($option)) {
                        xs_framework::download_language('en_GB');
                        $default = array(
                                'available_languages' => array('en_GB'),
                                'default_language' => 'en_GB',
                                'plugins' => [
                                        ''
                                ]
                        );
                        $option += $default;
                }
                if($selected != NULL)
                        return isset($option[$selected]) ? $option[$selected] : FALSE;
                else
                        return $option;
        }

        static function register_plugin($id, $option, $settings = array())
        {
                $framework_option = get_option('xs_framework_options', array());
                if(isset($framework_option['plugins'][$id]))
                        return TRUE;
                $framework_option['plugins'][$id]['option'] = $option;
                $framework_option['plugins'][$id]['settings'] = $settings;
                wp_cache_delete ( 'alloptions','options' );
                return update_option('xs_framework_options', $framework_option);

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

        static function directory_list($dir)
        {
                $offset = array();

                if ($handle = opendir($dir)) {
                        while (false !== ($entry = readdir($handle))) {
                                if($entry !== '.' && $entry !== '..')
                                        $offset[] = $entry;
                        }
                        closedir($handle);
                }

                return $offset;
        }

        static function read_xml_file($file_xml)
        {
                $file = fopen($file_xml, "r") or die("Unable to open file!");
                $xml = fread($file,filesize($file_xml));
                fclose($file);

                $offset = simplexml_load_string($xml) or die("Error: Cannot create object");
                return $offset;
        }

        static function read_xml($xml)
        {
                $offset = simplexml_load_string($xml) or die("Error: Cannot create object");
                return $offset;
        }


        static function get_content_file_url($file)
        {
                $url = str_replace( WP_CONTENT_DIR, WP_CONTENT_URL, $file );
                return $url;
        }

        static function get_wp_pages_link()
        {
                $pages = get_pages();
                $offset = array();
                foreach ( $pages as $page ) {
                        $offset[get_page_link( $page->ID )] = $page->post_title;
                }
                return $offset;
        }
}

endif;

include 'framework-options.php';

if(!is_admin())
add_filter('wp_get_nav_menu_items', 'get_menu_by_language', 10, 2);

function get_menu_by_language($items, $args)
{

        $options = xs_framework::get_option();
        $current_menu = $args->slug;
        $user_lang = xs_framework::get_user_language();

        if(isset($options['menu'][$user_lang])) {
                $menu = $options['menu'][$user_lang]['slug'];
                $domain = $options['menu'][$user_lang]['domain'];
        } else {
                $default_lang = $options['default_language'];
                $menu = $options['menu'][$default_lang]['slug'];
                $domain = $options['menu'][$default_lang]['domain'];
        }

        if($current_menu === $menu) {
                return apply_filters('xs_framework_menu_items', $items, $domain);
        } else {
                return wp_get_nav_menu_items($menu);
        }
}

add_action( 'init', 'xs_framework_session_init', 0 );

function xs_framework_session_init()
{
        if( !session_id() )
        {
                session_start();
        }

        if(!is_admin()) {
                wp_enqueue_style(
                        'xs_framework_fontawesome_style',
                        plugins_url('style/fontawesome/css/all.min.css', __FILE__)
                );
        } else {
                wp_enqueue_style(
                        'xs_framework_admin_style',
                        plugins_url('style/admin.min.css', __FILE__)
                );
                wp_enqueue_script(
                        'xs_framework_admin_script',
                        plugins_url('js/admin.min.js', __FILE__)
                );
        }
}

add_action( 'plugins_loaded', 'xs_framework_init', 0 );

function xs_framework_init()
{
        //take language from browser setting
        $language = xs_framework::language_browser();

        $language = xs_framework::set_user_language($language);
}

?>
