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

class xs_framework
{
        use html;
        use languages;
        
        static function get_option($selected = NULL) 
        {
                $default = array(
                        'available_languages' => array('en_GB' => 'English (UK)'),
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
        
        static function set_locale() 
        {
                $options = self::get_option();
                if ( is_admin() ) 
                        return $options['backend_language'];
                else
                        return $options['frontend_language'];
        }
        /**
        * This function retrieves the user language from the browser. It reads the headers sent by the browser about language preferences.
        *
        * @return mixed it returns a string containing a language code or false if there isn't any language detected.
        */
        static function language_browser(){
        if(!isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])){
                return false;
        }
        //split the header languages
        $browserLanguages = explode(',', $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
        
        

        //parse each language
        $parsedLanguages = array();
        foreach($browserLanguages as $bLang){
        //check for q-value and create associative array. No q-value means 1 by rule
        if(preg_match("/(.*);q=([0-1]{0,1}\.\d{0,4})/i",$bLang,$matches)){
                $matches[1] = strtolower(str_replace('-', '_', $matches[1]));
                $parsedLanguages []= array(
                'code' => (false !== strpos($matches[1] , '_')) ? $matches[1] : false,
                'l' => $matches[1],
                'q' => (float)$matches[2],
                );
        }
        else{
                $bLang = strtolower(str_replace('-', '_', $bLang));
                $parsedLanguages []= array(
                'code' => (false !== strpos($bLang , '_')) ? $bLang : false,
                'l' => $bLang,
                'q' => 1.0,
                );
        }
        }
        //get the languages activated in the site
        $validLanguages = uls_get_available_languages();
        
        //validate the languages
        $max = 0.0;
        $maxLang = false;
        foreach($parsedLanguages as $k => &$v){
        if(false !== $v['code']){
                //search the language in the installed languages using the language and location
                foreach($validLanguages as $vLang){
                if(strtolower($vLang) == $v['code']){
                //replace the preferred language
                if($v['q'] > $max){
                $max = $v['q'];
                $maxLang = $vLang;
                }
                }
                }//check for the complete code
        }
        }

        //if language hasn't been detected
        if(false == $maxLang){
        foreach($parsedLanguages as $k => &$v){
                //search only for the language
                foreach($validLanguages as $vLang){
                if(substr($vLang, 0, 2) == substr($v['l'], 0, 2)){
                //replace the preferred language
                if($v['q'] > $max){
                $max = $v['q'];
                $maxLang = $vLang;
                }
                }
                }//search only for the language
        }
        }

        return $maxLang;
        }

        
        static function cookie_language($language)
        {
                if($language == NULL || $language == false)  
                        return NULL;
                        
                if(!isset($_COOKIE['xs_framework_user_language'])){
                        setcookie('xs_framework_user_language', $language, time()+2*60*60, "/"); //set a cookie for 2 hour
                        return $language;
                } else {
                        return $_COOKIE['xs_framework_user_language'];
                }
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
        
        static function post_upload_file($settings)
        {
                $default_settings = array( 
                'file' => array(), 
                'dir' => '',
                'max_size' => 500000, 
                'types' => array()
                );
                $settings += $default_settings;
               
                $target_file = $settings["dir"] . basename($settings["file"]["name"]);
                $ok = 1;
                $type = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
               
                if (file_exists($target_file)) {
                        echo "Sorry, file already exists.";
                        $ok = 0;
                }
                
                if ($settings["file"]["size"] > $settings['max_size']) {
                        echo "Sorry, your file is too large.";
                        $ok = 0;
                }
                
                if(in_array($type,$settings["types"])) {
                        $ok = 0;
                }
                if ($ok == 0) {
                        echo "Sorry, your file was not uploaded.";
                } else {
                        if (move_uploaded_file($settings["file"]["tmp_name"], $target_file)) {
                                echo "The file ". basename($settings["file"]["name"]). " has been uploaded.";
                        } else {
                                echo "Sorry, there was an error uploading your file.";
                        }
                }
        }
       
} 
?>
