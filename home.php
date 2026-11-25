<?php
get_header();
$blog_id = (int) get_option('page_for_posts');
$category = is_category() ? get_queried_object() : null;
$banner = ($blog_id ? get_post_thumbnail_id($blog_id) : 0) ?: absint(get_theme_mod('author_home_hero_image'));
$featured_id = !$category && !is_paged() ? absint(get_query_var('author_theme_featured_post')) : 0;
$featured = $featured_id ? get_post($featured_id) : null;
$about = author_theme_home_page('about');
$categories = get_categories(['hide_empty' => true]);
$popular = get_posts([
    'post_status' => 'publish',
    'has_password' => false,
    'numberposts' => 3,
    'orderby' => ['comment_count' => 'DESC', 'date' => 'DESC'],
]);
$popular = array_filter($popular, static function ($post) {
    return (int) $post->comment_count > 0;
});
?>
<main class="books-archive blog-journal">
    <header class="books-hero journal-hero">
        <?php echo wp_get_attachment_image($banner, 'full', false, ['alt' => '', 'fetchpriority' => 'high']); ?>
        <div>
            <h1><?php echo esc_html($category ? $category->name : ($blog_id ? get_the_title($blog_id) : __('Blog', 'author-theme'))); ?></h1>
            <?php if ($category && $category->description) : ?>
                <p><?php echo esc_html(wp_strip_all_tags($category->description)); ?></p>
            <?php elseif (!$category) : ?>
                <p><?php esc_html_e('Writing notes, behind the scenes, travel, art, and other thoughts from my creative life.', 'author-theme'); ?></p>
            <?php endif; ?>
        </div>
    </header>

    <div class="books-inner journal-layout">
        <div>
            <?php if ($featured) : ?>
                <?php $featured_terms = get_the_category($featured->ID); ?>
                <article class="journal-feature<?php echo has_post_thumbnail($featured) ? ' has-image' : ''; ?>">
                    <?php echo get_the_post_thumbnail($featured, 'large', ['class' => 'journal-feature-image']); ?>
                    <div>
                        <?php if ($featured_terms) : ?><span class="journal-label"><?php echo esc_html($featured_terms[0]->name); ?></span><?php endif; ?>
                        <h2><a href="<?php echo esc_url(get_permalink($featured)); ?>"><?php echo esc_html(get_the_title($featured)); ?></a></h2>
                        <p><?php echo esc_html(author_theme_home_summary($featured, 42)); ?></p>
                        <a class="books-button books-button-primary" href="<?php echo esc_url(get_permalink($featured)); ?>"><?php esc_html_e('Read the Post', 'author-theme'); ?> →</a>
                    </div>
                </article>
            <?php endif; ?>

            <section aria-labelledby="journal-recent-title">
                <h2 class="books-rule" id="journal-recent-title"><?php echo esc_html($category ? __('Posts', 'author-theme') : __('Recent Posts', 'author-theme')); ?></h2>
                <?php if (have_posts()) : ?>
                    <div class="journal-grid">
                        <?php while (have_posts()) : the_post(); ?>
                            <?php $terms = get_the_category(); ?>
                            <article <?php post_class('journal-post'); ?>>
                                <?php if (!post_password_required()) : ?>
                                    <?php the_post_thumbnail('medium_large', ['class' => 'journal-post-image']); ?>
                                <?php endif; ?>
                                <?php if ($terms) : ?><a class="journal-label" href="<?php echo esc_url(get_category_link($terms[0])); ?>"><?php echo esc_html($terms[0]->name); ?></a><?php endif; ?>
                                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                                <?php if (!post_password_required()) : ?><p><?php echo esc_html(author_theme_home_summary(get_post(), 23)); ?></p><?php endif; ?>
                            </article>
                        <?php endwhile; ?>
                    </div>
                    <?php the_posts_pagination(); ?>
                <?php else : ?>
                    <p><?php esc_html_e('There are no posts here yet.', 'author-theme'); ?></p>
                <?php endif; ?>
            </section>
        </div>

        <aside class="journal-sidebar" aria-label="<?php esc_attr_e('About the author and blog', 'author-theme'); ?>">
            <?php if ($about) : ?>
                <section class="journal-note journal-author">
                    <?php echo get_the_post_thumbnail($about, 'thumbnail', ['class' => 'journal-author-image']); ?>
                    <div>
                        <h2><?php printf(esc_html__('Hi, I’m %s.', 'author-theme'), esc_html(get_bloginfo('name'))); ?></h2>
                        <p><?php echo esc_html(author_theme_home_summary($about, 26)); ?></p>
                        <a href="<?php echo esc_url(get_permalink($about)); ?>"><?php esc_html_e('More About Me', 'author-theme'); ?> →</a>
                    </div>
                </section>
            <?php endif; ?>
            <?php if ($categories) : ?>
                <section class="journal-note">
                    <h2><?php esc_html_e('Categories', 'author-theme'); ?></h2>
                    <ul class="journal-categories">
                        <?php foreach ($categories as $term) : ?>
                            <li><a href="<?php echo esc_url(get_category_link($term)); ?>"<?php if ($category && $category->term_id === $term->term_id) : ?> aria-current="page"<?php endif; ?>><?php echo esc_html($term->name); ?></a><span><?php echo esc_html(number_format_i18n($term->count)); ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endif; ?>
            <section class="journal-note">
                <h2><?php esc_html_e('Get New Posts', 'author-theme'); ?></h2>
                <p><?php esc_html_e('Follow new posts in your feed reader.', 'author-theme'); ?></p>
                <a class="books-button books-button-primary" href="<?php echo esc_url(get_feed_link()); ?>"><?php esc_html_e('Subscribe via RSS', 'author-theme'); ?></a>
            </section>
            <?php if ($popular) : ?>
                <section class="journal-note">
                    <h2><?php esc_html_e('Popular Posts', 'author-theme'); ?></h2>
                    <ul class="journal-popular">
                        <?php foreach ($popular as $entry) : ?>
                            <li><a href="<?php echo esc_url(get_permalink($entry)); ?>"><?php echo get_the_post_thumbnail($entry, 'thumbnail', ['alt' => '']); ?><?php echo esc_html(get_the_title($entry)); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endif; ?>
        </aside>
    </div>
</main>
<?php get_footer(); ?>
