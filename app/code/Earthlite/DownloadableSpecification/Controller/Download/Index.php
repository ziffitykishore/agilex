<?php
namespace Earthlite\DownloadableSpecification\Controller\Download;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Phrase;

class Index extends Action
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    protected $_productRepository;

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository
    ) {
        $this->fileFactory = $fileFactory;
        $this->_productRepository = $productRepository;
        parent::__construct($context);        
    }

    /**
     * to generate pdf
     *
     * @return void
     */
    public function execute()
    {
        $proId = $this->getRequest()->getParam('id');     
        
        if($proId && $product = $this->getProduct($proId)) {                                
            $pdf = new \Zend_Pdf();
            $pdf->pages[] = $pdf->newPage(\Zend_Pdf_Page::SIZE_A4);
            
            $page = $pdf->pages[0]; 
            
            $style = new \Zend_Pdf_Style();
            
            $style->setLineColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));
            $font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_TIMES);
            $style->setFont($font, 15);
            $page->setStyle($style);
            
            $width = $page->getWidth();
            $hight = $page->getHeight();
            
            $x = 30;
            $pageTopalign = 850; 
            $this->y = 850 - 100;
                        
            $page->drawRectangle(30, $this->y + 30, $page->getWidth()-30, $this->y +70, \Zend_Pdf_Page::SHAPE_DRAW_STROKE);
            
            $style->setFont($font, 16);
            $page->setStyle($style);
            
            $page->drawText(__("SPECIFICATION SHEET"), $x + 190, $this->y+45, 'UTF-8');
            
            $page->drawRectangle(30, $this->y - 250, $page->getWidth()-30, $this->y + 70, \Zend_Pdf_Page::SHAPE_DRAW_STROKE);
            
            $style->setFont($font, 14);
            $page->setStyle($style);                        
            
            $productSpecs = $this->getProductSpecs($proId);
            if($productSpecs) {
                $page->drawText(__("Product Name : %1", ucwords($product->getName())), $x + 30, $this->y - 0, 'UTF-8');

                $incr = -20;
                foreach ($productSpecs as $productSpec) 
                {                
                    $page->drawText(__("%1 : %2", $productSpec['label'], $productSpec['value']), $x + 30, $this->y + $incr, 'UTF-8');
                    
                    $incr = $incr - 20;                
                }
            }
            else
            {
                $page->drawText(__("No Specification Available"), $x + 30, $this->y - 20, 'UTF-8');                
            }                       

            $fileName = 'specsheet.pdf';

            $this->fileFactory->create(
                $fileName,
                $pdf->render(),
                \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                'application/pdf'
            );
        }
    }

    public function getProductSpecs($id, array $excludeAttr = [])
    {
        if($id) {
            $data = false;
            try 
            {
                $product = $this->_productRepository->getById($id);                   
                $attributes = $product->getAttributes();
                foreach ($attributes as $attribute) {
                    if ($this->isVisibleOnFrontend($attribute, $excludeAttr)) {
                        $value = $attribute->getFrontend()->getValue($product);

                        if ($value instanceof Phrase) {
                            $value = (string)$value;
                        } elseif ($attribute->getFrontendInput() == 'price' && is_string($value)) {
                            $value = $this->priceCurrency->convertAndFormat($value);
                        }

                        if (is_string($value) && strlen(trim($value))) {
                            $data[$attribute->getAttributeCode()] = [
                                'label' => $attribute->getStoreLabel(),
                                'value' => $value,
                                'code' => $attribute->getAttributeCode(),
                            ];
                        }
                    }
                }
            } 
            catch (\Magento\Framework\Exception\NoSuchEntityException $e)
            {
                $data = false;
            }
            return $data;
        }
        else
        {
            return false;
        }
    }

    public function isVisibleOnFrontend(
        \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute,
        array $excludeAttr
    ) {
        return ($attribute->getIsVisibleOnFront() && !in_array($attribute->getAttributeCode(), $excludeAttr));
    }

    public function getProduct($id)
    {
        try {
            $product = $this->_productRepository->getById($id);
        } 
        catch (\Magento\Framework\Exception\NoSuchEntityException $e){
            $product = false;
        }

        return $product;
    }
} 