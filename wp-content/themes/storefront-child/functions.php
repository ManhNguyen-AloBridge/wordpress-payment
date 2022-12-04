<?php
add_action('wp_enqueue_scripts', 'enqueue_parent_styles');
function enqueue_parent_styles()
{
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
}

function get_template_child_directory()
{
    $template     = 'storefront-child';
    $theme_root   = get_theme_root_uri($template);
    $template_dir = "$theme_root/$template";

    return apply_filters('template_directory', $template_dir, $template, $theme_root);
}

function prefix_update_existing_cart_item_meta()
{
    $cart = WC()->cart->cart_contents;

    if (isset($_REQUEST)) {
        $quantity = $_REQUEST['quantity'];
    }

    foreach ($cart as $cart_item_id => $cart_item) {
        $cart_item['quantity'] = $quantity;
        WC()->cart->cart_contents[$cart_item_id] = $cart_item;
    }
    WC()->cart->set_session();
}

add_action('wp_ajax_nopriv_prefix_update_existing_cart_item_meta', 'prefix_update_existing_cart_item_meta');
add_action('wp_ajax_prefix_update_existing_cart_item_meta', 'prefix_update_existing_cart_item_meta');


wp_enqueue_script('main', plugins_url('js/jquery.main.js'), array('jquery'), '', true);

wp_localize_script('main', 'ipAjaxVar', array(
    'ajaxurl' => admin_url('admin-ajax.php')
));

function hook_payment_ajax()
{
    wp_enqueue_script('script-checker', plugin_dir_url(__FILE__) . 'js/script-checker.js');
    wp_localize_script(
        'script-checker',
        'ipAjaxVar',
        array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'fail_message' => __('Connection to server failed. Check the mail credentials.', 'script-checker'),
            'success_message' => __('Connection successful. ', 'script-checker')
        )
    );
    // wp_enqueue_script('script-checker', plugins_url('js/jquery.main.js'), array('jquery'), '', true);
    // wp_localize_script('script-checker', 'ipAjaxVar', array(
    //     'ajaxurl' => admin_url('admin-ajax.php')
    // ));
}
add_action('enqueue_scripts', 'hook_payment_ajax');
add_action('admin_enqueue_scripts', 'hook_payment_ajax');

function payment_method_ajax()
{
    var_dump("Aloha");
}
add_action('wp_ajax_nopriv_payment_method_ajax', 'payment_method_ajax');
add_action('wp_ajax_payment_method_ajax', 'payment_method_ajax');




function set_price_in_cart(int $quantity = 1, ?array $cart): ?int
{
    foreach ($cart as $cart_item_id => $cart_item) {
        $paymentOnce = true;

        if ($paymentOnce) {
            $cart_item['product_id'] = 13;
        } else {
            $cart_item['product_id'] = 43;
        }

        $quantity = intval($cart_item['quantity']);


        WC()->cart->cart_contents[$cart_item_id] = $cart_item;
    }
    WC()->cart->set_session();

    return $quantity;
}

function get_list_price_non_subscription(array $products, array $array = []): array
{
    foreach ($products as $key => $product) {
        if ($product->slug == KEY_WORD_PRICE_ONCE) {
            $priceListNonSubscription['once'] = $product->price;
        }

        if ($product->slug == KEY_WORD_PRICE_DEFAULT) {
            $priceListNonSubscription['default'] = $product->price;
        }
    }

    return $array;
}

function get_customer_id_by_user_email()
{
    global $wpdb;
    $email = 'asd@gmail.com';

    $customerId = $wpdb->get_var($wpdb->prepare("
        SELECT customer_id FROM {$wpdb->prefix}wc_customer_lookup
        WHERE email = %s
    ", $email));

    return $customerId;
}


// var_dump(ABSPATH . 'vendor/stripe-php/init.php');
require_once ABSPATH . 'vendor/stripe-php/init.php';
require_once ABSPATH . 'vendor/autoload.php';


function get_list_customer_id()
{
    $stripe = new \Stripe\StripeClient(SECRET_KEY);

    $listCustomer = $stripe->customers->all();
    $listCustomerId = [];

    foreach ($listCustomer as $customer) {
        if ($customer->email) {
            // var_dump($customer);
            echo '<br/>';
            // var_dump($customer->name);
            echo '<br/>';
            array_push($listCustomerId, $customer->id);
        }
    }

    var_dump($listCustomerId);
    // get_card_by_customer_id($listCustomerId[1]);

}


function get_card_by_customer_id(string $customerId)
{
    $stripe = new \Stripe\StripeClient(SECRET_KEY);

    $listCard = $stripe->customers->allSources(
        $customerId,
        ['object' => 'card']
    );

    var_dump($listCard);
}
