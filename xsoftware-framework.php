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

add_action( 'plugins_loaded', 'load_framework', 0 ); //Load it first!

function load_framework()
{
        include 'framework-options.php';
}

add_action( 'init', 'xs_framework_init', 0 );

function xs_framework_init() 
{
        $options = xs_framework::get_option();
        //take language from browser setting
        $language = xs_framework::language_browser();
                
        if(isset($options['available_languages'][$language])) {
                $language = xs_framework::cookie_language($language);
        }
}

include 'html.php';
include 'languages.php';
include 'browser.php';
include 'style.php';
include 'user.php';

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
                                'style' => array( 
                                        '.xs_primary' => array(
                                                'default' => array( 'text' => 'primary' , 'bg' => '', 'bord' => ''), 
                                                'hover' => array( 'text' => '' , 'bg' => '', 'bord' => ''), 
                                                'focus' => array( 'text' => '' , 'bg' => '', 'bord' => ''),
                                        ),
                                        '.xs_secondary' => array(
                                                'default' => array( 'text' => 'secondary' , 'bg' => '', 'bord' => ''), 
                                                'hover' => array( 'text' => '' , 'bg' => '', 'bord' => ''), 
                                                'focus' => array( 'text' => '' , 'bg' => '', 'bord' => ''),
                                        ),
                                        '.xs_body' => array(
                                                'default' => array( 'text' => 'text' , 'bg' => 'background', 'bord' => ''), 
                                                'hover' => array( 'text' => '' , 'bg' => '', 'bord' => ''), 
                                                'focus' => array( 'text' => '' , 'bg' => '', 'bord' => ''),
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
        static function get_products_name()
        {
                $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

                if (mysqli_connect_error()) {
                        die("Connection to database failed: " . mysqli_connect_error());
                }
                if(is_resource($conn)) { 
                        $conn->query($conn, "SET NAMES 'utf8'"); 
                        $conn->query($conn, "SET CHARACTER SET 'utf8'"); 
                } 
                $offset = array();
                $sql = "SELECT name, title FROM xs_products WHERE lang='en_GB'"; //FIXME: FORCE LANG EN
                $result = $conn->query($sql);
                if (!$result) {
                        echo "Could not run query: SQL_ERROR -> " . $conn->error . " SQL_QUERY -> " . $sql;
                        exit;
                }
                if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                                $offset[$row['name']] = $row['title'];
                        }
                }
                $result->close();
                return $offset;
        }
} 
?>
