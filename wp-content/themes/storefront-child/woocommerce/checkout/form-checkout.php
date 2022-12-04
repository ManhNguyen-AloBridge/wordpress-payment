<?php

/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

if (!defined('ABSPATH')) {
	exit;
}

do_action('woocommerce_before_checkout_form', $checkout);

// If checkout registration is disabled and not logged in, the user cannot checkout.
if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
	echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
	return;
}



// =============================================================
global $woocommerce;

$products = wc_get_products([]);
$quantity = VALUE_QUANTITY_DEFAULT;
$cart = WC()->cart->cart_contents;



$priceListNonSubscription = get_list_price_non_subscription($products);
$quantity = set_price_in_cart($quantity, WC()->cart->cart_contents );


//get list orders
$query = new WC_Order_Query( array(
    'limit' => 50,
    'orderby' => 'date',
    'order' => 'DESC',
    'return' => 'ids',
) );
$orders = $query->get_orders();

// var_dump($orders);

$listUserId = [];

foreach($orders as $key => $orderId){
	$user_id = get_post_meta($orderId, '_customer_user', true);
	$listUserId[$key] = $user_id;
}

get_customer_id_by_user_email();

// var_dump($listUserId);

?>
<div class="checkout-page__select-quantity">
	<select id="select-quatity" class="form-select" aria-label="Default select example">
		<?php for ($i = 1; $i <= COUNT_PRODUCT_SELECT; $i++) {
			$selected = $i == $quantity ? 'selected' : '';
	
			echo ":<option value='{$i}' {$selected}>{$i}</option>";
		} ?>
	</select>
</div>



<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">

	<?php if ($checkout->get_checkout_fields()) : ?>

		<?php do_action('woocommerce_checkout_before_customer_details'); ?>

		<?php do_action('woocommerce_checkout_billing'); ?>

		<!-- <?php do_action('woocommerce_checkout_shipping'); ?> -->
		<div class="" id="customer_details">
			<div class="col-1">
			</div>

			<div class="col-2">
			</div>
		</div>

		<!-- <?php do_action('woocommerce_checkout_after_customer_details'); ?> -->

	<?php endif; ?>

	<!-- <?php do_action('woocommerce_checkout_before_order_review_heading'); ?> -->

	<h3 id=""><?php esc_html_e('Your order', 'woocommerce'); ?></h3>

	<?php do_action('woocommerce_checkout_before_order_review'); ?>

	<div id="" class="woocommerce-checkout-review-order">
		<?php do_action('woocommerce_checkout_order_review'); ?>
	</div>

	<?php do_action('woocommerce_checkout_after_order_review'); ?>

</form>


<script src="<?= get_template_child_directory() . '/assets/woocommerce/checkout/index.js' ?>"></script>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>