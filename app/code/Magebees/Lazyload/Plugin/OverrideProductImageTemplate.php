<?php
namespace Magebees\Lazyload\Plugin;

use Magento\Catalog\Block\Product\Image as ImageSubject;

class OverrideProductImageTemplate
{
	public function __construct(
		\Magebees\Lazyload\Helper\Data $lazyloadHelper        
    ) {
        $this->helper = $lazyloadHelper;
    }

    public function beforeSetTemplate(ImageSubject $subject, $template)
    {
		$config=$this->helper->getConfig();
		if($config['enable'])
		{
        $template = str_replace('Magento_Catalog', 'Magebees_Lazyload', $template);
        return [$template];
		}
    }
}
