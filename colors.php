<?php

trait colors
{

        static function get_colors_theme($name)
        {
                $colors = xs_framework::get_option('available_colors');
                $offset = isset($colors[$name]) ? $colors[$name] : '';
                return $offset;
        }
        
        static function menu_colors($items)
        {
                $colors = xs_framework::get_option('available_colors');
                $offset = '';
                $offset .= '<li>
                <a href="">Colors</a><ul class="sub-menu">';
                
                
                
                foreach($colors as $name => $prop)
                        $offset .= '<li><a onclick="xs_colors_theme_select(\'' . $name . '\');" href="">'.
                        $name.'</a></li>';
                        
                $offset .= '</ul></li>';
                return $items . $offset;
        }
        
        function generate_css($colors, $filename) 
        {
                $xs_dir = WP_CONTENT_DIR . '/xsoftware/';
                if(is_dir($xs_dir) === FALSE)
                        mkdir($xs_dir, 0774);
                $colors_dir = $xs_dir . 'style/';
                if(is_dir($colors_dir) === FALSE)
                        mkdir($colors_dir, 0774);
                
                $css = '';
                
                foreach($colors as $prop) {
                        $name = $prop['name'];
                        foreach($prop as $type => $value) {
                                $class = '';
                                $not_empty = FALSE;
                                
                                if($type === 'default')
                                        $class .= $name . '{';
                                else
                                        $class .= $name . ':' . $type . '{';
                                        
                                if(!empty($value['text'])) {
                                        $class .= 'color:' . $value['text'] . ';';
                                        $not_empty = TRUE;
                                }
                                if(!empty($value['bg'])) {
                                        $class .= 'background-color:' . $value['bg'] . ';';
                                        $not_empty = TRUE;
                                }
                                if(!empty($value['bord'])) {
                                        $class .= 'border-color:' . $value['bord'] . ';';
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

}

?>
