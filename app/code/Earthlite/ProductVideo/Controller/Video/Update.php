<?php

namespace Earthlite\ProductVideo\Controller\Video;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Earthlite\ProductVideo\Model\Youtube;
use Magento\Framework\View\Asset\Repository;

/**
 * Controller to run the video update data script
 */
class Update extends Action
{
    /**
     * @var Youtube
     */
    protected $youtubeModel;

    /**
     * @var Repository
     */
    protected $assetRepo;

    /**
     * @param Context $context
     * @param Youtube $youtubeModel
     * @param Repository $asset
     */
    public function __construct(
        Context $context,
        Youtube $youtubeModel,
        Repository $asset
    ) {
        parent::__construct($context);
        $this->youtubeModel = $youtubeModel;
        $this->assetRepo = $asset;
    }

    /**
     * Product video update action.
     * @return void
     */
    public function execute()
    {
        echo "Youtube Product Update Starts</br>";
        $csvFilePath = $this->assetRepo->getUrl("Earthlite_ProductVideo::csv/el-youtube.csv");
        echo $csvFilePath."</br>";
        $fileData=fopen($csvFilePath,'r');
        $videoData = [];

        while (($line = fgetcsv($fileData)) !== FALSE) {
            $videoData[] = $line;
        }

        foreach ($videoData as $data) {
            if (isset($data[0], $data[1]) && $data[0] != 'sku') {
                $this->youtubeModel->updateProductVideo($data[0], $data[1]);
            }
        }
        echo "</br>Youtube Product Update Ends</br>";
    }
}
