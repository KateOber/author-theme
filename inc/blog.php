<?php
add_action('pre_get_posts', function ($query) {
    if (is_admin() || !$query->is_main_query() || !$query->is_home() || 'page' !== get_option('show_on_front')) {
        return;
    }
    $sticky_ids = array_filter(array_map('absint', (array) get_option('sticky_posts', [])));
    $featured = $sticky_ids ? get_posts([
        'post__in' => $sticky_ids,
        'post_status' => 'publish',
        'has_password' => false,
        'numberposts' => 1,
        'ignore_sticky_posts' => true,
    ]) : [];
    if ($featured) {
        $featured_id = $featured[0]->ID;
        $query->set('author_theme_featured_post', $featured_id);
        $query->set('post__not_in', array_merge((array) $query->get('post__not_in'), [$featured_id]));
    }
    $query->set('ignore_sticky_posts', true);
    $query->set('posts_per_page', 6);
});
