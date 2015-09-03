<?php
echo "<pre>";
//////////////////////////////////////////////////////////
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once MAGENTO_BASE_URL.'app/Mage.php';
Mage::app();
//////////////////////////////////////////////////////////

$existing_product_skus_in_magento = array();

foreach(Mage::getModel('catalog/product')->getCollection() as $product)
{
    $existing_product_skus_in_magento[] = $product->getSku();
}

echo "There are " . count($existing_product_skus_in_magento) . ' products in the database';
echo " IT WORKS";



echo "<br /><br /><br /><br />";
die('WE DIED IN TESTFILE1.php'); 




//TODAY FTP PWD : RkTY9H