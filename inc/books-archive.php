<?php
function author_theme_book_filters() {
    return [
        'published' => __('Published', 'author-theme'),
        'in-progress' => __('In Progress', 'author-theme'),
        'upcoming' => __('Upcoming', 'author-theme'),
    ];
}

function author_theme_book_filter() {
    $filter = get_query_var('book_status', '');
    return is_string($filter) && isset(author_theme_book_filters()[$filter]) ? $filter : '';
}

function author_theme_book_statuses() {
    return [
        'published' => ['published', 'complete', 'completed', 'serializing', 'ongoing'],
        'in-progress' => ['in progress', 'in-progress', 'work in progress', 'drafting', 'revising', 'editing'],
        'upcoming' => ['upcoming', 'coming soon', 'concept', 'outlining', 'idea', 'research'],
    ];
}

add_filter('query_vars', function ($vars) {
    $vars[] = 'book_status';
    return $vars;
});

add_action('pre_get_posts', function ($query) {
    if (is_admin() || !$query->is_main_query() || !$query->is_post_type_archive('book')) {
        return;
    }

    $status = $query->get('book_status');
    $statuses = author_theme_book_statuses();
    if (!is_string($status) || !isset($statuses[$status])) {
        return;
    }

    $clause = ['key' => 'status', 'value' => $statuses[$status], 'compare' => 'IN'];
    if ('published' === $status) {
        $clause = [
            'relation' => 'OR',
            $clause,
            ['key' => 'status', 'compare' => 'NOT EXISTS'],
            ['key' => 'status', 'value' => '', 'compare' => '='],
        ];
    }
    $existing = $query->get('meta_query') ?: [];
    $existing[] = $clause;
    $query->set('meta_query', $existing);
});

add_action('customize_register', function ($customizer) {
    $section = 'author_theme_books';
    $customizer->add_section($section, ['title' => __('Books Page', 'author-theme')]);
    foreach ([
        'banner' => __('Books banner artwork', 'author-theme'),
        'postcard' => __('Image behind the featured cover', 'author-theme'),
    ] as $key => $label) {
        $setting = 'author_books_' . $key;
        $customizer->add_setting($setting, ['sanitize_callback' => 'absint']);
        $customizer->add_control(new WP_Customize_Media_Control($customizer, $setting, [
            'label' => $label, 'section' => $section, 'mime_type' => 'image',
        ]));
    }
});
