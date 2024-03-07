<?php
require_once('plugin-activation/activate-plugins.php');
if (is_plugin_active('wp-graphql/wp-graphql.php')) {
    require_once('graphql.php');
}
/**
 * Headless functions and definitions
 */

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

// add featured images to posts
add_theme_support('post-thumbnails');


// add custom categories at theme activation
add_action('after_setup_theme', 'custom_add_cat');

function custom_add_cat()
{

    wp_insert_term(
        'Code',
        'category',
        array('slug' => 'code')
    );

    wp_insert_term(
        'Tech',
        'category',
        array('slug' => 'tech')
    );

    wp_insert_term(
        'Gaming',
        'category',
        array('slug' => 'gaming')
    );

}

/*
 * Adds a Featured Post meta box to the post editing screen
 */
function blog_featured_meta()
{
    add_meta_box('blog_meta', __('Featured Post', 'blog-textdomain'), 'blog_meta_callback', 'post', 'side', 'high');
}
add_action('add_meta_boxes', 'blog_featured_meta');

/**
 * Outputs the content of the meta box
 */

function blog_meta_callback($post)
{
    wp_nonce_field(basename(__FILE__), 'blog_nonce');
    $blog_stored_meta = get_post_meta($post->ID);
    ?>

<p>
  <span class="blog-row-title">
    <?php _e('Check if this is a featured post: <br/> <span style="color: red;">Only one post can be featured at a time.</span>', 'blog-textdomain') ?>
  </span>
<div class="blog-row-content">
  <label for="featured-checkbox">
    <input type="checkbox" name="featured-checkbox" id="featured-checkbox" value="yes" <?php if (isset($blog_stored_meta['featured-checkbox']))
                checked($blog_stored_meta['featured-checkbox'][0], 'yes'); ?> />
    <?php _e('Featured Item', 'blog-textdomain') ?>
  </label>

</div>
</p>

<?php
}

/**
 * Saves the custom meta input
 */
function blog_meta_save($post_id)
{

    // Checks save status - overcome autosave, etc.
    $is_autosave = wp_is_post_autosave($post_id);
    $is_revision = wp_is_post_revision($post_id);
    $is_valid_nonce = (isset($_POST['blog_nonce']) && wp_verify_nonce($_POST['blog_nonce'], basename(__FILE__))) ? 'true' : 'false';

    // Exits script depending on save status
    if ($is_autosave || $is_revision || !$is_valid_nonce) {
        return;
    }

    // Checks for input and saves - save checked as yes and unchecked at no
    if (isset($_POST['featured-checkbox'])) {
        // unchecks all featured psots before saving the new one
        $args = array(
            'post_type' => 'post',
            'meta_query' => array(
                array(
                    'key' => 'featured-checkbox',
                    'value' => 'yes',
                ),
            ),
        );
        $query = new WP_Query($args);
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                update_post_meta(get_the_ID(), 'featured-checkbox', 'no');
            }
        }
        update_post_meta($post_id, 'featured-checkbox', 'yes');
    } else {
        update_post_meta($post_id, 'featured-checkbox', 'no');
    }

}
add_action('save_post', 'blog_meta_save');