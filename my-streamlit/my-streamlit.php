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
            </table>
            <?php submit_button(); ?>
        </form>
        <hr>
        <p>Made by <a href="https://keyvan.ai" target="_blank">Keyvan Hardani</a></p>
    </div>
    <?php
}

function my_streamlit_plugin_settings() {
    register_setting('my_streamlit_plugin_settings', 'api_key', 'my_streamlit_plugin_settings_callback');
}

function my_streamlit_plugin_settings_callback($input) {
    add_settings_error('my_streamlit_plugin_settings', esc_attr('settings_updated'), 'Settings updated successfully.', 'updated');
    return $input;
}

add_action('admin_init', 'my_streamlit_plugin_settings');

/**
* JWT Plugin for Streamlit
**/

add_filter('jwt_auth_expire', 'set_jwt_auth_expire');
function set_jwt_auth_expire() {
  return time() + (60*30);  // 30 minutes
}

add_action('rest_api_init', 'add_api_key_check');
function add_api_key_check() {
  if (!isset($_SERVER['HTTP_X_API_KEY']) || $_SERVER['HTTP_X_API_KEY'] != get_option('api_key')) {
    wp_die('Incorrect API Key');
  }
}
