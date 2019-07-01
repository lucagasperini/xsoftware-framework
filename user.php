<?php

if(!defined("ABSPATH")) die;

trait user
{
        /*
        *  array : get_user_display_name : void
        *  This method is used to fetch users in this format $array[ID] = Name
        */
        static function get_user_display_name()
        {
                /* Initialize the output array */
                $offset = array();
                /* Fetch all users */
                $users = get_users();
                /* Loop the users and format the informations */
                foreach($users as $single)
                        $offset[$single->ID] = $single->display_name;

                /* Return the array */
                return $offset;
        }

        /*
        *  bool : has_user_role : string, int
        *  This method is used to check if the user has a role
        *  $role is a string for role name, default role in wordpress are:
        *  'subscriber', 'contributor', 'author', 'editor', 'administrator'
        *  $user_id is the user id where check the role,
        *  default is NULL means will take user role from current user id
        */
        static function has_user_role($role, $user_id = NULL)
        {
                /* Check if is defined an $user_id, if not get the current user id */
                if($user_id === NULL)
                        $user_id = get_current_user_id();
                /* Return False if isn't a valid user id */
                if($user_id < 1)
                        return FALSE;

                /* Define this array to manage the user roles */
                $standard_roles = [
                        'subscriber' => 0,
                        'contributor' => 1,
                        'author' => 2,
                        'editor' => 3,
                        'administrator' => 4
                ];

                /* Get the list of the user roles */
                $user_roles = get_userdata($user_id)->roles;
                /* Initialize the higher user role as 0 */
                $higher_role = 0;
                /* Define the need role using $standard_roles at $role */
                $need_role = $standard_roles[$role];

                /* Search the highest role of the user */
                foreach($user_roles as $name) {
                        if($standard_roles[$name] > $higher_role)
                                $higher_role = $standard_roles[$name];
                }

                /* Return True if the user has the needed role or is higher, otherwise is False */
                return $higher_role >= $need_role;
        }
}

?>
