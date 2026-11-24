<?php
add_action('customize_register', function ($customizer) {
    $section = 'author_theme_about';
    $customizer->add_section($section, ['title' => __('Author About Page', 'author-theme')]);
    foreach ([
        'banner' => __('Banner artwork', 'author-theme'),
        'postcard' => __('Image behind the portrait', 'author-theme'),
        'story_image' => __('Story section artwork', 'author-theme'),
    ] as $key => $label) {
        $setting = 'author_about_' . $key;
        $customizer->add_setting($setting, ['sanitize_callback' => 'absint']);
        $customizer->add_control(new WP_Customize_Media_Control($customizer, $setting, [
            'label' => $label, 'section' => $section, 'mime_type' => 'image',
        ]));
    }
    foreach ([
        'interests' => [__('Writing & Interests', 'author-theme'), __('Enter one interest per line. Add an optional description after a vertical bar, e.g. Books | Contemporary fiction.', 'author-theme')],
        'story' => [__('The Story Behind the Name', 'author-theme'), ''],
    ] as $key => [$label, $description]) {
        $setting = 'author_about_' . $key;
        $customizer->add_setting($setting, ['default' => '', 'sanitize_callback' => 'sanitize_textarea_field']);
        $customizer->add_control($setting, [
            'label' => $label, 'description' => $description, 'section' => $section, 'type' => 'textarea',
        ]);
    }
});
