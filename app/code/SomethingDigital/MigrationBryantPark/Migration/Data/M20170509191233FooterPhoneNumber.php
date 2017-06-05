<?php

namespace SomethingDigital\MigrationBryantPark\Migration\Data;

use Magento\Framework\Setup\SetupInterface;
use SomethingDigital\Migration\Api\MigrationInterface;
use SomethingDigital\Migration\Helper\Cms\Page as PageHelper;
use SomethingDigital\Migration\Helper\Cms\Block as BlockHelper;
use SomethingDigital\Migration\Helper\Email\Template as EmailHelper;

class M20170509191233FooterPhoneNumber implements MigrationInterface
{
    protected $page;
    protected $block;
    protected $email;

    public function __construct(PageHelper $page, BlockHelper $block, EmailHelper $email)
    {
        $this->page = $page;
        $this->block = $block;
        $this->email = $email;
    }

    public function execute(SetupInterface $setup)
    {
        $this->block->create('footer_phone_number', 'Footer Phone Number', '{{config path="general/store_information/phone"}}');
    }
}
