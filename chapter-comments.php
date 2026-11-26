<?php if (post_password_required()) { return; } ?>
<?php $paragraph = is_singular('chapter') ? author_theme_chapter_thread($_GET['paragraph'] ?? '') : 0; ?>
<section id="chapter-discussion" class="chapter-comments" aria-labelledby="chapter-comments-heading"
    data-paragraph="<?php echo esc_attr($paragraph); ?>"
    data-link-label="<?php esc_attr_e('Discuss paragraph %s', 'author-theme'); ?>"
    data-count-label="<?php esc_attr_e('Discuss paragraph %1$s (%2$s)', 'author-theme'); ?>"
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
    <?php if (is_singular('chapter') && comments_open() && !is_user_logged_in()) : ?>
        <?php $return_url = ($paragraph ? add_query_arg('paragraph', $paragraph, get_permalink()) : get_permalink()) . '#chapter-discussion'; ?>
        <div class="chapter-comment-identity">
            <a href="<?php echo esc_url(wp_login_url($return_url)); ?>"><?php esc_html_e('Sign in', 'author-theme'); ?></a>
            <?php if (get_option('users_can_register')) : ?>
                <a href="<?php echo esc_url(wp_registration_url()); ?>"><?php esc_html_e('Sign up', 'author-theme'); ?></a>
            <?php endif; ?>
            <?php if (!get_option('comment_registration')) : ?>
                <button type="button" data-guest-start hidden><?php esc_html_e('Continue as guest', 'author-theme'); ?></button>
                <p data-guest-note hidden><?php esc_html_e('Enter your name and email once. This tab remembers them for your reading session.', 'author-theme'); ?></p>
                <button type="button" data-guest-reset hidden><?php esc_html_e('Use a different name or email', 'author-theme'); ?></button>
            <?php endif; ?>
        </div>
    <?php endif; ?>
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
