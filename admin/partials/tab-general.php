<div class="headless-config-section">
    <h3>📋 General Configuration</h3>
    
    <div class="headless-form-row">
        <label>
            <input type="checkbox" name="blocked_page_enabled" value="1" <?php checked($settings['blocked_page_enabled']); ?>>
            Show custom page when access is blocked
        </label>
        <p class="description">If disabled, a standard 403 error will be shown.</p>
    </div>

    <div class="headless-form-row">
        <label for="allowed_paths">Allowed Paths</label>
        <textarea name="allowed_paths" id="allowed_paths" rows="6"><?php echo esc_textarea($settings['allowed_paths']); ?></textarea>
        <p class="description">One path per line. These paths will not be blocked by the headless system.</p>
    </div>

    <div class="headless-grid">
        <div class="headless-form-row">
            <label>
                <input type="checkbox" name="media_access_enabled" value="1" <?php checked($settings['media_access_enabled']); ?>>
                Allow direct access to media files
            </label>
            <p class="description">Allows access to /wp-content/uploads/</p>
        </div>

        <div class="headless-form-row">
            <label>
                <input type="checkbox" name="preview_access_enabled" value="1" <?php checked($settings['preview_access_enabled']); ?>>
                Allow preview for authenticated users
            </label>
            <p class="description">Editors can view content previews.</p>
        </div>
    </div>
</div>

<div class="headless-config-section">
    <h3>🧹 WordPress Cleanup</h3>
    <div class="headless-grid">
        <div class="headless-form-row">
            <label>
                <input type="checkbox" name="disable_feeds" value="1" <?php checked($settings['disable_feeds']); ?>>
                Disable RSS feeds
            </label>
        </div>
        
        <div class="headless-form-row">
            <label>
                <input type="checkbox" name="disable_sitemaps" value="1" <?php checked($settings['disable_sitemaps']); ?>>
                Disable XML sitemaps
            </label>
        </div>
        
        <div class="headless-form-row">
            <label>
                <input type="checkbox" name="disable_comments" value="1" <?php checked($settings['disable_comments']); ?>>
                Disable comments
            </label>
        </div>
        
        <div class="headless-form-row">
            <label>
                <input type="checkbox" name="disable_embeds" value="1" <?php checked($settings['disable_embeds']); ?>>
                Disable oEmbed
            </label>
        </div>
        
        <div class="headless-form-row">
            <label>
                <input type="checkbox" name="disable_emojis" value="1" <?php checked($settings['disable_emojis']); ?>>
                Disable emojis
            </label>
        </div>
        
        <div class="headless-form-row">
            <label>
                <input type="checkbox" name="clean_wp_head" value="1" <?php checked($settings['clean_wp_head']); ?>>
                Clean wp_head()
            </label>
        </div>
    </div>
</div>