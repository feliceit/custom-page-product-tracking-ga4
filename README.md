# Custom Page Product Tracking for GA4

[![WordPress Plugin Version](https://img.shields.io/badge/WordPress-5.8%2B-blue.svg)](https://wordpress.org)
[![License](https://img.shields.io/badge/license-GPL%20v2%2B-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![WooCommerce](https://img.shields.io/badge/WooCommerce-6.0%2B-purple.svg)](https://woocommerce.com)

> Track WooCommerce products on custom landing pages with GA4 events. Works with any page builder.

## 📋 Overview

WordPress plugin that extends GTM4WP functionality to track **view_item** and **add_to_cart** events on custom pages where WooCommerce products are promoted using any page builder or custom HTML.

### Problem Solved

You created a custom landing page (with Elementor, Divi, or any page builder) promoting a WooCommerce product, but GA4 doesn't track ecommerce events because it's not a standard WooCommerce product page.

### Solution

This plugin lets you associate any page with a WooCommerce product and automatically track GA4 events when users view the page or click your custom "Add to Cart" button.

## ✨ Features

- ✅ **GA4 Standard Format** - Events in official Google Analytics 4 format
- ✅ **Universal Compatibility** - Works with ANY page builder
- ✅ **Non-Invasive** - Only tracks events, doesn't modify button behavior
- ✅ **GTM4WP Integration** - Uses standard GTM4WP filters
- ✅ **Zero Configuration** - Events work out of the box with GTM

## 🎯 Compatible Page Builders

| Page Builder | Status |
|--------------|--------|
| Elementor | ✅ |
| Divi | ✅ |
| Beaver Builder | ✅ |
| Oxygen | ✅ |
| Bricks | ✅ |
| WPBakery | ✅ |
| Gutenberg | ✅ |
| Custom HTML | ✅ |

## 📦 Requirements

- WordPress 5.8+
- PHP 7.4+
- **WooCommerce** (active)
- **GTM4WP** - Google Tag Manager for WordPress (active)
- Google Tag Manager container configured

## 🚀 Quick Start

### Installation

1. Download the [latest release](https://github.com/feliceit/custom-page-product-tracking-ga4/releases)
2. Upload to WordPress via Plugins → Add New → Upload Plugin
3. Activate plugin

### Configuration

1. **Settings → Product Tracking GA4**
   - Enable post types (e.g., Pages)
   - Set default button class (e.g., `buy-now-button`)

2. **Edit your landing page**
   - Use "GA4 Product Tracking Configuration" metabox
   - Select WooCommerce product
   - Set button CSS class

3. **Add class to button** (in your page builder)
   - Elementor: `Advanced → CSS Classes`
   - Divi: `Advanced → CSS Class`
   - HTML: `<button class="buy-now-button">Buy</button>`

## 📊 Events Tracked

### View Item
```javascript
{
  event: "view_item",
  ecommerce: {
    currency: "USD",
    value: 49.99,
    items: [{ item_id: "SKU", item_name: "Product", price: 49.99 }]
  }
}
```

### Add to Cart
```javascript
{
  event: "add_to_cart",
  ecommerce: {
    currency: "USD",
    value: 49.99,
    items: [{ item_id: "SKU", item_name: "Product", price: 49.99, quantity: 1 }]
  }
}
```

## 🧪 Testing

```javascript
// Open browser console (F12) and check:
console.log(window.dataLayer);

// Look for view_item and add_to_cart events
```

## 📖 Documentation

- [Full Documentation](https://www.taglyzer.com)
- [Changelog](CHANGELOG.md)
- [WordPress.org Plugin Page](#) _(coming soon)_

## 🐛 Troubleshooting

**Events not firing?**
- Check product configured in metabox
- Verify button CSS class matches
- Check GTM4WP is active

**Button not working?**
- Plugin only tracks events
- Button needs own functionality (link/AJAX)

## 🤝 Contributing

Pull requests are welcome! For major changes, please open an issue first.

## 📄 License

GPL v2 or later - [License Details](LICENSE)

## 👨‍💻 Author

**Taglyzer**  
Website: [taglyzer.com](https://www.taglyzer.com)

## 🌟 Support

Give a ⭐️ if this project helped you!

---

**Version**: 1.1.0  
**Released**: January 22, 2026
