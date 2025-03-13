<?php

if (!defined('ABSPATH')) {
    exit;
}

// Register AJAX Actions
add_action('wp_ajax_rust_update_server_data', 'rust_update_server_data');
add_action('wp_ajax_nopriv_rust_update_server_data', 'rust_update_server_data');

function rust_update_server_data() {
    rss_fetch_server_data(); // Calls the function from api-functions.php
    echo 'Updated!';
    wp_die();
}
