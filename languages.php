<?php

require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );

class xs_language
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
        static function get_name_list() 
        {
                self::check_translation();
                
                $list = array();
                foreach(self::$translations as $code => $prop) {
                      $list[$code] = $prop['english_name'];
                }
                return $list;
        }
        static function get_download_list() 
        {
                self::check_translation();
                
                $list = array();
                foreach(self::$translations as $code => $prop) {
                      $list[$code] = $prop['package'];
                }
                return $list;
        }
        
        static function get_download($lang)
        {
                self::check_translation();
               
                return self::$translations[$lang]['package'];
        }
}
?>
