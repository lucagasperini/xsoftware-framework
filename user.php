<?php

if(!defined("ABSPATH")) die;

trait user
{
        static function get_user_display_name($query = array())
        {
                $offset = array();
                $users = get_users();
                foreach($users as $single)
                {
                        $offset[$single->ID] = $single->display_name;
                }
                return $offset;
        }

        static function user_role($role, $user_id = NULL)
        {
                if($user_id == NULL)
                        $user_id = get_current_user_id();
                if($user_id < 1)
                        return FALSE;

                $standard_roles = array(0 => 'subscriber', 1 => 'contributor', 2 => 'author', 3 => 'editor', 4 => 'administrator');
                $user_roles = get_userdata($user_id)->roles;
                if(count($user_roles) != 1) //FIXME: Can user have more roles?
                        return FALSE;
                foreach($standard_roles as $key => $value) {
                        if($role == $value)
                                $find_need_role = $key;
                        if($user_roles[0] == $value) {
                                $find_user_role = $key;
                        }
                }
                return $find_user_role >= $find_need_role;
        }
}

?>
