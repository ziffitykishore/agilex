<?php
namespace Ziffity\Email\Model\Plugin;

/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This is the Summary for this element.
 * 
 * @inheritDoc
 */
class Bugfix 
{
   
    //fix for Something went wrong while saving the the configuration: Area is already set 
    public function afterSetForcedArea(Magento\Email\Model\AbstractTemplate $subject, $templateId)
    {
        echo "@@@@";exit;
        if (!isset($this->area)) {
            $this->area = $this->emailConfig->getTemplateArea($templateId);
        }
        return $this;
    }

}
