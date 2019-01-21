<?php

if (!class_exists('xs_framework_options')) :

include 'languages.php';

class xs_framework_options
{
        public $default = array(
                'available_languages' => array('en' => 'English')
                );
        public $settings = array();
        
        function __construct()
        {
                add_action('admin_menu', array($this, 'admin_menu'), 0); //Load it first!
                add_action('admin_init', array($this, 'section_menu'), 0); //Load it first!
                $this->settings = get_option('xs_framework_options', $this->default);
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
                if(isset($input['add_lang'])) {
                        $key = array_keys(xs_language::$language_codes, $input['add_lang']);
                        $current['available_languages'][$input['add_lang']] = $key[0];
                }
                return $current;
        }
        
        function show()
        {
                $settings = $this->settings;
                
                $langs = array();
                $i = 0;
                foreach($settings['available_languages'] as $key => $value) {
                        $langs[$i][] = $value;
                        $langs[$i][] = $key;
                        $i++;
                }
               
                xs_framework::create_table( array( 
                        'data' => $langs,
                        'headers' => array('Language', 'Code')
                ));
                
                $settings_field = array( 
                        'name' => 'xs_framework_options[add_lang]', 
                        'data' => xs_language::$language_codes,
                        'reverse' => true
                );
                
                add_settings_field($settings_field['name'], 
                'Add new language:',
                'xs_framework::create_select',
                'framework',
                'section_framework',
                $settings_field);
        }

}

$xs_framework_options = new xs_framework_options();

endif;
?>
