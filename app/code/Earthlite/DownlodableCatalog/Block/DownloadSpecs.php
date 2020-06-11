<?php

namespace Earthlite\DownlodableCatalog\Block;


class DownloadSpecs extends \Magento\Catalog\Block\Product\View
{    

    public function getFileUrl($attributeVal='')
    {
        $variable = substr($attributeVal, 0, strpos($attributeVal, "<img"));
        $variable = $this->escapeHtml($variable);
        preg_match('/{{(.*?)}}/', $variable, $matches);
        $targetPath = end($matches);        
        $mediaFiles = explode("=",$targetPath);        
        $mediaFile =  end($mediaFiles);             
        
        return $string = trim(html_entity_decode($mediaFile),'"');         
    }

    public function getMediaFileUrl($FilePath)
    {
        $mediaFile = $this->getBaseUrl().'media/'.$FilePath;
        return $mediaFile;
    }

    public function getAttributeLabel($code)
    {
        $attribute = $this->getproduct()->getResource()->getAttribute($code);
        return $attribute->getFrontend()->getLabel($this->getproduct());
    }
}
