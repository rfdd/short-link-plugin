<?php
/*
Plugin Name: Short Link Plugin
Description: Плагин для создания и управления короткими URL-ссылками.
Version: 1.0
Author: rfd
*/
require_once __DIR__ . '/vendor/autoload.php';

function short_link_plugin_init() {
    $args = [
        'public' => true,
        'rewrite' => true,  
        'label'  => __('Short Links', 'short-link-plugin'),
        // other arguments
    ];
    register_post_type('short_link', $args);
}
add_action('init', 'short_link_plugin_init');

function short_link_add_meta_box() {
    add_meta_box(
        'short_link_url',           // ID
        __('Original URL', 'short-link-plugin'),
        'short_link_meta_box_html', // Callback
        'short_link'                // Post type
    );
}
add_action('add_meta_boxes', 'short_link_add_meta_box');

function short_link_meta_box_html($post) {
    $value = get_post_meta($post->ID, '_short_link_url', true);
    $html = <<<HTML
    <label for="short_link_url_field">Enter URL:</label>
    <input type="url" id="short_link_url_field" name="short_link_url_field" value="%s" size="25">
    HTML;

    echo sprintf($html, esc_attr($value));
}


// Saves the URL entered in the meta box
function short_link_save_postdata($post_id) {
    if (array_key_exists('short_link_url_field', $_POST)) {
        $url = $_POST['short_link_url_field'];
        if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL)) {
            update_post_meta(
                $post_id,
                '_short_link_url',
                $url
            );
        } else {
            delete_post_meta($post_id, '_short_link_url');
        }
    }
}
add_action('save_post', 'short_link_save_postdata');



// Starts a PHP session if not already started
function short_link_start_session() {
    if (!session_id()) {
        session_start();
    }
}
add_action('init', 'short_link_start_session');


// Redirects to the original URL for short link posts
function short_link_redirect() {
    if (is_singular('short_link')) {
        $post_id = get_the_ID();
        $original_url = get_post_meta($post_id, '_short_link_url', true);

        //Click count
        $clicks_count = (int) get_post_meta($post_id, '_short_link_clicks', true);
        update_post_meta($post_id, '_short_link_clicks', $clicks_count + 1);

        //Uniq clicks
        $last_click = isset($_SESSION['last_click'][$post_id]) ? $_SESSION['last_click'][$post_id] : 0;
        $current_time = time();

        if ($current_time - $last_click >= 120) {
            $_SESSION['last_click'][$post_id] = $current_time;
            $unique_clicks_count = (int) get_post_meta($post_id, '_short_link_unique_clicks', true);
            update_post_meta($post_id, '_short_link_unique_clicks', $unique_clicks_count + 1);
        }

        if ($original_url) {
            wp_redirect($original_url, 301);
            exit;
        } else {
            global $wp_query;
            $wp_query->set_404();
            status_header(404);
            include( get_query_template( '404' ) ); 
            exit;
        }
    }
}
add_action('template_redirect', 'short_link_redirect');


// Adds custom columns to the admin list for short links
function short_link_columns_head($defaults) {
    $defaults['short_link_full_url'] = __('Full URL', 'short-link-plugin');
    $defaults['short_link_short_url'] = __('Short URL', 'short-link-plugin');
    $defaults['short_link_clicks'] = __('Total Clicks', 'short-link-plugin');
    $defaults['short_link_unique_clicks'] = __('Unique Clicks', 'short-link-plugin');
    return $defaults;
}
add_filter('manage_short_link_posts_columns', 'short_link_columns_head');

function short_link_columns_content($column_name, $post_id) {
    if ($column_name == 'short_link_full_url') {
        $original_url = get_post_meta($post_id, '_short_link_url', true);
        echo $original_url ? $original_url : '—';
    }
    if ($column_name == 'short_link_short_url') {
        $short_url = get_permalink($post_id);
        echo $short_url ? $short_url : '—';
    }
    if ($column_name == 'short_link_clicks') {
        $clicks_count = get_post_meta($post_id, '_short_link_clicks', true);
        echo $clicks_count ? $clicks_count : '0';
    }
    if ($column_name == 'short_link_unique_clicks') {
        $unique_clicks_count = get_post_meta($post_id, '_short_link_unique_clicks', true);
        echo $unique_clicks_count ? $unique_clicks_count : '0';
    }
}
add_action('manage_short_link_posts_custom_column', 'short_link_columns_content', 10, 2);


// Load plugin text domain for translations
function short_link_load_textdomain() {
    load_plugin_textdomain('short-link-plugin', false, basename(dirname(__FILE__)) . '/lang');
}
add_action('plugins_loaded', 'short_link_load_textdomain');
