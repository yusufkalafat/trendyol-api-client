<?php

class Trendyol {
    private string $supplierId;
    private string $apiKey;
    private string $apiSecret;
    private string $apiBaseUrl = 'https://api.trendyol.com/sapigw/suppliers/';

    public function __construct(string $supplierId, string $apiKey, string $apiSecret) {
        $this->supplierId = $supplierId;
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    /**
     * API'ye istek gönderir.
     */
    private function sendRequest(string $endpoint, array $params = [], string $method = 'GET') {
        $url = $this->apiBaseUrl . $this->supplierId . '/' . $endpoint;
        
        if ($method === 'GET' && !empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        
        if (in_array($method, ['POST', 'PUT'], true)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . base64_encode($this->apiKey . ':' . $this->apiSecret),
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            throw new Exception('cURL Hatası: ' . curl_error($ch));
        }

        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("API isteği başarısız! HTTP Kod: $httpCode, Yanıt: $response");
        }

        return json_decode($response, true);
    }

    /**
     * Belirtilen durumdaki siparişleri getirir.
     */
    public function getOrders(string $status, int $daysBack = 3): array {
        $params = [
            'status' => $status,
            'startDate' => strtotime("-$daysBack days") * 1000,
            'endDate' => time() * 1000,
            'orderByField' => 'PackageLastModifiedDate',
            'orderByDirection' => 'DESC',
            'size' => 200
        ];
        return $this->sendRequest('orders', $params);
    }

    /**
     * İptal edilen siparişleri getirir.
     */
    public function getCancelledOrders(): array {
        return $this->getOrders('Cancelled');
    }

    /**
     * Kargoya verilmiş siparişleri getirir.
     */
    public function getShippedOrders(): array {
        return $this->getOrders('Delivered');
    }

    /**
     * Onaylanmış ürünleri getirir.
     */
    public function getApprovedProducts(?string $sku = null, int $size = 20, int $page = 0, string $dateQueryType = 'CREATED_DATE', ?bool $onSale = null): array {
        $params = [
            'approved' => 'true',
            'barcode' => $sku,
            'size' => $size,
            'dateQueryType' => $dateQueryType,
            'page' => $page,
            'onSale' => $onSale
        ];
        return $this->sendRequest('products', $params);
    }

    /**
     * Ürünün fiyat ve stok bilgilerini günceller.
     */
    public function updatePriceAndInventory(string $barcode, float $salePrice, float $listPrice): array {
        $data = [
            'items' => [[
                'barcode' => $barcode,
                'salePrice' => $salePrice,
                'listPrice' => $listPrice
            ]]
        ];
        return $this->sendRequest('products/price-and-inventory', $data, 'POST');
    }

    /**
     * Sadece stok günceller.
     */
    public function updateStock(string $barcode, int $stock): array {
        $data = [
            'items' => [[
                'barcode' => $barcode,
                'quantity' => $stock
            ]]
        ];
        return $this->sendRequest('products/price-and-inventory', $data, 'POST');
    }

    /**
     * Sipariş paket durumunu günceller.
     */
    public function updateShipmentPackageStatus(int $packageId, string $status = 'Picking'): array {
        $data = [
            'lines' => [['lineId' => $packageId, 'quantity' => 1]],
            'params' => [],
            'status' => $status
        ];
        return $this->sendRequest("shipment-packages/$packageId", $data, 'PUT');
    }
}
