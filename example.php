<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 20.02.2020
 * Time: 1:03
 */

require 'RealtyCloudApi.php';

$apiKey = "XXXXX-XXXXX-XXXXX-XXXXX";

$realtyCloud = new RealtyCloudApi($apiKey);

$searchResult = $realtyCloud->search('77:04:0002010:1101');

$products = $realtyCloud->getProducts();

$orders = $realtyCloud->getOrders([
    'itemStatus' => '00000000-0000-0000-0000-000000000000'
]);
$object = $realtyCloud->getObject("77:04:0002010:1101");

$resultOrderMake = $realtyCloud->makeOrder([
    [
        "product_name" => "EgrnObject",
        "object_key" => "77:04:0002010:1101"
    ],
    [
        "product_name" => "EgrnObject",
        "object_key" => "77:04:0002013:3312",
    ]
]);

echo '<pre>';
echo '<hr>';
print_r($searchResult);
echo '<hr>';
print_r($products);
echo '<hr>';
print_r($orders);
echo '<hr>';
print_r($object);
echo '<hr>';
print_r($resultOrderMake);
echo '<hr>';
echo '</pre>';

