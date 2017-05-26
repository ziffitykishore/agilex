<?php

namespace SomethingDigital\MigrationBryantPark\Migration\Data;

use Magento\Framework\Setup\SetupInterface;
use SomethingDigital\Migration\Api\MigrationInterface;
use SomethingDigital\Migration\Helper\Cms\Page as PageHelper;
use SomethingDigital\Migration\Helper\Cms\Block as BlockHelper;
use SomethingDigital\Migration\Helper\Cms\Bluefoot as BluefootHelper;
use SomethingDigital\Migration\Helper\Email\Template as EmailHelper;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;

class M20170526181125CreateHomepageWITHBluefootContent implements MigrationInterface
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
        // create CMS page "home"
        $data19 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '24',
            'alt_tag' => 'Tropical Tuesdays',
            'has_lightbox' => '0',
            'show_caption' => '0',
            'opacity' => '248',
            'image' => '/b/b/bb2-hp-dog3.jpg',
        ];
        $bluefootEntity19 = $this->bluefoot->create($data19);

        $data20 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '22',
            'title' => 'Tropical Tuesdays',
            'heading_type' => '228',
            'opacity' => '248',
        ];
        $bluefootEntity20 = $this->bluefoot->create($data20);

        $data21 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '36',
            'opacity' => '248',
            'button_items' => '1',
        ];
        $bluefootEntity21 = $this->bluefoot->create($data21);

        $data22 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '35',
            'link_text' => 'Shop Now',
            'link_url' => 'https://tmblr.co/Zdas8t2L3at7i',
            'opacity' => '248',
        ];
        $bluefootEntity22 = $this->bluefoot->create($data22);

        $data23 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '22',
            'title' => 'Monochrome Mondays',
            'heading_type' => '228',
            'opacity' => '248',
        ];
        $bluefootEntity23 = $this->bluefoot->create($data23);

        $data24 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '36',
            'opacity' => '248',
            'button_items' => '1',
        ];
        $bluefootEntity24 = $this->bluefoot->create($data24);

        $data25 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '35',
            'link_text' => 'Shop Now',
            'link_url' => 'https://tmblr.co/Zdas8t2L1QA1e',
            'opacity' => '248',
        ];
        $bluefootEntity25 = $this->bluefoot->create($data25);

        $data26 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '24',
            'alt_tag' => 'Tropical Tuesdays',
            'has_lightbox' => '0',
            'show_caption' => '0',
            'opacity' => '248',
            'image' => '/b/b/bb2-hp-monday_mono.jpg',
        ];
        $bluefootEntity26 = $this->bluefoot->create($data26);

        $data27 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '23',
            'alt_tag' => 'Jackets',
            'link_text' => 'Shop Jackets',
            'link_url' => 'https://tmblr.co/Zdas8t2LgX73u',
            'target_blank' => '0',
            'opacity' => '248',
            'image' => '/b/b/bb2-hp-dogjacket.jpg',
        ];
        $bluefootEntity27 = $this->bluefoot->create($data27);

        $data28 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '23',
            'alt_tag' => 'Sunglasses',
            'link_text' => 'Shop Sunglasses',
            'link_url' => 'https://tmblr.co/Zdas8t2L6n3jT',
            'target_blank' => '0',
            'opacity' => '248',
            'image' => '/b/b/bb2-hp-dog-glasses.jpeg',
        ];
        $bluefootEntity28 = $this->bluefoot->create($data28);

        $data29 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '23',
            'alt_tag' => 'Party Shirts',
            'link_text' => 'Shop Party Shirts',
            'link_url' => 'https://tmblr.co/Zdas8t2KzuypJ',
            'target_blank' => '0',
            'opacity' => '248',
            'image' => '/b/b/bb2-hp-hawaii_1.jpg',
        ];
        $bluefootEntity29 = $this->bluefoot->create($data29);

        $data30 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '27',
            'category_id' => '15',
            'product_count' => '6',
            'hide_out_of_stock' => '1',
            'opacity' => '248',
        ];
        $bluefootEntity30 = $this->bluefoot->create($data30);

        $data31 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '28',
            'opacity' => '248',
            'html' => '<p>Puppy kitty ipsum dolor sit good dog scratcher id tag left paw fur.</p>',
        ];
        $bluefootEntity31 = $this->bluefoot->create($data31);

        $data32 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '28',
            'opacity' => '248',
            'html' => '<p>Biscuit fetch stripes barky nap feeder left paw turtle feeder lick chirp.</p>',
        ];
        $bluefootEntity32 = $this->bluefoot->create($data32);

        $data34 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '45',
            'title' => 'Dogswear Summer Collection',
            'link_text' => 'Shop Now',
            'link_url' => '/sale',
            'background_color' => 'ffffff',
            'banner_height' => '50vh',
            'target_blank' => '0',
            'text_alignment' => '235',
            'hv_position' => '239',
            'opacity' => '249',
            'fullbleed' => '1',
            'image' => '/b/b/bb2-hp-leia_spring.jpg',
            'a_single_paragraph' => 'All new items for Summer 2017',
        ];
        $bluefootEntity34 = $this->bluefoot->create($data34);

        $data35 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '22',
            'title' => 'Dogerrific Deals',
            'heading_type' => '227',
            'opacity' => '248',
        ];
        $bluefootEntity35 = $this->bluefoot->create($data35);

        $data37 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '22',
            'title' => 'Cats are cool too',
            'heading_type' => '228',
            'opacity' => '248',
        ];
        $bluefootEntity37 = $this->bluefoot->create($data37);

        $data39 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '36',
            'opacity' => '248',
            'button_items' => '1',
        ];
        $bluefootEntity39 = $this->bluefoot->create($data39);

        $data40 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '35',
            'link_text' => 'Read About Our Cats',
            'link_url' => '#',
            'opacity' => '248',
        ];
        $bluefootEntity40 = $this->bluefoot->create($data40);

        $data41 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '19',
            'color' => '000000',
            'hr_height' => '1px',
            'hr_width' => '100%',
            'opacity' => '248',
        ];
        $bluefootEntity41 = $this->bluefoot->create($data41);

        $data42 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '28',
            'opacity' => '248',
            'html' => '<p>Destroy couch as revenge hide when guests come over, or cat snacks swat at dog, for make muffins. </p>',
        ];
        $bluefootEntity42 = $this->bluefoot->create($data42);

        $extraData = [
            'title' => 'Home Page',
            'page_layout' => '1column',
            'meta_title' => '',
            'meta_keywords' => '',
            'meta_description' => '',
            'content_heading' => '',
            'layout_update_xml' => '',
            'custom_theme' => '',
            'custom_root_template' => '',
            'is_active' => 1,
            'stores' => [0]
        ];

        $this->page->update('home', '<!--GENE_BLUEFOOT="[{"type":"row","formData":{"template":"full-width.phtml","background_color":"","background_image":"","css_classes":"","undefined":"","align":"","metric":""},"children":[{"contentType":"category_banner","entityId":"' . $bluefootEntity34 . '","formData":{"align":"","metric":""}}]},{"type":"row","formData":{"template":"full-width.phtml","background_color":"","background_image":"","css_classes":"bluefoot-row--reverse bluefoot--vertical-center","undefined":"","align":"","metric":""},"children":[{"type":"column","formData":{"width":"0.500","remove_padding":"1","css_classes":"","undefined":"","align":"center","metric":""},"children":[{"contentType":"heading","entityId":"' . $bluefootEntity23 . '","formData":{"align":"","metric":""}},{"contentType":"html","entityId":"' . $bluefootEntity31 . '","formData":{"align":"","metric":""}},{"contentType":"buttons","children":{"button_items":[{"contentType":"button_item","entityId":"' . $bluefootEntity25 . '","formData":{"align":"","metric":""}}]},"entityId":"' . $bluefootEntity24 . '","formData":{"align":"","metric":""}}]},{"type":"column","formData":{"width":"0.500"},"children":[{"contentType":"image","entityId":"' . $bluefootEntity26 . '","formData":{"align":"center","metric":""}}]}]},{"type":"row","formData":{"template":"full-width.phtml","background_color":"","background_image":"","css_classes":"bluefoot--vertical-center","undefined":"","align":"","metric":""},"children":[{"type":"column","formData":{"width":"0.500"},"children":[{"contentType":"image","entityId":"' . $bluefootEntity19 . '","formData":{"align":"center","metric":""}}]},{"type":"column","formData":{"width":"0.500","remove_padding":"1","css_classes":"","undefined":"","align":"center","metric":""},"children":[{"contentType":"heading","entityId":"' . $bluefootEntity20 . '","formData":{"align":"","metric":""}},{"contentType":"html","entityId":"' . $bluefootEntity32 . '","formData":{"align":"","metric":""}},{"contentType":"buttons","children":{"button_items":[{"contentType":"button_item","entityId":"' . $bluefootEntity22 . '","formData":{"align":"","metric":""}}]},"entityId":"' . $bluefootEntity21 . '","formData":{"align":"","metric":""}}]}]},{"type":"row","children":[{"type":"column","formData":{"width":0.333},"children":[{"contentType":"driver","entityId":"' . $bluefootEntity27 . '","formData":{"align":"center","metric":""}}]},{"type":"column","formData":{"width":0.333},"children":[{"contentType":"driver","entityId":"' . $bluefootEntity28 . '","formData":{"align":"center","metric":""}}]},{"type":"column","formData":{"width":0.333},"children":[{"contentType":"driver","entityId":"' . $bluefootEntity29 . '","formData":{"align":"center","metric":""}}]}]},{"type":"row","formData":{"template":"full-width.phtml","background_color":"","background_image":"","css_classes":"","undefined":"","align":"center","metric":""},"children":[{"contentType":"heading","entityId":"' . $bluefootEntity35 . '","formData":{"align":"center","metric":""}},{"contentType":"product_list","entityId":"' . $bluefootEntity30 . '","formData":{"align":"","metric":""}},{"contentType":"hr","entityId":"' . $bluefootEntity41 . '","formData":{"align":"","metric":""}}]},{"type":"row","formData":{"template":"full-width.phtml","background_color":"","background_image":"","css_classes":"bluefoot--vertical-center","undefined":"","align":"","metric":""},"children":[{"type":"column","formData":{"width":"0.500","remove_padding":"1","css_classes":"","undefined":"","align":"","metric":"{\\"margin\\":\\"- - - -\\",\\"padding\\":\\"- - - 0px\\"}"}},{"type":"column","formData":{"width":"0.500","remove_padding":"1","css_classes":"","undefined":"","align":"center","metric":""},"children":[{"contentType":"heading","entityId":"' . $bluefootEntity37 . '","formData":{"align":"","metric":""}},{"contentType":"html","entityId":"' . $bluefootEntity42 . '","formData":{"align":"","metric":""}},{"contentType":"buttons","children":{"button_items":[{"contentType":"button_item","entityId":"' . $bluefootEntity40 . '","formData":{"align":"","metric":""}}]},"entityId":"' . $bluefootEntity39 . '","formData":{"align":"","metric":""}}]}]}]"-->', $extraData);
    }
}
