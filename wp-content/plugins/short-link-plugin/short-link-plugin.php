<?php
/*
Plugin Name: Short Link Plugin
Description: Плагин для создания и управления короткими URL-ссылками.
Version: 1.0
Author: rfd
*/

function short_link_plugin_init() {
    $args = [
        'public' => true,
        'rewrite' => true,  
        'label'  => 'Short Links',
        // other arguments
    ];
    register_post_type('short_link', $args);
}
add_action('init', 'short_link_plugin_init');

function short_link_add_meta_box() {
    add_meta_box(
        'short_link_url',           // ID
        'Original URL',
        'short_link_meta_box_html', // Callback
        'short_link'                // Post type
    );
}
add_action('add_meta_boxes', 'short_link_add_meta_box');

function short_link_meta_box_html($post) {
    $value = get_post_meta($post->ID, '_short_link_url', true);
    echo '<label for="short_link_url_field">Введите URL:</label>';
    echo '<input type="url" id="short_link_url_field" name="short_link_url_field" value="' . esc_attr($value) . '" size="25">';
}

function short_link_save_postdata($post_id) {
    if (array_key_exists('short_link_url_field', $_POST)) {
        update_post_meta(
            $post_id,
            '_short_link_url',
            $_POST['short_link_url_field']
        );
    }
}
add_action('save_post', 'short_link_save_postdata');

function short_link_start_session() {
    if (!session_id()) {
        session_start();
    }
}
add_action('init', 'short_link_start_session');


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
