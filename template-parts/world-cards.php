<ul class="book-world-cards">
    <?php foreach ($args['entries'] as $entry) : ?>
        <?php
        $label = get_post_meta($entry->ID, '_author_world_label', true);
        $summary = author_theme_home_summary($entry, 22);
        $listening_url = 'playlist' === $entry->post_type ? esc_url(get_post_meta($entry->ID, '_author_world_url', true)) : '';
        ?>
        <li>
            <article class="book-world-card book-world-card-<?php echo esc_attr($entry->post_type); ?>">
                <a class="book-world-card-title" href="<?php echo esc_url(get_permalink($entry)); ?>">
                    <?php echo get_the_post_thumbnail($entry, 'medium_large', ['alt' => '']); ?>
                    <h3><?php echo esc_html(get_the_title($entry)); ?></h3>
                </a>
                <?php if ($label) : ?><p class="book-world-label"><?php echo esc_html($label); ?></p><?php endif; ?>
                <?php if ($summary) : ?><p><?php echo esc_html($summary); ?></p><?php endif; ?>
                <?php if ($listening_url) : ?>
                    <a href="<?php echo esc_url($listening_url); ?>" aria-label="<?php echo esc_attr(sprintf(__('Listen to %s', 'author-theme'), get_the_title($entry))); ?>"><?php esc_html_e('Listen', 'author-theme'); ?> →</a>
                <?php endif; ?>
            </article>
        </li>
    <?php endforeach; ?>
</ul>
