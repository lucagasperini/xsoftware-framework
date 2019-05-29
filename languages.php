<?php

if(!defined("ABSPATH")) die;

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

        static function get_lang_property($lang)
        {
                self::check_translation();

                if(empty($lang))
                        return array();

                $prop = self::$translations[$lang];

                if(strpos($lang, '_') !== FALSE)
                        list($name, $iso) = explode('_', $lang);
                else
                        $iso = $lang;

                $prop['iso'] = strtolower($iso);

                return $prop;
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

        static function get_available_language($query = array())
        {
                $default = array(
                        'language' => FALSE,
                        'version' => FALSE,
                        'updated' => FALSE,
                        'english_name' => TRUE,
                        'native_name' => FALSE,
                        'package' => FALSE,
                        'iso' => FALSE,
                        'strings' => FALSE
                );

                $query += $default;

                /*
                   Fetch WordPress language packs and current
                   available languages on the framework
                */
                $wp_languages = wp_get_available_translations();
                $xs_languages = xs_framework::get_option('available_languages');

                foreach($xs_languages as $key) {
                        $languages[$key] = $wp_languages[$key];

                        /*
                           Removing from $languages array
                           all FALSE values on query
                        */
                        foreach($query as $type => $value) {
                                if($value === FALSE)
                                        unset($languages[$key][$type]);
                        }
                        /*
                           If is requested 'iso' cut $key if is present '_'
                           and use this value as iso format
                        */
                        if($query['iso'] === TRUE)
                        {
                                if(strpos($key, '_') !== FALSE)
                                        list($name, $iso) = explode('_', $key);
                                else
                                        $iso = $lang;

                                $languages[$key]['iso'] = strtolower($iso);
                        }
                        /*
                           If is requested only one value from the query
                           reset array to get the value with $languages[$key]
                           instead the default $languages[$key][$type]
                        */
                        if(count($languages[$key]) === 1)
                                $languages[$key] = reset($languages[$key]);
                }

                return $languages;
        }
        static function set_user_language($language)
        {
                if($language == NULL || $language == false)
                        return FALSE;

                if(!isset($_COOKIE['xs_framework_user_language'])) {
                        $_SESSION['xs_framework_user_language'] = $language;
                        setcookie('xs_framework_user_language', $language, time()+24*60*60, "/"); //set a cookie for 24 hour
                }

                return TRUE;
        }

        static function get_user_language()
        {
                if(!isset($_COOKIE['xs_framework_user_language'])) {
                        return isset($_SESSION['xs_framework_user_language']) ?
                                $_SESSION['xs_framework_user_language'] :
                                xs_framework::get_option('default_language');
                }
                else
                        return $_COOKIE['xs_framework_user_language'];
        }


        static function download_language($lang_code)
        {
                $remoteFile = xs_framework::get_lang_download($lang_code);

                $lang_dir = WP_CONTENT_DIR . '/languages';
                $package = sys_get_temp_dir()."/package.zip";

                $flag = file_put_contents(
                        $package,
                        file_get_contents($remoteFile)
                );

                if($flag === FALSE || !class_exists('ZipArchive'))
                        return FALSE;

                $zip = new ZipArchive;

                if ($zip->open($package) !== TRUE)
                        return FALSE;

                $zip->extractTo(
                        $lang_dir,
                        array(
                                $lang_code.".mo",
                                $lang_code.".po"
                        )
                );

                $zip->close();
                unlink($package);

                return TRUE;
        }

        static function remove_language($lang_code)
        {
                $lang_dir = WP_CONTENT_DIR . '/languages/';
                unlink($lang_dir.$lang_code.'.mo');
                unlink($lang_dir.$lang_code.'.po');
                return TRUE;
        }


}
?>
