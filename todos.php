<?php
error_reporting(E_ALL);


require __DIR__ . '/vendor/autoload.php';

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;


$woocommerce = new Client(
    'https://www.arignon.com.ar',
    'ck_5e9e49b954cfc51d346cc854362b37d00c2f3dfe',
    'cs_eb89e854d410556a8eafb0c67074b05e2e63d055',
    [
        'wp_api' => true,
        'version' => 'wc/v2',
       // 'query_string_auth' => true
    ]
);
$TODOS = $woocommerce->get('products');
echo '<pre>'   ;
echo json_encode($TODOS);
echo '</pre>'   ;exit;
foreach ( $TODOS as $prod=>$data ) 
{
    echo '<pre>'   ;
    var_export($data);
    echo '</pre>'   ;
    //echo "<hr>  $data->id   $data->sku $data->name ";
}
