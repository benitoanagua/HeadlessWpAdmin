# Headless WordPress Admin

A complete headless WordPress solution with full admin dashboard configuration.

## Features

- ✅ Completely block frontend access
- ✅ Configurable GraphQL (requires WPGraphQL plugin)
- ✅ Configurable REST API
- ✅ Fully customizable blocked page
- ✅ CORS configuration for development
- ✅ Automatic WordPress cleanup
- ✅ Security headers
- ✅ Logging and debugging
- ✅ Compatibility with other plugins

## Installation

1. Upload the plugin to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > Headless Mode to configure

## Recommended Configuration

- Enable GraphQL (install WPGraphQL plugin)
- Disable REST API (unless needed)
- Configure CORS for your frontend
- Customize blocked page with your branding
- Enable security headers

## Development

- Use GraphQL endpoint: /graphql
- Configure CORS for localhost during development
- Enable debug logging for troubleshooting

## Production

- Disable debug logging
- Configure CORS only for production domains
- Enable all security features
- Fully customize the blocked page

## Support

For support and documentation, visit [website URL]

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for details.
