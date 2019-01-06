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
        
        static function create_checkbox_input($settings)
        {
                $default_settings = array('options' => array(), 'defaults' => array(), 'name' => '', 'compare' => '', 'field_name' => '', 'return' => false);
                $settings += $default_settings;
                
                $value = '';
                if(isset($settings['defaults'][$settings['name']]))
                        $value = (isset($settings['options'][$settings['name']])) ? $settings['options'][$settings['name']] : $settings['defaults'][$settings['name']];
                        
                $name = (empty($settings['field_name'])) ? $settings['name'] : $settings['field_name'];
                $checked = ($value == $settings['compare']) ? 'checked' : '';
                
                $return_string = "<input type='checkbox' name='".$name."' ".$checked." />";
                
                if($settings['return'] == false)
                        echo $return_string;
                else
                        return $return_string;
        }

        static function create_text_input($settings)
        {
        
                $default_settings = array('options' => array(), 'defaults' => array(), 'name' => '', 'readonly' => '', 'type' => 'text', 'field_name' => '', 'return' => false);
                $settings += $default_settings;
                
                $value = '';
                if(isset($settings['defaults'][$settings['name']]))
                        $value = (isset($settings['options'][$settings['name']])) ? $settings['options'][$settings['name']] : $settings['defaults'][$settings['name']];
                        
                $name = (empty($settings['field_name'])) ? $settings['name'] : $settings['field_name'];
                $readonly = (empty($settings['readonly'])) ? '' : 'readonly';
                
                $return_string = "<input type='".$settings['type']."' name='". $name . "' value='".$value."' " . $readonly  . "/>";
                
                if($settings['return'] == false)
                        echo $return_string;
                else
                        return $return_string;
        }
        
        
        static function create_table($settings)
        {
                $default_settings = array('options' => array(), 'defaults' => array(), 'class' => '', 'headers' => array(), 'data' => array( array() ) );
                $settings += $default_settings;
                
                $class = empty($settings['class']) ? "" :  "class=\"".$settings['class']."\"";
                echo "<table ". $class ." ><tr>";
                
                foreach($settings['header'] as $header)
                        echo "<th>" . $header . "</th>";

                foreach($settings['data'] as $row) {
                        echo '<tr>';
                        foreach($row as $element)
                                echo "<td>".$element."</td>";
                        echo "</tr>";
                }

                echo "</table>";

        }
} 
?>
