<?php

namespace SomethingDigital\MigrationBryantPark\Migration\Data;

use Magento\Framework\Setup\SetupInterface;
use SomethingDigital\Migration\Api\MigrationInterface;
use SomethingDigital\Migration\Helper\Cms\Page as PageHelper;
use SomethingDigital\Migration\Helper\Cms\Block as BlockHelper;
use SomethingDigital\Migration\Helper\Cms\Bluefoot as BluefootHelper;
use SomethingDigital\Migration\Helper\Email\Template as EmailHelper;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;

class M20170530193017CreateMenswearLandingContent implements MigrationInterface
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
        
        // create CMS block "catland_mens"
        $data264 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '22',
            'title' => 'Coats',
            'heading_type' => '227',
            'opacity' => '248',
        ];
        $bluefootEntity264 = $this->bluefoot->create($data264);

        $data268 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '22',
            'title' => 'Suits & Sports Jackets',
            'heading_type' => '227',
        ];
        $bluefootEntity268 = $this->bluefoot->create($data268);

        $data271 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '45',
            'title' => 'Dogedealz',
            'color' => 'ffffff',
            'link_text' => 'Find Your Perfect Match',
            'link_url' => '#',
            'background_color' => '000000',
            'banner_height' => '320px',
            'target_blank' => '0',
            'text_alignment' => '235',
            'hv_position' => '241',
            'opacity' => '249',
            'fullbleed' => '0',
            'image' => '/m/e/mesnwear_deals_1.jpg',
            'a_single_paragraph' => 'Shop for fantastic dealz',
        ];
        $bluefootEntity271 = $this->bluefoot->create($data271);

        $data272 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '19',
            'color' => '5d5d5d',
            'hr_height' => '2px',
            'hr_width' => '100%',
            'opacity' => '248',
        ];
        $bluefootEntity272 = $this->bluefoot->create($data272);

        $data273 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '19',
            'color' => '5d5d5d',
            'hr_height' => '2px',
            'hr_width' => '100%',
            'opacity' => '248',
        ];
        $bluefootEntity273 = $this->bluefoot->create($data273);

        $data274 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '44',
            'title' => 'Club Jackets',
            'color' => '000000',
            'background_color' => '00ffea',
            'target_blank' => '0',
            'text_alignment' => '235',
            'hv_position' => '245',
            'opacity' => '248',
            'image' => '/m/e/menswear_clubwear.jpg',
        ];
        $bluefootEntity274 = $this->bluefoot->create($data274);

        $data275 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '44',
            'title' => 'Lux Tux',
            'color' => 'ffffff',
            'background_color' => '000000',
            'target_blank' => '0',
            'text_alignment' => '235',
            'hv_position' => '239',
            'opacity' => '248',
            'image' => '/m/e/menswear_suit.jpg',
        ];
        $bluefootEntity275 = $this->bluefoot->create($data275);

        $data276 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '44',
            'title' => 'Beanie Life',
            'target_blank' => '0',
            'opacity' => '248',
            'image' => '/m/e/menswear_beaniejacket.jpg',
        ];
        $bluefootEntity276 = $this->bluefoot->create($data276);

        $data277 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '44',
            'title' => 'Trendy Jackets',
            'target_blank' => '0',
            'opacity' => '248',
            'image' => '/m/e/menswear_jacketshades.jpg',
        ];
        $bluefootEntity277 = $this->bluefoot->create($data277);

        $data278 = [
            'entity_type_id' => '0',
            'attribute_set_id' => '44',
            'title' => 'Director Life',
            'target_blank' => '0',
            'opacity' => '248',
            'image' => '/m/e/menswear_capjacket.jpg',
        ];
        $bluefootEntity278 = $this->bluefoot->create($data278);

        $extraData = [
            'title' => 'Category Landing :: Menswear',
            'is_active' => 1,
            'stores' => [0]
        ];

        $this->block->create('catland_mens', 'Category Landing :: Menswear', '<!--GENE_BLUEFOOT="[{"type":"row","formData":{"template":"full-width.phtml","background_color":"","background_image":"","css_classes":"","undefined":"","align":"center","metric":""},"children":[{"contentType":"heading","entityId":"' . $bluefootEntity268 . '","formData":{"align":"center","metric":""}}]},{"type":"row","formData":{"template":"full-width.phtml","background_color":"","background_image":"","css_classes":"","undefined":"","align":"center","metric":""},"children":[{"type":"column","formData":{"width":"1.000","remove_padding":"1","css_classes":"catland__section catland__row catland__touts","undefined":"","align":"","metric":""},"children":[{"contentType":"category_tout","children":{"catland_tout":[]},"entityId":"' . $bluefootEntity274 . '","formData":{"align":"","metric":""}},{"contentType":"category_tout","children":{"catland_tout":[]},"entityId":"' . $bluefootEntity275 . '","formData":{"align":"","metric":""}}]}]},{"type":"row","formData":{"template":"full-width.phtml","background_color":"","background_image":"","css_classes":"","undefined":"","align":"center","metric":""},"children":[{"type":"column","formData":{"width":"0.333"},"children":[{"contentType":"hr","entityId":"' . $bluefootEntity272 . '","formData":{"align":"","metric":""}}]},{"type":"column","formData":{"width":"0.333"},"children":[{"contentType":"heading","entityId":"' . $bluefootEntity264 . '","formData":{"align":"center","metric":"{\\"margin\\":\\"-2px - 20px -\\",\\"padding\\":\\"- - - -\\"}"}}]},{"type":"column","formData":{"width":"0.333"},"children":[{"contentType":"hr","entityId":"' . $bluefootEntity273 . '","formData":{"align":"","metric":""}}]}]},{"type":"row","formData":{"template":"full-width.phtml","background_color":"","background_image":"","css_classes":"","undefined":"","align":"center","metric":""},"children":[{"type":"column","formData":{"width":0.333},"children":[{"contentType":"category_tout","children":{"catland_tout":[]},"entityId":"' . $bluefootEntity276 . '","formData":{"align":"","metric":""}}]},{"type":"column","formData":{"width":0.333},"children":[{"contentType":"category_tout","children":{"catland_tout":[]},"entityId":"' . $bluefootEntity277 . '","formData":{"align":"","metric":""}}]},{"type":"column","formData":{"width":0.333},"children":[{"contentType":"category_tout","children":{"catland_tout":[]},"entityId":"' . $bluefootEntity278 . '","formData":{"align":"","metric":""}}]}]},{"type":"row","formData":{"template":"full-width.phtml","background_color":"","background_image":"","css_classes":"","undefined":"","align":"","metric":"{\\"margin\\":\\"50px - - -\\",\\"padding\\":\\"- - - -\\"}"},"children":[{"contentType":"category_banner","entityId":"' . $bluefootEntity271 . '","formData":{"align":"","metric":""}}]}]"-->', $extraData);

    }
}
