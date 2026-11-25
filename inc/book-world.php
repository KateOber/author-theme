<?php
function author_theme_world_types() {
    return [
        'character' => [__('Characters', 'author-theme'), __('Character', 'author-theme')],
        'location' => [__('Locations', 'author-theme'), __('Location', 'author-theme')],
        'artwork' => [__('Artwork', 'author-theme'), __('Artwork', 'author-theme')],
        'playlist' => [__('Playlists', 'author-theme'), __('Playlist', 'author-theme')],
    ];
}

function author_theme_register_world_types() {
    foreach (author_theme_world_types() as $type => [$plural, $singular]) {
        register_post_type($type, [
            'labels' => ['name' => $plural, 'singular_name' => $singular],
            'public' => true,
            'show_in_rest' => true,
            'show_in_nav_menus' => false,
            'has_archive' => false,
            'rewrite' => false,
            'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
        ]);
    }
}
add_action('init', 'author_theme_register_world_types');

function author_theme_world_relationships($type) {
    $books = ['book' => __('Books', 'author-theme')];
    $characters = ['character' => __('Characters', 'author-theme')];
    if ('book' === $type) {
        return [];
    }
    if ('artwork' === $type) {
        return $books + $characters + ['chapter' => __('Chapters', 'author-theme')];
    }
    return 'playlist' === $type ? $books + $characters : $books;
}

function author_theme_world_fields($type) {
    if ('book' === $type) {
        return [
            '_author_wattpad_url' => [__('Wattpad URL', 'author-theme'), 'url'],
            '_author_discussion_url' => [__('Book discussion URL', 'author-theme'), 'url'],
        ];
    }
    $fields = ['_author_world_label' => [__('Short label (for example, Protagonist)', 'author-theme'), 'text']];
    if ('playlist' === $type) {
        $fields['_author_world_url'] = [__('Listening URL', 'author-theme'), 'url'];
    }
    return $fields;
}

add_action('add_meta_boxes', function ($type) {
    if ('book' !== $type && !isset(author_theme_world_types()[$type])) {
        return;
    }
    add_meta_box('author-book-world', __('Book World', 'author-theme'), function ($post) {
        wp_nonce_field('author_save_world', 'author_world_nonce');
        foreach (author_theme_world_fields($post->post_type) as $key => [$label, $input]) {
            ?>
            <p><label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label><br>
            <input class="widefat" type="<?php echo esc_attr($input); ?>" id="<?php echo esc_attr($key); ?>"
                name="author_world_fields[<?php echo esc_attr($key); ?>]"
                value="<?php echo esc_attr(get_post_meta($post->ID, $key, true)); ?>"></p>
            <?php
        }
        if ('artwork' === $post->post_type) {
            $category = get_post_meta($post->ID, '_author_gallery_category', true);
            ?>
            <p><label for="author-gallery-category"><?php esc_html_e('Gallery Category', 'author-theme'); ?></label><br>
            <select id="author-gallery-category" name="author_gallery_category">
                <option value=""><?php esc_html_e('Uncategorized', 'author-theme'); ?></option>
                <?php foreach (author_theme_gallery_categories() as $key => $label) : ?>
                    <option value="<?php echo esc_attr($key); ?>" <?php selected($category, $key); ?>><?php echo esc_html($label); ?></option>
                <?php endforeach; ?>
            </select></p>
            <?php
        }
        foreach (author_theme_world_relationships($post->post_type) as $type => $label) {
            $selected = array_map('intval', get_post_meta($post->ID, '_author_world_' . $type, false));
            $choices = get_posts([
                'post_type' => $type,
                'post_status' => ['publish', 'draft', 'pending', 'future', 'private'],
                'numberposts' => -1,
                'orderby' => 'title',
                'order' => 'ASC',
            ]);
            ?>
            <fieldset style="margin: 1em 0">
                <legend><strong><?php echo esc_html($label); ?></strong></legend>
                <div style="max-height: 12em; overflow: auto">
                    <?php foreach ($choices as $choice) : ?>
                        <?php if (current_user_can('read_post', $choice->ID) || in_array($choice->ID, $selected, true)) : ?>
                            <label style="display: block">
                                <input type="checkbox" name="author_world_links[<?php echo esc_attr($type); ?>][]"
                                    value="<?php echo esc_attr($choice->ID); ?>" <?php checked(in_array($choice->ID, $selected, true)); ?>>
                                <?php echo esc_html(get_the_title($choice)); ?>
                            </label>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </fieldset>
            <?php
        }
    }, $type);
}, 10, 1);

add_action('save_post', function ($id, $post) {
    $nonce = $_POST['author_world_nonce'] ?? '';
    if (!is_string($nonce) || !wp_verify_nonce(wp_unslash($nonce), 'author_save_world')
        || wp_is_post_autosave($id) || wp_is_post_revision($id) || !current_user_can('edit_post', $id)) {
        return;
    }
    if ('book' !== $post->post_type && !isset(author_theme_world_types()[$post->post_type])) {
        return;
    }

    foreach (author_theme_world_fields($post->post_type) as $key => [, $input]) {
        $value = $_POST['author_world_fields'][$key] ?? null;
        if (!is_string($value)) {
            continue;
        }
        $value = wp_unslash($value);
        update_post_meta($id, $key, wp_slash('url' === $input ? esc_url_raw($value) : sanitize_text_field($value)));
    }
    if ('artwork' === $post->post_type && isset($_POST['author_gallery_category']) && is_string($_POST['author_gallery_category'])) {
        $category = sanitize_key(wp_unslash($_POST['author_gallery_category']));
        if (isset(author_theme_gallery_categories()[$category])) {
            update_post_meta($id, '_author_gallery_category', $category);
        } elseif ('' === $category) {
            delete_post_meta($id, '_author_gallery_category');
        }
    }
    foreach (author_theme_world_relationships($post->post_type) as $type => $label) {
        $key = '_author_world_' . $type;
        $old = array_map('intval', get_post_meta($id, $key, false));
        $values = $_POST['author_world_links'][$type] ?? [];
        $new = [];
        foreach ((array) $values as $value) {
            if (is_string($value) && ctype_digit($value) && get_post_type((int) $value) === $type
                && (in_array((int) $value, $old, true) || current_user_can('read_post', (int) $value))) {
                $new[] = (int) $value;
            }
        }
        foreach (array_diff($old, $new) as $removed) {
            delete_post_meta($id, $key, $removed);
        }
        foreach (array_unique(array_diff($new, $old)) as $added) {
            add_post_meta($id, $key, $added);
        }
    }
}, 10, 2);

function author_theme_world_entries($type, $relationships = []) {
    $args = [
        'post_type' => $type,
        'post_status' => 'publish',
        'has_password' => false,
        'numberposts' => -1,
        'orderby' => ['menu_order' => 'ASC', 'title' => 'ASC', 'ID' => 'ASC'],
    ];
    if ($relationships) {
        $args['meta_query'] = ['relation' => 'OR'];
        foreach ($relationships as $related_type => $ids) {
            if ($ids) {
                $args['meta_query'][] = [
                    'key' => '_author_world_' . $related_type,
                    'value' => array_values($ids),
                    'compare' => 'IN',
                    'type' => 'UNSIGNED',
                ];
            }
        }
    }
    return get_posts($args);
}

function author_theme_book_world($book_id, $chapters) {
    $relationship = ['book' => [$book_id]];
    $characters = author_theme_world_entries('character', $relationship);
    $related_characters = $relationship + ['character' => wp_list_pluck($characters, 'ID')];
    $public_chapters = array_filter($chapters, static function ($chapter) {
        return !$chapter->post_password;
    });
    return [
        'characters' => $characters,
        'world' => author_theme_world_entries('location', $relationship),
        'artwork' => author_theme_world_entries('artwork', $related_characters + ['chapter' => wp_list_pluck($public_chapters, 'ID')]),
        'playlists' => author_theme_world_entries('playlist', $related_characters),
    ];
}

function author_theme_world_collection_type() {
    foreach (['gallery' => 'artwork', 'playlists' => 'playlist'] as $slug => $type) {
        $page = author_theme_home_page($slug);
        if ($page && is_page($page->ID)) {
            return $type;
        }
    }
    return '';
}

add_filter('the_content', function ($content) {
    if (!is_page() || is_front_page() || !is_main_query() || !in_the_loop() || post_password_required()) {
        return $content;
    }
    $type = author_theme_world_collection_type();
    if ('playlist' !== $type) {
        return $content;
    }
    $entries = author_theme_world_entries($type);
    if (!$entries) {
        return $content;
    }

    ob_start();
    get_template_part('template-parts/world-cards', null, ['entries' => $entries]);
    return $content . ob_get_clean();
});
