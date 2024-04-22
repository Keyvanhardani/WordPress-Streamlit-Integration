<?php
/*
Plugin Name: My Streamlit
Description: A plugin to integrate Streamlit and WordPress
Version:     1.0
Author:      Keyvan Hardani
Author URI:  https://keyvan.ai
Plugin URI: https://keyvan.ai/streamlit-auth-wordpress
*/

// Ensure no direct script access is allowed
defined('ABSPATH') or die('Direct script access disallowed.');

function my_streamlit_plugin_menu() {
    add_menu_page(
        'Streamlit Plugin Page', 
        'Streamlit Plugin', 
        'manage_options', 
        'streamlit-plugin', 
        'my_streamlit_plugin_page', 
        'dashicons-media-code', 
        20
    );
}

add_action('admin_menu', 'my_streamlit_plugin_menu');

function my_streamlit_plugin_page() {
    ?>
    <div class="wrap">
        <h1><span class="dashicons dashicons-media-code"></span> Streamlit Plugin Settings</h1>
        <?php settings_errors('my_streamlit_plugin_settings'); ?>
        <form method="post" action="options.php" style="max-width:600px;">
            <?php
            settings_fields('my_streamlit_plugin_settings');
            do_settings_sections('my_streamlit_plugin_settings');
            ?>
            <table class="form-table">
                <tr valign="top">
                <th scope="row">API Key</th>
                <td><input type="text" name="api_key" style="width:100%;" value="<?php echo esc_attr(get_option('api_key')); ?>" /></td>
                </tr>
                <tr valign="top">
                <th scope="row">JWT Expire Time (in minutes)</th>
                <td><input type="number" name="jwt_expire_time" style="width:100%;" value="<?php echo esc_attr(get_option('jwt_expire_time')); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
        <hr>
        <p>Made by <a href="https://keyvan.ai" target="_blank">Keyvan Hardani</a></p>
    </div>
    <?php
}

function my_streamlit_plugin_settings() {
    register_setting('my_streamlit_plugin_settings', 'api_key');
    register_setting('my_streamlit_plugin_settings', 'jwt_expire_time');
}

add_action('admin_init', 'my_streamlit_plugin_settings');

/**
* JWT Plugin for Streamlit
**/

add_filter('jwt_auth_expire', 'set_jwt_auth_expire');
function set_jwt_auth_expire() {
  $expire_time = get_option('jwt_expire_time', 30);  // Default to 30 minutes
  return time() + (60 * $expire_time);
}

add_action('rest_api_init', function () {
    register_rest_route('myplugin/v1', '/endpoint', array(
        'methods' => 'GET',
        'callback' => 'my_endpoint_callback',
        'permission_callback' => 'my_api_key_check'
    ));
});

function my_jwt_payload($payload, $user) {
    $payload['ip_address'] = $_SERVER['REMOTE_ADDR'];
    return $payload;
}
add_filter('jwt_auth_token_before_sign', 'my_jwt_payload', 10, 2);

/**
 * Bind token to user's IP address
 */
function my_jwt_payload($payload, $user) {
    $payload['ip_address'] = $_SERVER['REMOTE_ADDR'];
    return $payload;
}
add_filter('jwt_auth_token_before_sign', 'my_jwt_payload', 10, 2);

function my_jwt_ip_check($user, $token) {
    if ($token->data->ip_address !== $_SERVER['REMOTE_ADDR']) {
        return new WP_Error('rest_forbidden', 'Invalid IP address', array('status' => 403));
    }
    return $user;
}
add_filter('jwt_auth_token_validate', 'my_jwt_ip_check', 10, 2);

/**
 * Set token expiration time
 */
function my_jwt_expiration($payload, $user) {
    $expire_time = get_option('jwt_expire_time', 30); // Expiration time in minutes
    $payload['exp'] = time() + (60 * $expire_time); // Set expiration time
    return $payload;
}
add_filter('jwt_auth_token_before_sign', 'my_jwt_expiration', 10, 2);
