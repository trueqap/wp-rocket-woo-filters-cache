<?php
/**
 * Plugin Name: WP Rocket WooCommerce Filters Cache
 * Plugin URI: https://github.com/trueqap/wp-rocket-woo-filters-cache
 * Description: Automatically adds WooCommerce attribute filters to WP Rocket cache_query_strings
 * Version: 1.0.1
 * Author: trueqap
 * License: GPL v2 or later
 * Text Domain: wp-rocket-woo-filters-cache
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WP_Rocket_Woo_Filters_Cache {
    
    private $filter_start_marker = '# filter_start';
    private $filter_end_marker = '# filter_end';
    
    public function __construct() {
        add_action( 'init', array( $this, 'init' ) );
        add_action( 'woocommerce_attribute_added', array( $this, 'update_filters' ) );
        add_action( 'woocommerce_attribute_updated', array( $this, 'update_filters' ) );
        add_action( 'woocommerce_attribute_deleted', array( $this, 'update_filters' ) );
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
    }
    
    public function init() {
        if ( ! class_exists( 'WooCommerce' ) ) {
            add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
            return;
        }
        
        if ( ! function_exists( 'rocket_clean_domain' ) ) {
            add_action( 'admin_notices', array( $this, 'wp_rocket_missing_notice' ) );
            return;
        }
    }
    
    public function woocommerce_missing_notice() {
        ?>
        <div class="notice notice-error">
            <p><?php _e( 'WP Rocket WooCommerce Filters Cache requires WooCommerce to be installed and active.', 'wp-rocket-woo-filters-cache' ); ?></p>
        </div>
        <?php
    }
    
    public function wp_rocket_missing_notice() {
        ?>
        <div class="notice notice-error">
            <p><?php _e( 'WP Rocket WooCommerce Filters Cache requires WP Rocket to be installed and active.', 'wp-rocket-woo-filters-cache' ); ?></p>
        </div>
        <?php
    }
    
    public function get_woocommerce_filters() {
        $filters = array();
        
        if ( ! function_exists( 'wc_get_attribute_taxonomies' ) ) {
            return $filters;
        }
        
        $attribute_taxonomies = wc_get_attribute_taxonomies();
        
        foreach ( $attribute_taxonomies as $attribute ) {
            $filters[] = 'filter_' . $attribute->attribute_name;
            $filters[] = 'query_type_' . $attribute->attribute_name;
        }
        
        $filters[] = 'min_price';
        $filters[] = 'max_price';
        $filters[] = 'rating_filter';
        $filters[] = 'orderby';
        $filters[] = 'product_cat';
        $filters[] = 'product_tag';
        $filters[] = 'per_page';
        $filters[] = 'columns';
        $filters[] = 'sort';
        
        return array_unique( $filters );
    }
    
    public function update_filters() {
        $option = get_option( 'wp_rocket_settings' );
        
        if ( ! is_array( $option ) ) {
            $option = array();
        }
        
        if ( ! isset( $option['cache_query_strings'] ) || ! is_array( $option['cache_query_strings'] ) ) {
            $option['cache_query_strings'] = array();
        }
        
        $existing_strings = $option['cache_query_strings'];
        $new_filters = $this->get_woocommerce_filters();
        
        $start_index = array_search( $this->filter_start_marker, $existing_strings );
        $end_index = array_search( $this->filter_end_marker, $existing_strings );
        
        if ( $start_index !== false && $end_index !== false && $end_index > $start_index ) {
            $before = array_slice( $existing_strings, 0, $start_index );
            $after = array_slice( $existing_strings, $end_index + 1 );
            
            $new_cache_strings = array_merge(
                $before,
                array( $this->filter_start_marker ),
                $new_filters,
                array( $this->filter_end_marker ),
                $after
            );
        } else {
            if ( $start_index !== false || $end_index !== false ) {
                $existing_strings = array_filter( $existing_strings, function( $item ) {
                    return $item !== $this->filter_start_marker && $item !== $this->filter_end_marker;
                });
            }
            
            $new_cache_strings = array_merge(
                $existing_strings,
                array( $this->filter_start_marker ),
                $new_filters,
                array( $this->filter_end_marker )
            );
        }
        
        $new_cache_strings = array_values( array_unique( $new_cache_strings ) );
        
        $option['cache_query_strings'] = $new_cache_strings;
        
        update_option( 'wp_rocket_settings', $option );
        
        if ( function_exists( 'rocket_clean_domain' ) ) {
            rocket_clean_domain();
        }
    }
    
    public function activate() {
        $this->update_filters();
    }
}

new WP_Rocket_Woo_Filters_Cache();