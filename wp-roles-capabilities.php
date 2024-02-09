<?php
/*
Plugin Name: Roles & Capabilities
Plugin URI: https://gaziakter.com/plugin/roles-capabilities/
Description: Usagae of role and capabilities
Version: 1.0.0
Author: Gazi Akter
Author URI: https://gaziakter.com/
License: GPLv2 or later
Text Domain: roles-capabilities
 */

add_action( 'admin_enqueue_scripts', function ( $hook ) {
    if ( 'toplevel_page_roles-capability' == $hook ) {
        wp_enqueue_style( 'pure-grid-css', '//unpkg.com/purecss@1.0.1/build/grids-min.css' );
        wp_enqueue_style( 'roles-capabilities-css', plugin_dir_url( __FILE__ ) . "assets/css/style.css", null, time() );
        wp_enqueue_script( 'roles-capabilities-js', plugin_dir_url( __FILE__ ) . "assets/js/main.js", array( 'jquery' ), time(), true );
        $nonce = wp_create_nonce( 'roles_display_result' );
        wp_localize_script(
            'roles-capabilities-js',
            'plugindata',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'nonce' => $nonce )
        );
    }
} );

add_action( 'wp_ajax_roles_display_result', function () {
    global $roles;
    $table_name = $roles->prefix . 'persons';
    if ( wp_verify_nonce( $_POST['nonce'], 'roles_display_result' ) ) {
        $task = $_POST['task'];
        if ( 'current-user-details' == $task ) {
            $user = wp_get_current_user();
            $username = $user->display_name;
            if(is_user_logged_in(  )){
                echo 'Hello '. $username. '!';


                echo"<br/>";
            }

            //print_r( $user );
        } elseif ( 'any-user-detail' == $task ) {
            $user = New WP_User( 2 );
            $username = $user->display_name;
            echo "Our Another is: ".$username;

        }  elseif ( 'current-role' == $task ) {
            $user = New WP_User(1);
            $userrole = $user->roles;
            foreach($userrole as $role){
                echo "This is user role: ".$role;
            }

        }   elseif ( 'all-roles' == $task ) {
            global $wp_roles;
            $role_name = $wp_roles->roles;     

            foreach( $role_name as $role => $roledetails){
                echo  "{$role} </br>";
            }
            print_r($wp_roles);

        }  elseif ( 'current-capabilities' == $task ) {

                if(is_user_logged_in(  )){
                    $current_user = wp_get_current_user(  );
                    $capability =  $current_user->allcaps;
                    foreach($capability as $cap => $value){
                        echo $cap .'</br>';

                    }
                }

             //print_r( $current_user->allcaps);

        } elseif ( 'create-user' == $task ) {

            $create_user = wp_create_user('adilfayar', '12345d678', 'htisjdddkfn@gmai;.com' );
            echo $create_user;
            
        }
        
        elseif ( 'set-role' == $task ) {

           $user = New WP_User(3);
            $user->remove_role( 'subscriber' );
            $user->add_role( 'author' );
            print_r($user);
            
        }

        elseif ( 'users-by-role' == $task ) {

            $blogusers = get_users( array( 'role__in' => array( 'author', 'subscriber' ) ) );
            // Array of WP_User objects.
            foreach ( $blogusers as $user ) {
                echo '<span>' . esc_html( $user->display_name ) . '</span> </br>';
            }
             
         }

         elseif ( 'users-by-role' == $task ) {

            $blogusers = get_users( array( 'role__in' => array( 'author', 'subscriber' ) ) );
            // Array of WP_User objects.
            foreach ( $blogusers as $user ) {
                echo '<span>' . esc_html( $user->display_name ) . '</span> </br>';
            }
             
         }

         elseif ( 'create-role' == $task ) {
            /* $role = add_role('super_author',__('Super Author','roles-demo'),[
            'read'=>true,
            'delete_posts'=>true,
            'edit_posts'=>true,
            'custom_cap_one'=>true,
            'custom_cap_two'=>false
            ]);
            print_r($role);*/
            $user = new WP_User( 3 );
            $user->add_role( 'super_author' );
            if ( $user->has_cap( 'custom_cap_one' ) ) {
                echo "Jane can do custom_cap_one<br/>";
            }

            if ( !$user->has_cap( 'custom_cap_two' ) ) {
                echo "Jane can not do custom_cap_two<br/>";
            }
            print_r( $user );

        }
    }
    die( 0 );
} );

add_action( 'admin_menu', function () {
    add_menu_page( 'Roles & Capabilities', 'Roles & Capabilities', 'manage_options', 'roles-capability', 'roles_capabilities_admin_page' );
} );

function roles_capabilities_admin_page() {
    ?>
        <div class="container" style="padding-top:20px;">
            <h1>Roles & Capabilities</h1>
            <div class="pure-g">
                <div class="pure-u-1-4" style='height:100vh;'>
                    <div class="plugin-side-options">
                        <button class="action-button" data-task='current-user-details'>Get Current User Details</button>
                        <button class="action-button" data-task='any-user-detail'>Get Any User Details</button>
                        <button class="action-button" data-task='current-role'>Detect Any User Role</button>
                        <button class="action-button" data-task='all-roles'>Get All Roles List</button>
                        <button class="action-button" data-task='current-capabilities'>Current User Capability</button>
                        <button class="action-button" data-task='check-user-cap'>Check User Capability</button>
                        <button class="action-button" data-task='create-user'>Create A New User</button>
                        <button class="action-button" data-task='set-role'>Assign Role To A New User</button>
                        <button class="action-button" data-task='login'>Login As A User</button>
                        <button class="action-button" data-task='users-by-role'>Find All Users From Role</button>
                        <button class="action-button" data-task='change-role'>Change User Role</button>
                        <button class="action-button" data-task='create-role'>Create New Role</button>
                    </div>
                </div>
                <div class="pure-u-3-4">
                    <div class="plugin-demo-content">
                        <h3 class="plugin-result-title">Result</h3>
                        <div id="roles_capabilities_result" class="plugin-result"></div>
                    </div>
                </div>
            </div>
        </div>
    <?php
}
