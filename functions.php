<?php
require_once get_theme_file_path('inc/publications.php');
require_once get_theme_file_path('inc/homepage.php');
require_once get_theme_file_path('inc/about.php');
require_once get_theme_file_path('inc/books-archive.php');
require_once get_theme_file_path('inc/blog.php');
require_once get_theme_file_path('inc/social-links.php');

add_action('after_setup_theme', function () {
    load_theme_textdomain('author-theme', get_template_directory() . '/languages');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', ['unlink-homepage-logo' => true]);
    register_nav_menu('primary', __('Primary navigation', 'author-theme'));
    add_theme_support('automatic-feed-links');
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('author-theme', get_stylesheet_uri(), [], (string) filemtime(get_stylesheet_directory() . '/style.css'));
    $navigation_script = 'assets/navigation.js';
    wp_enqueue_script('author-theme-navigation', get_theme_file_uri($navigation_script), [], (string) filemtime(get_theme_file_path($navigation_script)), true);
    if (is_front_page()) {
        $home_css = 'assets/homepage.css';
        wp_enqueue_style('author-theme-homepage', get_theme_file_uri($home_css), ['author-theme'], (string) filemtime(get_theme_file_path($home_css)));
    }
    if (is_page('about') || is_page_template('page-about.php')) {
        $about_css = 'assets/about.css';
        wp_enqueue_style('author-theme-about', get_theme_file_uri($about_css), ['author-theme'], (string) filemtime(get_theme_file_path($about_css)));
    }
    $discussion_page = is_page('discussion') || is_page_template('page-discussion.php');
    $blog_page = (is_home() && !is_front_page()) || is_category();
    if (is_post_type_archive(['book', 'short_story']) || is_singular('book') || $discussion_page || $blog_page) {
        $books_css = 'assets/books.css';
        wp_enqueue_style('author-theme-books', get_theme_file_uri($books_css), ['author-theme'], (string) filemtime(get_theme_file_path($books_css)));
    }
    if ($blog_page) {
        $blog_css = 'assets/blog.css';
        wp_enqueue_style('author-theme-blog', get_theme_file_uri($blog_css), ['author-theme-books'], (string) filemtime(get_theme_file_path($blog_css)));
    }
    if ($discussion_page) {
        $discussion_css = 'assets/discussion.css';
        wp_enqueue_style('author-theme-discussion', get_theme_file_uri($discussion_css), ['author-theme-books'], (string) filemtime(get_theme_file_path($discussion_css)));
    }
    if (is_singular('chapter')) {
        $reader_css = 'assets/chapter-reader.css';
        wp_enqueue_style('author-theme-chapter-reader', get_theme_file_uri($reader_css), ['author-theme'], (string) filemtime(get_theme_file_path($reader_css)));
        if (comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }
    }
});
