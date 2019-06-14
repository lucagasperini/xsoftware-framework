<?php

if(!defined("ABSPATH")) die;

trait html_utils
{

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
                                'onclick' => 'wp_media_gallery_url(\'' .
$settings['id'].'['.$key.'][input]' . '\',\'' . $settings['id'].'['.$key.'][image]' . '\')',
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

        static function html_input_array_types()
        {
                $types = [
                        'img' => 'Images',
                        'lang' => 'Languages',
                        'text' => 'Text',
                        'line' => 'Line',
                        'url' => 'URL'
                ];

                return $types;
        }

        static function html_input_array_to_table($array, $settings = [])
        {
                $default_settings = [
                        'class' => '',
                        'echo' => TRUE
                ];

                $settings += $default_settings;

                $data = array();

                foreach($array as $key => $single) {

                        $class = isset($single['class']) ? $single['class'] : '';
                        $value = isset($single['value']) ? $single['value'] : '';
                        $label = isset($single['label']) ? $single['label'] : '';
                        $name = isset($single['name']) ? $single['name'] : '';
                        $id = isset($single['id']) ? $single['id'] : '';

                        switch($single['type']) {
                                case 'img':
                                        $data[$key][0] = $label;
                                        $data[$key][1] = xs_framework::create_select_media_gallery([
                                                'width' => 150,
                                                'height' => 150,
                                                'class' => $class,
                                                'src' => $value,
                                                'alt' => $value,
                                                'id' => $id,
                                                'name' => $name
                                        ]);
                                        break;
                                case 'lang':
                                        $languages = xs_framework::get_available_language();

                                        $data[$key][0] = $label;
                                        $data[$key][1] = xs_framework::create_select([
                                                'class' => $class,
                                                'name' => $name,
                                                'data' => $languages,
                                                'selected' => $value,
                                                'default' => 'Select a Language'
                                        ]);
                                        break;
                                case 'text':
                                        $data[$key][0] = $label;
                                        $data[$key][1] = xs_framework::create_textarea([
                                                'class' => $class,
                                                'name' => $name,
                                                'text' => $value
                                        ]);
                                        break;
                                case 'line':
                                        $data[$key][0] = $label;
                                        $data[$key][1] = xs_framework::create_input([
                                                'class' => $class,
                                                'name' => $name,
                                                'value' => $value
                                        ]);
                                        break;
                                case 'url':
                                        $data[$key][0] = $label;
                                        $data[$key][1] = xs_framework::create_input([
                                                'class' => $class,
                                                'type' => 'url',
                                                'name' => $name,
                                                'value' => $value
                                        ]);
                                        break;
                                default:
                                        $data[$key][0] = $label;
                                        $data[$key][1] = xs_framework::create_input([
                                                'class' => $class,
                                                'name' => $name,
                                                'value' => $value
                                        ]);
                        }

                }

                return xs_framework::create_table([
                        'class' => $settings['class'],
                        'data' => $data,
                        'echo' => $settings['class'],
                ]);
        }

}

?>