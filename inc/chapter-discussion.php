<?php

function author_theme_chapter_paragraphs($content) {
    if (!class_exists('WP_HTML_Tag_Processor')) {
        return $content;
    }

    $html = new WP_HTML_Tag_Processor($content);
    $index = 0;
    while ($html->next_tag('P')) {
        $html->set_attribute('data-chapter-paragraph', (string) ++$index);
    }
    return $html->get_updated_html();
}

function author_theme_chapter_thread($source) {
    return is_string($source) && preg_match('/^[1-9][0-9]{0,6}$/D', $source) ? (int) $source : 0;
}

add_filter('preprocess_comment', function ($comment) {
    $post_id = (int) $comment['comment_post_ID'];
    if (get_post_type($post_id) !== 'chapter') {
        return $comment;
    }

    $parent_id = (int) ($comment['comment_parent'] ?? 0);
    $parent = $parent_id ? get_comment($parent_id) : null;
    $paragraph = $parent_id
        ? ($parent && (int) $parent->comment_post_ID === $post_id ? (int) get_comment_meta($parent->comment_ID, 'author_chapter_paragraph', true) : 0)
        : author_theme_chapter_thread($_POST['author_chapter_paragraph'] ?? '');

    if ($paragraph && !post_password_required($post_id) && in_array($comment['comment_type'] ?? '', ['', 'comment'], true)) {
        $comment['comment_meta']['author_chapter_paragraph'] = $paragraph;
    }
    return $comment;
});

add_filter('comment_post_redirect', function ($location, $comment) {
    $paragraph = (int) get_comment_meta($comment->comment_ID, 'author_chapter_paragraph', true);
    if (!$paragraph || get_post_type($comment->comment_post_ID) !== 'chapter') {
        return $location;
    }

    parse_str((string) wp_parse_url($location, PHP_URL_QUERY), $query);
    $query = array_intersect_key($query, array_flip(['unapproved', 'moderation-hash']));
    $query['paragraph'] = $paragraph;
    return add_query_arg($query, get_permalink($comment->comment_post_ID)) . '#comment-' . $comment->comment_ID;
}, 10, 2);
