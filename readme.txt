=== Custom Page Product Tracking for GA4 ===
Contributors: taglyzer
Tags: google analytics, ga4, woocommerce, tracking, ecommerce, gtm, page builder
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Track WooCommerce products on custom landing pages with GA4 events. Works with any page builder (Elementor, Divi, Beaver Builder, etc).

== Description ==

**Custom Page Product Tracking for GA4** extends GTM4WP functionality to track view_item and add_to_cart events on custom pages where WooCommerce products are promoted using any page builder or custom HTML.

= Use Case =

You created a custom landing page promoting a WooCommerce product, but GA4 doesn't track **view_item** and **add_to_cart** events because it's not a standard WooCommerce product page.

This plugin solves that problem by letting you associate any page with a WooCommerce product and track GA4 events automatically.

= Key Features =

* ✅ GA4 Standard Event Format
* ✅ Works with ANY page builder (Elementor, Divi, Beaver Builder, Oxygen, Bricks, etc.)
* ✅ Non-invasive - only tracks events, doesn't modify button behavior
* ✅ Compatible with GTM4WP
* ✅ Zero GTM configuration needed

= Compatible Page Builders =

* Elementor
* Divi
* Beaver Builder
* Oxygen
* Bricks
* WPBakery
* Gutenberg
* Custom HTML/CSS

= Events Tracked =

* **view_item**: Automatically sent on page load when product is configured
* **add_to_cart**: Sent when user clicks button with configured CSS class

= Requirements =

* WooCommerce (active)
* GTM4WP - Google Tag Manager for WordPress (active)
* Google Tag Manager container configured

= How It Works =

1. Configure which WooCommerce product is associated with your custom page
2. Set the CSS class of your "Add to Cart" button
3. Plugin automatically tracks view_item and add_to_cart events in GA4 format
4. Your button maintains its original behavior (AJAX, redirect, etc.)

= Privacy =

This plugin does not collect, store, or transmit any personal data. It only pushes product information to the browser's dataLayer for Google Tag Manager.

== Installation ==

= Automatic Installation =

1. Log in to your WordPress admin panel
2. Go to Plugins → Add New
3. Search for "Custom Page Product Tracking for GA4"
4. Click "Install Now" and then "Activate"

= Manual Installation =

1. Download the plugin ZIP file
2. Go to Plugins → Add New → Upload Plugin
3. Choose the ZIP file and click "Install Now"
4. Click "Activate"

= After Activation =

1. Go to Settings → Product Tracking GA4
2. Select enabled post types (usually "Pages")
3. Set default button CSS class (e.g., "buy-now-button")
4. Save settings

= Configure a Page =

1. Edit your custom landing page
2. In the sidebar, find "GA4 Product Tracking Configuration" metabox
3. Select the WooCommerce product
4. Set button CSS class
5. Publish page

= Add Button Class =

In your page builder, add the CSS class to your button:

* **Elementor**: Advanced → CSS Classes
* **Divi**: Advanced → CSS Class
* **Beaver Builder**: Advanced → Class
* **Custom HTML**: `<button class="buy-now-button">Buy Now</button>`

== Frequently Asked Questions ==

= Does this work without GTM4WP? =

No. This plugin requires GTM4WP (Google Tag Manager for WordPress) as it uses GTM4WP's filters and dataLayer structure.

= Can I use this with page builders other than Elementor? =

Yes! It works with ANY page builder or custom HTML. You just need to add a CSS class to your button.

= Does the plugin add products to the cart? =

No. The plugin only tracks events in the dataLayer. Your button must handle the actual add-to-cart action (via link, AJAX, JavaScript, etc.).

= Can I track multiple buttons on the same page? =

Yes, as long as they all use the same CSS class. All clicks will track the same configured product.

= What if my button already has other classes? =

That's fine! Add the tracking class alongside existing classes:
`<button class="my-style tracking-class">Buy</button>`

= Does this affect page performance? =

No. The script only loads on pages where a product is configured, and it's a lightweight event listener.

= How do I verify events are firing? =

1. Visit your landing page
2. Open browser console (F12)
3. Type: `console.log(window.dataLayer)`
4. Click your button
5. Check for "add_to_cart" event in console

= Is this GDPR compliant? =

The plugin itself doesn't collect personal data. However, Google Analytics and Tag Manager have their own privacy considerations. Make sure to implement proper consent management for your GA4/GTM setup.

== Screenshots ==

1. Metabox configuration - Select product and button class
2. Settings page - Configure enabled post types and default button class
3. Browser console showing tracked events
4. GA4 showing view_item event
5. GA4 showing add_to_cart event

== Changelog ==

= 1.1.0 - 2026-01-22 =
* Rebranded to "Custom Page Product Tracking for GA4"
* Universal page builder support (not limited to Elementor)
* All interface strings translated to English
* Improved metabox UI with compatibility info
* Added page builder compatibility list
* Updated documentation

= 1.0.2 - 2026-01-22 =
* Removed AJAX add to cart functionality
* Plugin now only tracks dataLayer events
* No interference with button behavior
* Updated author info to Taglyzer

= 1.0.1 - 2026-01-22 =
* Fixed GA4 event format for add_to_cart
* Fixed GA4 event format for view_item
* Corrected button behavior (no longer blocks redirects)
* Added ecommerce clear before push (best practice)

= 1.0.0 - 2026-01-22 =
* Initial release
* Support for view_item event
* Support for add_to_cart event
* Metabox for product configuration
* Settings page
* Compatible with GTM4WP filters

== Upgrade Notice ==

= 1.1.0 =
Major update with universal page builder support and improved interface. All data is preserved during upgrade.

= 1.0.2 =
Important update: Plugin now only tracks events without interfering with button behavior.

= 1.0.1 =
Critical update: Fixes GA4 event format to match official standards. Please update immediately.

== Support ==

For support and documentation, visit [Taglyzer.com](https://www.taglyzer.com)

== Credits ==

This plugin integrates with:
* WooCommerce - The most popular ecommerce platform for WordPress
* GTM4WP - Google Tag Manager for WordPress by Thomas Geiger
