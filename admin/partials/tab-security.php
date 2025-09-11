<div class="headless-config-section">
    <h3>🔒 Security Configuration</h3>
    
    <div class="headless-grid">
        <div class="headless-form-row">
            <label>
                <input type="checkbox" name="security_headers_enabled" value="1" <?php checked($settings['security_headers_enabled']); ?>>
                Enable security headers
            </label>
            <p class="description">Adds X-Content-Type-Options, X-Frame-Options, etc.</p>
        </div>

        <div class="headless-form-row">
            <label>
                <input type="checkbox" name="block_theme_access" value="1" <?php checked($settings['block_theme_access']); ?>>
                Block direct access to theme files
            </label>
            <p class="description">Prevents direct access to theme PHP files</p>
        </div>

        <div class="headless-form-row">
            <label>
                <input type="checkbox" name="rate_limiting_enabled" value="1" <?php checked($settings['rate_limiting_enabled']); ?>>
                Enable basic rate limiting
            </label>
            <p class="description">Basic request limiting by IP (experimental)</p>
        </div>

        <div class="headless-form-row">
            <label>
                <input type="checkbox" name="debug_logging" value="1" <?php checked($settings['debug_logging']); ?>>
                Enable debug logging
            </label>
            <p class="description">Logs blocked access attempts to error.log</p>
        </div>
    </div>
</div>

<div class="headless-config-section">
    <h3>📊 Block Statistics (Last 24h)</h3>
    <div id="security-stats">
        <?php
        $stats = HeadlessWPAdmin\Helpers\Debug::get_stats();
        if (!empty($stats)) : ?>
            <div class="headless-grid">
                <div class="headless-form-row">
                    <strong>Total Requests:</strong> <?php echo $stats['total_requests']; ?>
                </div>
                <div class="headless-form-row">
                    <strong>Blocked Requests:</strong> <?php echo $stats['blocked_requests']; ?>
                </div>
                <div class="headless-form-row">
                    <strong>Allowed Requests:</strong> <?php echo $stats['allowed_requests']; ?>
                </div>
                <div class="headless-form-row">
                    <strong>Last 24h:</strong> <?php echo $stats['last_24h']; ?>
                </div>
            </div>
            
            <?php if (!empty($stats['by_ip'])) : ?>
                <h4>Top IP Addresses</h4>
                <ul>
                    <?php $count = 0; ?>
                    <?php foreach ($stats['by_ip'] as $ip => $requests) : ?>
                        <?php if ($count++ < 5) : ?>
                            <li><?php echo esc_html($ip); ?>: <?php echo $requests; ?> requests</li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            
            <p>
                <button type="button" id="clear-logs" class="button button-secondary">Clear Logs</button>
                <button type="button" id="view-logs" class="button">View Logs</button>
            </p>
        <?php else : ?>
            <p>No statistics available. Enable debug logging to collect data.</p>
        <?php endif; ?>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#clear-logs').click(function() {
        if (confirm('Are you sure you want to clear all logs?')) {
            $.post(headless_admin.ajax_url, {
                action: 'headless_clear_logs',
                nonce: headless_admin.nonce
            }, function(response) {
                if (response.success) {
                    alert('Logs cleared successfully');
                    location.reload();
                } else {
                    alert('Error: ' + response.data);
                }
            });
        }
    });
    
    $('#view-logs').click(function() {
        $('#log-viewer').toggle();
    });
});
</script>

<div id="log-viewer" style="display: none; margin-top: 20px;">
    <h4>Recent Log Entries</h4>
    <textarea style="width: 100%; height: 200px; font-family: monospace; font-size: 12px;" readonly>
        <?php
        $logs = HeadlessWPAdmin\Helpers\Debug::get_logs(50);
        foreach ($logs as $log) {
            echo esc_html($log);
        }
        ?>
    </textarea>
</div>