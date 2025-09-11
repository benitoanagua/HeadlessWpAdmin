<div class="headless-config-section">
    <h3>⚙️ Advanced Configuration</h3>
    
    <div class="headless-form-row">
        <label for="custom_redirect_rules">Custom Redirect Rules</label>
        <textarea name="custom_redirect_rules" id="custom_redirect_rules" rows="6" placeholder="# Example:&#10;/old-page -> /new-page&#10;/blog/* -> https://external-blog.com/*"><?php echo esc_textarea($settings['custom_redirect_rules'] ?? ''); ?></textarea>
        <p class="description">One rule per line. Use -> to separate source and destination</p>
    </div>
    
    <div class="headless-form-row">
        <label for="custom_headers">Custom HTTP Headers</label>
        <textarea name="custom_headers" id="custom_headers" rows="4" placeholder="X-Custom-Header: value&#10;Cache-Control: no-cache"><?php echo esc_textarea($settings['custom_headers'] ?? ''); ?></textarea>
        <p class="description">One header per line in "Name: Value" format</p>
    </div>

    <div class="headless-form-row">
        <label for="webhook_urls">Webhook URLs</label>
        <textarea name="webhook_urls" id="webhook_urls" rows="3" placeholder="https://api.example.com/webhook1&#10;https://api.example.com/webhook2"><?php echo esc_textarea($settings['webhook_urls'] ?? ''); ?></textarea>
        <p class="description">Will be called when content is updated (experimental)</p>
    </div>
</div>

<div class="headless-config-section">
    <h3>🔧 Maintenance Tools</h3>
    
    <div class="headless-tools-grid">
        <button type="button" class="button" onclick="if(confirm('Clear GraphQL cache?')) headlessClearCache('graphql')">🗑️ Clear GraphQL Cache</button>
        <button type="button" class="button" onclick="window.open('<?php echo home_url('/wp-json/'); ?>', '_blank')">🔍 Explore REST API</button>
        <button type="button" class="button" onclick="window.open('<?php echo home_url('/graphql'); ?>', '_blank')">⚡ Open GraphQL</button>
        <button type="button" class="button" onclick="exportSettings()">📤 Export Settings</button>
        <button type="button" class="button" onclick="document.getElementById('import-file').click()">📥 Import Settings</button>
        <input type="file" id="import-file" accept=".json" style="display: none;">
    </div>
</div>

<div class="headless-config-section">
    <h3>📋 System Information</h3>
    
    <table class="headless-system-info">
        <tr><td><strong>WordPress Version:</strong></td><td><?php echo get_bloginfo('version'); ?></td></tr>
        <tr><td><strong>PHP Version:</strong></td><td><?php echo PHP_VERSION; ?></td></tr>
        <tr><td><strong>GraphQL Plugin:</strong></td><td><?php echo class_exists('WPGraphQL') ? '✅ Installed' : '❌ Not installed'; ?></td></tr>
        <tr><td><strong>Headless Plugin Version:</strong></td><td><?php echo HEADLESS_WP_ADMIN_VERSION; ?></td></tr>
        <tr><td><strong>Debug Mode:</strong></td><td><?php echo WP_DEBUG ? '✅ Active' : '❌ Disabled'; ?></td></tr>
        <tr><td><strong>Frontend URL:</strong></td><td><code><?php echo home_url(); ?></code></td></tr>
        <tr><td><strong>GraphQL URL:</strong></td><td><code><?php echo home_url('/graphql'); ?></code></td></tr>
        <tr><td><strong>REST API URL:</strong></td><td><code><?php echo home_url('/wp-json/'); ?></code></td></tr>
        <tr><td><strong>Admin URL:</strong></td><td><code><?php echo admin_url(); ?></code></td></tr>
    </table>
</div>

<script>
function headlessClearCache(type) {
    jQuery.post(headless_admin.ajax_url, {
        action: 'headless_clear_cache',
        type: type,
        nonce: headless_admin.nonce
    }, function(response) {
        if (response.success) {
            alert('Cache cleared successfully');
        } else {
            alert('Error: ' + response.data);
        }
    });
}
</script>