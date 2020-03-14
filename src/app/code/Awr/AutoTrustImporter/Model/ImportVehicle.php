<?php

namespace Awr\AutoTrustImporter\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Interceptor;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Model\AbstractModel;
use Zend\Http\Client;
use Zend\Http\Headers;
use Zend\Http\Request;

class ImportVehicle extends AbstractModel
{
    const AUTH = 'WELCOME$1';
    const APPNAME = 'AUTOTRUST_WEBSITE';
    const CONTENTTYPE = 'application/json';
    const URI = 'https://autotrustwebsrvtst.awrostamani.ae/AutotrustProxy/resources/autotrustProxy/getVehiclesProxy';
    const METHOD = Request::METHOD_GET;

    private function prepareRequest(): Request
    {
        $request = new Request();
        $headers = new Headers();
        $headers->addHeaders(
            [
                'auth' => self::AUTH,
                'appName' => self::APPNAME,
                'Content-Type' => self::CONTENTTYPE,
                'Accept' => self::CONTENTTYPE
            ]
        );
        $request->setHeaders($headers);
        $request->setUri(self::URI);
        $request->setMethod(self::METHOD);

        return $request;
    }

    public function getVehicles()
    {
        $client = new Client();
        $options = [
            'adapter' => 'Zend\Http\Client\Adapter\Curl',
            'curloptions' => [CURLOPT_FOLLOWLOCATION => true],
            'maxredirects' => 0,
            'timeout' => 120
        ];
        $client->setOptions($options);
        $response = $client->send($this->prepareRequest());

        if (!empty($response->getBody())) {
            return json_decode($response->getBody(), true);
        }
        return null;
    }

    private function mockData()
    {
        return json_decode(Data::$sample, true);
    }

    public function save()
    {
        $data = $this->getVehicles();
        if (empty($data) || empty($data['vehicles'])) {
            echo "Nothing to process";
            die;
        }
        $objectManager = ObjectManager::getInstance();
        /** @var $productModel ProductRepositoryInterface */
        $productModel = $objectManager->create('\Magento\Catalog\Api\ProductRepositoryInterface');
        foreach ($data['vehicles'] as $vehicle) {
            /** @var $product Interceptor */
            try {
                $product = $productModel->get($vehicle['itemCode']);
            } catch (\Exception $e) {
                $product = $objectManager->create('\Magento\Catalog\Model\Product');
            }
            if (empty($product->getCategoryIds())) {
                $product->setCategoryIds([3]);
            }
            $product->setSku($vehicle['itemCode']);
            $product->setName(str_replace('.', '. ', $vehicle['attribute1']));
            $product->setPrice($vehicle['vhlPrice']);
            $product->setVisibility(4);
            $product->setTaxClassId(0);
            $product->setTypeId('simple');
            if (empty($product->getAttributeSetId())) {
                $product->setAttributeSetId(4);
            }
            if ($product->getStatus() != 0) {
                $product->setStatus('1');
            }
            if (!empty($vehicle['reservationId']) || !empty($vehicle['reservedFlag'])) {
                $stock = [
                    'use_config_manage_stock' => 0,
                    'manage_stock' => 1,
                    'is_in_stock' => 1,
                    'qty' => 0
                ];
            } else {
                $stock = [
                    'use_config_manage_stock' => 0,
                    'manage_stock' => 1,
                    'is_in_stock' => 1,
                    'qty' => 1
                ];
            }
            if (!empty($vehicle['newOfferPrice'])) {
                $product->setSpecialPrice($vehicle['newOfferPrice']);
            } else {
                $product->setSpecialPrice('');
            }
            $product->setStockData($stock);
            foreach ($vehicle as $key => $value) {
                $product->setCustomAttribute($key, $value);
            }
            $product->setWebsiteIds([1]);
            if (empty($product->getImage()) && $this->downloadImageFromUrl($vehicle['thumbnailImage'], $vehicle['itemCode'])) {
                try {
                    $product->addImageToMediaGallery($this->getImageTmpDir() . basename($vehicle['thumbnailImage']), ['image', 'small_image', 'thumbnail'], false, false);
                } catch (\Exception $e) {
                    echo $e->getMessage();
                    die;
                }
            }
            try {
                $product->save();
            } catch (\Exception $e) {
                print_r($e->getMessage());
                echo 'item code : ' . $vehicle['itemCode'] . PHP_EOL;
            }
//            print_r($vehicle);
//            die;
        }
        echo 'done';
        die;
    }

    private function downloadImageFromUrl(string $url, string $itemCode): bool
    {
        if (!empty($url)) {
            try {
                $file = new File();

                $filename = basename($url);
                $path = $this->getImageTmpDir() . $filename;
                $result = $file->read($url, $path);
                if ($result) {
                    return true;
                }
            } catch (\Exception $e) {
            }
            echo 'Image fetch failed' . PHP_EOL;
            echo 'image path : ' . $url . PHP_EOL;
            echo 'item code : ' . $itemCode . PHP_EOL;

            return false;
        }
        return false;
    }

    private function getImageTmpDir()
    {
        $objectManager = ObjectManager::getInstance();
        /** @var DirectoryList $dir */
        $dir = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');

        return $dir->getPath('media') . DIRECTORY_SEPARATOR . 'tmp/';
    }
}
