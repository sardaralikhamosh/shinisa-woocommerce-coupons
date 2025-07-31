<?php
/**
 * Plugin Name: SHINISA WooCommerce Coupon Display
 * Plugin URI: https://github.com/shinisa-woocommerce-coupons
 * Description: Displays active WooCommerce coupons with Instagram redirect buttons on offers page.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://shinisa.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: shinisa-coupons
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function shinisa_display_active_coupons() {
    $output = '';
    $today = current_time('timestamp');
    $args = array(
        'post_type' => 'shop_coupon',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    );
    $coupons = get_posts($args);

    if ($coupons) {
        foreach ($coupons as $coupon_post) {
            $coupon = new WC_Coupon($coupon_post->post_title);
            $expiry = $coupon->get_date_expires();
            $discount_type = $coupon->get_discount_type();
            $amount = $coupon->get_amount();

            $output .= '<div class="shinisa-coupon-box">';
            $output .= '<strong>Coupon Code:</strong> ' . esc_html($coupon->get_code()) . ' ';
            $output .= '<button class="show-password-btn" onclick="window.open(\'https://www.instagram.com/shinisaofficial/\', \'_blank\')">Show Password</button><br>';
            $output .= '<strong>Discount:</strong> ' . esc_html($amount) . ($discount_type === 'percent' ? '% OFF' : ' PKR OFF') . '<br>';
            $output .= '<strong>Description:</strong> ' . esc_html($coupon_post->post_excerpt) . '<br>';

            if ($expiry && $expiry->getTimestamp() < $today) {
                $output .= '<span class="coupon-status expired">Status: Expired</span>';
            } else {
                $output .= '<span class="coupon-status active">Status: Active</span>';
            }

            $output .= '</div>';
        }
    } else {
        $output = '<p>No coupons available at the moment.</p>';
    }

    return $output;
}
add_shortcode('shinisa_coupons', 'shinisa_display_active_coupons');

// Enqueue styles
function shinisa_coupons_styles() {
    wp_enqueue_style(
        'shinisa-coupons-style',
        plugins_url('assets/css/style.css', __FILE__)
    );
}
add_action('wp_enqueue_scripts', 'shinisa_coupons_styles');