<?php

if(!defined("ABSPATH")) die;

trait html
{
        static function create_input_number($settings)
        {
                $default_settings = array(
                        'class' => '',
                        'value' => 1,
                        'name' => '',
                        'id' => '',
                        'min' => '0',
                        'max' => 100,
                        'step' => 1,
                        'echo' => FALSE
                );

                $settings += $default_settings;

                $value = empty($settings['value']) ? "" : "value=\"".$settings['value']."\"";
                $name =  empty($settings['name']) ? "" : "name=\"" . $settings['name'] . "\"";
                $class = empty($settings['class']) ? "" : "class=\"".$settings['class']."\"";
                $id = empty($settings['id']) ? "" : "id=\"".$settings['id']."\"";
                $min = empty($settings['min']) ? "min=\"0\"" : "min=\"".$settings['min']."\"";
                $max = empty($settings['max']) ? "" : "max=\"".$settings['max']."\"";
                $step = empty($settings['step']) ? "" : "step=\"".$settings['step']."\"";

                $return_string = "<input type='number' ".
                $value." ".$class." ".$name." ".$id." ".$value." ".$min." ".$max." ".$step."/>";

                if($settings['echo'] !== FALSE)
                        echo $return_string;
                else
                        return $return_string;
        }

        static function create_input_checkbox($settings)
        {
                $default_settings = array(
                        'class' => '',
                        'value' => 1,
                        'name' => '',
                        'compare' => '',
                        'echo' => FALSE
                );

                $settings += $default_settings;

                $value =        empty($settings['value'])       ? "" : "value=\"".$settings['value']."\"";
                $name =         empty($settings['name'])        ? "" : "name=\"" . $settings['name'] . "\"";
                $class =        empty($settings['class'])       ? "" : "class=\"".$settings['class']."\"";
                $checked =      !$settings['compare']            ? "" : "checked";

                $return_string = "
                <input type='checkbox' ".$value." ".$class." ".
                $name . " ".$checked." />";

                if($settings['echo'] !== FALSE)
                        echo $return_string;
                else
                        return $return_string;
        }

        static function create_input($settings)
        {

                $default_settings = array(
                        'id' => '',
                        'style' => '',
                        'class' => '',
                        'value' => '',
                        'name' => '',
                        'onclick' => '',
                        'readonly' => '',
                        'type' => 'text',
                        'echo' => FALSE
                );
                $settings += $default_settings;

                $value =        empty($settings['value'])       ? "" : "value=\"".$settings['value']."\"";
                $name =         empty($settings['name'])        ? "" : "name=\"" . $settings['name'] . "\"";
                $class =        empty($settings['class'])       ? "" : "class=\"".$settings['class']."\"";
                $type =         empty($settings['type'])        ? "" : "type=\"".$settings['type']."\"";
                $id =           empty($settings['id'])          ? "" : "id=\"".$settings['id']."\"";
                $style =           empty($settings['style'])       ? "" : "style=\"".$settings['style']."\"";
                $onclick =      empty($settings['onclick'])     ? "" : "onclick=\"".$settings['onclick']."\"";
                $readonly =     empty($settings['readonly'])    ? "" : "readonly";

                $return_string = "<input ".$id." ".$class." ".$style." ".$type." ".$name." ".$value." ".$onclick." ".$readonly."/>";

                if($settings['echo'] !== FALSE)
                        echo $return_string;
                else
                        return $return_string;
        }


        static function create_table($settings) //FIXME: ECHO => TRUE ?
        {
                $default_settings = array(
                        'class' => '',
                        'headers' => array(),
                        'data' => array()
                );

                $settings += $default_settings;

                if(empty($settings['data']))
                        return;

                $class = empty($settings['class']) ? "" :  "class=\"".$settings['class']."\"";
                echo "<table ". $class ." >";

                foreach($settings['headers'] as $header)
                        echo "<th>" . $header . "</th>";

                foreach($settings['data'] as $row) {
                        echo '<tr>';
                        foreach($row as $element)
                                echo "<td>".$element."</td>";
                        echo "</tr>";
                }

                echo "</table>";

        }

        static function create_button($settings)
        {
                $default_settings = array(
                        'name' => '',
                        'class' => '',
                        'value' => '',
                        'text' => '',
                        'onclick' => '',
                        'echo' => FALSE
                );

                $settings += $default_settings;

                $text = $settings['text'];
                $value =        empty($settings['value'])       ? "" : "value=\"".$settings['value']."\"";
                $name =         empty($settings['name'])        ? "" : "name=\"" . $settings['name'] . "\"";
                $class =        empty($settings['class'])       ? "" : "class=\"".$settings['class']."\"";
                $onclick =      empty($settings['onclick'])     ? "" : "onclick=\"".$settings['onclick']."\"";

                $return_string = "<button ".$class." ". $name . " " . $value . " " . $onclick . ">".$text."</button>";

                if($settings['echo'] !== FALSE)
                        echo $return_string;
                else
                        return $return_string;
        }

        static function create_textarea($settings)
        {
                $default_settings = array(
                        'name' => '',
                        'class' => '',
                        'value' => '',
                        'text' => '',
                        'echo' => FALSE
                );
                $settings += $default_settings;

                $text = empty($settings['text']) ? "" : $settings['text'];
                $value = empty($settings['value']) ? "" : "value=\"".$settings['value']."\"";
                $name = empty($settings['name']) ? "" : "name=\"" . $settings['name'] . "\"";
                $class = empty($settings['class']) ? "" :  "class=\"".$settings['class']."\"";

                $return_string = "<textarea ".$class." ". $name . " " . $value . ">".$text."</textarea>";

                if($settings['echo'] !== FALSE)
                        echo $return_string;
                else
                        return $return_string;
        }

        static function create_select($settings)
        {
                $default_settings = array(
                        'name' => '',
                        'class' => '',
                        'data' => array(),
                        'selected' => '',
                        'default' => '',
                        'compare_key' => TRUE,
                        'echo' => FALSE
                );

                $settings += $default_settings;
                $name = empty($settings['name']) ? "" : "name=\"" . $settings['name'] . "\"";
                $class = empty($settings['class']) ? "" :  "class=\"".$settings['class']."\"";
                $default = empty($settings['default']) ? "" : "<option disabled value=\"\" selected>".$settings['default']."</option>";

                $return_string = "<select ".$class." ". $name . " >";

                $buffer = '';
                $one_selected = FALSE;

                foreach($settings['data'] as $key => $value ) {

                        if($settings['compare_key'] !== true)
                                $compare = $value;
                        else
                                $compare = $key;

                        if($compare === $settings['selected']) {
                                $buffer .= '<option value="'. $key .'" selected>'.$value.'</option>';
                                $one_selected = TRUE;
                        } else {
                                $buffer .= '<option value="'. $key .'">'.$value.'</option>';
                        }
                }

                if(!empty($default) && $one_selected === FALSE)
                        $return_string .= $default;

                $return_string .= $buffer;
                $return_string .= "</select>";

                if($settings['echo'] !== FALSE)
                        echo $return_string;
                else
                        return $return_string;
        }

        static function create_link($settings)
        {
                $default_settings = array(
                        'href' => '',
                        'class' => '',
                        'text' => '',
                        'title' => '',
                        'type' => '',
                        'hreflang' => '',
                        'download' => FALSE,
                        'echo' => FALSE
                );
                $settings += $default_settings;

                $href =         empty($settings['href'])         ? "" : "href=\"" . $settings['href'] . "\"";
                $class =        empty($settings['class'])        ? "" : "class=\"".$settings['class']."\"";
                $title =        empty($settings['title'])        ? "" : "title=\"".$settings['title']."\"";
                $type =         empty($settings['type'])         ? "" : "type=\"" . $settings['type'] . "\"";
                $hreflang =     empty($settings['hreflang'])     ? "" : "hreflang=\"".$settings['hreflang']."\"";
                $download =     empty($settings['download'])     ? "" : "download";
                $text =         $settings['text'];

                $return_string = "<a ". $href ." ". $class . " ". $title . " " . $type . " " . $hreflang . " " . $download . ">" . $text . "</a>";

                if($settings['echo'] !== FALSE)
                        echo $return_string;
                else
                        return $return_string;
        }

        static function create_tabs($settings)
        {
                $default_settings = array(
                        'href' => '',
                        'tabs' => '',
                        'home' => '',
                        'name' => ''
                );

                $settings += $default_settings;

                $name = isset($settings['name']) ? $settings['name'] : 'xs_current_tab';
                $home = isset($settings['home']) ? $settings['home'] : '';
                $current = isset($_GET[$name]) ? $_GET[$name] : $home;
                $tabs = $settings['tabs'];

                if($current === '0')
                        $current = 0;

                echo '<h2 class="nav-tab-wrapper">';

                foreach( $tabs as $code => $title ){
                        $class = ( $code === $current ) ? ' nav-tab-active' : '';
                        $url = xs_framework::append_query_url($settings['href'], array($name => $code));
                        echo '<a class="nav-tab'.$class.'" href="'.$url.'">'.$title.'</a>';
                }
                echo '</h2>';

                return $current;
        }

        static function create_image($settings)
        {
                $default_settings = array(
                        'class' => '',
                        'src' => '',
                        'alt' => '',
                        'id' => '',
                        'width' => '',
                        'height' => '',
                        'echo' => FALSE
                );

                $settings += $default_settings;

                $src =        empty($settings['src'])        ? "" : "src=\"".$settings['src']."\"";
                $alt =         empty($settings['alt'])         ? "" : "alt=\"" . $settings['alt'] . "\"";
                $width =     empty($settings['width'])     ? "" : "width=\"".$settings['width']."\"";
                $height =     empty($settings['height'])     ? "" : "height=\"".$settings['height']."\"";
                $id =           empty($settings['id'])          ? "" : "id=\"".$settings['id']."\"";
                $class = empty($settings['class']) ? "" :  "class=\"".$settings['class']."\"";


                $return_string = "<img ".$src." ".$alt." ".$width." ".$height." ".$class." ".$id.">";

                if($settings['echo'] !== FALSE)
                        echo $return_string;
                else
                        return $return_string;

        }

        static function create_container($settings)
        {
                $default_settings = array(
                        'style' => '',
                        'class' => '',
                        'obj' => array(),
                        'echo' => FALSE
                );

                $settings += $default_settings;

                $class = empty($settings['class']) ? "" :  "class=\"".$settings['class']."\"";
                $style = empty($settings['style']) ? "" :  "style=\"".$settings['style']."\"";

                $return_string = "<div ".$class." ".$style.">";

                foreach($settings['obj'] as $key => $html) {
                        $return_string .= $html;
                }

                $return_string .= "</div>";

                if($settings['echo'] !== FALSE)
                        echo $return_string;
                else
                        return $return_string;
        }

        static function create_label($settings)
        {
                $default_settings = array(
                        'class' => '',
                        'for' => '',
                        'obj' => array(),
                        'echo' => FALSE
                );

                $settings += $default_settings;

                $class = empty($settings['class']) ? "" :  "class=\"".$settings['class']."\"";
                $for = empty($settings['for']) ? ""     :  "for=\"".$settings['for']."\"";

                $return_string = "<label ".$class." ".$for.">";

                foreach($settings['obj'] as $key => $html) {
                        $return_string .= $html;
                }

                $return_string .= "</label>";

                if($settings['echo'] !== FALSE)
                        echo $return_string;
                else
                        return $return_string;
        }

        static function create_select_media_gallery($settings)
        {
                $default_settings = array(
                        'id' => '',
                        'class' => '',
                        'src' => '',
                        'alt' => '',
                        'width' => '',
                        'height' => '',
                        'echo' => FALSE
                );

                $settings += $default_settings;

                $src = $settings['src'];
                $id_input = $settings['id'].'[xs_id_input]';
                $id_image = $settings['id'];

                $alt =         empty($settings['alt'])         ? "" : "alt=\"" . $settings['alt'] . "\"";
                $width =     empty($settings['width'])     ? "" : "width=\"".$settings['width']."\"";
                $height =     empty($settings['height'])     ? "" : "height=\"".$settings['height']."\"";
                $class = empty($settings['class']) ? "" :  "class=\"".$settings['class']."\"";



                $onclick = 'onclick="wp_media_gallery_url(\''.$id_input.'\',\''.$id_image.'\')"';

                $return_string = '<label '.$class.' for="'.$id_input.'">';
                $return_string .= '<input id="'.$id_input.'" style="display:none;" type="text" name="'.$id_image.'" value="'.$src.'" '.$onclick.'>';
                $return_string .= '<img src="'.$src.'" '.$alt.' id="'.$id_image.'" '.$width.' '.$height.'>';
                $return_string .= '</label>';

                if($settings['echo'] !== FALSE)
                        echo $return_string;
                else
                        return $return_string;
        }
        static function obj_list_edit($settings)
        {
                $default_settings = array(
                        'id' => 'xs_obj_id',
                        'name' => 'xs_obj_name',
                        'data' => array(),
                        'btn_add_text' => 'Add new item',
                        'btn_remove_text' => 'Remove'
                );

                $settings += $default_settings;


                $data = array();

                xs_framework::create_button([
                        'class' => 'button-primary xs_margin',
                        'text' => $settings['btn_add_text'],
                        'name' => $settings['name'].'[add]',
                        'echo' => TRUE
                ]);

                if(empty($settings['data']))
                        return;

                foreach($settings['data'] as $key => $prop) {
                        $img_input = xs_framework::create_input([
                                'id' => $settings['id'].'['.$key.'][input]',
                                'style' => 'display:none;',
                                'name' => $settings['name'].'[obj_list]['.$key.'][img]',
                                'onclick' => 'wp_media_gallery_url(\'' . $settings['id'].'['.$key.'][input]' . '\',\'' . $settings['id'].'['.$key.'][image]' . '\')',
                                'value' => $prop['img']
                        ]);
                        if(empty($prop['img']))
                                $url_img = xs_framework::url_image('select.png');
                        else
                                $url_img = $prop['img'];

                        $img = xs_framework::create_image([
                                'src' => $url_img,
                                'alt' => $prop['name'],
                                'id' => $settings['id'].'['.$key.'][image]',
                                'width' => 150,
                                'height' => 150,
                        ]);

                        $name = xs_framework::create_input([
                                'name' => $settings['name'].'[obj_list]['.$key.'][name]',
                                'value' => $prop['name']
                        ]);
                        $descr = xs_framework::create_textarea([
                                'name' => $settings['name'].'[obj_list]['.$key.'][descr]',
                                'text' => $prop['descr']
                        ]);

                        $data[$key]['img'] = xs_framework::create_label([
                                'for' => $settings['id'].'['.$key.'][input]',
                                'obj' => [$img_input, $img]
                        ]);

                        $data[$key]['text'] = xs_framework::create_container([
                                'class' => 'xs_framework_obj_list_container',
                                'obj' => [$name, $descr],
                        ]);
                        $data[$key]['delete'] = xs_framework::create_button([
                                'class' => 'button-primary',
                                'text' => $settings['btn_remove_text'],
                                'onclick' => 'return confirm_box()',
                                'value' => $key,
                                'name' => $settings['name'].'[remove]',
                                'return' => TRUE
                        ]);
                }

                xs_framework::create_table([
                        'class' => 'xs_framework_obj_list_table',
                        'data' => $data
                ]);

        }
}
?>
