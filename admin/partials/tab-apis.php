<div class="headless-config-section">
    <h3>⚡ GraphQL API</h3>
    
    <div class="headless-form-row">
        <label>
            <span class="status-indicator <?php echo $settings['graphql_enabled'] ? 'status-active' : 'status-inactive'; ?>"></span>
            <input type="checkbox" name="graphql_enabled" value="1" <?php checked($settings['graphql_enabled']); ?>>
            Enable GraphQL API
        </label>
        <p class="description">
            Endpoint: <code><?php echo home_url('/graphql'); ?></code> 
            <button type="button" id="test-graphql" class="button button-small">Test</button>
        </p>
    </div>

    <div class="headless-form-row">
        <label>
            <input type="checkbox" name="graphql_cors_enabled" value="1" <?php checked($settings['graphql_cors_enabled']); ?>>
            Enable CORS for GraphQL
        </label>
    </div>

    <div class="headless-form-row">
        <label for="graphql_cors_origins">Allowed CORS Origins (GraphQL)</label>
        <textarea name="graphql_cors_origins" id="graphql_cors_origins"><?php echo esc_textarea($settings['graphql_cors_origins']); ?></textarea>
        <p class="description">One origin per line (e.g., https://localhost:3000)</p>
    </div>

    <div class="headless-grid">
        <div class="headless-form-row">
            <label>
                <input type="checkbox" name="graphql_introspection" value="1" <?php checked($settings['graphql_introspection']); ?>>
                Enable introspection
            </label>
            <p class="description">Allows exploring the GraphQL schema</p>
        </div>

        <div class="headless-form-row">
            <label>
                <input type="checkbox" name="graphql_tracing" value="1" <?php checked($settings['graphql_tracing']); ?>>
                Enable tracing
            </label>
            <p class="description">For debugging and performance analysis</p>
        </div>

        <div class="headless-form-row">
            <label>
                <input type="checkbox" name="graphql_caching" value="1" <?php checked($settings['graphql_caching']); ?>>
                Enable query caching
            </label>
            <p class="description">Improves GraphQL performance</p>
        </div>
    </div>
</div>

<div class="headless-config-section">
    <h3>🔌 REST API</h3>
    
    <div class="headless-form-row">
        <label>
            <span class="status-indicator <?php echo $settings['rest_api_enabled'] ? 'status-active' : 'status-inactive'; ?>"></span>
            <input type="checkbox" name="rest_api_enabled" value="1" <?php checked($settings['rest_api_enabled']); ?>>
            Enable REST API
        </label>
        <p class="description">
            Endpoint: <code><?php echo home_url('/wp-json/'); ?></code>
            <button type="button" id="test-rest" class="button button-small">Test</button>
        </p>
    </div>

    <div class="headless-form-row">
        <label>
            <input type="checkbox" name="rest_api_auth_required" value="1" <?php checked($settings['rest_api_auth_required']); ?>>
            Require authentication for REST API
        </label>
        <p class="description">Only authenticated users can access</p>
    </div>

    <div class="headless-form-row">
        <label for="rest_api_allowed_routes">Allowed REST Routes</label>
        <textarea name="rest_api_allowed_routes" id="rest_api_allowed_routes"><?php echo esc_textarea($settings['rest_api_allowed_routes']); ?></textarea>
        <p class="description">One route per line. Leave empty to allow all.</p>
    </div>

    <div class="headless-form-row">
        <label for="rest_api_cors_origins">Allowed CORS Origins (REST API)</label>
        <textarea name="rest_api_cors_origins" id="rest_api_cors_origins"><?php echo esc_textarea($settings['rest_api_cors_origins']); ?></textarea>
        <p class="description">One origin per line for CORS configuration</p>
    </div>
</div>