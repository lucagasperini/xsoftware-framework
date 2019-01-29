<?php

if (!class_exists('xs_framework_options')) :

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
                if(isset($input['add_lang']) && !empty($input['add_lang'])) {
                        $current['available_languages'][$input['add_lang']] = xs_framework::get_lang_property($input['add_lang']);
                        $res = $this->download_language($input['add_lang']);
                        if($res == FALSE)
                                return; //FIXME: Create an error handler!
                }
                if(isset($input['remove_lang'])) {
                        unset($current['available_languages'][$input['remove_lang']]);
                        $this->remove_language($input['remove_lang']);
                }
                if(isset($input['backend_language']) && !empty($input['backend_language']))
                        $current['backend_language'] = $input['backend_language'];
                        
                if(isset($input['frontend_language']) && !empty($input['frontend_language']))
                        $current['frontend_language'] = $input['frontend_language'];
                        
                if(isset($input['new_color']) && !empty($input['new_color']) && !empty($input['new_color']['name']))
                        $current['available_colors'][$input['new_color']['name']] = $input['new_color']['url'];
               
                if(isset($input['remove_color']) && !empty($input['remove_color']))
                        unset($current['available_colors'][$input['remove_color']]);
                        
                return $current;
        }
        
        function show()
        {
                // get the current tab or default tab
                $current = isset($_GET['tab']) ? $_GET['tab'] : 'homepage';
                // add the tabs that you want to use in the plugin
                $tabs = array(
                        'homepage' => 'Homepage',
                        'language' => 'Languages',
                        'colors' => 'Colors'
                );
                echo '<h2 class="nav-tab-wrapper">';
                // configurate the url with your personal_url and add the class for the activate tab
                foreach( $tabs as $tab => $name ){
                        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
                        echo "<a class='nav-tab$class' href='?page=xsoftware&tab=$tab'>$name</a>";
                }
                echo '</h2>';
                
                switch($current)
                {
                        case 'homepage':
                                return;
                        case 'language':
                                $this->show_languages();
                                return;
                        case 'colors': 
                                $this->show_colors();
                                return;
                }

        }
        
        function show_colors()
        {
                $colors = $this->settings['available_colors'];

                $table = array();
                foreach ($colors as $name => $url) {
                        $delete_button = xs_framework::create_button( array( 
                                        'name' => 'xs_framework_options[remove_color]', 
                                        'class' => 'button-primary', 
                                        'value' => $name, 
                                        'text' => 'Remove', 
                                        'return' => true
                                ));
                        $table[$name][] = $delete_button;
                        $table[$name][] = $name; 
                        $table[$name][] = $url;
                }
                $add = array();
                $add[] = '';
                $add[] = xs_framework::create_input( array(
                        'name' => 'xs_framework_options[new_color][name]',
                        'return' => TRUE
                ));
                $add[] = xs_framework::create_input( array(
                        'name' => 'xs_framework_options[new_color][url]',
                        'return' => TRUE
                ));
                
                $table[] = $add;
                
                xs_framework::create_table( array( 
                        'data' => $table,
                        'headers' => array('Actions', 'Name', 'Url')
                ));
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
                
                $options = array(
                        'name' => 'xs_framework_options[frontend_language]',
                        'data' => xs_framework::get_available_language(),
                        'selected' => $this->settings['frontend_language']
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
                        'data' => xs_framework::get_available_language(),
                        'selected' => $this->settings['backend_language']
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
                $remoteFile = xs_framework::get_lang_download($lang_code);

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
