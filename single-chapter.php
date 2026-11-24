<?php get_header(); ?>
<main>
    <?php while (have_posts()) : the_post(); ?>
        <?php $chapter_id = get_the_ID(); ?>
        <?php $book = author_theme_chapter_book($chapter_id); ?>
        <article class="reader">
            <?php if ($book) : ?>
                <p><a href="<?php echo esc_url(get_permalink($book)); ?>"><?php echo esc_html(get_the_title($book)); ?></a></p>
            <?php endif; ?>
            <h1><?php the_title(); ?></h1>
            <?php the_content(); ?>
            <?php if ($book && !post_password_required()) : ?>
                <?php
                $chapters = author_theme_book_chapters($book->ID);
                $position = array_search($chapter_id, wp_list_pluck($chapters, 'ID'), true);
                $previous = false === $position ? null : ($chapters[$position - 1] ?? null);
                $next = false === $position ? null : ($chapters[$position + 1] ?? null);
                ?>
                <?php if ($previous || $next) : ?>
                    <nav class="chapter-nav" aria-label="<?php esc_attr_e('Chapter navigation', 'author-theme'); ?>">
                        <?php if ($previous) : ?>
                            <a rel="prev" href="<?php echo esc_url(get_permalink($previous)); ?>"><?php esc_html_e('Previous Chapter', 'author-theme'); ?></a>
                        <?php endif; ?>
                        <?php if ($next) : ?>
                            <a rel="next" href="<?php echo esc_url(get_permalink($next)); ?>"><?php esc_html_e('Next Chapter', 'author-theme'); ?></a>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </article>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>
