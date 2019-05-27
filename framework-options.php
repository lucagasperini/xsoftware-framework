<?php

if(!defined("ABSPATH")) die;

if (!class_exists('xs_framework_options')) :

class xs_framework_options
{
        public $settings = array();
        
        function __construct()
        {
                add_action('admin_menu', array($this, 'admin_menu'), 0); //Load it first!
                add_action('admin_init', array($this, 'section_menu'), 0); //Load it first!
                $this->settings = xs_framework::get_option();
                add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        }
        
        
        function enqueue_styles()
        {
                wp_enqueue_style('xs_framework_globals_style', content_url('xsoftware/style/xsoftware.css'));
        }
        
        function admin_menu()
        {
                add_menu_page( 'XSoftware', 'XSoftware', 'manage_options', 'xsoftware', array($this, 'menu_page') );
        }
       
        public function menu_page()
        {
                if ( !current_user_can( 'manage_options' ) )  {
                        wp_die( __( 'Exit!' ) );
                }
                
                xs_framework::init_admin_style();
                
                echo '<div class="wrap">';

                echo '<h2>XSoftware Framework</h2>';
               
                echo '<form action="options.php" method="post">';

                settings_fields('setting_framework');
                do_settings_sections('framework');

                submit_button( '', 'primary', 'field_update', true, NULL );
              
                echo "</form>";
                
                echo '</div>';
        }

        function section_menu()
        {
                register_setting( 'setting_framework', 'xs_framework_options', array($this, 'input') );
                add_settings_section( 'section_framework', 'Framework settings', array($this, 'show'), 'framework' );
        }

        function input($input)
        {
                $current = $this->settings;
                
                if(isset($input['add_lang']) && !empty($input['add_lang'])) {
                        $current['available_languages'][$input['add_lang']] = xs_framework::get_lang_property($input['add_lang']);
                        $res = xs_framework::download_language($input['add_lang']);
                        if($res == FALSE)
                                return; //FIXME: Create an error handler!
                }
                if(isset($input['remove_lang'])) {
                        unset($current['available_languages'][$input['remove_lang']]);
                        xs_framework::remove_language($input['remove_lang']);
                }
                
                if(isset($input['default_language']) && !empty($input['default_language']))
                        $current['default_language'] = $input['default_language'];
                        
                if(isset($input['colors']) && !empty($input['colors'])) {
                        $current['colors'] = $input['colors'];
                }
                if(isset($input['style']) && !empty($input['style'])) {
                        $current['style'] = $input['style'];
                        xs_framework::generate_css($current['style'], 'xsoftware.css', $current['colors']);
                }
                        
                return $current;
        }
        
        function show()
        {
                $tab = xs_framework::create_tabs( array(
                        'href' => '?page=xsoftware',
                        'tabs' => array(
                                'homepage' => 'Homepage',
                                'language' => 'Languages',
                                'style' => 'Styles'
                        ),
                        'home' => 'homepage',
                        'name' => 'main_tab'
                ));
                
                switch($tab)
                {
                        case 'homepage':
                                return;
                        case 'language':
                                $this->show_languages();
                                return;
                        case 'style': 
                                $this->show_style();
                                return;
                }

        }
        
        function show_style()
        {
                $tab = xs_framework::create_tabs( array(
                        'href' => '?page=xsoftware&main_tab=style',
                        'tabs' => array(
                                'color' => 'Colors' 
                        ),
                        'home' => 'color',
                        'name' => 'style_tab'
                ));
                
                switch($tab)
                {
                        case 'color':
                                $this->show_style_colors();
                                return;
                }
        }
        
        function show_style_colors()
        {
                $style = $this->settings['style'];
                
                foreach($style as $name => $prop) {
                        $data[$name][] = $name;
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop[0]['color'],
                                'name' => 'xs_framework_options[style]['.$name.'][0][color]'
                        ));
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['hover']['color'],
                                'name' => 'xs_framework_options[style]['.$name.'][hover][color]'
                        ));
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['focus']['color'],
                                'name' => 'xs_framework_options[style]['.$name.'][focus][color]'
                        ));
                        
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop[0]['background-color'],
                                'name' => 'xs_framework_options[style]['.$name.'][0][background-color]'
                        ));
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['hover']['background-color'],
                                'name' => 'xs_framework_options[style]['.$name.'][hover][background-color]'
                        ));
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['focus']['background-color'],
                                'name' => 'xs_framework_options[style]['.$name.'][focus][background-color]'
                        ));
                        
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop[0]['border-color'],
                                'name' => 'xs_framework_options[style]['.$name.'][0][border-color]'
                        ));
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['hover']['border-color'],
                                'name' => 'xs_framework_options[style]['.$name.'][hover][border-color]'
                        ));
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['focus']['border-color'],
                                'name' => 'xs_framework_options[style]['.$name.'][focus][border-color]'
                        ));
                }
                $headers = array(
                        'Name', 
                        'Color', 
                        'Hover', 
                        'Focus', 
                        'Background Color', 
                        'Background Hover', 
                        'Background Focus', 
                        'Border Color',
                        'Border Hover',
                        'Border Focus'
                );
                xs_framework::create_table(array(
                        'class' => 'xs_admin_table',
                        'headers' => $headers, 
                        'data' => $data
                ));
                
                $colors = $this->settings['colors'];

                $settings = array( 
                        'type' => 'color',
                        'name' => 'xs_framework_options[colors][primary]', 
                        'value' => $colors['primary'],
                        'echo' => TRUE
                );
                
                add_settings_field(
                        $settings['name'], 
                        'Primary Color:',
                        'xs_framework::create_input',
                        'framework',
                        'section_framework',
                        $settings
                );
                
                $settings = array( 
                        'type' => 'color',
                        'name' => 'xs_framework_options[colors][secondary]', 
                        'value' => $colors['secondary'],
                        'echo' => TRUE
                );
                
                add_settings_field(
                        $settings['name'], 
                        'Secondary Color:',
                        'xs_framework::create_input',
                        'framework',
                        'section_framework',
                        $settings
                );
                
                $settings = array( 
                        'type' => 'color',
                        'name' => 'xs_framework_options[colors][background]', 
                        'value' => $colors['background'],
                        'echo' => TRUE
                );
                
                add_settings_field(
                        $settings['name'], 
                        'Background Color:',
                        'xs_framework::create_input',
                        'framework',
                        'section_framework',
                        $settings
                );
                
                $settings = array( 
                        'type' => 'color',
                        'name' => 'xs_framework_options[colors][text]', 
                        'value' => $colors['text'],
                        'echo' => TRUE
                );
                
                add_settings_field(
                        $settings['name'], 
                        'Text Color:',
                        'xs_framework::create_input',
                        'framework',
                        'section_framework',
                        $settings
                );
        }
        
        function show_languages()
        {
                $langs = $this->settings['available_languages'];

                foreach ($langs as $code => $prop) {
                        $delete_button = xs_framework::create_button( array( 
                                        'name' => 'xs_framework_options[remove_lang]', 
                                        'class' => 'button-primary', 
                                        'value' => $code, 
                                        'text' => 'Remove'
                                ));
                        array_unshift($langs[$code], $delete_button);
                        unset($langs[$code]['strings']);
                }
                
                $lang_list = xs_framework::get_lang_name_list();
                xs_framework::create_table( array( 
                        'class' => 'xs_admin_table',
                        'data' => $langs,
                        'headers' => array('Actions', 'Code', 'WP Version', 'Last Update', 'Name', 'Native Name', 'Package', 'ISO')
                ));
                
                $options = array( 
                        'name' => 'xs_framework_options[add_lang]', 
                        'default' => 'Select a language',
                        'data' => $lang_list,
                        'echo' => TRUE
                );
                
                add_settings_field(
                        $options['name'], 
                        'Add new language:',
                        'xs_framework::create_select',
                        'framework',
                        'section_framework',
                        $options
                );
                $options = array( 
                        'name' => 'xs_framework_options[default_language]', 
                        'selected' => $this->settings['default_language'],
                        'data' => xs_framework::get_available_language(),
                        'echo' => TRUE
                );
                
                add_settings_field(
                        $options['name'],
                        'Select a default language:',
                        'xs_framework::create_select',
                        'framework',
                        'section_framework',
                        $options
                );
        }

}

$xs_framework_options = new xs_framework_options();

endif;
?>
