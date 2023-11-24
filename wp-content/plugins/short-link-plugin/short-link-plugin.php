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
        'label'  => 'Short Links',
        // other arguments
    ];
    register_post_type('short_link', $args);
}
add_action('init', 'short_link_plugin_init');
