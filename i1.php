<?php
require __DIR__ . '/vendor/autoload.php';
 
use Automattic\WooCommerce\Client;
 
$woocommerce = new Client(
    'http://arignon.com.ar', 
    'ck_cc8b27189722b83350e427a01b2f44a08be77f51',
    'cs_f1b31898ffaa23cd27cbfbfb3c4baf637c2e4ffe',
    [
        'wp_api' => true,
        'version' => 'wc/v2',
    ]
);
