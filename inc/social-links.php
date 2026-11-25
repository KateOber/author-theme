<?php
function author_theme_social_links() {
    $links = [];
    foreach (['Wattpad', 'Instagram', 'Goodreads', 'Amazon'] as $network) {
        $url = esc_url(get_theme_mod('author_home_' . strtolower($network), ''));
        if ($url) {
            $links[$network] = $url;
        }
    }
    return $links;
}

function author_theme_render_social_links($links) {
    $icons = [
        'Wattpad' => '<path d="M3.7 15.6 5.7 9c.6-2 1.5-3.2 3-3.2 1.4 0 2 1.1 1.8 2.7l-.5 3.9 2.8-5c.6-1 1.6-1.7 2.6-1.7 1.3 0 2 1.2 1.6 2.5l-.9 3.1 3.1-4.1c.7-.9 1.6-1.7 2.8-1.3 1 .4 1 1.4.4 2.3l-5.6 8.2c-1 1.5-2 2.2-3.4 2.2-1.4 0-2.1-.9-2-2.3l.3-2.5-1.7 3c-.8 1.4-1.8 2-3 2C4.5 18.8 3.1 17.8 3.7 15.6Z" fill="currentColor"/>',
        'Instagram' => '<rect x="3" y="3" width="18" height="18" rx="5" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="17.4" cy="6.7" r="1.2" fill="currentColor"/>',
        'Goodreads' => '<path d="M17.5 4.2v14c0 2.4-1.9 3.8-5 3.8-2.2 0-4-.8-4.8-2.4m9.8-9.1c0 3.3-1.8 5.5-4.8 5.5S8 13.8 8 10.5 9.8 5 12.7 5s4.8 2.2 4.8 5.5Z" fill="none" stroke="currentColor" stroke-width="1.55" stroke-linecap="round"/>',
        'Amazon' => '<path d="M16 15V8.6c0-2.7-1.6-4.2-4.5-4.2-2.2 0-3.7.8-4.6 2.3m9.1 3c-5.9-.9-8.7.5-8.7 3 0 1.7 1.2 2.8 3 2.8 2.3 0 4.2-1.4 5.7-3m0 1 .9 1.7" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round"/><path d="M3.6 18.2c5.3 3.3 10.7 3.5 16.2.6m-2.6-.8 3-.1-.7 2.5" fill="none" stroke="#a75c24" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>',
    ];
    ?>
    <ul class="author-social-links">
        <?php foreach ($links as $label => $url) : ?>
            <li>
                <a class="author-social-<?php echo esc_attr(strtolower($label)); ?>" href="<?php echo esc_url($url); ?>">
                    <svg class="author-social-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><?php echo $icons[$label]; ?></svg>
                    <span><?php echo esc_html($label); ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php
}
