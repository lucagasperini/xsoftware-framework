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
                $colors = $this->settings['style'];
                
                foreach($colors as $name => $prop) {
                        $data[$name][] = $name;
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['default']['text'],
                                'name' => 'xs_framework_options[style]['.$name.'][default][text]',
                                'return' => TRUE
                        ));
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['hover']['text'],
                                'name' => 'xs_framework_options[style]['.$name.'][hover][text]',
                                'return' => TRUE
                        ));
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['focus']['text'],
                                'name' => 'xs_framework_options[style]['.$name.'][focus][text]',
                                'return' => TRUE
                        ));
                        
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['default']['bg'],
                                'name' => 'xs_framework_options[style]['.$name.'][default][bg]',
                                'return' => TRUE
                        ));
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['hover']['bg'],
                                'name' => 'xs_framework_options[style]['.$name.'][hover][bg]',
                                'return' => TRUE
                        ));
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['focus']['bg'],
                                'name' => 'xs_framework_options[style]['.$name.'][focus][bg]',
                                'return' => TRUE
                        ));
                        
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['default']['bord'],
                                'name' => 'xs_framework_options[style]['.$name.'][default][bord]',
                                'return' => TRUE
                        ));
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['hover']['bord'],
                                'name' => 'xs_framework_options[style]['.$name.'][hover][bord]',
                                'return' => TRUE
                        ));
                        $data[$name][] = xs_framework::create_input( array(
                                'value' => $prop['focus']['bord'],
                                'name' => 'xs_framework_options[style]['.$name.'][focus][bord]',
                                'return' => TRUE
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
                xs_framework::create_table(array('headers' => $headers, 'data' => $data));
                
                $colors = $this->settings['colors'];

                $settings = array( 
                        'type' => 'color',
                        'name' => 'xs_framework_options[colors][primary]', 
                        'value' => $colors['primary']
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
                        'value' => $colors['secondary']
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
                        'value' => $colors['background']
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
                        'value' => $colors['text']
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
                                        'text' => 'Remove', 
                                        'return' => true
                                ));
                        array_unshift($langs[$code], $delete_button);
                        unset($langs[$code]['strings']);
                }
                
                $lang_list = xs_framework::get_lang_name_list();
                array_unshift($lang_list, 'Select a language');
                xs_framework::create_table( array( 
                        'data' => $langs,
                        'headers' => array('Actions', 'Code', 'WP Version', 'Last Update', 'Name', 'Native Name', 'Package', 'ISO')
                ));
                
                $this->settings_field = array( 
                        'name' => 'xs_framework_options[add_lang]', 
                        'data' => $lang_list
                );
                
                add_settings_field(
                        $this->settings_field['name'], 
                        'Add new language:',
                        'xs_framework::create_select',
                        'framework',
                        'section_framework',
                        $this->settings_field
                );
        }

}

$xs_framework_options = new xs_framework_options();

endif;
?>
