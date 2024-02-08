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
            echo $user->user_email . "<br/>";
            if ( is_user_logged_in() ) {
                echo "Someone is logged in<br/>";
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