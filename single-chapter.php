<?php get_header(); ?>
<main class="chapter-page">
    <?php while (have_posts()) : the_post(); ?>
        <?php
        $chapter_id = get_the_ID();
        $book = author_theme_chapter_book($chapter_id);
        $protected = post_password_required();
        $show_comments = !$protected && (comments_open() || get_comments_number());
        $chapters = $book && !$protected ? author_theme_book_chapters($book->ID) : [];
        $position = array_search($chapter_id, wp_list_pluck($chapters, 'ID'), true);
        $previous = false === $position ? null : ($chapters[$position - 1] ?? null);
        $next = false === $position ? null : ($chapters[$position + 1] ?? null);
        $book_url = $book ? get_permalink($book) : '';
        $book_title = $book ? get_the_title($book) : '';
        $chapter_number = get_post_meta($chapter_id, 'chapter_number', true);
        $title = get_the_title();
        $number_heading = '' !== $chapter_number && (int) $chapter_number > 0
            ? sprintf(__('Chapter %s', 'author-theme'), number_format_i18n((int) $chapter_number))
            : '';
        if ($number_heading && preg_match('/^(?:chapter|ch\.?)\s*\d+\b[\s:–—.\-]*/iu', $title, $prefix)) {
            $title = trim(substr($title, strlen($prefix[0])));
        }
        ?>
        <?php if (!$protected && has_post_thumbnail()) : ?>
            <div class="chapter-banner">
                <?php the_post_thumbnail('full', ['alt' => '', 'fetchpriority' => 'high']); ?>
            </div>
        <?php endif; ?>
        <div class="chapter-layout<?php echo $chapters ? ' has-chapter-sidebar' : ''; ?>">
            <?php if ($chapters) : ?>
                <aside class="chapter-sidebar">
                    <a class="chapter-book-cover" href="<?php echo esc_url($book_url); ?>">
                        <?php if (has_post_thumbnail($book->ID) && !post_password_required($book)) : ?>
                            <?php echo get_the_post_thumbnail($book->ID, 'medium_large', ['alt' => $book_title]); ?>
                        <?php else : ?>
                            <?php echo esc_html($book_title); ?>
                        <?php endif; ?>
                    </a>
                    <?php if (!post_password_required($book)) : ?>
                        <?php $genre = get_post_meta($book->ID, 'genre', true); ?>
                        <?php if ($genre) : ?>
                            <p class="chapter-book-genre"><?php echo esc_html($genre); ?></p>
                        <?php endif; ?>
                        <?php if (has_excerpt($book)) : ?>
                            <p><?php echo esc_html(wp_strip_all_tags(get_the_excerpt($book))); ?></p>
                        <?php endif; ?>
                    <?php endif; ?>
                    <nav aria-label="<?php esc_attr_e('Chapters in this book', 'author-theme'); ?>">
                        <ol class="chapter-index">
                            <?php foreach ($chapters as $entry) : ?>
                                <li>
                                    <a href="<?php echo esc_url(get_permalink($entry)); ?>"<?php if ($entry->ID === $chapter_id) : ?> aria-current="page"<?php endif; ?>>
                                        <?php echo esc_html(get_the_title($entry)); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </nav>
                    <a class="chapter-back" href="<?php echo esc_url($book_url); ?>">← <?php esc_html_e('Back to Book', 'author-theme'); ?></a>
                </aside>
            <?php endif; ?>
            <article class="chapter-reading">
                <header class="chapter-heading">
                    <?php if ($book) : ?>
                        <a class="chapter-book-name" href="<?php echo esc_url($book_url); ?>"><?php echo esc_html($book_title); ?></a>
                    <?php endif; ?>
                    <h1><?php echo esc_html($number_heading ?: $title); ?></h1>
                    <?php if ($number_heading && $title) : ?>
                        <p class="chapter-subtitle"><?php echo esc_html($title); ?></p>
                    <?php endif; ?>
                </header>
                <?php if ($show_comments) : ?>
                    <button class="chapter-discussion-toggle" type="button" aria-pressed="false" hidden><?php esc_html_e('Show paragraph comment controls', 'author-theme'); ?></button>
                <?php endif; ?>
                <div class="chapter-prose">
                    <?php
                    if ($show_comments) {
                        add_filter('the_content', 'author_theme_chapter_paragraphs', PHP_INT_MAX);
                    }
                    the_content();
                    remove_filter('the_content', 'author_theme_chapter_paragraphs', PHP_INT_MAX);
                    ?>
                </div>
                <?php if ($chapters) : ?>
                    <nav class="chapter-pagination" aria-label="<?php esc_attr_e('Chapter navigation', 'author-theme'); ?>">
                        <?php if ($previous) : ?>
                            <a class="chapter-previous" rel="prev" href="<?php echo esc_url(get_permalink($previous)); ?>">
                                <span>← <?php esc_html_e('Previous', 'author-theme'); ?></span>
                                <span><?php echo esc_html(get_the_title($previous)); ?></span>
                            </a>
                        <?php endif; ?>
                        <a class="chapter-list-link" href="<?php echo esc_url($book_url . '#chapters'); ?>"><span aria-hidden="true">☷</span><?php esc_html_e('Chapters', 'author-theme'); ?></a>
                        <?php if ($next) : ?>
                            <a class="chapter-next" rel="next" href="<?php echo esc_url(get_permalink($next)); ?>">
                                <span><?php esc_html_e('Next', 'author-theme'); ?> →</span>
                                <span><?php echo esc_html(get_the_title($next)); ?></span>
                            </a>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>
                <?php if ($show_comments) : ?>
                    <?php
                    $paragraph = author_theme_chapter_thread($_GET['paragraph'] ?? '');
                    $thread_query = static function ($args) use ($paragraph) {
                        if ($paragraph) {
                            $args['meta_key'] = 'author_chapter_paragraph';
                            $args['meta_value'] = $paragraph;
                            $args['number'] = 0;
                            $args['offset'] = 0;
                        }
                        return $args;
                    };
                    add_filter('comments_template_query_args', $thread_query);
                    comments_template('/chapter-comments.php');
                    remove_filter('comments_template_query_args', $thread_query);
                    ?>
                <?php endif; ?>
            </article>
        </div>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>
