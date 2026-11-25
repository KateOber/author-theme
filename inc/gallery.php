<?php
function author_theme_gallery_categories() {
    return [
        'illustrations' => __('Illustrations', 'author-theme'),
        'character-art' => __('Character Art', 'author-theme'),
        'locations' => __('Locations', 'author-theme'),
        'sketches' => __('Sketches', 'author-theme'),
        'extras' => __('Extras', 'author-theme'),
    ];
}

add_filter('template_include', function ($template) {
    if (is_page() && !is_front_page() && 'artwork' === author_theme_world_collection_type()) {
        return get_theme_file_path('page-gallery.php');
    }
    return $template;
});
