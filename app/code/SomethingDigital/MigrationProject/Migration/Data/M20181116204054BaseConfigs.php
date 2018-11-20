<?php

namespace SomethingDigital\MigrationProject\Migration\Data;

use Magento\Framework\Setup\SetupInterface;
use SomethingDigital\Migration\Api\MigrationInterface;
use SomethingDigital\Migration\Helper\Cms\Page as PageHelper;
use SomethingDigital\Migration\Helper\Cms\Block as BlockHelper;
use SomethingDigital\Migration\Helper\Email\Template as EmailHelper;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;

class M20181116204054BaseConfigs implements MigrationInterface
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
            'general/store_information/name' => 'Travers Tool Co.',
            'general/store_information/phone' => '800-221-0270',
            'general/store_information/country_id' => 'US',
            'general/store_information/region_id' => '43',
            'general/store_information/postcode' => '11354',
            'general/store_information/city' => 'New York',
            'general/store_information/street_line1' => '128-15 26th Avenue',
            'general/store_information/street_line2' => '',
            'design/socialprofiles/social_facebook' => 'https://www.facebook.com/traverstool/',
            'design/socialprofiles/social_instagram' => 'https://www.instagram.com/traverstool/',
            'design/pagination/pagination_frame' => '12',
            'design/socialprofiles/social_twitter' => 'https://twitter.com/traverstool',
            'design/socialprofiles/social_pinterest' => 'https://www.pinterest.com/traverstoolco/',
            'design/footer/copyright' => 'Copyright © {{ YEAR }} Travers Tool Co. All rights reserved.',

            'currency/options/allow' => 'USD',
            'currency/import/error_email' => 'project-traverstool@somethingdigital.onmicrosoft.com',

            'system/magento_scheduled_import_export_log/error_email' => 'project-traverstool@somethingdigital.onmicrosoft.com',

            'contact/email/recipient_email' => 'project-traverstool@somethingdigital.onmicrosoft.com',

            'trans_email/ident_general/name' => 'Travers Tool Co.',
            'trans_email/ident_general/email' => 'sales@travers.com',
            'trans_email/ident_sales/name' => 'Travers Tool Co.',
            'trans_email/ident_sales/email' => 'sales@travers.com',
            'trans_email/ident_support/name' => 'Travers Tool Co.',
            'trans_email/ident_support/email' => 'sales@travers.com',
            'trans_email/ident_custom1/name' => 'Travers Tool Co.',
            'trans_email/ident_custom1/email' => 'sales@travers.com',
            'trans_email/ident_custom2/name' => 'Travers Tool Co.',
            'trans_email/ident_custom2/email' => 'sales@travers.com',

            'catalog/seo/search_terms' => '0',
            'catalog/seo/product_url_suffix' => '',
            'catalog/seo/category_url_suffix' => '',
            'catalog/seo/category_canonical_tag' => '1',
            'catalog/seo/product_canonical_tag' => '1',
            'catalog/magento_targetrule/related_position_limit' => '12',
            'catalog/magento_targetrule/crosssell_position_limit' => '6',
            'catalog/magento_targetrule/upsell_position_limit' => '12',
            'catalog/productalert_cron/error_email' => 'project-traverstool@somethingdigital.onmicrosoft.com',
            'cataloginventory/item_options/max_sale_qty' => '750',
            'catalog/review/allow_guest' => '0',
            'catalog/downloadable/disable_guest_checkout' => '1',

            'wishlist/general/active' => '0',

            'sitemap/generate/enabled' => '1',
            'sitemap/generate/error_email' => 'project-traverstool@somethingdigital.onmicrosoft.com',

            'customer/magento_customerbalance/is_enabled' => '0',
            'customer/magento_customerbalance/show_history' => '0',

            'magento_invitation/general/enabled' => '0',
            'magento_invitation/general/enabled_on_front' => '0',
            'magento_reward/general/is_enabled' => '0',
            'magento_reward/general/is_enabled_on_front' => '0',
            'magento_giftregistry/general/enabled' => '0',
            'magento_giftregistry/general/enabled' => '0',
            'magento_reward/general/is_enabled' => '0',
            'magento_invitation/general/enabled' => '0',

            'sales/gift_options/wrapping_allow_order' => '0',
            'sales/gift_options/wrapping_allow_items' => '0',
            'sales/gift_options/allow_gift_receipt' => '1',
            'sales/gift_options/allow_printed_card' => '1',
            'sales/product_sku/my_account_enable' => '0',
            'sales/magento_rma/enabled' => '0',
            'sales/magento_rma/enabled_on_product' => '0',

            'checkout/options/guest_checkout' => '1',
            'checkout/sidebar/enable_minicart_sidebar' => '1',
            'checkout/cart/configurable_product_image' => 'itself',
            'checkout/options/guest_checkout' => '0',

            'carriers/tablerate/active' => '0',

            'google/analytics/active' => '1',
            'google/analytics/type' => 'tag_manager',

            'payment/paypal_billing_agreement/active' => '0',
            'payment/payflowpro/active' => '',
            'payment/braintree_cc_vault/active' => '0',

            'payment_us/checkmo/active' => '0',
            'payment_us/purchaseorder/active' => '1',
            'payment_us/purchaseorder/title' => 'Purchase Order',

            'giftcard/general/is_redeemable' => '0',
            'giftcard/general/allow_message' => '0',

            'btob/website_configuration/company_active' => '1',
            'btob/website_configuration/sharedcatalog_active' => '1',
            'btob/website_configuration/negotiablequote_active' => '1',
            'btob/website_configuration/quickorder_active' => '1',
            'btob/website_configuration/requisition_list_active' => '1',
            'btob/default_b2b_payment_methods/applicable_payment_methods' => '0',

            'company/general/allow_company_registration' => '1',
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
        $this->resourceConfig->saveConfig($path, $value, 'default', 0);
    }
}

