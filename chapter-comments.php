<?php if (post_password_required()) { return; } ?>
<section class="chapter-comments" aria-labelledby="chapter-comments-heading">
    <h2 id="chapter-comments-heading">
        <?php
        $total = (int) get_comments_number();
        printf(esc_html(_n('Comment (%s)', 'Comments (%s)', $total, 'author-theme')), esc_html(number_format_i18n($total)));
        ?>
    </h2>
    <?php comment_form(['title_reply_before' => '<h3 id="reply-title" class="comment-reply-title">', 'title_reply_after' => '</h3>']); ?>
    <?php if (have_comments()) : ?>
        <ol class="chapter-comment-list">
            <?php wp_list_comments(['style' => 'ol', 'avatar_size' => 44, 'short_ping' => true]); ?>
        </ol>
        <?php the_comments_pagination(); ?>
    <?php endif; ?>
</section>
