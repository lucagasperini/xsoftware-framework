<?php

if(!defined("ABSPATH")) die;

trait browser {
        /**
        * This function returns the URL used in the browser.
        *
        * @return string URL in the browser.
        */
        static function get_browser_url()
        {
                if(!isset($_SERVER['HTTP_HOST']) || !isset($_SERVER['REQUEST_URI']))
                        return false;
                
                //$options = uls_get_options();
                
                if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]=="on")
                        $url = "https://";
                else
                        $url = "http://";
                        
                $url .= $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
                
                return $url;
        }
        
        static function append_query_url($url, $query)
        {
                $offset = $url;
                if(strpos($offset, '?') === false)
                {
                        $offset .= '?';
                }
                
                foreach($query as $key => $value)
                        $offset .= '&' . $key . '=' . $value;
                
                return $offset;
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
        $languages = xs_framework::get_available_language(array('language' => TRUE, 'english_name' => FALSE));
        
        //validate the languages
        $max = 0.0;
        $maxLang = false;
        foreach($parsedLanguages as $k => &$v){
        if(false !== $v['code']){
                //search the language in the installed languages using the language and location
                foreach($languages as $vLang){
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
                foreach($languages as $vLang){
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
}
?>
