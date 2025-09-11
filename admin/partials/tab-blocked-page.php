<div class="headless-config-section">
    <h3>🎨 Page Appearance</h3>
    
    <div class="headless-grid">
        <div class="headless-form-row">
            <label for="blocked_page_title">Main Title</label>
            <input type="text" name="blocked_page_title" id="blocked_page_title" value="<?php echo esc_attr($settings['blocked_page_title']); ?>">
        </div>

        <div class="headless-form-row">
            <label for="blocked_page_subtitle">Subtitle</label>
            <input type="text" name="blocked_page_subtitle" id="blocked_page_subtitle" value="<?php echo esc_attr($settings['blocked_page_subtitle']); ?>">
        </div>
    </div>

    <div class="headless-form-row">
        <label for="blocked_page_message">Main Message</label>
        <textarea name="blocked_page_message" id="blocked_page_message" rows="4"><?php echo esc_textarea($settings['blocked_page_message']); ?></textarea>
        <p class="description">You can use basic HTML. Leave empty to not show a message.</p>
    </div>

    <div class="headless-grid">
        <div class="headless-form-row">
            <label for="blocked_page_icon">Icon/Emoji</label>
            <input type="text" name="blocked_page_icon" id="blocked_page_icon" value="<?php echo esc_attr($settings['blocked_page_icon']); ?>" maxlength="10">
            <p class="description">Emoji or symbol to display large</p>
        </div>

        <div class="headless-form-row">
            <label for="blocked_page_logo_url">Logo URL</label>
            <input type="url" name="blocked_page_logo_url" id="blocked_page_logo_url" value="<?php echo esc_url($settings['blocked_page_logo_url']); ?>">
            <p class="description">Full URL of the logo image</p>
        </div>
    </div>
</div>

<div class="headless-config-section">
    <h3>🎨 Colors and Design</h3>
    
    <div class="headless-grid">
        <div class="headless-form-row">
            <label for="blocked_page_background_color">Base Background Color</label>
            <input type="text" name="blocked_page_background_color" id="blocked_page_background_color" value="<?php echo esc_attr($settings['blocked_page_background_color']); ?>" class="color-picker">
        </div>

        <div class="headless-form-row">
            <label for="blocked_page_background_gradient">Background Gradient (CSS)</label>
            <input type="text" name="blocked_page_background_gradient" id="blocked_page_background_gradient" value="<?php echo esc_attr($settings['blocked_page_background_gradient']); ?>">
            <p class="description">E.g., linear-gradient(135deg, #667eea 0%, #764ba2 100%)</p>
        </div>
    </div>

    <div class="headless-form-row">
        <label for="blocked_page_custom_css">Custom CSS</label>
        <textarea name="blocked_page_custom_css" id="blocked_page_custom_css" rows="8"><?php echo esc_textarea($settings['blocked_page_custom_css']); ?></textarea>
        <p class="description">Additional CSS to fully customize the page</p>
    </div>
</div>

<div class="headless-config-section">
    <h3>📝 Content and Links</h3>
    
    <div class="headless-grid">
        <div class="headless-form-row">
            <label>
                <input type="checkbox" name="blocked_page_show_admin_link" value="1" <?php checked($settings['blocked_page_show_admin_link']); ?>>
                Show admin link
            </label>
        </div>

        <div class="headless-form-row">
            <label>
                <input type="checkbox" name="blocked_page_show_graphql_link" value="1" <?php checked($settings['blocked_page_show_graphql_link']); ?>>
                Show GraphQL link
            </label>
        </div>

        <div class="headless-form-row">
            <label>
                <input type="checkbox" name="blocked_page_show_status_info" value="1" <?php checked($settings['blocked_page_show_status_info']); ?>>
                Show API status information
            </label>
        </div>
    </div>

    <div class="headless-form-row">
        <label for="blocked_page_contact_info">Contact Information</label>
        <textarea name="blocked_page_contact_info" id="blocked_page_contact_info" rows="3"><?php echo esc_textarea($settings['blocked_page_contact_info']); ?></textarea>
        <p class="description">Additional information that will appear at the bottom of the page (HTML allowed)</p>
    </div>
</div>