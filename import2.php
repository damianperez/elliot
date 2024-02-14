<?php

define( 'FILE_TO_IMPORT', 'db/products.json' );

require __DIR__ . '/vendor/autoload.php';

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;
error_reporting(E_ALL);
if ( ! file_exists( FILE_TO_IMPORT ) ) :
	die( 'Unable to find ' . FILE_TO_IMPORT );
endif;	




function getWoocommerceConfig()
{

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

    return $woocommerce;
}



/**
 * Parse JSON file.
 *
 * @param  string $file
 * @return array
 */
function getJsonFromFile()
{
    $file = 'db/products.json';
    $json = json_decode(file_get_contents($file), true);
    return $json;
}


function checkProductBySku($skuCode)
{
    $woocommerce = getWoocommerceConfig();

    $products = $woocommerce->get('products');

    foreach ($products as $product)
        {
        $currentSku = strtolower($product['sku']);
        $skuCode = strtolower($skuCode);
        if ($currentSku === $skuCode)
            {
            return ['exist' => true, 'idProduct' => $product['id']];
        }
    }

    return ['exist' => false, 'idProduct' => null];
}
/// talla
function getproductAtributesNames($articulos)
{
 
 /*'attributes' => array(
                    array(
                        'name' => 'talla',
                        'slug' => 'attr_var_mattress_hardness',
                        'visible' => true,
                        'variation' => true,
                        'options' => array(
                            'hard', 'medium', 'soft'
                        )
                    )
                )*/


    $keys = array();


    foreach ($articulos as $articulo)
        {

        $terms = $articulo['config'];
        foreach ($terms as $key => $term)
            {
            array_push($keys, $key);
        }


    }
   /* remove repeted keys*/
    $keys = array_unique($keys);
    $configlist = array_column($articulos, 'config');


    $options = array();
    foreach ($keys as $key)
        {
   /*(getTermsByKeyName($key, $configlist));*/
        $attributes = array(
            array(
                'name' => $key,
                'slug' => 'attr_' . $key,
                'visible' => true,
                'variation' => true,
                'options' => getTermsByKeyName($key, $configlist)
            )
        );

    }

    return $attributes;

}


function getTermsByKeyName($keyName, $configList)
{
 //var_dump($configList);
    $options = array();
    foreach ($configList as $config)
        {


        foreach ($config as $key => $term)
            {
            if ($key == $keyName)
                {
                array_push($options, $term);
            }
        }

    }
    return $options;
}



function createProducts()
{
    $woocommerce = getWoocommerceConfig();

    $products = getJsonFromFile();

// create categories
    $imgCounter = 0;
    foreach ($products as $product)
        {
  /*Chec sku before create the product */
        $productExist = checkProductBySku($product['sku']);

        $imagesFormated = array();
     /*Main information */
        $name = $product['titulo'];
        $slug = $product['url'];
        $sku = $product['sku'];
        $description = $product['desc'];
        $images = $product['pics'];
        $articulos = $product['articulos'];
        $categories = $product['categorias'];

        $categoriesIds = array();
        foreach ($images as $image)
            {
            $imagesFormated[] = [
                'src' => $image,
                'position' => 0
            ]; /* TODO: FIX POSITON */
            $imgCounter++;
        } 

  /* Prepare categories */
        foreach ($categories as $category)
            {
            $categoriesIds[] = ['id' => getCategoryIdByName($category)];
        }
        $finalProduct = [
            'name' => $name,
            'slug' => $slug,
            'sku' => $sku,
            'description' => $description,
            'images' => $imagesFormated,
            'categories' => $categoriesIds,
            'attributes' => getproductAtributesNames($articulos)

        ];


        if (!$productExist['exist'])
            {
            $productResult = $woocommerce->post('products', $finalProduct);
        }
        else
            {
   /*Update product information */
            $idProduct = $productExist['idProduct'];
            $woocommerce->put('products/' . $idProduct, $finalProduct);
        }


    }
}

function updateProduct()
{
    $woocommerce = getWoocommerceConfig();

    $data = [
        'regular_price' => '24.54'
    ];

    print_r($woocommerce->put('products/794', $data));

}





function createProductVariations($productId, $variations)
{
    $woocommerce = getWoocommerceConfig();
 /*
  array(5) {
    ["precio"]=>
    float(28.95)
    ["descuento"]=>
    int(10)
    ["impuesto"]=>
    int(13)
    ["existencias"]=>
    int(0)
    ["config"]=>
    array(1) {
     ["talla"]=>
     string(1) "M"
    }
 }
     */

    foreach ($variations as $variation)
        {
        $attributes[] = $variation['config'];
    }


    $data = [
  /*'sale_price' => $variation['precio'],*/
        'stock_quantity' => $variation['existencias'],
        'attributes' => $attributes,
    ];

    $woocommerce->post('products/' . $productId . '/variations', $data);
}

function getAtributeId($attributeName)
{
    $woocommerce = getWoocommerceConfig();
    $attributes = $woocommerce->get('products/attributes');
    foreach ($attributes as $attribute)
        {
        if ($attribute['name'] === $attributeName)
            {
            return $attribute['id'];
        }
    }
}
function checkAttributes($attributeName)
{
    $woocommerce = getWoocommerceConfig();
    $currentattributes = $woocommerce->get('products/attributes');
    foreach ($currentattributes as $currentattribute)
        {
        $attributeSlug = strtolower($currentattribute['name']);
        $attributeName = strtolower($attributeName);
        if ($attributeSlug === $attributeName)
            {
            return true;
        }
    }

    return false;
}


function createAttributes()
{
    $woocommerce = getWoocommerceConfig();
    $products = getJsonFromFile();
    foreach ($products as $product)
        {
        $articulos = $product['articulos'];


        foreach ($articulos as $config)
            {
            $attributeNames = array_keys($config['config']);

            foreach ($attributeNames as $attributenName)
                {
                if (checkAttributes($attributenName) == false)
                    {
                    $data = [
                        'name' => $attributenName,
                        'slug' => $attributenName,
                        'type' => 'select',
                        'order_by' => 'menu_order',
                        'has_archives' => true
                    ];
                    $resultAttributes = $woocommerce->post('products/attributes', $data);
                    createAtrributeTerms($resultAttributes['id'], $attributenName);
                }
            }
        }
    }
}

function createAtrributeTerms($idTerm, $attributeName)
{
    $woocommerce = getWoocommerceConfig();
    $products = getJsonFromFile();
    $articulos = array_column($products, 'articulos');

    foreach ($articulos as $articulo)
        {
        foreach ($articulo as $config)
            {
            $configurations = $config['config'];

            foreach ($configurations as $config)
                {
                $listTerms[] = $config;
            }
        }
    }
    $listTerms = array_unique($listTerms);
    foreach ($listTerms as $term)
        {
        $data = [
            'name' => $term
        ];
        $woocommerce->post('products/attributes/' . $idTerm . '/terms', $data);
    }
}




/** CATEGORIES  **/
function getCategories()
{
    $products = getJsonFromFile();
    $categories = array_column($products, 'categorias');

    foreach ($categories as $categoryItems)
        {
        foreach ($categoryItems as $categoryValue)
            {
            $categoryPlainValues[] = $categoryValue;

        }
    }
    $categoryList = array_unique($categoryPlainValues);
    return $categoryList;
}


function createCategories()
{
    $categoryValues = getCategories();
    $woocommerce = getWoocommerceConfig();
 
 /* Add a category verificator */
    foreach ($categoryValues as $value)
        {
        if (!checkCategoryByname($value))
            {
            $data = [
                'name' => $value
            ];
            print_r($woocommerce->post('products/categories', $data));
        }
    }
}

function checkCategoryByName($categoryName)
{
    $woocommerce = getWoocommerceConfig();
    $categories = $woocommerce->get('products/categories');
    foreach ($categories as $category)
        {
        if ($category['name'] === $categoryName)
            {
            return true;
        }
    }
    return false;
}

function getCategoryIdByName($categoryName)
{
    $woocommerce = getWoocommerceConfig();
    $categories = $woocommerce->get('products/categories');
    foreach ($categories as $category)
        {
        if ($category['name'] == $categoryName)
            {
            return $category['id'];
        }
    }
}
function assignCategoriestoProduct($idproduct, $idcategory)
{

}


function prepareInitialConfig()
{
    /**$woocommerce= getWoocommerceConfig(); 45
 print_r($woocommerce->get('products/attributes'));*/

    echo ('<h1>Importing data....</h1>');
    echo ('<br>');
 /*createAttributes();*/
 /* Create categories */
    createCategories();
 /* get all categories*/
    createProducts();
    echo ('<br>');
    echo ('</h1>Done!</h1>');
}

prepareInitialConfig();


?>