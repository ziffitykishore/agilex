<?php

namespace SomethingDigital\BryantPark\Migration\Data;

use Magento\Framework\Setup\SetupInterface;
use SomethingDigital\Migration\Api\MigrationInterface;
use SomethingDigital\Migration\Helper\Cms\Page as PageHelper;
use SomethingDigital\Migration\Helper\Cms\Block as BlockHelper;
use SomethingDigital\Migration\Helper\Cms\Bluefoot as BluefootHelper;
use SomethingDigital\Migration\Helper\Email\Template as EmailHelper;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;

class M20170822140418AddJsHideToPromo implements MigrationInterface
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
        $extraData = [
            'title' => 'Promotional Top Bar',
            'is_active' => 1,
            'stores' => [0]
        ];

        $this->block->update('sdbp_promobar', '<div class="promobar js-hide">
                                <div class="container">
                                    Receive 10% off all sportswear with code 2017BB2. 
                                    <a href="#" class="promobar__cta">Shop Now</a>
                                    <button class="promobar__close" type="button"><svg title="Close Promo Bar" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><path d="M9.3 22.7C9.5 22.9 9.7 23 10 23s0.5-0.1 0.7-0.3l5.3-5.3 5.3 5.3c0.2 0.2 0.5 0.3 0.7 0.3s0.5-0.1 0.7-0.3c0.4-0.4 0.4-1 0-1.4L17.4 16l5.3-5.3c0.4-0.4 0.4-1 0-1.4s-1-0.4-1.4 0L16 14.6l-5.3-5.3c-0.4-0.4-1-0.4-1.4 0s-0.4 1 0 1.4l5.3 5.3 -5.3 5.3C8.9 21.7 8.9 22.3 9.3 22.7z"/></svg></button>
                                </div>
                            </div>', $extraData);

    }
}
