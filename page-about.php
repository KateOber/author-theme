<?php
/**
 * Template Name: Author About
 */
get_header();
while (have_posts()) :
    the_post();
    $locked = post_password_required();
    $banner = absint(get_theme_mod('author_about_banner'));
    $postcard = absint(get_theme_mod('author_about_postcard'));
    $story_image = absint(get_theme_mod('author_about_story_image'));
    $story = get_theme_mod('author_about_story', '');
    $interests = array_filter(array_map('trim', preg_split('/\R/u', get_theme_mod('author_about_interests', '')) ?: []));
    $social = author_theme_social_links();
    ?>
    <main class="author-about">
        <header class="author-about-banner">
            <?php if (!$locked && $banner) : ?>
                <?php echo wp_get_attachment_image($banner, 'full', false, ['alt' => '', 'fetchpriority' => 'high']); ?>
            <?php endif; ?>
            <h1><?php the_title(); ?></h1>
        </header>
        <div class="author-about-inner">
            <section class="author-about-intro<?php echo !$locked && has_post_thumbnail() ? ' has-portrait' : ''; ?>">
                <?php if (!$locked && has_post_thumbnail()) : ?>
                    <div class="author-about-portrait">
                        <?php if ($postcard) : ?>
                            <?php echo wp_get_attachment_image($postcard, 'medium_large', false, ['class' => 'author-about-postcard', 'alt' => '']); ?>
                        <?php endif; ?>
                        <?php the_post_thumbnail('large', ['class' => 'author-about-photo']); ?>
                    </div>
                <?php endif; ?>
                <div class="author-about-bio"><?php the_content(); ?></div>
            </section>

            <?php if (!$locked && $interests) : ?>
                <section aria-labelledby="author-interests">
                    <h2 class="author-about-rule" id="author-interests"><?php esc_html_e('Writing & Interests', 'author-theme'); ?></h2>
                    <ul class="author-about-interests">
                        <?php foreach ($interests as $interest) : ?>
                            <?php [$name, $detail] = array_pad(explode('|', $interest, 2), 2, ''); ?>
                            <li><strong><?php echo esc_html(trim($name)); ?></strong><?php if (trim($detail)) : ?><span><?php echo esc_html(trim($detail)); ?></span><?php endif; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endif; ?>

            <?php if (!$locked && trim($story)) : ?>
                <section class="author-about-story<?php echo $story_image ? ' has-story-image' : ''; ?>">
                    <div>
                        <h2><?php esc_html_e('The Story Behind the Name', 'author-theme'); ?></h2>
                        <?php echo wpautop(esc_html($story)); ?>
                    </div>
                    <?php if ($story_image) : ?>
                        <?php echo wp_get_attachment_image($story_image, 'large'); ?>
                    <?php endif; ?>
                </section>
            <?php endif; ?>

            <?php if (!$locked && $social) : ?>
                <nav class="author-about-social" aria-labelledby="author-connect">
                    <h2 class="author-about-rule" id="author-connect"><?php esc_html_e('Let’s Connect', 'author-theme'); ?></h2>
                    <?php author_theme_render_social_links($social); ?>
                </nav>
            <?php endif; ?>
        </div>
    </main>
<?php endwhile; ?>
<?php get_footer(null, ['social_links_shown' => !empty($social) && !$locked]); ?>
