<?php
/*
Plugin Name: Rust Server Status
Description: Displays Rust server status using BattleMetrics API with caching.
Version: 1.5
Author: RocketMyrr
*/

if (!defined('ABSPATH')) {
    exit;
}

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/api-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/cron-jobs.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-page.php';
require_once plugin_dir_path(__FILE__) . 'ajax-handler.php';

// Initialize Plugin
class RustServerStatus {
    public function __construct() {
        add_action('admin_menu', [$this, 'create_admin_menu']);
        add_shortcode('rust_server_status', [$this, 'display_server_status']);
        add_action('wp_ajax_rss_manual_refresh', 'rss_manual_refresh');
    }

    public function create_admin_menu() {
        add_menu_page('Rust Server Status', 'Rust Servers', 'manage_options', 'rust-server-status', 'rss_admin_page');
    }

    function rss_manual_refresh() {
        rss_fetch_server_data(); // Manually fetch and update data
        echo "Server data refreshed!";
        wp_die();
    }
    
    public function display_server_status($atts) {
        if (function_exists('rss_fetch_server_data')) {
            rss_fetch_server_data(); // Fetch updated data
            $server_data = get_option('rust_server_cache', []);
    
            $server_id = $atts['id'] ?? '';
            if (isset($server_data[$server_id])) {
                $server = $server_data[$server_id];
                $queue_text = ($server['queue'] > 0) ? " | Queue: {$server['queue']}" : "";
                return "{$server['name']} | Players: {$server['players']}/{$server['maxPlayers']}{$queue_text} <br> Status: {$server['status']}";
            } else {
                return "Error: Server not found.";
            }
        } else {
            return "Error: rss_fetch_server_data() function is missing.";
        }
    }
}

new RustServerStatus();
