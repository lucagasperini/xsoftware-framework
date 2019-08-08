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

                if(isset($input['plugins']['del_option']))
                        delete_option($input['plugins']['del_option']);

                if(isset($input['add_lang']) && !empty($input['add_lang'])) {
                        $current['available_languages'][] = $input['add_lang'];
                        xs_framework::download_language($input['add_lang']);
                }
                if(isset($input['remove_lang'])) {
                        unset($current['available_languages'][$input['remove_lang']]);
                        xs_framework::remove_language($input['remove_lang']);
                }

                if(isset($input['default_language']) && !empty($input['default_language']))
                        $current['default_language'] = $input['default_language'];

                if(isset($input['menu']) && !empty($input['menu'])) {
                        $current['menu'] = $input['menu'];
                }

                return $current;
        }

        function show()
        {
                $tab = xs_framework::create_tabs( array(
                        'href' => '?page=xsoftware',
                        'tabs' => array(
                                'homepage' => 'Homepage',
                                'plugin' => 'Plugins',
                                'language' => 'Languages',
                                'menu' => 'Menus'
                        ),
                        'home' => 'homepage',
                        'name' => 'main_tab'
                ));

                switch($tab)
                {
                        case 'homepage':
                                return;
                        case 'plugin':
                                $this->show_plugins();
                                return;
                        case 'language':
                                $this->show_languages();
                                return;
                        case 'menu':
                                $this->show_menu();
                                return;
                }

        }

        /* FIXME: It's not end */
        function show_plugins()
        {
                $data = array();
                if(!isset($_GET['opt'])) {
                        $options = $this->settings['plugins'];

                        foreach($options as $id => $values){
                                $href = xs_framework::append_query_url(
                                        '?page=xsoftware&main_tab=plugin',
                                        ['opt' => $id]
                                );

                                $data[][0] = xs_framework::create_link([
                                        'href' => $href,
                                        'text' => $id
                                ]);
                        }
                        xs_framework::create_table([
                                'class' => 'xs_admin_table',
                                'data' => $data,
                                'headers' => ['Options']
                        ]);
                        return;
                }
                $opt = explode('/',$_GET['opt']);

                $option = $this->settings['plugins'][$opt[0]]['option'];
                $root = get_option($option, array());
                for($i = 1; $i < count($opt); $i++)
                        $root = $root[$opt[$i]];

                foreach($root as $key => $value) {
                        $data[$key]['id'] = $key;
                        if(is_array($value)) {
                                $href = xs_framework::append_query_url(
                                        '?page=xsoftware&main_tab=plugin',
                                        ['opt' => implode('/',$opt).'/'.$key]
                                );
                                $data[$key]['value']=
                                xs_framework::create_link([
                                        'href' =>$href,
                                        'text' => $key,
                                        'echo' => FALSE
                                ]);
                        } else {
                                $data[$key]['value'] = $value;
                        }

                }

                xs_framework::create_table([
                        'class' => 'xs_admin_table',
                        'data' => $data,
                        'headers' => ['ID', 'Value']
                ]);

                xs_framework::create_button([
                        'class' => 'button-primary',
                        'name' => 'xs_framework_options[plugins][del_option]',
                        'value' => $option,
                        'text' => 'DELETE OPTION',
                        'echo' => TRUE
                ]);
        }


        function show_languages()
        {
                $langs = xs_framework::get_available_language([
                        'language' => TRUE,
                        'version' => TRUE,
                        'updated' => TRUE,
                        'english_name' => TRUE,
                        'native_name' => TRUE,
                        'package' => TRUE
                ]);

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
                xs_framework::create_table( [
                        'class' => 'xs_admin_table',
                        'data' => $langs,
                        'headers' => [
                                'Actions',
                                'Code',
                                'WP Version',
                                'Last Update',
                                'Name',
                                'Native Name',
                                'Package'
                        ]
                ]);

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
        function show_menu()
        {
                echo '<h2>Translated Menu Navbar</h2>';
                // get the all languages available in the wp
                $languages = xs_framework::get_available_language([
                        'language' => FALSE,
                        'english_name' => TRUE
                ]);
                $menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) ); // get menues
                $selected_menu = $this->settings['menu'];
                foreach ($menus as $menu ) {
                        $data_menu[$menu->slug] = $menu->name;
                }

                foreach ($languages as $code => $name ) {
                        $headers[]  = $name;
                        if(isset($selected_menu[$code]))
                                $selected = $selected_menu[$code];
                        else
                                $selected = reset($data_menu);

                        $data_table[0][$code] = xs_framework::create_select( array(
                                'name' => 'xs_framework_options[menu]['.$code.'][slug]',
                                'data' => $data_menu,
                                'selected' => $selected['slug']
                        ));
                        $data_table[1][$code] = xs_framework::create_input_number( array(
                                'name' => 'xs_framework_options[menu]['.$code.'][domain]',
                                'value' => $selected['domain'],
                                'max' => 9999999999999
                        ));
                }
                xs_framework::create_table([
                        'headers' => $headers,
                        'data' => $data_table,
                        'class' => 'widefat fixed'
                ]);
        }

}

$xs_framework_options = new xs_framework_options();

endif;
?>
