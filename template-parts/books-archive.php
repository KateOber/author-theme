<?php
get_header();
$short_stories = is_post_type_archive('short_story');
$filter = $short_stories ? '' : author_theme_book_filter();
$archive_url = get_post_type_archive_link('book');
$banner = absint(get_theme_mod('author_books_banner')) ?: absint(get_theme_mod('author_home_hero_image'));
$postcard = absint(get_theme_mod('author_books_postcard'));
$books = $GLOBALS['wp_query']->posts;
$featured = !$short_stories && !$filter && !is_paged() ? author_theme_home_featured_book() : null;
if ($featured && !in_array($featured->ID, wp_list_pluck($books, 'ID'), true)) {
    $featured = null;
}
$groups = ['progress' => [], 'other' => []];
$statuses = author_theme_book_statuses();
foreach ($books as $book) {
    if ($featured && $book->ID === $featured->ID) {
        continue;
    }
    $status = strtolower(trim((string) get_post_meta($book->ID, 'status', true)));
    $group = !$short_stories && in_array($status, $statuses['in-progress'], true) ? 'progress' : 'other';
    $groups[$group][] = $book;
}
?>
<main class="books-archive">
    <header class="books-hero">
        <?php if ($banner) : ?>
            <?php echo wp_get_attachment_image($banner, 'full', false, ['alt' => '', 'fetchpriority' => 'high']); ?>
        <?php endif; ?>
        <h1><?php echo esc_html($short_stories ? __('Short Stories', 'author-theme') : __('Books', 'author-theme')); ?></h1>
    </header>
    <div class="books-inner">
        <p class="books-intro"><?php echo esc_html($short_stories ? __('Short fiction, ideas, and experiments.', 'author-theme') : __('Novels, works in progress, and stories in the making.', 'author-theme')); ?></p>
        <nav class="books-filters" aria-label="<?php esc_attr_e('Browse publications', 'author-theme'); ?>">
            <a href="<?php echo esc_url($archive_url); ?>"<?php if (!$short_stories && !$filter) : ?> aria-current="page"<?php endif; ?>><?php esc_html_e('All Books', 'author-theme'); ?></a>
            <?php foreach (author_theme_book_filters() as $key => $label) : ?>
                <a href="<?php echo esc_url(add_query_arg('book_status', $key, $archive_url)); ?>"<?php if ($filter === $key) : ?> aria-current="page"<?php endif; ?>><?php echo esc_html($label); ?></a>
            <?php endforeach; ?>
            <a href="<?php echo esc_url(get_post_type_archive_link('short_story')); ?>"<?php if ($short_stories) : ?> aria-current="page"<?php endif; ?>><?php esc_html_e('Short Stories', 'author-theme'); ?></a>
        </nav>

        <?php if ($featured) : ?>
            <?php
            $book_url = get_permalink($featured);
            $chapters = author_theme_book_chapters($featured->ID);
            $metadata = array_filter([
                __('Setting', 'author-theme') => get_post_meta($featured->ID, 'setting', true),
                __('Genre', 'author-theme') => get_post_meta($featured->ID, 'genre', true),
                __('Themes', 'author-theme') => get_post_meta($featured->ID, 'themes', true),
            ]);
            ?>
            <section class="books-feature<?php echo has_post_thumbnail($featured) ? ' has-cover' : ''; ?>" aria-labelledby="books-feature-title">
                <?php if (has_post_thumbnail($featured)) : ?>
                    <a class="books-cover" href="<?php echo esc_url($book_url); ?>" aria-label="<?php echo esc_attr(get_the_title($featured)); ?>">
                        <?php if ($postcard) : ?>
                            <?php echo wp_get_attachment_image($postcard, 'medium', false, ['class' => 'books-postcard', 'alt' => '']); ?>
                        <?php endif; ?>
                        <?php echo get_the_post_thumbnail($featured, 'large', ['class' => 'books-cover-image', 'alt' => '']); ?>
                    </a>
                <?php endif; ?>
                <div class="books-feature-copy">
                    <p class="books-kicker"><?php esc_html_e('Featured Novel', 'author-theme'); ?></p>
                    <h2 id="books-feature-title"><a href="<?php echo esc_url($book_url); ?>"><?php echo esc_html(get_the_title($featured)); ?></a></h2>
                    <?php $series = get_post_meta($featured->ID, 'series', true); ?>
                    <?php if ($series) : ?><p><?php echo esc_html($series); ?></p><?php endif; ?>
                    <p><?php echo esc_html(author_theme_home_summary($featured, 70)); ?></p>
                    <div class="books-actions">
                        <a class="books-button books-button-primary" href="<?php echo esc_url($chapters ? get_permalink($chapters[0]) : $book_url); ?>"><?php echo esc_html($chapters ? __('Read Chapters', 'author-theme') : __('View Book', 'author-theme')); ?> →</a>
                        <?php if ($chapters) : ?><a class="books-button" href="<?php echo esc_url($book_url . '#chapters'); ?>"><?php esc_html_e('Chapter List', 'author-theme'); ?></a><?php endif; ?>
                    </div>
                </div>
                <?php if ($metadata) : ?>
                    <dl class="books-meta">
                        <?php foreach ($metadata as $label => $value) : ?>
                            <div><dt><?php echo esc_html($label); ?></dt><dd><?php echo esc_html($value); ?></dd></div>
                        <?php endforeach; ?>
                    </dl>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <?php
        $sections = [
            'progress' => __('Work in Progress', 'author-theme'),
            'other' => $short_stories ? '' : ($filter ? author_theme_book_filters()[$filter] : __('Other Projects', 'author-theme')),
        ];
        foreach ($sections as $group => $heading) :
            if (!$groups[$group]) {
                continue;
            }
            ?>
            <section class="books-projects books-projects-<?php echo esc_attr($group); ?>">
                <?php if ($heading) : ?><h2 class="books-rule"><?php echo esc_html($heading); ?></h2><?php endif; ?>
                <div class="books-project-grid">
                    <?php foreach ($groups[$group] as $book) : ?>
                        <?php
                        $protected = post_password_required($book);
                        $status = $protected ? '' : get_post_meta($book->ID, 'status', true);
                        $progress = $protected ? '' : get_post_meta($book->ID, 'progress', true);
                        ?>
                        <article class="books-project">
                            <?php if (!$protected) : ?>
                                <?php echo get_the_post_thumbnail($book, 'medium_large', ['class' => 'books-project-image']); ?>
                            <?php endif; ?>
                            <div>
                                <h3><a href="<?php echo esc_url(get_permalink($book)); ?>"><?php echo esc_html(get_the_title($book)); ?></a></h3>
                                <?php if ($status) : ?><p class="books-kicker"><?php echo esc_html($status); ?></p><?php endif; ?>
                                <?php if (!$protected) : ?><p><?php echo esc_html(author_theme_home_summary($book, 32)); ?></p><?php endif; ?>
                            </div>
                            <?php if ('progress' === $group && is_numeric($progress) && $progress >= 0 && $progress <= 100) : ?>
                                <div class="books-progress">
                                    <label><?php esc_html_e('Progress', 'author-theme'); ?> <progress value="<?php echo esc_attr($progress); ?>" max="100"></progress></label>
                                    <span><?php echo esc_html(number_format_i18n((float) $progress) . '%'); ?></span>
                                </div>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>

        <?php if (!$books) : ?><p class="books-empty"><?php esc_html_e('There are no titles in this section yet.', 'author-theme'); ?></p><?php endif; ?>
        <?php the_posts_pagination(); ?>
    </div>
</main>
<?php get_footer(); ?>
