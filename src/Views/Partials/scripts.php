<?php

/**
 * Scripts Partial
 */
?>
<script>
    jQuery(document).ready(function($) {
        // Initialize color picker
        if (typeof $.fn.wpColorPicker === 'function') {
            $('.color-picker').wpColorPicker();
        } else {
            console.warn('wpColorPicker not available, using color input');
            $('.color-picker').attr('type', 'color');
        }

        // Test GraphQL endpoint
        $('#test-graphql').click(function() {
            $.post(ajaxurl, {
                action: 'headless_test_endpoint',
                endpoint: 'graphql'
            }, function(response) {
                if (response.success) {
                    alert('GraphQL endpoint is working correctly');
                } else {
                    alert('Error: ' + response.data);
                }
            });
        });

        // Reset settings
        $('#reset-settings').click(function() {
            if (confirm('Are you sure you want to reset all settings?')) {
                $.post(ajaxurl, {
                    action: 'headless_reset_settings',
                    nonce: '<?php echo wp_create_nonce("headless_reset"); ?>'
                }, function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + response.data);
                    }
                });
            }
        });

        // Preview blocked page
        $('#preview-blocked-page').click(function() {
            window.open('<?php echo home_url(); ?>', '_blank');
        });
    });
</script>