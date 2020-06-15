<?php

namespace Earthlite\ProductVideo\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Youtube
{
    /**
     * Youtube API endpoint URL.
     */
    const YOUTUBE_API_ENDPOINT = 'https://www.googleapis.com/youtube/v3/videos';

    /**
     * @var array
     */
    protected $_allowedMimeTypes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'png' => 'image/png',
    ];

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Update product video to the specified product.
     * NOTE: Bearer token is hard-coded.
     *
     * @param string $sku
     * @param string $youtubeLink
     * @return bool
     */
    public function updateProductVideo($sku, $youtubeLink)
    {
        $isSkuExist = $this->checkSkuExist($sku);
        $queryString = parse_url($youtubeLink, PHP_URL_QUERY);
        parse_str($queryString, $params);

        if ($isSkuExist && isset($params['v'])) {
            $isVideoExist = $this->checkVideoAlreadyExist($sku, $youtubeLink);
            if ($isVideoExist) {
                echo "</br>Product video not updated. SKU = ".$sku;
                return false;
            }
            $videoData = $this->getVideoInfo($params['v']);

            if ($videoData != false && is_array($videoData)) {
                $data = [
                    'entry' => [
                        'id' => null,
                        'media_type' => 'external-video',
                        'label' => null,
                        'position' => 3,
                        'types' => ['image', 'small-image', 'thumbnail'],
                        'disabled' => false,
                        'content' => [
                            'base64_encoded_data' => base64_encode(file_get_contents($videoData['thumbnail'])),
                            'type' => $videoData['imageType'],
                            'name' => $videoData['imageName']
                        ],
                        'extension_attributes' => [
                            'video_content' => [
                                'media_type' => 'external-video',
                                'video_provider' => 'youtube',
                                'video_url' => $youtubeLink,
                                'video_title' => $videoData['title'],
                                'video_description' => $videoData['description'],
                                'video_metadata' => null
                            ]
                        ]
                    ]
                ];
                $ch = curl_init($this->storeManager->getStore()->getBaseUrl() . "rest/all/V1/products/" . $sku . "/media");
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer 43getqzg8jw3ivnif3tlodrobk12macc"));
                $response = curl_exec($ch);
                $result = json_decode($response, true);

                if (is_string($result)) {
                    echo "</br>Product video updated. SKU = ".$sku;
                    return true;
                }
            }
        }
        echo "</br>Product video not updated. SKU = ".$sku;
        return false;
    }

    /**
     * Check whether the youtube video
     * already associated with the product or not.
     * NOTE: Bearer token is hard-coded.
     *
     * @param string $sku
     * @param string $videoUrl
     * @return bool
     */
    public function checkVideoAlreadyExist($sku, $videoUrl)
    {
        $ch = curl_init($this->storeManager->getStore()->getBaseUrl() . "rest/all/V1/products/" . $sku);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer 43getqzg8jw3ivnif3tlodrobk12macc"));
        $response = curl_exec($ch);
        $result = json_decode($response, true);

        try {
            if (isset($result['media_gallery_entries'])) {
                foreach ($result['media_gallery_entries'] as $media) {
                    if ($media['media_type'] == 'external-video'
                    && $media['extension_attributes']['video_content']['video_url'] == $videoUrl
                    ) {
                        return true;
                    }
                }
            }
        } catch (\Exception $e) {
            return false;
        }
        return false;
    }

    /**
     * Check SKU is exist or not.
     * NOTE: Bearer token is hard-coded.
     *
     * @param string $sku
     * @return bool
     */
    public function checkSkuExist($sku)
    {
        $ch = curl_init($this->storeManager->getStore()->getBaseUrl() . "rest/all/V1/products/" . $sku);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer 43getqzg8jw3ivnif3tlodrobk12macc"));
        $response = curl_exec($ch);
        $result = json_decode($response, true);

        if (isset($result['id'])) {
            return true;
        }
        return false;
    }

    /**
     * Get Video Information from Youtube API
     *
     * @param string $videoId
     * @return mixed
     */
    public function getVideoInfo(string $videoId)
    {
        $apiKey = $this->scopeConfig->getValue(
            'catalog/product_video/youtube_api_key',
            ScopeInterface::SCOPE_STORE
        );
        if (!$apiKey) {
            return false;
        }
        $endpointUrl = self::YOUTUBE_API_ENDPOINT
            .'?id='.$videoId.'&part=snippet,contentDetails,statistics,status&key='
            .$apiKey.'&alt=json&callback=';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $endpointUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        $result = json_decode($response, true);
        curl_close($curl);

        if (isset($result['error']['code'])
            || isset($result['pageInfo']['totalResults'])
            && $result['pageInfo']['totalResults'] == 0
        ) {
            return false;
        }

        if ($result['pageInfo']['totalResults'] > 0) {
            $name = pathinfo($result['items'][0]['snippet']['thumbnails']['high']['url'], PATHINFO_FILENAME);
            $extension = pathinfo($result['items'][0]['snippet']['thumbnails']['high']['url'], PATHINFO_EXTENSION);

            if (isset($this->_allowedMimeTypes[$extension])) {
                $videoData = [
                    'channel' => $result['items'][0]['snippet']['channelTitle'],
                    'channelId' => $result['items'][0]['snippet']['channelId'],
                    'title' => $result['items'][0]['snippet']['title'],
                    'description' => $result['items'][0]['snippet']['description'],
                    'thumbnail' => $result['items'][0]['snippet']['thumbnails']['high']['url'],
                    'imageName' => $name .".". $extension,
                    'imageType' => $this->_allowedMimeTypes[$extension],
                    'videoId' => $videoId,
                    'videoProvider' => "youtube",
                    'useYoutubeNocookie' => false
                ];
                return $videoData;        
            }
        }
        return false;
    }
}
