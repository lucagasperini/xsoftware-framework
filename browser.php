<?php
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
}
?>
