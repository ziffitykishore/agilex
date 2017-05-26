<?php

namespace SomethingDigital\MigrationBryantPark\Migration\Data;

use Magento\Framework\Setup\SetupInterface;
use SomethingDigital\Migration\Api\MigrationInterface;
use SomethingDigital\Migration\Helper\Cms\Page as PageHelper;
use SomethingDigital\Migration\Helper\Cms\Block as BlockHelper;
use SomethingDigital\Migration\Helper\Email\Template as EmailHelper;

class M20170526143953UpdateContactBlock implements MigrationInterface
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
        $this->block->create('contact_us_content', 'Contact Us Content', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec sed metus nisl. Ut ornare nisi eget velit facilisis, eu laoreet diam maximus. Donec erat nulla, condimentum non vehicula quis, tristique at nisl. In scelerisque lacus non urna aliquam.</p>');
    }
}
