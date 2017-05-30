<?php

namespace SomethingDigital\MigrationBryantPark\Migration\Data;

use Magento\Framework\Setup\SetupInterface;
use SomethingDigital\Migration\Api\MigrationInterface;
use SomethingDigital\Migration\Helper\Cms\Page as PageHelper;
use SomethingDigital\Migration\Helper\Cms\Block as BlockHelper;
use SomethingDigital\Migration\Helper\Cms\Bluefoot as BluefootHelper;
use SomethingDigital\Migration\Helper\Email\Template as EmailHelper;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;

class M20170530183408CreatePromobarFromBluefootBlock implements MigrationInterface
{
    protected $page;
    protected $block;
    protected $bluefoot;
    protected $email;
    protected $resourceConfig;

    public function __construct(PageHelper $page, BlockHelper $block, BluefootHelper $bluefoot, EmailHelper $email, ResourceConfig $resourceConfig)
    {
        $this->page = $page;
        $this->block = $block;
        $this->bluefoot = $bluefoot;
        $this->email = $email;
        $this->resourceConfig = $resourceConfig;
    }

    public function execute(SetupInterface $setup)
    {
        
        // update CMS block "sdbp_promobar"
        $data263 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '46',
            'title' => 'Receive 10% off all sportswear with code 2017BB2.',
            'link_text' => 'Shop Now',
            'link_url' => '#',
            'target_blank' => '0',
        ];
        $bluefootEntity263 = $this->bluefoot->create($data263);

        $extraData = [
            'title' => 'Promotional Top Bar',
            'is_active' => 1,
            'stores' => [0]
        ];

        $this->block->update('sdbp_promobar', '<!--GENE_BLUEFOOT="[{"type":"row","formData":{"template":"full-width.phtml","background_color":"","background_image":"","css_classes":"bluefoot--full-width","undefined":"","align":"","metric":""},"children":[{"contentType":"promobar","entityId":"' . $bluefootEntity263 . '","formData":{"align":"","metric":""}}]}]"-->', $extraData);

    }
}
