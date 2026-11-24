<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="site-header">
    <div class="site-header-inner">
        <div class="site-brand">
            <?php if (has_custom_logo()) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <a class="site-name" href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
            <?php endif; ?>
        </div>
        <nav class="site-navigation" id="primary-navigation" aria-label="<?php esc_attr_e('Primary navigation', 'author-theme'); ?>">
            <?php if (has_nav_menu('primary')) : ?>
                <?php wp_nav_menu(['theme_location' => 'primary', 'container' => false, 'menu_class' => 'site-menu']); ?>
            <?php else : ?>
                <ul class="site-menu">
                    <li><a href="<?php echo esc_url(home_url('/')); ?>"<?php if (is_front_page()) : ?> aria-current="page"<?php endif; ?>><?php esc_html_e('Home', 'author-theme'); ?></a></li>
                    <li><a href="<?php echo esc_url(get_post_type_archive_link('book')); ?>"<?php if (is_post_type_archive('book')) : ?> aria-current="page"<?php endif; ?>><?php esc_html_e('Books', 'author-theme'); ?></a></li>
                    <?php wp_list_pages(['title_li' => '', 'depth' => 1, 'exclude' => (string) (int) get_option('page_on_front')]); ?>
                </ul>
            <?php endif; ?>
        </nav>
        <div class="site-header-tools">
            <details class="site-search">
                <summary aria-label="<?php esc_attr_e('Search', 'author-theme'); ?>"><span class="site-search-icon" aria-hidden="true"></span></summary>
                <div class="site-search-panel"><?php get_search_form(); ?></div>
            </details>
            <button class="site-menu-toggle" type="button" aria-controls="primary-navigation" aria-expanded="false" hidden>
                <span class="site-menu-icon" aria-hidden="true"></span>
                <span class="site-visually-hidden"><?php esc_html_e('Menu', 'author-theme'); ?></span>
            </button>
        </div>
    </div>
</header>
