<?php get_header(); ?>
<?php while (have_posts()) : the_post(); ?>
    <?php
    $book_id = get_the_ID();
    $locked = post_password_required();
    $meta = $locked ? [] : array_map('strval', array_map('reset', array_intersect_key(get_post_meta($book_id), array_flip([
        'series', 'book_number', 'setting', 'genre', 'themes', 'status', 'what_to_expect',
        'featured_quote', 'pov', 'content_notes', 'update_schedule', 'banner_image_id',
        'expect_image_id', 'quote_image_id',
    ]))));
    $banner = $locked ? 0 : (absint($meta['banner_image_id'] ?? 0) ?: absint(get_theme_mod('author_books_banner')) ?: absint(get_theme_mod('author_home_hero_image')));
    $chapters = $locked ? [] : author_theme_book_chapters($book_id);
    $facts = ['setting' => __('Setting', 'author-theme'), 'genre' => __('Genre', 'author-theme'), 'themes' => __('Themes', 'author-theme'), 'status' => __('Status', 'author-theme')];
    $details = ['pov' => __('POV', 'author-theme'), 'content_notes' => __('Content Notes', 'author-theme'), 'update_schedule' => __('Update Schedule', 'author-theme')];
    $facts = array_intersect_key($facts, array_filter($meta));
    $details = array_intersect_key($details, array_filter($meta));
    ?>
    <main class="books-archive book-detail">
        <header class="books-hero book-detail-hero">
            <?php if ($banner) : ?>
                <?php echo wp_get_attachment_image($banner, 'full', false, ['alt' => '', 'fetchpriority' => 'high']); ?>
            <?php endif; ?>
            <div>
                <?php if (!empty($meta['series'])) : ?><p class="books-kicker"><?php echo esc_html($meta['series']); ?></p><?php endif; ?>
                <h1><?php the_title(); ?></h1>
                <?php if (!empty($meta['book_number'])) : ?><p class="books-kicker"><?php echo esc_html($meta['book_number']); ?></p><?php endif; ?>
            </div>
        </header>
        <div class="books-inner book-detail-inner">
            <article class="books-feature<?php echo !$locked && has_post_thumbnail() ? ' has-cover' : ''; ?>">
                <?php if (!$locked && has_post_thumbnail()) : ?>
                    <div class="books-cover">
                        <?php echo wp_get_attachment_image(absint(get_theme_mod('author_books_postcard')), 'medium', false, ['class' => 'books-postcard', 'alt' => '']); ?>
                        <?php the_post_thumbnail('large', ['class' => 'books-cover-image']); ?>
                    </div>
                <?php endif; ?>
                <div class="books-feature-copy">
                    <?php if (!$locked) : ?>
                        <h2><?php the_title(); ?></h2>
                        <?php if (has_excerpt()) : ?><p class="book-detail-pitch"><?php echo esc_html(wp_strip_all_tags(get_the_excerpt())); ?></p><?php endif; ?>
                    <?php endif; ?>
                    <?php the_content(); ?>
                    <?php if ($chapters) : ?>
                        <div class="books-actions">
                            <a class="books-button books-button-primary" href="<?php echo esc_url(get_permalink($chapters[0])); ?>"><?php esc_html_e('Start Reading', 'author-theme'); ?> →</a>
                            <a class="books-button" href="#chapters"><?php esc_html_e('Chapter List', 'author-theme'); ?></a>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if ($facts) : ?>
                    <dl class="books-meta">
                        <?php foreach ($facts as $key => $label) : ?>
                            <div><dt><?php echo esc_html($label); ?></dt><dd><?php echo esc_html($meta[$key]); ?></dd></div>
                        <?php endforeach; ?>
                    </dl>
                <?php endif; ?>
            </article>

            <?php if (!empty($meta['what_to_expect']) || $chapters) : ?>
                <div class="book-detail-panels">
                    <?php if (!empty($meta['what_to_expect'])) : ?>
                        <section class="book-detail-panel">
                            <h2 class="books-rule"><?php esc_html_e('What to Expect', 'author-theme'); ?></h2>
                            <?php echo wp_get_attachment_image(absint($meta['expect_image_id'] ?? 0), 'medium_large', false, ['class' => 'book-detail-expect-art', 'alt' => '']); ?>
                            <?php echo wpautop(esc_html($meta['what_to_expect'])); ?>
                        </section>
                    <?php endif; ?>
                    <?php if ($chapters) : ?>
                        <section class="book-detail-panel">
                            <h2 class="books-rule"><?php esc_html_e('Chapter Preview', 'author-theme'); ?></h2>
                            <ol class="book-detail-chapters">
                                <?php foreach (array_slice($chapters, 0, 5) as $chapter) : ?>
                                    <li><a href="<?php echo esc_url(get_permalink($chapter)); ?>"><?php echo esc_html(get_the_title($chapter)); ?> <span aria-hidden="true">›</span></a></li>
                                <?php endforeach; ?>
                            </ol>
                            <a href="#chapters"><?php esc_html_e('View Full Chapter List', 'author-theme'); ?> →</a>
                        </section>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($meta['featured_quote'])) : ?>
                <figure class="book-detail-quote">
                    <?php echo wp_get_attachment_image(absint($meta['quote_image_id'] ?? 0), 'large', false, ['class' => 'book-detail-quote-art', 'alt' => '']); ?>
                    <blockquote><p><?php echo esc_html($meta['featured_quote']); ?></p></blockquote>
                    <figcaption><?php the_title(); ?></figcaption>
                </figure>
            <?php endif; ?>

            <?php if ($details) : ?>
                <section>
                    <h2 class="books-rule"><?php esc_html_e('Story Details', 'author-theme'); ?></h2>
                    <dl class="book-detail-facts">
                        <?php foreach ($details as $key => $label) : ?>
                            <div><dt><?php echo esc_html($label); ?></dt><dd><?php echo esc_html($meta[$key]); ?></dd></div>
                        <?php endforeach; ?>
                    </dl>
                </section>
            <?php endif; ?>

            <?php if ($chapters) : ?>
                <section class="book-detail-panel book-detail-full-list" aria-labelledby="chapters">
                    <h2 id="chapters" class="books-rule"><?php esc_html_e('Chapters', 'author-theme'); ?></h2>
                    <ol class="book-detail-chapters">
                        <?php foreach ($chapters as $chapter) : ?>
                            <li><a href="<?php echo esc_url(get_permalink($chapter)); ?>"><?php echo esc_html(get_the_title($chapter)); ?> <span aria-hidden="true">›</span></a></li>
                        <?php endforeach; ?>
                    </ol>
                </section>
            <?php endif; ?>
        </div>
    </main>
<?php endwhile; ?>
<?php get_footer(); ?>
