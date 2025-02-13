<?php
require 'src/Trendyol.php';

$trendyol = new Trendyol('SUPPLIER_ID', 'API_KEY', 'API_SECRET');

// Siparişleri getir
$orders = $trendyol->getOrders('Delivered');
print_r($orders);