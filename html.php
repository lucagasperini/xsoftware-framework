<?php
trait html
{
        static function create_input_checkbox($settings)
        {
                $default_settings = array('class' => '', 'value' => '', 'name' => '', 'compare' => '', 'return' => false);
                $settings += $default_settings;
                
                $value =        empty($settings['value'])       ? "" : "value=\"".$settings['value']."\"";
                $name =         empty($settings['name'])        ? "" : "name=\"" . $settings['name'] . "\"";
                $class =        empty($settings['class'])       ? "" : "class=\"".$settings['class']."\"";
                $checked =      $settings['value'] != $settings['compare']   ? "" : "checked";
                
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
                                        'compare_key' => true, 
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
                                        ($settings['compare_key'] !== true && $value === $settings['selected'] ) ||
                                        ($settings['compare_key'] === true && $key === $settings['selected'] )
                                )
                                        $return_string .= '<option value="'. $key .'" selected>'.$value.'</option>';
                                else
                                        $return_string .= '<option value="'. $key .'">'.$value.'</option>';
                        }
                } else { 
                        foreach($settings['data'] as $key => $value ) {
                                if(
                                        ($settings['compare_key'] !== true && $value === $settings['selected']) ||
                                        ($settings['compare_key'] === true && $key === $settings['selected'])
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
                $default_settings = array( 'href' => '', 'class' => '', 'text' => '', 'title' => '', 'type' => '', 'hreflang' => '', 'download' => false, 
'return' => false);
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
        static function create_upload_file($settings)
        {
                $default_settings = array( 
                'name' => '', 
                'class' => '',
                'id' => '',
                'return' => false);
                $settings += $default_settings;
                
                $name =         empty($settings['name'])        ? "" : " name=\"" . $settings['name'] . "\"";
                $class =        empty($settings['class'])       ? "" : " class=\"".$settings['class']."\"";
                $id =           empty($settings['class'])       ? "" : " class=\"".$settings['class']."\"";
                
                $return_string = "<input type=\"file\" " . $name . $id . $class . ">";
                
                if($settings['return'] == false)
                        echo $return_string;
                else
                        return $return_string;
        }
}
?>
