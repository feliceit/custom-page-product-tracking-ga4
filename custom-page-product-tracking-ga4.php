<?php
/**
 * Plugin Name: Custom Page Product Tracking for GA4
 * Plugin URI: https://www.taglyzer.com
 * Description: Track WooCommerce products on custom landing pages with GA4 events (view_item, add_to_cart). Compatible with GTM4WP. Works with any page builder or custom HTML/CSS.
 * Version: 1.1.0
 * Author: Taglyzer
 * Author URI: https://www.taglyzer.com
 * Text Domain: custom-page-product-tracking
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Requires Plugins: woocommerce
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Verifica dipendenze
add_action( 'admin_init', 'cppt_ga4_check_dependencies' );
function cppt_ga4_check_dependencies() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        add_action( 'admin_notices', function() {
            echo '<div class="error"><p><strong>Custom Page Product Tracking for GA4</strong> requires WooCommerce to be active.</p></div>';
        });
        deactivate_plugins( plugin_basename( __FILE__ ) );
        return;
    }
    
    if ( ! defined( 'GTM4WP_VERSION' ) ) {
        add_action( 'admin_notices', function() {
            echo '<div class="error"><p><strong>Custom Page Product Tracking for GA4</strong> requires GTM4WP (Google Tag Manager for WordPress) to be active.</p></div>';
        });
        deactivate_plugins( plugin_basename( __FILE__ ) );
        return;
    }
}

/**
 * Main plugin class
 */
class Custom_Page_Product_Tracking_GA4 {
    
    private static $instance = null;
    
    const META_KEY_PRODUCT_ID = '_cppt_product_id';
    const META_KEY_BUTTON_CLASS = '_cppt_button_class';
    const OPTION_KEY_SETTINGS = 'cppt_ga4_settings';
    
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Admin hooks
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post', array( $this, 'save_meta_boxes' ) );
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
        
        // Frontend hooks
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_footer', array( $this, 'output_tracking_script' ) );
        
        // GTM4WP filters
        add_filter( 'gtm4wp_compile_datalayer', array( $this, 'add_product_datalayer' ) );
    }
    
    /**
     * Enqueue admin styles
     */
    public function enqueue_admin_styles( $hook ) {
        // Carica solo nelle pagine necessarie
        if ( ! in_array( $hook, array( 'post.php', 'post-new.php', 'settings_page_cppt-ga4-settings' ) ) ) {
            return;
        }
        
        // Inline CSS per semplicità
        $css = '
        .cppt-config { padding: 10px 0; }
        .cppt-config p { margin-bottom: 15px; }
        .cppt-config label { display: block; margin-bottom: 5px; }
        .cppt-config select, .cppt-config input[type="text"] { margin-top: 5px; }
        .cppt-config .description { display: block; margin-top: 5px; font-style: italic; color: #646970; }
        @media screen and (max-width: 782px) {
            .cppt-config select, .cppt-config input[type="text"] { width: 100% !important; }
        }
        ';
        
        wp_add_inline_style( 'wp-admin', $css );
    }
    
    /**
     * Aggiunge metabox per configurazione prodotto
     */
    public function add_meta_boxes() {
        $post_types = $this->get_enabled_post_types();
        
        foreach ( $post_types as $post_type ) {
            add_meta_box(
                'cppt_product_config',
                __( 'GA4 Product Tracking Configuration', 'custom-page-product-tracking' ),
                array( $this, 'render_meta_box' ),
                $post_type,
                'side',
                'default'
            );
        }
    }
    
    /**
     * Render metabox
     */
    public function render_meta_box( $post ) {
        wp_nonce_field( 'cppt_save_meta', 'cppt_nonce' );
        
        $product_id = get_post_meta( $post->ID, self::META_KEY_PRODUCT_ID, true );
        $button_class = get_post_meta( $post->ID, self::META_KEY_BUTTON_CLASS, true );
        
        // Default button class from settings
        if ( empty( $button_class ) ) {
            $settings = get_option( self::OPTION_KEY_SETTINGS, array() );
            $button_class = isset( $settings['default_button_class'] ) ? $settings['default_button_class'] : '';
        }
        
        ?>
        <div class="cppt-config">
            <p>
                <label for="cppt_product_id">
                    <strong><?php _e( 'WooCommerce Product:', 'custom-page-product-tracking' ); ?></strong>
                </label>
                <select name="cppt_product_id" id="cppt_product_id" style="width: 100%;">
                    <option value=""><?php _e( '-- Select Product --', 'custom-page-product-tracking' ); ?></option>
                    <?php
                    $products = $this->get_products_list();
                    foreach ( $products as $product ) {
                        printf(
                            '<option value="%d" %s>%s (ID: %d)</option>',
                            $product->ID,
                            selected( $product_id, $product->ID, false ),
                            esc_html( $product->post_title ),
                            $product->ID
                        );
                    }
                    ?>
                </select>
            </p>
            
            <p>
                <label for="cppt_button_class">
                    <strong><?php _e( 'Add to Cart Button CSS Class:', 'custom-page-product-tracking' ); ?></strong>
                </label>
                <input 
                    type="text" 
                    name="cppt_button_class" 
                    id="cppt_button_class" 
                    value="<?php echo esc_attr( $button_class ); ?>" 
                    placeholder="e.g., buy-now-button"
                    style="width: 100%;"
                />
                <span class="description">
                    <?php _e( 'Enter the CSS class of your add to cart button (without the dot).', 'custom-page-product-tracking' ); ?>
                </span>
            </p>
            
            <p class="description">
                <?php _e( 'Configure which WooCommerce product is associated with this page and the CSS class of the button to track view_item and add_to_cart events in GA4.', 'custom-page-product-tracking' ); ?>
            </p>
            
            <p class="description" style="margin-top: 10px; padding: 8px; background: #f0f6fc; border-left: 3px solid #2271b1;">
                <strong><?php _e( 'Compatible with:', 'custom-page-product-tracking' ); ?></strong><br>
                <?php _e( 'Any page builder (Elementor, Divi, Beaver Builder, Oxygen, etc.) or custom HTML/CSS.', 'custom-page-product-tracking' ); ?>
            </p>
        </div>
        <?php
    }
    
    /**
     * Salva metabox
     */
    public function save_meta_boxes( $post_id ) {
        // Verifica nonce
        if ( ! isset( $_POST['cppt_nonce'] ) || ! wp_verify_nonce( $_POST['cppt_nonce'], 'cppt_save_meta' ) ) {
            return;
        }
        
        // Verifica autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        
        // Verifica permessi
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
        
        // Salva product ID
        if ( isset( $_POST['cppt_product_id'] ) ) {
            $product_id = intval( $_POST['cppt_product_id'] );
            if ( $product_id > 0 ) {
                update_post_meta( $post_id, self::META_KEY_PRODUCT_ID, $product_id );
            } else {
                delete_post_meta( $post_id, self::META_KEY_PRODUCT_ID );
            }
        }
        
        // Salva button class
        if ( isset( $_POST['cppt_button_class'] ) ) {
            $button_class = sanitize_text_field( $_POST['cppt_button_class'] );
            update_post_meta( $post_id, self::META_KEY_BUTTON_CLASS, $button_class );
        }
    }
    
    /**
     * Aggiunge pagina impostazioni
     */
    public function add_settings_page() {
        add_options_page(
            __( 'Custom Page Product Tracking', 'custom-page-product-tracking' ),
            __( 'Product Tracking GA4', 'custom-page-product-tracking' ),
            'manage_options',
            'cppt-ga4-settings',
            array( $this, 'render_settings_page' )
        );
    }
    
    /**
     * Registra impostazioni
     */
    public function register_settings() {
        register_setting( 'cppt_settings_group', self::OPTION_KEY_SETTINGS );
    }
    
    /**
     * Render pagina impostazioni
     */
    public function render_settings_page() {
        $settings = get_option( self::OPTION_KEY_SETTINGS, array() );
        $enabled_post_types = isset( $settings['enabled_post_types'] ) ? $settings['enabled_post_types'] : array( 'page' );
        $default_button_class = isset( $settings['default_button_class'] ) ? $settings['default_button_class'] : '';
        
        ?>
        <div class="wrap">
            <h1><?php _e( 'Custom Page Product Tracking for GA4 - Settings', 'custom-page-product-tracking' ); ?></h1>
            
            <form method="post" action="options.php">
                <?php settings_fields( 'cppt_settings_group' ); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <?php _e( 'Enabled Post Types', 'custom-page-product-tracking' ); ?>
                        </th>
                        <td>
                            <?php
                            $post_types = get_post_types( array( 'public' => true ), 'objects' );
                            foreach ( $post_types as $post_type ) {
                                if ( in_array( $post_type->name, array( 'attachment', 'product' ) ) ) {
                                    continue;
                                }
                                $checked = in_array( $post_type->name, $enabled_post_types );
                                printf(
                                    '<label><input type="checkbox" name="%s[enabled_post_types][]" value="%s" %s> %s</label><br>',
                                    self::OPTION_KEY_SETTINGS,
                                    esc_attr( $post_type->name ),
                                    checked( $checked, true, false ),
                                    esc_html( $post_type->label )
                                );
                            }
                            ?>
                            <p class="description">
                                <?php _e( 'Select post types where the product configuration will be available.', 'custom-page-product-tracking' ); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="default_button_class">
                                <?php _e( 'Default Button CSS Class', 'custom-page-product-tracking' ); ?>
                            </label>
                        </th>
                        <td>
                            <input 
                                type="text" 
                                name="<?php echo self::OPTION_KEY_SETTINGS; ?>[default_button_class]" 
                                id="default_button_class" 
                                value="<?php echo esc_attr( $default_button_class ); ?>" 
                                class="regular-text"
                                placeholder="e.g., buy-now-button"
                            />
                            <p class="description">
                                <?php _e( 'Default CSS class for add to cart buttons. Can be overridden for each page.', 'custom-page-product-tracking' ); ?>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
            
            <hr>
            
            <h2><?php _e( 'How to Use', 'custom-page-product-tracking' ); ?></h2>
            <ol>
                <li><?php _e( 'Configure enabled post types and default button CSS class above.', 'custom-page-product-tracking' ); ?></li>
                <li><?php _e( 'On your custom pages, use the "GA4 Product Tracking Configuration" metabox to select the associated WooCommerce product.', 'custom-page-product-tracking' ); ?></li>
                <li><?php _e( 'Add the configured CSS class to your add to cart button (works with any page builder or custom HTML).', 'custom-page-product-tracking' ); ?></li>
                <li><?php _e( 'The plugin will automatically send view_item and add_to_cart events to GTM/GA4.', 'custom-page-product-tracking' ); ?></li>
            </ol>
            
            <h3><?php _e( 'Tracked Events', 'custom-page-product-tracking' ); ?></h3>
            <ul>
                <li><strong>view_item</strong>: <?php _e( 'Sent automatically on page load when a product is configured.', 'custom-page-product-tracking' ); ?></li>
                <li><strong>add_to_cart</strong>: <?php _e( 'Sent when the user clicks the button with the configured class.', 'custom-page-product-tracking' ); ?></li>
            </ul>
            
            <h3><?php _e( 'Compatible With', 'custom-page-product-tracking' ); ?></h3>
            <ul>
                <li>✅ Elementor</li>
                <li>✅ Divi</li>
                <li>✅ Beaver Builder</li>
                <li>✅ Oxygen</li>
                <li>✅ Bricks</li>
                <li>✅ WPBakery</li>
                <li>✅ Gutenberg</li>
                <li>✅ Custom HTML/CSS</li>
                <li>✅ Any page builder</li>
            </ul>
            
            <div style="background: #f0f6fc; padding: 15px; margin-top: 20px; border-left: 4px solid #2271b1;">
                <h3 style="margin-top: 0;"><?php _e( 'Requirements', 'custom-page-product-tracking' ); ?></h3>
                <ul>
                    <li>✅ WooCommerce active</li>
                    <li>✅ GTM4WP (Google Tag Manager for WordPress) active</li>
                    <li>✅ Google Tag Manager configured</li>
                </ul>
            </div>
        </div>
        <?php
    }
    
    /**
     * Ottiene lista prodotti WooCommerce
     */
    private function get_products_list() {
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
            'post_status' => 'publish'
        );
        
        return get_posts( $args );
    }
    
    /**
     * Ottiene post types abilitati
     */
    private function get_enabled_post_types() {
        $settings = get_option( self::OPTION_KEY_SETTINGS, array() );
        $enabled = isset( $settings['enabled_post_types'] ) ? $settings['enabled_post_types'] : array( 'page' );
        return is_array( $enabled ) ? $enabled : array( 'page' );
    }
    
    /**
     * Aggiunge dati prodotto al dataLayer
     */
    public function add_product_datalayer( $dataLayer ) {
        if ( ! is_singular() ) {
            return $dataLayer;
        }
        
        $post_id = get_the_ID();
        $product_id = get_post_meta( $post_id, self::META_KEY_PRODUCT_ID, true );
        
        if ( empty( $product_id ) ) {
            return $dataLayer;
        }
        
        $product = wc_get_product( $product_id );
        
        if ( ! $product ) {
            return $dataLayer;
        }
        
        // Prepara array prodotto per GA4
        $product_array = $this->prepare_product_array( $product );
        
        // Converti in formato GA4 item
        $ga4_item = array(
            'item_id' => $product_array['sku'] ? $product_array['sku'] : $product_array['id'],
            'item_name' => $product_array['name'],
            'price' => floatval( $product_array['price'] ),
        );
        
        // Aggiungi parametri opzionali
        if ( ! empty( $product_array['category'] ) ) {
            $ga4_item['item_category'] = $product_array['category'];
        }
        if ( ! empty( $product_array['brand'] ) ) {
            $ga4_item['item_brand'] = $product_array['brand'];
        }
        if ( ! empty( $product_array['variant'] ) ) {
            $ga4_item['item_variant'] = $product_array['variant'];
        }
        
        // Aggiungi evento view_item al dataLayer (formato GA4)
        $dataLayer['event'] = 'view_item';
        $dataLayer['ecommerce'] = array(
            'currency' => get_woocommerce_currency(),
            'value' => floatval( $product_array['price'] ),
            'items' => array( $ga4_item )
        );
        
        // Mantieni pagePostType per compatibilità con trigger GTM esistenti
        $dataLayer['pagePostType'] = 'product';
        
        return $dataLayer;
    }
    
    /**
     * Prepara array prodotto secondo standard GTM4WP
     */
    private function prepare_product_array( $product ) {
        $product_cat = '';
        $categories = get_the_terms( $product->get_id(), 'product_cat' );
        if ( $categories && ! is_wp_error( $categories ) ) {
            $category_names = array();
            foreach ( $categories as $category ) {
                $category_names[] = $category->name;
            }
            $product_cat = implode( '/', $category_names );
        }
        
        $product_array = array(
            'id' => $product->get_id(),
            'name' => $product->get_name(),
            'sku' => $product->get_sku() ? $product->get_sku() : $product->get_id(),
            'category' => $product_cat,
            'price' => $product->get_price(),
            'stocklevel' => $product->get_stock_quantity() ? $product->get_stock_quantity() : 0,
        );
        
        // Brand se disponibile
        $brands = wp_get_post_terms( $product->get_id(), 'product_brand' );
        if ( ! empty( $brands ) && ! is_wp_error( $brands ) ) {
            $product_array['brand'] = $brands[0]->name;
        }
        
        // Variante per prodotti variabili
        if ( $product->is_type( 'variable' ) ) {
            $product_array['variant'] = 'variable';
        }
        
        // Applica filtro GTM4WP per compatibilità
        if ( function_exists( 'apply_filters' ) ) {
            $product_array = apply_filters( 'gtm4wp_eec_product_array', $product_array, 'productdetail' );
        }
        
        return $product_array;
    }
    
    /**
     * Enqueue scripts frontend
     */
    public function enqueue_scripts() {
        if ( ! is_singular() ) {
            return;
        }
        
        $post_id = get_the_ID();
        $product_id = get_post_meta( $post_id, self::META_KEY_PRODUCT_ID, true );
        
        if ( empty( $product_id ) ) {
            return;
        }
        
        $button_class = get_post_meta( $post_id, self::META_KEY_BUTTON_CLASS, true );
        
        if ( empty( $button_class ) ) {
            return;
        }
        
        // Passa dati allo script
        wp_localize_script( 'jquery', 'gtm4wpElemData', array(
            'productId' => $product_id,
            'buttonClass' => $button_class,
            'ajaxUrl' => admin_url( 'admin-ajax.php' )
        ));
    }
    
    /**
     * Output script tracking nel footer
     */
    public function output_tracking_script() {
        if ( ! is_singular() ) {
            return;
        }
        
        $post_id = get_the_ID();
        $product_id = get_post_meta( $post_id, self::META_KEY_PRODUCT_ID, true );
        
        if ( empty( $product_id ) ) {
            return;
        }
        
        $button_class = get_post_meta( $post_id, self::META_KEY_BUTTON_CLASS, true );
        
        if ( empty( $button_class ) ) {
            return;
        }
        
        $product = wc_get_product( $product_id );
        
        if ( ! $product ) {
            return;
        }
        
        $product_array = $this->prepare_product_array( $product );
        
        ?>
        <script type="text/javascript">
        (function($) {
            'use strict';
            
            // Product data
            var cpptProduct = <?php echo json_encode( $product_array ); ?>;
            
            // Add to Cart tracking - dataLayer only, no interference with button behavior
            $(document).on('click', '.<?php echo esc_js( $button_class ); ?>', function(e) {
                
                // Calculate total value (price * quantity)
                var itemPrice = parseFloat(cpptProduct.price) || 0;
                var itemQuantity = 1;
                var totalValue = itemPrice * itemQuantity;
                
                // Prepare GA4 item (correct format)
                var ga4Item = {
                    item_id: cpptProduct.sku || cpptProduct.id,
                    item_name: cpptProduct.name,
                    price: itemPrice,
                    quantity: itemQuantity
                };
                
                // Add optional parameters if available
                if (cpptProduct.category) {
                    ga4Item.item_category = cpptProduct.category;
                }
                if (cpptProduct.brand) {
                    ga4Item.item_brand = cpptProduct.brand;
                }
                if (cpptProduct.variant) {
                    ga4Item.item_variant = cpptProduct.variant;
                }
                
                // Clear previous ecommerce object (GA4 best practice)
                if (typeof window.dataLayer !== 'undefined') {
                    window.dataLayer.push({ ecommerce: null });
                }
                
                // Prepare add_to_cart event in GA4 format
                var addToCartData = {
                    event: 'add_to_cart',
                    ecommerce: {
                        currency: '<?php echo get_woocommerce_currency(); ?>',
                        value: totalValue,
                        items: [ga4Item]
                    }
                };
                
                // Push to dataLayer
                if (typeof window.dataLayer !== 'undefined') {
                    window.dataLayer.push(addToCartData);
                    
                    if (typeof console !== 'undefined' && console.log) {
                        console.log('Custom Page Product Tracking - Add to Cart event:', addToCartData);
                    }
                }
                
                // NO interference with button behavior
                // The button will do its normal job (AJAX, redirect, etc.)
            });
            
        })(jQuery);
        </script>
        <?php
    }
}

// Initialize plugin
function cppt_ga4_init() {
    Custom_Page_Product_Tracking_GA4::get_instance();
}
add_action( 'plugins_loaded', 'cppt_ga4_init' );
