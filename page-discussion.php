<?php
/**
 * Template Name: Discussion Board
 */
get_header();
while (have_posts()) :
    the_post();
    $locked = post_password_required();
    $enabled = !$locked && function_exists('bbp_get_forum_post_type') && shortcode_exists('bbp-topic-form');
    $banner = $locked ? 0 : (get_post_thumbnail_id() ?: absint(get_theme_mod('author_home_hero_image')));
    $forums = [];
    $topics = [];
    $active = [];
    $sorts = ['recent' => __('Recent', 'author-theme'), 'popular' => __('Popular', 'author-theme'), 'unanswered' => __('Unanswered', 'author-theme')];
    $sort = isset($_GET['discussion_sort']) && is_string($_GET['discussion_sort']) ? sanitize_key(wp_unslash($_GET['discussion_sort'])) : '';
    $sort = isset($sorts[$sort]) ? $sort : 'recent';
    $page_url = get_permalink();
    if ($enabled) {
        $forum_posts = get_posts([
            'post_type' => bbp_get_forum_post_type(), 'post_status' => 'publish',
            'has_password' => false, 'numberposts' => -1, 'orderby' => ['menu_order' => 'ASC', 'title' => 'ASC'],
        ]);
        foreach ($forum_posts as $forum) {
            if (bbp_is_forum_public($forum->ID, true)) {
                $forums[$forum->ID] = $forum;
            }
        }
        if ($forums) {
            $query = [
                'post_type' => bbp_get_topic_post_type(), 'post_status' => 'publish',
                'has_password' => false, 'post_parent__in' => array_keys($forums),
                'numberposts' => 12,
            ];
            $ordering = $sort === 'popular' ? '_bbp_reply_count' : '_bbp_last_active_time';
            $query['meta_key'] = $ordering;
            $query['orderby'] = $sort === 'popular' ? 'meta_value_num' : 'meta_value';
            if ($sort === 'unanswered') {
                $query['meta_query'] = [['key' => '_bbp_reply_count', 'value' => 0, 'compare' => '=', 'type' => 'NUMERIC']];
            }
            $topics = get_posts($query);
            unset($query['meta_query']);
            $query['meta_key'] = '_bbp_last_active_time';
            $query['orderby'] = 'meta_value';
            $query['numberposts'] = 3;
            $active = $sort === 'recent' ? array_slice($topics, 0, 3) : get_posts($query);
        }
    }
    $feed = $enabled && function_exists('bbp_get_topics_rss_url') ? bbp_get_topics_rss_url() : '';
    ?>
    <main class="books-archive discussion-page">
        <header class="books-hero discussion-hero">
            <?php if ($banner) : ?>
                <?php echo wp_get_attachment_image($banner, 'full', false, ['alt' => '', 'fetchpriority' => 'high']); ?>
            <?php endif; ?>
            <div>
                <h1><?php the_title(); ?></h1>
                <?php if (!$locked) : ?><p><?php esc_html_e('Talk about the stories, share theories, and ask questions.', 'author-theme'); ?></p><?php endif; ?>
            </div>
        </header>
        <div class="books-inner discussion-inner">
            <?php if ($enabled && $forums) : ?>
                <div class="books-actions discussion-actions">
                    <a class="books-button books-button-primary" href="#new-topic"><?php esc_html_e('Start a New Topic', 'author-theme'); ?></a>
                    <?php if ($feed) : ?><a class="books-button" href="<?php echo esc_url($feed); ?>"><?php esc_html_e('Follow Discussions (RSS)', 'author-theme'); ?></a><?php endif; ?>
                </div>
            <?php endif; ?>
            <?php if ($locked || trim(get_the_content())) : ?>
                <div class="discussion-page-content"><?php the_content(); ?></div>
            <?php endif; ?>
            <?php if (!$locked) : ?>
                <div class="discussion-layout">
                    <div>
                        <section aria-labelledby="discussion-boards">
                            <h2 class="books-rule" id="discussion-boards"><?php esc_html_e('Discussion Boards', 'author-theme'); ?></h2>
                            <?php if ($forums) : ?>
                                <ul class="discussion-boards">
                                    <?php foreach ($forums as $forum) : ?>
                                        <li>
                                            <a href="<?php echo esc_url(get_permalink($forum)); ?>">
                                                <?php echo get_the_post_thumbnail($forum, 'medium', ['alt' => '']); ?>
                                                <strong><?php echo esc_html(get_the_title($forum)); ?></strong>
                                                <?php $description = author_theme_home_summary($forum, 16); ?>
                                                <?php if ($description) : ?><span><?php echo esc_html($description); ?></span><?php endif; ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else : ?>
                                <p><?php esc_html_e('There are no discussion boards yet.', 'author-theme'); ?></p>
                            <?php endif; ?>
                        </section>

                        <?php if ($forums) : ?>
                            <section class="discussion-topics" aria-labelledby="discussion-topics-title">
                                <h2 class="books-rule" id="discussion-topics-title"><?php esc_html_e('Discussions', 'author-theme'); ?></h2>
                                <nav class="books-filters discussion-filters" aria-label="<?php esc_attr_e('Sort discussions', 'author-theme'); ?>">
                                    <?php foreach ($sorts as $key => $label) : ?>
                                        <a href="<?php echo esc_url(add_query_arg('discussion_sort', $key, $page_url) . '#discussion-topics-title'); ?>"<?php if ($sort === $key) : ?> aria-current="page"<?php endif; ?>><?php echo esc_html($label); ?></a>
                                    <?php endforeach; ?>
                                </nav>
                                <?php if (!$topics) : ?><p><?php esc_html_e('There are no discussions in this view yet.', 'author-theme'); ?></p><?php endif; ?>
                                <ul class="discussion-topic-list">
                                    <?php foreach ($topics as $topic) : ?>
                                        <?php $count = (int) bbp_get_topic_reply_count($topic->ID, true); ?>
                                        <li>
                                            <div>
                                                <a href="<?php echo esc_url(get_permalink($topic)); ?>"><?php echo esc_html(get_the_title($topic)); ?></a>
                                                <span class="discussion-topic-board"><?php echo esc_html(get_the_title($forums[$topic->post_parent])); ?></span>
                                            </div>
                                            <div class="discussion-topic-meta">
                                                <span><?php printf(esc_html(_n('%s reply', '%s replies', $count, 'author-theme')), esc_html(number_format_i18n($count))); ?></span>
                                                <span><?php echo esc_html(bbp_get_topic_last_active_time($topic->ID) ?: get_the_date('', $topic)); ?></span>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </section>
                        <?php endif; ?>
                    </div>

                    <aside class="discussion-sidebar">
                        <?php if ($enabled && is_user_logged_in()) : ?>
                            <?php $member = wp_get_current_user(); ?>
                            <section class="discussion-note discussion-member">
                                <?php echo get_avatar($member->ID, 56); ?>
                                <div>
                                    <h2><?php printf(esc_html__('Hi, %s!', 'author-theme'), esc_html($member->display_name)); ?></h2>
                                    <a href="<?php echo esc_url(bbp_get_user_profile_url($member->ID)); ?>"><?php esc_html_e('My Profile', 'author-theme'); ?></a>
                                </div>
                            </section>
                        <?php endif; ?>
                        <section class="discussion-note">
                            <h2><?php esc_html_e('Community Guidelines', 'author-theme'); ?></h2>
                            <ul>
                                <li><?php esc_html_e('Be respectful and kind.', 'author-theme'); ?></li>
                                <li><?php esc_html_e('Label spoilers clearly.', 'author-theme'); ?></li>
                                <li><?php esc_html_e('Stay on topic.', 'author-theme'); ?></li>
                                <li><?php esc_html_e('Keep hate speech and personal attacks out of the discussion.', 'author-theme'); ?></li>
                            </ul>
                        </section>
                        <?php if ($active) : ?>
                            <section class="discussion-note">
                                <h2><?php esc_html_e('Active Discussions', 'author-theme'); ?></h2>
                                <ul>
                                    <?php foreach (array_slice($active, 0, 3) as $topic) : ?>
                                        <li><a href="<?php echo esc_url(get_permalink($topic)); ?>"><?php echo esc_html(get_the_title($topic)); ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </section>
                        <?php endif; ?>
                    </aside>
                </div>
                <?php if ($enabled && $forums) : ?>
                    <section class="discussion-note discussion-compose" id="new-topic">
                        <h2><?php esc_html_e('Start a New Topic', 'author-theme'); ?></h2>
                        <?php if (is_user_logged_in() || bbp_allow_anonymous()) : ?>
                            <?php echo do_shortcode('[bbp-topic-form]'); ?>
                        <?php else : ?>
                            <a href="<?php echo esc_url(wp_login_url($page_url . '#new-topic')); ?>"><?php esc_html_e('Log in to start a discussion', 'author-theme'); ?></a>
                        <?php endif; ?>
                    </section>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>
    <?php
endwhile;
get_footer();
