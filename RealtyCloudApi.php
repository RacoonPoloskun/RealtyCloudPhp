<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 20.02.2020
 * Time: 0:58
 */

class RealtyCloudApi
{
    private $apiKey;
    private $isAssoc;

    const API_URLS = [
        'products.get' => 'https://api.realtycloud.ru/products',
        'orders.make' => 'https://api.realtycloud.ru/order',
        'orders.get' => 'https://api.realtycloud.ru/orders',
        'object.get' => 'https://api.realtycloud.ru/object/',
        'search' => 'https://api.realtycloud.ru/search',
    ];

    public function __construct($apiKey, $assoc = true)
    {
        if (!$apiKey) {
            throw new Error('api key required');
        }

        $this->isAssoc = $assoc;
        $this->apiKey = $apiKey;
    }

    protected function sendCurl($url, $arData = [], $method = "POST")
    {
        $curl = curl_init();

        if (!$curl) {
            return false;
        }

        if (!is_array($arData)) {
            $arData = [$arData];
        }

        $arData = json_encode($arData, JSON_UNESCAPED_UNICODE);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json;"));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("API-Key: " . $this->apiKey));

        if ($arData) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $arData);
        }

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_ENCODING, "");

        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }

    /**
     * Продукты можно получать с API ключом и без.
     * С ключом выводит специальные предложения для пользователя в соответствие с его тарифом.
     *
     * @param bool $asoc
     * @return mixed
     */
    public function getProducts()
    {
        $response = $this->sendCurl(self::API_URLS['products.get'], [], "GET");
        return json_decode($response, $this->isAssoc);
    }

    /**
     * Список статусов заказа
     *
     * @param array $arFields
     * @param bool $asoc
     * @return mixed
     */
    public function getOrders($arFields = [])
    {
        $requestParamsMap = [
            "itemStatus",
            "limit",
            "offset",
            "creationDateFrom",
            "creationDateTo",
            "orderID",
            "orderItemID",
            "paid",
            "kadastrNumber",
            "haveKadastr",
            "productID",
        ];

        $requestFields = [];
        foreach ($arFields as $key => $value) {
            if (in_array($key, $requestParamsMap)) {
                $requestFields[$key] = $value;
            }
        }

        $url = self::API_URLS['orders.get'] . "?" . http_build_query($requestFields);
        $response = $this->sendCurl($url, [], "GET");

        return json_decode($response, $this->isAssoc);
    }

    /**
     * Создание заказа.
     * https://api.realtycloud.ru/products
     *
     * Структура $arItems
     * $arItems = [
     *      [
     *         "product_id" => "8f439998-2dba-47e6-8f25-3180cb402786",
     *         "object_key" => "77:04:0002010:1101",
     *      ],
     *      [
     *         "product_id" => "8f439998-2dba-47e6-8f25-3180cb402786",
     *         "object_key" => "77:04:0002010:1101",
     *         "coupon_id" => "cd2b200c-de79-4ddd-8ec8-4781eaa5da30"
     *      ]
     * ]
     *
     * @param $arItems - массив заказов
     * @return bool|string
     */
    public function makeOrder($arItems)
    {
        $data = [];
        foreach ($arItems as $order) {
            $arOrder = [
                'product_name' => $order['product_name'],
                'object_key' => $order['object_key'],
            ];

            if (isset($order['coupon_id'])) {
                $arOrder['coupon_id'] = $order['coupon_id'];
            }

            $data[] = $arOrder;
        }

        if (!$data) {
            return false;
        }

        $requestData['order_items'] = $data;

        $response = $this->sendCurl(self::API_URLS['orders.make'], $requestData);

        return json_decode($response, $this->isAssoc);
    }

    /**
     * Поиск по запросу
     *
     * @param $query - строка поиска
     * @return bool|mixed
     */
    public function search($query)
    {
        if (!$query) {
            return false;
        }

        $response = file_get_contents(self::API_URLS['search'] . "?query=" . $query);

        return json_decode($response, $this->isAssoc);
    }

    /**
     * Карточка объекта здесь выводится основная информация о объекте и о заказе.
     *
     * @param $strObject
     * @param bool $asoc
     * @return bool|false|string
     */
    public function getObject($strObject)
    {
        if (!$strObject) {
            return false;
        }

        $url = self::API_URLS['object.get'] . $strObject;
        $response = $this->sendCurl($url, [], "GET");

        return json_decode($response, $this->isAssoc);
    }
}































