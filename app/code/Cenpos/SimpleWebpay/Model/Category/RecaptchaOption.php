<?php 
namespace Cenpos\SimpleWebpay\Model\Category;

class RecaptchaOption implements \Magento\Framework\Option\ArrayInterface
{
 public function toOptionArray()
 {
  return [
    ['value' => 'v2', 'label' => __('Version 2')],
    ['value' => 'v3', 'label' => __('Version 3')],
  ];
 }
}
?>