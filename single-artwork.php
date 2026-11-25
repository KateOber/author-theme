<?php get_header(); ?>
<main class="gallery-artwork">
    <?php while (have_posts()) : the_post(); ?>
        <?php $gallery_page = author_theme_home_page('gallery'); ?>
        <?php if ($gallery_page) : ?>
            <a href="<?php echo esc_url(get_permalink($gallery_page)); ?>">← <?php esc_html_e('Back to Gallery', 'author-theme'); ?></a>
        <?php endif; ?>
        <article>
            <h1><?php the_title(); ?></h1>
            <?php if (!post_password_required()) : ?>
                <?php the_post_thumbnail('full'); ?>
            <?php endif; ?>
            <?php the_content(); ?>
        </article>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>
