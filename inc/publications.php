<?php
function author_theme_register_publications() {
    register_post_type('book', [
        'labels' => ['name' => __('Books', 'author-theme'), 'singular_name' => __('Book', 'author-theme')],
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'books', 'with_front' => false],
        'supports' => ['title', 'editor', 'custom-fields', 'thumbnail', 'excerpt'],
    ]);
    register_post_type('chapter', [
        'labels' => ['name' => __('Chapters', 'author-theme'), 'singular_name' => __('Chapter', 'author-theme')],
        'public' => true,
        'show_in_rest' => true,
        'rewrite' => false,
        'supports' => ['title', 'editor', 'custom-fields', 'thumbnail', 'comments'],
    ]);
    register_post_type('short_story', [
        'labels' => ['name' => __('Short Stories', 'author-theme'), 'singular_name' => __('Short Story', 'author-theme')],
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'short-stories', 'with_front' => false],
        'supports' => ['title', 'editor', 'custom-fields', 'thumbnail'],
    ]);
    add_rewrite_tag('%author_book%', '([^/]+)');
    add_rewrite_rule(
        '^books/([^/]+)/(?!feed/?$|embed/?$|trackback/?$|rdf/?$|rss2?/?$|atom/?$|[0-9]+/?$)([^/]+)/?$',
        'index.php?chapter=$matches[2]&author_book=$matches[1]',
        'top'
    );
}
add_action('init', 'author_theme_register_publications');
add_action('after_switch_theme', function () {
    author_theme_register_publications();
    flush_rewrite_rules(false);
});

function author_theme_chapter_book($chapter_id) {
    $book_id = (int) get_post_meta($chapter_id, 'book_id', true);
    $book = $book_id > 0 ? get_post($book_id) : null;
    return $book && 'book' === $book->post_type && 'publish' === $book->post_status ? $book : null;
}

add_filter('post_type_link', function ($url, $post) {
    if ('chapter' !== $post->post_type || !$post->post_name || !get_option('permalink_structure')) {
        return $url;
    }
    $book = author_theme_chapter_book($post->ID);
    return $book ? home_url(user_trailingslashit('/books/' . $book->post_name . '/' . $post->post_name)) : $url;
}, 10, 2);

add_action('template_redirect', function () {
    $book_slug = get_query_var('author_book');
    if (!$book_slug || !is_singular('chapter')) {
        return;
    }
    $book = author_theme_chapter_book(get_queried_object_id());
    if ($book && rawurldecode($book->post_name) === rawurldecode($book_slug)) {
        return;
    }

    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    nocache_headers();
    remove_action('template_redirect', 'redirect_canonical');
}, 1);

function author_theme_book_chapters($book_id) {
    return get_posts([
        'post_type' => 'chapter',
        'post_status' => 'publish',
        'numberposts' => -1,
        'meta_key' => 'chapter_number',
        'meta_query' => [['key' => 'book_id', 'value' => $book_id]],
        'orderby' => ['meta_value_num' => 'ASC', 'ID' => 'ASC'],
    ]);
}

add_action('add_meta_boxes_chapter', function () {
    add_meta_box('author-chapter-details', __('Chapter Details', 'author-theme'), function ($post) {
        wp_nonce_field('author_save_chapter_details', 'author_chapter_nonce');
        $books = get_posts(['post_type' => 'book', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC']);
        $parent = get_post_meta($post->ID, 'book_id', true);
        ?>
        <p><label for="author-book-id"><?php esc_html_e('Parent Book', 'author-theme'); ?></label>
        <select name="author_book_id" id="author-book-id">
            <option value="0"><?php esc_html_e('Select a book', 'author-theme'); ?></option>
            <?php if ($parent && !author_theme_chapter_book($post->ID)) : ?>
                <option value="<?php echo esc_attr($parent); ?>" selected><?php esc_html_e('Current book unavailable', 'author-theme'); ?></option>
            <?php endif; ?>
            <?php foreach ($books as $book) : ?>
                <option value="<?php echo esc_attr($book->ID); ?>" <?php selected($parent, $book->ID); ?>><?php echo esc_html($book->post_title); ?></option>
            <?php endforeach; ?>
        </select></p>
        <p><label for="author-chapter-number"><?php esc_html_e('Chapter Number', 'author-theme'); ?></label>
        <input type="number" name="author_chapter_number" id="author-chapter-number" min="0"
            value="<?php echo esc_attr(get_post_meta($post->ID, 'chapter_number', true)); ?>"></p>
        <?php
    }, 'chapter');
});

add_action('save_post_chapter', function ($post_id) {
    if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id) || !current_user_can('edit_post', $post_id)) {
        return;
    }
    $nonce = $_POST['author_chapter_nonce'] ?? null;
    if (!is_string($nonce) || !wp_verify_nonce(wp_unslash($nonce), 'author_save_chapter_details')) {
        return;
    }

    $book_id = $_POST['author_book_id'] ?? null;
    if (is_string($book_id) && ctype_digit($book_id)) {
        if ('0' === $book_id) {
            delete_post_meta($post_id, 'book_id');
        } elseif ('book' === get_post_type((int) $book_id)) {
            update_post_meta($post_id, 'book_id', (int) $book_id);
        }
    }
    $number = $_POST['author_chapter_number'] ?? null;
    if (is_string($number) && ctype_digit($number)) {
        update_post_meta($post_id, 'chapter_number', (int) $number);
    }
});
