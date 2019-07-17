<?php
/*
Plugin Name: XSoftware Framework
Description: Framework for xsoftware wordpress plugins.
Version: 1.0
Author: Luca Gasperini
Author URI: https://xsoftware.it/
Text Domain: xsoftware_framework
*/

if(!defined("ABSPATH")) die;

if (!class_exists("xs_framework")) :

include 'html.php';
include 'html-utils.php';
include 'languages.php';
include 'browser.php';
include 'user.php';
include 'menus.php';

define('XS_CONTENT_DIR', WP_CONTENT_DIR.'/xsoftware/');

/*
*  XSoftware Framework Plugin Class
*  The following class is used to define framework functions
*/
class xs_framework
{
        /* Include the HTML function to rapid development */
        use html;
        /* Include the complex HTML function to rapid development */
        use html_utils;
        /* Include the global language management */
        use languages;
        /* Include the specific browser functions */
        use browser;
        /* Include the user management functions */
        use user;
        /* Include the global nav menu management */
        use menus;

        /*
        *  array : get_option : string
        *  This method is used to fetch the framework options
        *  $selected is the name of suboption in the array to fetch instead fetch all
        */
        static function get_option($selected = NULL)
        {
                /* Get the option using wordpress API */
                $option = get_option('xs_framework_options', array());
                /* Check if is $option empty, so get default option */
                if(empty($option)) {
                        xs_framework::download_language('en_GB');
                        $option = [
                                'available_languages' => ['en_GB'],
                                'default_language' => 'en_GB',
                                'plugins' => [
                                ]
                        ];
                }
                /*
                *  Check if $selected is not on default value, if so return $option at $selected
                *  or FALSE is $option at $selected not exists, return all options
                *  if $selected is the default value
                */
                if($selected !== NULL)
                        return isset($option[$selected]) ? $option[$selected] : FALSE;
                else
                        return $option;
        }

        /*
        *  bool : register_plugin : string, string, array
        *  This method is used to register an option of a plugin in the framework
        *  $id is the id of plugin, be sure that aren't two plugin with same id
        *  $option is the name of the option on the plugin
        *  $settings is an array where some settings can be stored, btw is not used!
        */
        /* TODO: is useless $settings? */
        static function register_plugin($id, $option, $settings = array())
        {
                /* Get the option using wordpress API */
                $framework_option = get_option('xs_framework_options', array());
                /* Check if the id is already register, if so return TRUE */
                if(isset($framework_option['plugins'][$id]))
                        return TRUE;
                /* Add the option on register plugin */
                $framework_option['plugins'][$id]['option'] = $option;
                /* Add the settings on register plugin */
                $framework_option['plugins'][$id]['settings'] = $settings;
                /* Refresh the option deleting the cache */
                wp_cache_delete ( 'alloptions','options' );
                /* Update the option on framework and return the value */
                return update_option('xs_framework_options', $framework_option);
        }

        /*
        *  bool : update_option : string, array
        *  This method is used to update an suboption in the framework options
        *  $name is the name of the suboption, if is not exists the suboption return false
        *  $values is the array/mixed of values to update
        */
        static function update_option($name, $values)
        {
                /* Get the option using wordpress API */
                $options = get_option('xs_framework_options', array());
                /* Return if $options at $name is not set */
                if(!isset($options[$name]))
                        return FALSE;

                /* Replace the value at $name with $values */
                $options[$name] = $values;
                /* Refresh the option deleting the cache */
                wp_cache_delete ( 'alloptions', 'options' );
                /* Update the option on framework and return the value */
                return update_option('xs_framework_options', $options);

        }

        /*
        *  string : url_image : string
        *  This method is used to fetch the image URL from images on framework
        *  $image is the filename of the image
        */
        /* FIXME: What if image doesn't exists? */
        static function url_image($image)
        {
                /* Return the URL of the image */
                return plugins_url('svg/'.$image, __FILE__);
        }

        /*
        *  string : code_name : string
        *  This method is used to transform a string in a code like: Amazing Code -> amazing_code
        *  $name is the string to transform in code
        */
        /* FIXME: Manage symbols and non ASCII string */
        /* FIXME: $name is a copy so can work without $tmp? */
        static function code_name($name)
        {
                /* Tmp variable to work */
                $tmp = $name;

                /* Replace the spaces with "_" */
                $tmp = str_replace(' ', '_', $tmp);
                /* Make all text in lowercase */
                $tmp = strtolower($tmp);

                /* Return the code */
                return $tmp;
        }

        /*
        *  array : directory_list : string
        *  This method is used to read from a directory all sub directory
        *  $dir is the directory to analyze
        */
        /* TODO: Review the code */
        /* TODO: Is useless method? */
        static function directory_list($dir)
        {
                /* Inizialize the output array */
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

        /*
        *  class : read_xml_file : string
        *  This method is used to read a xml file in class with properties
        *  $file_xml is the xml file to read
        */
        static function read_xml_file($file_xml)
        {
                /* Open the file stream as read */
                $file = fopen($file_xml, "r") or die("Unable to open file!");
                /* Read from the stream while the file is end */
                $xml = fread($file,filesize($file_xml));
                /* Close the stream */
                fclose($file);

                /* Parse the xml in a class */
                $offset = simplexml_load_string($xml) or die("Error: Cannot create object");
                /* Return the parsed xml */
                return $offset;
        }

        /*
        *  class : read_xml_file : string
        *  This method is used to parse a xml code in class with properties
        *  $xml is the xml code to parse
        */
        static function read_xml($xml)
        {
                /* Parse the xml in a class */
                $offset = simplexml_load_string($xml) or die("Error: Cannot create object");
                /* Return the parsed xml */
                return $offset;
        }

        /*
        *  string : get_content_file_url : string
        *  This method is used to transform a file path on content directory in the URL
        *  $file is the file path on content directory
        */
        /* TODO: What if the file is not on content or not exists? */
        static function get_content_file_url($file)
        {
                /* Replace the path in a URL */
                $url = str_replace( WP_CONTENT_DIR, WP_CONTENT_URL, $file );
                /* return the URL */
                return $url;
        }

        /*
        *  array : get_wp_pages_link : void
        *  This method is used to fetch all pages on wordpress with their link and the title
        */
        static function get_wp_pages_link()
        {
                /* Fetch all pages */
                $pages = get_pages();
                /* Inizialize the output array */
                $offset = array();
                /* Loop the pages and store in output array with format: $offset[link] = title */
                foreach ( $pages as $page ) {
                        $offset[get_page_link( $page->ID )] = $page->post_title;
                }
                /* Return the output array */
                return $offset;
        }

        static function can_use_cookie()
        {
                return isset($_COOKIE['xs_framework_privacy']) && $_SESSION['xs_framework_privacy'] === 'accept';
        }
}

endif;

include 'framework-options.php';

/* If is not administration panel add the menu language on navbar menu */
if(!is_admin())
        add_filter('wp_get_nav_menu_items', 'get_menu_by_language', 10, 2);

/*
*  array : get_menu_by_language : array, class
*  This method is used to add the menu language on navbar menu
*  $items is the array of classes with all items on navbar menu
*  $args is a class with current menu definition
*/
function get_menu_by_language($items, $args)
{
        /* Get all options from the framework */
        $options = xs_framework::get_option();
        /* Get the slug from the current menu */
        $current_menu = $args->slug;
        /* Get current user language */
        $user_lang = xs_framework::get_user_language();

        /* Check if exists the menu for the user language */
        if(isset($options['menu'][$user_lang])) {
                /* Use the menu for the current user language */
                $menu = $options['menu'][$user_lang]['slug'];
                /* Get the domain number for the current user language */
                $domain = $options['menu'][$user_lang]['domain'];
        } else {
                /* Use the default language if cannot use the user language */
                $default_lang = $options['default_language'];
                /* Use the menu for the default language */
                $menu = $options['menu'][$default_lang]['slug'];
                /* Get the domain number for the default language */
                $domain = $options['menu'][$default_lang]['domain'];
        }

        /* Check if the current menu is the same of the user/default language menu */
        if($current_menu === $menu) {
                /* Stop the loop */
                return apply_filters('xs_framework_menu_items', $items, $domain);
        } else {
                /* Loop with new defined menu */
                return wp_get_nav_menu_items($menu);
        }
}

/* Inizialize session variable and some style and javascript */
add_action( 'init', 'xs_framework_session_init', 0 );

/*
*  void : xs_framework_session_init : void
*  This method is used to initilize the session, the style and the javascript
*/
function xs_framework_session_init()
{
        /* If session is not inizialized, initilizes it */
        if( !session_id() )
                session_start();

        /* If is not on administration panel */
        if(!is_admin()) {
                /* Add font awesome css on all frontend pages */
                wp_enqueue_style(
                        'xs_framework_fontawesome_style',
                        plugins_url('style/fontawesome/css/all.min.css', __FILE__)
                );
                /* Add user javascript for all frontend pages */
                wp_enqueue_script(
                        'xs_framework_user_script',
                        plugins_url('js/user.min.js', __FILE__)
                );
        } else {
                /* Add administration css on all backend pages */
                wp_enqueue_style(
                        'xs_framework_admin_style',
                        plugins_url('style/admin.min.css', __FILE__)
                );
                /* Add administration javascript on all backend pages */
                wp_enqueue_script(
                        'xs_framework_admin_script',
                        plugins_url('js/admin.min.js', __FILE__)
                );
        }
}

add_action( 'plugins_loaded', 'xs_framework_init', 0 );

/*
*  void : xs_framework_init : void
*  This method is used to initilize the framework
*/
function xs_framework_init()
{
        /* Take language from browser setting */
        $language = xs_framework::language_browser();

        /* Set as the user language */
        $language = xs_framework::set_user_language($language);
}

add_action( 'wp_footer', 'xs_framework_privacy' );

function xs_framework_privacy()
{

        if(isset($_COOKIE['xs_framework_privacy']) && $_COOKIE['xs_framework_privacy'] === 'accept') {
                if(isset($_SESSION['xs_framework_privacy']) && $_SESSION['xs_framework_privacy'] !== 'accept') {
                        $_SESSION['xs_framework_privacy'] = 'accept';
                        echo '<script>location.reload();</script>';
                }
                return;
        }
        if(!isset($_SESSION['xs_framework_privacy']) || empty($_SESSION['xs_framework_privacy']))
                $_SESSION['xs_framework_privacy'] = 'show';

        echo apply_filters('xs_framework_privacy_show', null);

}

?>
