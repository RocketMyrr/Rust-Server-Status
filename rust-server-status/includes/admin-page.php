<?php

if (!defined('ABSPATH')) {
    exit;
}

// Admin Page for Managing Servers
function rss_admin_page() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rust_servers'])) {
        $servers = array_map(function ($server) {
            return [
                'id' => sanitize_text_field($server['id']),
                'name' => sanitize_text_field($server['name']),
                'ip' => sanitize_text_field($server['ip'])
            ];
        }, $_POST['rust_servers']);

        update_option('rust_server_list', $servers);
        rss_fetch_server_data(); // Update immediately after saving
    }
    add_action('admin_enqueue_scripts', function() {
        wp_enqueue_script('rss-admin-script', plugin_dir_url(__FILE__) . 'admin.js', ['jquery'], null, true);
        wp_localize_script('rss-admin-script', 'rss_ajax', [
            'ajax_url' => admin_url('admin-ajax.php')
        ]);
    });
    $servers = get_option('rust_server_list', []);
    ?>
    <div class="wrap">
        <h1>Rust Server Status</h1>
        <form method="post">
            <table class="widefat">
                <thead>
                    <tr>
                        <th>Server Name</th>
                        <th>BattleMetrics Server ID</th>
                        <th>Server IP</th>
                        <th>Shortcode</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="server-list">
                    <?php foreach ($servers as $index => $server) : 
                        $server_id = esc_attr($server['id']);
                        $shortcode = "[rust_server_status id=\"$server_id\"]"; ?>
                        <tr>
                            <td><input type="text" name="rust_servers[<?php echo $index; ?>][name]" value="<?php echo esc_attr($server['name']); ?>" /></td>
                            <td><input type="text" name="rust_servers[<?php echo $index; ?>][id]" value="<?php echo esc_attr($server['id']); ?>" /></td>
                            <td><input type="text" name="rust_servers[<?php echo $index; ?>][ip]" value="<?php echo esc_attr($server['ip']); ?>" /></td>
                            <td><input type="text" style='width: 250px' value="<?php echo esc_attr($shortcode); ?>" readonly onclick="this.select();"></td>
                            <td><button type="button" class="button remove-server">Remove</button></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="button" class="button" id="add-server">Add Server</button>
            <br><br>
            <button type="submit" class="button button-primary">Save</button><br><br>
            <button id="rss-refresh-btn" class="button button-primary">Manual Refresh</button>
<div id="rss-refresh-status"></div>
        </form>
    </div>
    <script>
        document.getElementById('add-server').addEventListener('click', function() {
            let table = document.getElementById('server-list');
            let index = table.children.length;
            let row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="text" name="rust_servers[${index}][name]" value="" /></td>
                <td><input type="text" name="rust_servers[${index}][id]" value="" /></td>
                <td><input type="text" name="rust_servers[${index}][ip]" value="" /></td>
                <td><button type="button" class="button remove-server">Remove</button></td>
            `;
            table.appendChild(row);
        });
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-server')) {
                event.target.closest('tr').remove();
            }
        });
    </script>
    <?php
}
