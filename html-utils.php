<?php

if(!defined("ABSPATH")) die;

trait html_utils
{

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