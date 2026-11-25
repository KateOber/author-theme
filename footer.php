<?php $footer_social_links = empty($args['social_links_shown']) ? author_theme_social_links() : []; ?>
<footer class="site-footer">
    <?php if ($footer_social_links) : ?>
        <nav aria-label="<?php esc_attr_e('Social profiles', 'author-theme'); ?>">
            <?php author_theme_render_social_links($footer_social_links); ?>
        </nav>
    <?php endif; ?>
    <a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
</footer>
<?php wp_footer(); ?>
</body>
</html>
