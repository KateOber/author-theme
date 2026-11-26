<?php if (post_password_required()) { return; } ?>
<?php $paragraph = author_theme_chapter_thread($_GET['paragraph'] ?? ''); ?>
<section id="chapter-discussion" class="chapter-comments" aria-labelledby="chapter-comments-heading"
    data-paragraph="<?php echo esc_attr($paragraph); ?>"
    data-link-label="<?php esc_attr_e('Discuss paragraph %s', 'author-theme'); ?>"
    data-chapter-url="<?php echo esc_url(get_permalink()); ?>">
    <?php if ($paragraph) : ?>
        <a href="<?php echo esc_url(get_permalink() . '#chapter-discussion'); ?>"><?php esc_html_e('All chapter comments', 'author-theme'); ?></a>
    <?php endif; ?>
    <h2 id="chapter-comments-heading">
        <?php
        if ($paragraph) {
            printf(esc_html__('Paragraph %s discussion', 'author-theme'), esc_html(number_format_i18n($paragraph)));
        } else {
            $total = (int) get_comments_number();
            printf(esc_html(_n('Comment (%s)', 'Comments (%s)', $total, 'author-theme')), esc_html(number_format_i18n($total)));
        }
        ?>
    </h2>
    <?php
    $paragraph_field = static function () use ($paragraph) {
        if ($paragraph) {
            printf('<input type="hidden" name="author_chapter_paragraph" value="%d">', $paragraph);
        }
    };
    add_action('comment_form', $paragraph_field);
    comment_form(['title_reply_before' => '<h3 id="reply-title" class="comment-reply-title">', 'title_reply_after' => '</h3>']);
    remove_action('comment_form', $paragraph_field);
    ?>
    <?php if (have_comments()) : ?>
        <ol class="chapter-comment-list">
            <?php wp_list_comments(array_merge(['style' => 'ol', 'avatar_size' => 44, 'short_ping' => true], $paragraph ? ['per_page' => 0] : [])); ?>
        </ol>
        <?php if (!$paragraph) { the_comments_pagination(); } ?>
    <?php endif; ?>
</section>
