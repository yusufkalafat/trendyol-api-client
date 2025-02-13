# 📦 Trendyol API PHP İstemcisi

Bu PHP kütüphanesi, Trendyol API ile entegrasyonu kolaylaştırır. Siparişleri listeleyebilir, stokları güncelleyebilir ve ürün fiyatlarını değiştirebilirsiniz.

## 🚀 Kurulum

```sh
git clone https://github.com/yusufkalafat/trendyol-api-client.git
cd trendyol-api-client
```

Veya Composer ile:

```sh
composer require yusufkalafat/trendyol-api-client
```

## 🔧 Kullanım

```php
require 'src/Trendyol.php';

$trendyol = new Trendyol('SUPPLIER_ID', 'API_KEY', 'API_SECRET');

// Siparişleri getir
$orders = $trendyol->getOrders('Delivered');
print_r($orders);
```

## 📜 Lisans

Bu proje, MIT Lisansı altında dağıtılmaktadır.