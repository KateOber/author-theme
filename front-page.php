<?php
if (is_paged()) {
    require get_theme_file_path('index.php');
    return;
}

get_header();
$featured = author_theme_home_featured_book();
$about = author_theme_home_page('about');
$hero_image = absint(get_theme_mod('author_home_hero_image', 0));
$about_image = absint(get_theme_mod('author_home_about_image', 0));
$site_name = get_bloginfo('name');
$tagline = get_bloginfo('description');
$links = [];
foreach ([
    'gallery' => __('Gallery', 'author-theme'),
    'discussion' => __('Discussion Board', 'author-theme'),
    'playlists' => __('Playlists', 'author-theme'),
] as $key => $label) {
    $page = author_theme_home_page($key);
    if ($page) {
        $links[$label] = get_permalink($page);
    }
}
$socials = [];
foreach (['Wattpad', 'Instagram', 'Goodreads', 'Amazon'] as $network) {
    $url = get_theme_mod('author_home_' . strtolower($network), '');
    if ($url) {
        $socials[$network] = $url;
    }
}
$updates = get_posts(['posts_per_page' => 3, 'has_password' => false]);
$blog_page = (int) get_option('page_for_posts');
?>
<main class="author-home">
    <section class="author-home-hero<?php echo $hero_image ? ' has-artwork' : ''; ?>" aria-labelledby="author-home-title">
        <?php if ($hero_image) : ?>
            <?php echo wp_get_attachment_image($hero_image, 'full', false, ['class' => 'author-home-hero-art', 'alt' => '', 'fetchpriority' => 'high']); ?>
        <?php endif; ?>
        <div class="author-home-inner author-home-intro">
            <h1 id="author-home-title"><?php echo esc_html($site_name); ?></h1>
            <?php if ($tagline && 'Just another WordPress site' !== $tagline) : ?>
                <p><?php echo esc_html($tagline); ?></p>
            <?php endif; ?>
        </div>
    </section>

    <div class="author-home-inner author-home-sections">
        <?php if ($featured) : ?>
            <?php
            $book_url = get_permalink($featured);
            $chapters = get_posts([
                'post_type' => 'chapter',
                'post_status' => 'publish',
                'has_password' => false,
                'posts_per_page' => 3,
                'meta_query' => [['key' => 'book_id', 'value' => $featured->ID]],
                'orderby' => ['date' => 'DESC', 'ID' => 'DESC'],
            ]);
            ?>
            <section class="author-home-feature<?php echo has_post_thumbnail($featured) ? ' has-cover' : ''; ?><?php echo $chapters ? ' has-chapters' : ''; ?>" aria-labelledby="author-home-book">
                <?php if (has_post_thumbnail($featured)) : ?>
                    <a class="author-home-cover" href="<?php echo esc_url($book_url); ?>" aria-label="<?php echo esc_attr(get_the_title($featured)); ?>">
                        <?php echo get_the_post_thumbnail($featured, 'medium_large', ['alt' => '']); ?>
                    </a>
                <?php endif; ?>
                <div class="author-home-book-copy">
                    <p class="author-home-kicker"><?php esc_html_e('Featured Novel', 'author-theme'); ?></p>
                    <h2 id="author-home-book"><?php echo esc_html(get_the_title($featured)); ?></h2>
                    <?php $summary = author_theme_home_summary($featured, 55); ?>
                    <?php if ($summary) : ?><p><?php echo esc_html($summary); ?></p><?php endif; ?>
                    <div class="author-home-actions">
                        <a class="author-home-button" href="<?php echo esc_url($book_url); ?>"><?php esc_html_e('Read the Novel', 'author-theme'); ?> →</a>
                        <?php if ($chapters) : ?><a class="author-home-button is-outline" href="<?php echo esc_url($book_url . '#chapters'); ?>"><?php esc_html_e('Chapter List', 'author-theme'); ?></a><?php endif; ?>
                    </div>
                </div>
                <?php if ($chapters) : ?>
                    <section class="author-home-recent" aria-labelledby="author-home-recent-title">
                        <h3 id="author-home-recent-title"><?php esc_html_e('Recent Chapters', 'author-theme'); ?></h3>
                        <ul>
                            <?php foreach ($chapters as $chapter) : ?>
                                <li>
                                    <a href="<?php echo esc_url(get_permalink($chapter)); ?>"><?php echo esc_html(get_the_title($chapter)); ?></a>
                                    <time datetime="<?php echo esc_attr(get_the_date('c', $chapter)); ?>"><?php echo esc_html(get_the_date('', $chapter)); ?></time>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="<?php echo esc_url($book_url . '#chapters'); ?>"><?php esc_html_e('All Chapters', 'author-theme'); ?> →</a>
                    </section>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <?php if ($links) : ?>
            <nav class="author-home-discover" aria-label="<?php esc_attr_e('Explore the site', 'author-theme'); ?>">
                <?php foreach ($links as $label => $url) : ?>
                    <a href="<?php echo esc_url($url); ?>"><?php echo esc_html($label); ?> <span aria-hidden="true">↗</span></a>
                <?php endforeach; ?>
            </nav>
        <?php endif; ?>

        <?php if ($about) : ?>
            <section class="author-home-about<?php echo $about_image || has_post_thumbnail($about) ? ' has-artwork' : ''; ?>" aria-labelledby="author-home-about-title">
                <?php if ($about_image) : ?>
                    <?php echo wp_get_attachment_image($about_image, 'large', false, ['class' => 'author-home-about-art']); ?>
                <?php elseif (has_post_thumbnail($about)) : ?>
                    <?php echo get_the_post_thumbnail($about, 'large', ['class' => 'author-home-about-art']); ?>
                <?php endif; ?>
                <div>
                    <p class="author-home-kicker"><?php esc_html_e('About the Author', 'author-theme'); ?></p>
                    <h2 id="author-home-about-title"><?php echo esc_html(get_the_title($about)); ?></h2>
                    <p><?php echo esc_html(author_theme_home_summary($about, 65)); ?></p>
                    <a href="<?php echo esc_url(get_permalink($about)); ?>"><?php esc_html_e('More About Me', 'author-theme'); ?> →</a>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($updates) : ?>
            <section class="author-home-updates" aria-labelledby="author-home-updates-title">
                <div class="author-home-section-heading">
                    <h2 id="author-home-updates-title"><?php esc_html_e('Blog / Updates', 'author-theme'); ?></h2>
                    <?php if ($blog_page && get_post_status($blog_page) === 'publish') : ?>
                        <a href="<?php echo esc_url(get_permalink($blog_page)); ?>"><?php esc_html_e('All Posts', 'author-theme'); ?> →</a>
                    <?php endif; ?>
                </div>
                <div class="author-home-posts">
                    <?php foreach ($updates as $update) : ?>
                        <article>
                            <?php echo get_the_post_thumbnail($update, 'medium_large', ['class' => 'author-home-post-image']); ?>
                            <time datetime="<?php echo esc_attr(get_the_date('c', $update)); ?>"><?php echo esc_html(get_the_date('', $update)); ?></time>
                            <h3><a href="<?php echo esc_url(get_permalink($update)); ?>"><?php echo esc_html(get_the_title($update)); ?></a></h3>
                            <p><?php echo esc_html(author_theme_home_summary($update, 24)); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if (is_page()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <?php if (post_password_required() || trim(get_the_content())) : ?>
                    <div class="author-home-page-content"><?php the_content(); ?></div>
                <?php endif; ?>
            <?php endwhile; ?>
        <?php endif; ?>

        <?php if ($socials) : ?>
            <nav class="author-home-social" aria-labelledby="author-home-social-title">
                <h2 id="author-home-social-title"><?php esc_html_e('Find Me Elsewhere', 'author-theme'); ?></h2>
                <ul>
                    <?php foreach ($socials as $label => $url) : ?>
                        <li><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($label); ?> <span aria-hidden="true">↗</span></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
