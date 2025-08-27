# WP Rocket WooCommerce Filters Cache

Automatically adds WooCommerce attribute filters to WP Rocket cache_query_strings settings.

## Description

This WordPress plugin automatically manages the addition of WooCommerce product filters to WP Rocket cache settings. The plugin ensures that WooCommerce filters work properly with WP Rocket caching enabled.

## Features

- **Automatic filter detection**: Recognizes all WooCommerce attributes
- **Auto-update**: Updates when attributes are added, modified, or deleted
- **Safe handling**: Places filters between `# filter_start` and `# filter_end` markers
- **Preserves manual modifications**: Does not overwrite custom settings outside the markers

## Managed Filters

- WooCommerce attribute filters (`filter_*`, `query_type_*`)
- Price filters (`min_price`, `max_price`)
- Rating filter (`rating_filter`)
- Sorting options (`orderby`, `sort`)
- Product category and tag (`product_cat`, `product_tag`)
- Display settings (`per_page`, `columns`)

## Requirements

- WordPress 5.0+
- WooCommerce 3.0+
- WP Rocket

## Installation

1. Download the plugin
2. Copy the `wp-rocket-woo-filters-cache` folder to your WordPress `wp-content/plugins/` directory
3. Activate the plugin from the WordPress admin panel
4. The plugin will automatically add filters upon activation

## Usage

The plugin is fully automatic. After activation:

1. All existing WooCommerce attribute filters are added to WP Rocket's cache_query_strings setting
2. The list automatically updates when new attributes are created
3. Updates also occur when attributes are modified or deleted

## How It Works

The plugin operates as follows:

1. Places filters into WP Rocket's `cache_query_strings` option
2. Adds automatic filters between `# filter_start` and `# filter_end` markers
3. Manual settings outside the markers are preserved
4. Clears cache after making changes

## Development

### Hooks

The plugin uses the following WordPress/WooCommerce hooks:

- `woocommerce_attribute_added` - when a new attribute is added
- `woocommerce_attribute_updated` - when an attribute is updated
- `woocommerce_attribute_deleted` - when an attribute is deleted

### Filter Format

The generated filters format:
```
# filter_start
filter_color
query_type_color
filter_size
query_type_size
min_price
max_price
rating_filter
orderby
product_cat
product_tag
per_page
columns
sort
# filter_end
```

## Support

If you find a bug or have a feature request, please open an issue in the GitHub repository.

## License

GPL v2 or later

## Changelog

### 1.0.1
- Removed admin menu
- Pure automatic operation

### 1.0.0
- Initial release
- Automatic WooCommerce filter detection
- WP Rocket integration