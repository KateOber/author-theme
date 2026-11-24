<?php get_header(); ?>
<main>
    <?php while (have_posts()) : the_post(); ?>
        <article class="reader">
            <h1><?php the_title(); ?></h1>
            <?php if (!post_password_required()) : ?>
                <?php the_post_thumbnail('medium_large', ['class' => 'book-cover']); ?>
            <?php endif; ?>
            <?php the_content(); ?>
            <?php if (!post_password_required()) : ?>
                <?php $chapters = author_theme_book_chapters(get_the_ID()); ?>
                <?php if ($chapters) : ?>
                    <p><a href="<?php echo esc_url(get_permalink($chapters[0])); ?>"><?php esc_html_e('Start Reading', 'author-theme'); ?></a></p>
                    <h2><?php esc_html_e('Chapters', 'author-theme'); ?></h2>
                    <ol>
                        <?php foreach ($chapters as $chapter) : ?>
                            <li><a href="<?php echo esc_url(get_permalink($chapter)); ?>"><?php echo esc_html(get_the_title($chapter)); ?></a></li>
                        <?php endforeach; ?>
                    </ol>
                <?php endif; ?>
            <?php endif; ?>
        </article>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>
