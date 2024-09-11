<?php
/*
Plugin Name: Share Hit
Description: Displays a share popup when any element with the class `share-blog-hit` is clicked.
Version: 1.0
Author: Hitesh Lendi
*/

// Prevent direct access to this file
if (!defined('ABSPATH')) {
    exit;
}

// Enqueue the necessary styles and scripts
function dynamic_share_popup_enqueue_scripts() {
    wp_enqueue_style('dynamic-share-popup-style', plugins_url('style.css', __FILE__));
    wp_enqueue_script('dynamic-share-popup-script', plugins_url('script.js', __FILE__), array('jquery'), null, true);

    // Localize script to pass AJAX URL and nonce to JavaScript
    wp_localize_script('dynamic-share-popup-script', 'dynamicSharePopup', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('dynamic_share_popup_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'dynamic_share_popup_enqueue_scripts');

// Add the share popup HTML to the footer
function dynamic_share_popup_add_html() {
    if (is_single() || is_page()) { // Ensure it is used on single posts/pages
        echo '
        <div class="share-popup">
            <div class="share-popup-content">
                <span class="share-popup-close">&times;</span>
                <div class="share-popup-buttons">
                    <a href="#" class="share-button share-facebook" data-share-url="">Share on Facebook</a>
                    <a href="#" class="share-button share-twitter" data-share-url="">Share on Twitter</a>
                    <a href="#" class="share-button share-email" data-share-url="">Share via Email</a>
                </div>
            </div>
        </div>';
    }
}
add_action('wp_footer', 'dynamic_share_popup_add_html');

// Handle AJAX requests
function handle_share() {
    // Verify nonce
    check_ajax_referer('dynamic_share_popup_nonce', 'nonce');
    
    // Sanitize input
    $url = isset($_POST['url']) ? esc_url_raw($_POST['url']) : '';

    // Example: Handle the sharing logic (e.g., logging, tracking, etc.)
    if ($url) {
        wp_send_json_success('Share URL received: ' . $url);
    } else {
        wp_send_json_error('Invalid URL');
    }
}
add_action('wp_ajax_handle_share', 'handle_share');
add_action('wp_ajax_nopriv_handle_share', 'handle_share');
