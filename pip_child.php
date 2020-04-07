<?php
/**

 * @wordpress-plugin

 * Plugin Name:       Modify Picking List

 * Plugin URI:        https://www.moorefarmsandfriends.com

 * Description:       This is a small custom child plugin to extending functionality of Woocommerce PIP plugin.

 * Version:           1.0.0

 * Author:            Rituparna

 * Author URI:        https://twitter.com/Rituparna143

 * License:           GPL-2.0+

 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt

 * Text Domain:       mfaf

 */



## If this file is called directly, abort. ##

if ( ! defined( 'WPINC' ) ) {

	die;

}



## Check if parent plugin exists ##

if ( !is_plugin_active( 'woocommerce/woocommerce.php' ) 

	&& !is_plugin_active( 'woocommerce-pip/woocommerce-pip.php' )  ) {

	return;

}



add_filter('wc_pip_document_company_extra_info','add_extra_info', 10, 3);

function add_extra_info( $company_extra_info, $order_id, $type ) {

	if( 'packing-list' !== $type ) {

		return $company_extra_info;

	}



	$order = wc_get_order( $order_id );

	$order_data = $order->get_data();

	$customer_name = $order_data['billing']['first_name'].' '.$order_data['billing']['last_name'];

	global $wpdb;

	$order_item_id = $wpdb->get_var( "SELECT `order_item_id` FROM `wp_woocommerce_order_items` WHERE `order_item_type` = 'shipping' AND `order_id` = {$order_id}" );

	$order_pickup = wc_get_order_item_meta($order_item_id, '_pickup_location_address', false);
	$order_pickup_name = wc_get_order_item_meta($order_item_id, '_pickup_location_name', false);

	## not using at this moment, but we can fet these data too ##
	/*$pickup_loc = $order_pickup[0]['address_1'].', '.$order_pickup[0]['address_2'].', '.$order_pickup[0]['state'].' '.$order_pickup[0]['postcode'];*/

	$pickup_loc = empty($order_pickup[0]['address_1']) ? $order_pickup_name[0] : $order_pickup[0]['address_1'];

	$company_extra_info .= $customer_name.'</h5>';

	$company_extra_info .= '<h5 class="company-subtitle pick-loc">'.$pickup_loc;

	return $company_extra_info;

}


## Custom Order note, via custom checkout fields ##
add_action('wc_pip_after_body', 'pip_footer_note_2017', 10, 4);

function pip_footer_note_2017($type, $action, $document, $order) {

	$order_data = $order->get_data();

	$lordernote = get_post_meta( $order_data['id'], '_wc_acof_2', true );

	$pordernote = get_post_meta( $order_data['id'], '_wc_acof_3', true );

    if ( 'packing-list' === $type ) {

        if (!empty($lordernote)) :?>

        	<p style="color: #333333;text-transform:capitalize;-webkit-font-smoothing: antialiased;padding: 0; margin: 0;font: bold 12px/150% Verdana, Arial, Helvetica, sans-serif;">
        		<strong style="font-weight: 800;">Laurie's Order Note: </strong><?php echo $lordernote;?>
        	</p>

        <?php endif; ?>

        <?php if (!empty($pordernote)) :?>

        <p style="color: #333333;text-transform:capitalize;-webkit-font-smoothing: antialiased;padding: 0; margin: 0;font: bold 12px/150% Verdana, Arial, Helvetica, sans-serif;"><strong style="font-weight: 800;">Private Note: </strong><?php echo $pordernote; ?></p>    

    <?php endif;

	}

}
