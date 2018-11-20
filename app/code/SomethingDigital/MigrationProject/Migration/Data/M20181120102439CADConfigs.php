<?php

namespace SomethingDigital\MigrationProject\Migration\Data;

use Magento\Framework\Setup\SetupInterface;
use SomethingDigital\Migration\Api\MigrationInterface;
use SomethingDigital\Migration\Helper\Cms\Page as PageHelper;
use SomethingDigital\Migration\Helper\Cms\Block as BlockHelper;
use SomethingDigital\Migration\Helper\Email\Template as EmailHelper;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;

class M20181120102439CADConfigs implements MigrationInterface
{
    protected $page;
    protected $block;
    protected $email;
    protected $resourceConfig;

    public function __construct(PageHelper $page, BlockHelper $block, EmailHelper $email, ResourceConfig $resourceConfig)
    {
        $this->page = $page;
        $this->block = $block;
        $this->email = $email;
        $this->resourceConfig = $resourceConfig;
    }

    public function execute(SetupInterface $setup)
    {
        $this->setConfigs([
            'general/store_information/phone' => '800-503-0843',
            'currency/options/allow' => 'CAD',

            'trans_email/ident_general/name' => 'Travers Tool Co.',
            'trans_email/ident_general/email' => 'canada@travers.com',
            'trans_email/ident_sales/name' => 'Travers Tool Co.',
            'trans_email/ident_sales/email' => 'canada@travers.com',
            'trans_email/ident_support/name' => 'Travers Tool Co.',
            'trans_email/ident_support/email' => 'canada@travers.com',
            'trans_email/ident_custom1/name' => 'Travers Tool Co.',
            'trans_email/ident_custom1/email' => 'canada@travers.com',
            'trans_email/ident_custom2/name' => 'Travers Tool Co.',
            'trans_email/ident_custom2/email' => 'canada@travers.com',
        ]);
    }

    protected function setConfigs(array $configs)
    {
        foreach ($configs as $path => $value) {
            $this->setConfig($path, $value);
        }
    }

    protected function setConfig($path, $value)
    {
        $this->resourceConfig->saveConfig($path, $value, 'store', 'en_ca');
    }
}

