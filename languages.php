<?php

require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );

trait languages
{
        static private $translations = NULL;
        
        static private function check_translation()
        {
                if(isset(self::$translations) && !empty(self::$translations)) {
                        return self::$translations;
                } else {
                        self::$translations = wp_get_available_translations();
                        return self::$translations;
                }
        }
        
        static function get_lang_name_list() 
        {
                self::check_translation();
                
                $list = array();
                foreach(self::$translations as $code => $prop) {
                      $list[$code] = $prop['english_name'];
                }
                return $list;
        }
        
        static function get_lang_download_list() 
        {
                self::check_translation();
                
                $list = array();
                foreach(self::$translations as $code => $prop) {
                      $list[$code] = $prop['package'];
                }
                return $list;
        }
        
        static function get_lang_download($lang)
        {
                self::check_translation();
               
                return self::$translations[$lang]['package'];
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
        
        static function get_user_language()
        {
                return isset($_COOKIE['xs_framework_user_language']) ? $_COOKIE['xs_framework_user_language'] : self::get_option('frontend_language');
        }
        
}
?>
