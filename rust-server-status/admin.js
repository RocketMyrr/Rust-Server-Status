jQuery(document).ready(function($) {
    $('#rss-refresh-btn').click(function() {
        $('#rss-refresh-status').html('Refreshing...');
        
        $.post(rss_ajax.ajax_url, { action: 'rss_manual_refresh' }, function(response) {
            $('#rss-refresh-status').html(response);
        });
    });
});
