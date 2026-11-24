<?php
require_once get_theme_file_path('inc/publications.php');

add_action('after_setup_theme', function () {
    load_theme_textdomain('author-theme', get_template_directory() . '/languages');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('author-theme', get_stylesheet_uri(), [], wp_get_theme()->get('Version'));
});
