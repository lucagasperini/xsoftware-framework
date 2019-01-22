<?php

if (!class_exists('xs_framework_options')) :

include 'languages.php';

class xs_framework_options
{
        public $settings = array();
        
        function __construct()
        {
                add_action('admin_menu', array($this, 'admin_menu'), 0); //Load it first!
                add_action('admin_init', array($this, 'section_menu'), 0); //Load it first!
                $this->settings = xs_framework::get_option();
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
                        $lang_list = xs_language::get_name_list();
                        $current['available_languages'][$input['add_lang']] = $lang_list[$input['add_lang']];
                        $res = $this->download_language($input['add_lang']);
                        if($res == FALSE)
                                return; //FIXME: Create an error handler!
                }
                if(isset($input['remove_lang'])) {
                        unset($current['available_languages'][$input['remove_lang']]);
                        $this->remove_language($input['remove_lang']);
                }
                return $current;
        }
        
        function show()
        {
                $settings = $this->settings;
                
                $langs = array();
                $i = 0;
                foreach($settings['available_languages'] as $key => $value) {
                        $langs[$i][] = xs_framework::create_button( array( 
                                'name' => 'xs_framework_options[remove_lang]', 
                                'class' => 'button-primary', 
                                'value' => $key, 
                                'text' => 'Remove', 
                                'return' => true
                        ));
                        $langs[$i][] = $value;
                        $langs[$i][] = $key;
                        $i++;
                }
               
                xs_framework::create_table( array( 
                        'data' => $langs,
                        'headers' => array('Actions', 'Language', 'Code')
                ));
                
                $settings_field = array( 
                        'name' => 'xs_framework_options[add_lang]', 
                        'data' => xs_language::get_name_list()
                );
                
                add_settings_field(
                        $settings_field['name'], 
                        'Add new language:',
                        'xs_framework::create_select',
                        'framework',
                        'section_framework',
                        $settings_field
                );
                
                $options = array(
                        'name' => 'xs_framework_options[frontend_language]',
                        'data' => $settings['available_languages'],
                        'selected' => $settings['frontend_language']
                );
        
                add_settings_field(
                        $options['name'],
                        'Default language',
                        'xs_framework::create_select',
                        'framework',
                        'section_framework',
                        $options
                );

                $options = array(
                        'name' => 'xs_framework_options[backend_language]',
                        'data' => $settings['available_languages'],
                        'selected' => $settings['backend_language']
                );
                add_settings_field(
                        $options['name'],
                        'Default language for admin side',
                        'xs_framework::create_select',
                        'framework',
                        'section_framework',
                        $options
                );
                

        }
        
        function download_language($lang_code) 
        {
                $remoteFile = xs_language::get_download($lang_code);

                $lang_dir = WP_CONTENT_DIR . '/languages/';
                $package = $lang_dir."package.zip";

                $flag = file_put_contents($package, fopen($remoteFile, 'r'));

                if($flag === FALSE || !class_exists('ZipArchive'))
                        return FALSE;
                        
                $zip = new ZipArchive;
                
                if ($zip->open($package) !== TRUE)
                        return FALSE;

                $zip->extractTo($lang_dir, array($lang_code.".mo", $lang_code.".po"));
                $zip->close();
                unlink($package);

                return TRUE;
        }
        
        function remove_language($lang_code)
        {
                $lang_dir = WP_CONTENT_DIR . '/languages/';
                unlink($lang_dir.$lang_code.'.mo');
                unlink($lang_dir.$lang_code.'.po');
                return TRUE;
        }


}

$xs_framework_options = new xs_framework_options();

endif;
?>
