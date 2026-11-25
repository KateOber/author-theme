<?php get_header(); ?>
<?php while (have_posts()) : the_post(); ?>
    <?php
    $locked = post_password_required();
    $banner = $locked ? 0 : (get_post_thumbnail_id() ?: absint(get_theme_mod('author_home_hero_image')));
    $categories = author_theme_gallery_categories();
    $requested = $_GET['gallery_category'] ?? '';
    $filter = is_string($requested) ? sanitize_key(wp_unslash($requested)) : '';
    $filter = isset($categories[$filter]) ? $filter : '';
    $gallery_url = get_permalink();
    $artworks = $locked ? [] : array_filter(author_theme_world_entries('artwork'), static function ($artwork) use ($filter) {
        return has_post_thumbnail($artwork) && (!$filter || get_post_meta($artwork->ID, '_author_gallery_category', true) === $filter);
    });
    ?>
    <main class="books-archive author-gallery">
        <header class="books-hero gallery-hero">
            <?php echo wp_get_attachment_image($banner, 'full', false, ['alt' => '', 'fetchpriority' => 'high']); ?>
            <h1><?php the_title(); ?></h1>
        </header>
        <div class="gallery-inner">
            <div class="gallery-intro">
                <?php if ($locked || trim(get_the_content())) : ?>
                    <?php the_content(); ?>
                <?php else : ?>
                    <p><?php esc_html_e('Illustrations, character art, location studies, and other visual pieces from my stories and creative work.', 'author-theme'); ?></p>
                <?php endif; ?>
            </div>
            <?php if (!$locked) : ?>
                <nav class="books-filters gallery-filters" aria-label="<?php esc_attr_e('Filter artwork', 'author-theme'); ?>">
                    <a href="<?php echo esc_url($gallery_url); ?>"<?php if (!$filter) : ?> aria-current="page"<?php endif; ?>><?php esc_html_e('All', 'author-theme'); ?></a>
                    <?php foreach ($categories as $key => $label) : ?>
                        <a href="<?php echo esc_url(add_query_arg('gallery_category', $key, $gallery_url)); ?>"<?php if ($filter === $key) : ?> aria-current="page"<?php endif; ?>><?php echo esc_html($label); ?></a>
                    <?php endforeach; ?>
                </nav>
                <?php if ($artworks) : ?>
                    <ul class="gallery-grid">
                        <?php foreach ($artworks as $artwork) : ?>
                            <li>
                                <a href="<?php echo esc_url(get_permalink($artwork)); ?>" aria-label="<?php echo esc_attr(get_the_title($artwork)); ?>">
                                    <?php echo get_the_post_thumbnail($artwork, 'large', ['sizes' => '(min-width: 1024px) 33vw, (min-width: 608px) 50vw, 100vw']); ?>
                                    <span aria-hidden="true"><?php echo esc_html(get_the_title($artwork)); ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p class="gallery-empty"><?php echo esc_html($filter ? __('No artwork in this category yet.', 'author-theme') : __('No artwork has been added yet.', 'author-theme')); ?></p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>
<?php endwhile; ?>
<?php get_footer(); ?>
