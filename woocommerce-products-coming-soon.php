<?php
/**
 * Plugin Name: Woocommerce Products Coming Soon
 * Plugin URI: http://glorywebs.com/woocommerce_products_coming_soon
 * Description: Plugin for Woocommerce Products "Coming Soon" mode with countdown timer. This plugin shows coming soon for products till the date entered.
 * Version: 1.0
 * Author: Glorywebs
 * Author URI: http://glorywebs.com
 * Requires at least: 4.4
 * Tested up to: 4.7
 *
 * Text Domain: woocommerce-products-coming-soon
 * Domain Path: /languages/
 *
 * @package woocommerce-products-coming-soon
 * @author Glorywebs
 */
 

define( 'WPCS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'WPCS_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );
define( 'WPCS_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );
define( 'WPCS_CONTENT_URL',  content_url( ));
require_once WPCS_PLUGIN_DIR . '/settings.php';