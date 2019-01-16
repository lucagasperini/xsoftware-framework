<?php
/*
Plugin Name: XSoftware Framework
Description: Framework for xsoftware wordpress plugins.
Version: 1.0
Author: Luca Gasperini
Author URI: https://xsoftware.eu/
Text Domain: xsoftware_products
*/

add_action( 'plugins_loaded', 'load_framework', 0 );

function load_framework()
{

}

add_action( 'init', 'xs_framework_init_meta_boxes', 9999 );

function xs_framework_init_meta_boxes() {
    if( ! class_exists( 'cmb_Meta_Box' ) )
        require_once(plugin_dir_path( __FILE__ ) . 'meta-boxes.php');
}


class xs_framework
{
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
        
        static function create_input_checkbox($settings)
        {
                $default_settings = array('class' => '', 'value' => '', 'name' => '', 'compare' => '', 'return' => false);
                $settings += $default_settings;
                
                $value =        empty($settings['value'])       ? "" : "value=\"".$settings['value']."\"";
                $name =         empty($settings['name'])        ? "" : "name=\"" . $settings['name'] . "\"";
                $class =        empty($settings['class'])       ? "" : "class=\"".$settings['class']."\"";
                $checked =      $name != $settings['compare']   ? "" : "checked";
                
                $return_string = "<input type='checkbox' ".$class." " . $name . " ".$checked." />";
                
                if($settings['return'] == false)
                        echo $return_string;
                else
                        return $return_string;
        }

        static function create_input($settings)
        {
        
                $default_settings = array('class' => '', 'value' => '', 'name' => '', 'readonly' => '', 'type' => 'text', 'return' => false);
                $settings += $default_settings;
                
                $value =        empty($settings['value'])       ? "" : "value=\"".$settings['value']."\"";
                $name =         empty($settings['name'])        ? "" : "name=\"" . $settings['name'] . "\"";
                $class =        empty($settings['class'])       ? "" : "class=\"".$settings['class']."\"";
                $type =         empty($settings['type'])        ? "" : "type=\"".$settings['type']."\"";
                $readonly =     empty($settings['readonly'])    ? "" : "readonly";
                
                $return_string = "<input " . $class . " " . $type . " ". $name . " " . $value . " " . $readonly  . "/>";
                
                if($settings['return'] == false)
                        echo $return_string;
                else
                        return $return_string;
        }
        
        
        static function create_table($settings)
        {
                $default_settings = array('class' => '', 'headers' => array(), 'data' => array( array() ) );
                $settings += $default_settings;
                
                $class = empty($settings['class']) ? "" :  "class=\"".$settings['class']."\"";
                echo "<table ". $class ." ><tr>";
                
                foreach($settings['headers'] as $header)
                        echo "<th>" . $header . "</th>";

                foreach($settings['data'] as $row) {
                        echo '<tr>';
                        foreach($row as $element)
                                echo "<td>".$element."</td>";
                        echo "</tr>";
                }

                echo "</tr></table>";

        }
        
        static function create_button($settings)
        {
                $default_settings = array( 'name' => '', 'class' => '', 'value' => '', 'text' => '', 'onclick' => '', 'return' => false);
                $settings += $default_settings;
                
                $text = $settings['text'];
                $value =        empty($settings['value'])       ? "" : "value=\"".$settings['value']."\"";
                $name =         empty($settings['name'])        ? "" : "name=\"" . $settings['name'] . "\"";
                $class =        empty($settings['class'])       ? "" : "class=\"".$settings['class']."\"";
                $onclick =      empty($settings['onclick'])     ? "" : "onclick=\"".$settings['onclick']."\"";
                
                $return_string = "<button ".$class." ". $name . " " . $value . " " . $onclick . ">".$text."</button>";
                
                if($settings['return'] == false)
                        echo $return_string;
                else
                        return $return_string;
        }
        
        static function create_textarea($settings)
        {
                $default_settings = array( 'name' => '', 'class' => '', 'value' => '', 'text' => '', 'return' => false);
                $settings += $default_settings;
                
                $text = $settings['text'];
                $value = empty($settings['value']) ? "" : "value=\"".$settings['value']."\"";
                $name = empty($settings['name']) ? "" : "name=\"" . $settings['name'] . "\"";
                $class = empty($settings['class']) ? "" :  "class=\"".$settings['class']."\"";
                
                $return_string = "<textarea ".$class." ". $name . " " . $value . ">".$text."</textarea>";
                
                if($settings['return'] == false)
                        echo $return_string;
                else
                        return $return_string;
        }
        
        static function create_select($settings)
        {
                $default_settings = array( 'name' => '', 
                                        'class' => '', 
                                        'data' => array(), 
                                        'selected' => '', 
                                        'compare_key' => false, 
                                        'reverse' => false,
                                        'return' => false
                                        );
                                        
                $settings += $default_settings;
                
                $name = empty($settings['name']) ? "" : "name=\"" . $settings['name'] . "\"";
                $class = empty($settings['class']) ? "" :  "class=\"".$settings['class']."\"";
                
                $return_string = "<select ".$class." ". $name . " >";
                
                if($settings['reverse'] == false) {
                        foreach($settings['data'] as $key => $value ) {
                                if(
                                        ($value == $settings['selected'] && $settings['compare_key'] !== true) ||
                                        ($key == $settings['selected'] && $settings['compare_key'] === true)
                                )
                                        $return_string .= '<option value="'. $key .'" selected>'.$value.'</option>';
                                else
                                        $return_string .= '<option value="'. $key .'">'.$value.'</option>';
                        }
                } else { 
                        foreach($settings['data'] as $key => $value ) {
                                if(
                                        ($value == $settings['selected'] && $settings['compare_key'] !== true) ||
                                        ($key == $settings['selected'] && $settings['compare_key'] === true)
                                )
                                        $return_string .= '<option value="'. $value .'" selected>'.$key.'</option>';
                                else
                                        $return_string .= '<option value="'. $value .'">'.$key.'</option>';
                        }
                }
                
                $return_string .= "</select>";
                
                if($settings['return'] == false)
                        echo $return_string;
                else
                        return $return_string;
        }
        
        static function create_link($settings)
        {
                $default_settings = array( 'href' => '', 'class' => '', 'text' => '', 'title' => '', 'type' => '', 'hreflang' => '', 'download' => false, 'return' => false);
                $settings += $default_settings;
                
                $href =         empty($settings['href'])         ? "" : "href=\"" . $settings['href'] . "\"";
                $class =        empty($settings['class'])        ? "" : "class=\"".$settings['class']."\"";
                $title =        empty($settings['title'])        ? "" : "title=\"".$settings['title']."\"";
                $type =         empty($settings['type'])         ? "" : "type=\"" . $settings['type'] . "\"";
                $hreflang =     empty($settings['hreflang'])     ? "" : "hreflang=\"".$settings['hreflang']."\"";
                $download =     empty($settings['download'])     ? "" : "download";
                $text =         $settings['text'];
                
                $return_string = "<a ". $href ." ". $class . " ". $title . " " . $type . " " . $hreflang . " " . $download . ">" . $text . "</a>";
                
                if($settings['return'] == false)
                        echo $return_string;
                else
                        return $return_string;
        }
} 
?>
