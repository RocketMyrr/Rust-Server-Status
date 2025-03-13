<?php

if (!defined('ABSPATH')) {
    exit;
}

// Add Custom Cron Schedule
function rss_add_cron_interval($schedules) {
    $schedules['five_minutes'] = array(
        'interval' => 300,
        'display'  => esc_html__('Every 5 Minutes')
    );
    return $schedules;
}
add_filter('cron_schedules', 'rss_add_cron_interval');

// Schedule Cron Job if Not Already Scheduled
if (!wp_next_scheduled('rust_update_server_data')) {
    wp_schedule_event(time(), 'five_minutes', 'rust_update_server_data');
}

// Hook Function to Cron Job
add_action('rust_update_server_data', 'rss_fetch_server_data');
