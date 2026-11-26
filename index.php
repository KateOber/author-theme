<?php get_header(); ?>
<main>
    <?php if (is_archive()) : ?>
        <h1><?php echo esc_html(get_the_archive_title()); ?></h1>
    <?php endif; ?>
    <?php if (is_404()) : ?>
        <h1><?php esc_html_e('Page not found', 'author-theme'); ?></h1>
    <?php endif; ?>
    <?php while (have_posts()) : the_post(); ?>
        <article>
            <?php if (is_singular()) : ?>
                <h1><?php the_title(); ?></h1>
                <?php the_content(); ?>
            <?php else : ?>
                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <?php the_excerpt(); ?>
            <?php endif; ?>
        </article>
        <?php if (is_singular('post') && !post_password_required() && (comments_open() || get_comments_number())) : ?>
            <?php comments_template('/chapter-comments.php'); ?>
        <?php endif; ?>
    <?php endwhile; ?>
    <?php the_posts_pagination(); ?>
</main>
<?php get_footer(); ?>
