<?php
add_action('customize_register', function ($customizer) {
    $section = 'author_theme_homepage';
    $customizer->add_section($section, ['title' => __('Author Homepage', 'author-theme')]);

    foreach ([
        'hero_image' => __('Homepage banner artwork', 'author-theme'),
        'about_image' => __('Author section artwork', 'author-theme'),
    ] as $key => $label) {
        $setting = 'author_home_' . $key;
        $customizer->add_setting($setting, ['sanitize_callback' => 'absint']);
        $customizer->add_control(new WP_Customize_Media_Control($customizer, $setting, [
            'label' => $label, 'section' => $section, 'mime_type' => 'image',
        ]));
    }

    $books = get_posts(['post_type' => 'book', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC']);
    $choices = [0 => __('Automatic when there is one public book', 'author-theme')];
    foreach ($books as $book) {
        if (!$book->post_password) {
            $choices[$book->ID] = $book->post_title;
        }
    }
    $customizer->add_setting('author_home_book', ['sanitize_callback' => 'absint']);
    $customizer->add_control('author_home_book', [
        'label' => __('Featured book', 'author-theme'), 'section' => $section,
        'type' => 'select', 'choices' => $choices,
    ]);

    foreach ([
        'about' => __('About', 'author-theme'),
        'gallery' => __('Gallery', 'author-theme'),
        'discussion' => __('Discussion Board', 'author-theme'),
        'playlists' => __('Playlists', 'author-theme'),
    ] as $key => $label) {
        $setting = 'author_home_' . $key;
        $customizer->add_setting($setting, ['sanitize_callback' => 'absint']);
        $customizer->add_control($setting, [
            'label' => $label, 'section' => $section, 'type' => 'dropdown-pages',
        ]);
    }

    foreach (['Wattpad', 'Instagram', 'Goodreads', 'Amazon'] as $network) {
        $setting = 'author_home_' . strtolower($network);
        $customizer->add_setting($setting, ['sanitize_callback' => 'esc_url_raw']);
        $customizer->add_control($setting, [
            'label' => $network, 'section' => $section, 'type' => 'url',
        ]);
    }
});

function author_theme_home_featured_book() {
    $selected = absint(get_theme_mod('author_home_book', 0));
    $books = get_posts([
        'post_type' => 'book',
        'post_status' => 'publish',
        'has_password' => false,
        'posts_per_page' => $selected ? 1 : 2,
        'post__in' => $selected ? [$selected] : [],
    ]);
    return count($books) === 1 ? $books[0] : null;
}

function author_theme_home_page($key) {
    $id = absint(get_theme_mod('author_home_' . $key, 0));
    $page = $id ? get_post($id) : get_page_by_path($key);
    return $page && $page->post_type === 'page' && $page->post_status === 'publish'
        && !$page->post_password && $page->ID !== (int) get_option('page_on_front') ? $page : null;
}

function author_theme_home_summary($post, $words) {
    return wp_trim_words(wp_strip_all_tags(strip_shortcodes(get_the_excerpt($post))), $words);
}
