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
    <a class="site-name" href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
    <nav aria-label="<?php esc_attr_e('Publications', 'author-theme'); ?>">
        <a href="<?php echo esc_url(get_post_type_archive_link('book')); ?>"><?php esc_html_e('Books', 'author-theme'); ?></a>
    </nav>
</header>
