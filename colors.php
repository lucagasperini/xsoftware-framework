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

}

?>
