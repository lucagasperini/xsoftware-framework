<?php

if(!defined("ABSPATH")) die;

trait style
{
        static function generate_css($colors, $filename, $typedef = array()) 
        {
                $xs_dir = WP_CONTENT_DIR . '/xsoftware/';
                if(is_dir($xs_dir) === FALSE)
                        mkdir($xs_dir, 0774);
                $colors_dir = $xs_dir . 'style/';
                if(is_dir($colors_dir) === FALSE)
                        mkdir($colors_dir, 0774);
                
                $css = '';
                
                foreach($colors as $name => $prop) {
                        foreach($prop as $type => $value) {
                                $class = '';
                                $not_empty = FALSE;
                                
                                if($type === 'default')
                                        $class .= $name . '{';
                                else
                                        $class .= $name . ':' . $type . '{';
                                        
                                if(!empty($value['text'])) {
                                        $val = isset($typedef[$value['text']]) ? $typedef[$value['text']] : $value['text'];
                                        $class .= 'color:' . $val . ';';
                                        $not_empty = TRUE;
                                }
                                if(!empty($value['bg'])) {
                                        $val = isset($typedef[$value['bg']]) ? $typedef[$value['bg']] : $value['bg'];
                                        $class .= 'background-color:' . $val . ';';
                                        $not_empty = TRUE;
                                }
                                if(!empty($value['bord'])) {
                                        $val = isset($typedef[$value['bord']]) ? $typedef[$value['bord']] : $value['bord'];
                                        $class .= 'border-color:' . $val . ';';
                                        $not_empty = TRUE;
                                }
                                
                                $class .= '}';
                                if($not_empty == TRUE)
                                        $css .= $class;
                        }
                }
                $file_style = fopen($colors_dir.$filename, 'w') or die('Unable to open file!');
                fwrite($file_style, $css);
                fclose($file_style);
        }
        
        function install_style_pack($style)
        {
                $not_empty = FALSE;
                $options = xs_framework::get_option('style');
                foreach($style as $class => $values)
                {
                        if(!isset($options[$class])) {
                                $options[$class] = $values;
                                $not_empty = TRUE;
                        }
                }
                if($not_empty === TRUE)
                        xs_framework::update_option('style', $style);
        }

}

?>
