<?php

if (!defined('ABSPATH')) {
    exit;
}

// Fetch Server Data from BattleMetrics API
function rss_fetch_server_data() {
    $servers = get_option('rust_server_list', []);
    $server_data = [];

    foreach ($servers as $server) {
        $server_id = esc_attr($server['id']);
        $response = wp_remote_get("https://api.battlemetrics.com/servers/{$server_id}");

        if (!is_wp_error($response)) {
            $data = json_decode(wp_remote_retrieve_body($response), true);
            $server_data[$server_id] = [
                'name' => $server['name'],
                'ip' => $server['ip'],
                'players' => $data['data']['attributes']['players'] ?? 'N/A',
                'maxPlayers' => $data['data']['attributes']['maxPlayers'] ?? 'N/A',
                'queue' => $data['data']['attributes']['details']['rust_queued_players'] ?? 0,
                'status' => $data['data']['attributes']['status'] === 'online' ? '🟢 Online' : '🔴 Offline'
            ];
        }
    }

    update_option('rust_server_cache', $server_data);
}
