<?php
namespace Travers\Feedback\Model\Config\Source;

class IssueList implements \Magento\Framework\Data\OptionSourceInterface
{
 public function toOptionArray()
 {
  return [
    ['value' => '0', 'label' => __('Select Issue Type')],
    ['value' => '10000', 'label' => __('Epic')],
    ['value' => '10001', 'label' => __('Story')],
    ['value' => '10002', 'label' => __('Task')],
    ['value' => '10003', 'label' => __('Sub-task')],
    ['value' => '10004', 'label' => __('Bug')],
    ['value' => '10005', 'label' => __('Spike')],   
    ['value' => '10006', 'label' => __('Test')],
    ['value' => '10007', 'label' => __('Test Set')],
    ['value' => '10008', 'label' => __('Test Plan')],
    ['value' => '10009', 'label' => __('Test Execution')],
    ['value' => '10010', 'label' => __('Precondition')],
    ['value' => '10011', 'label' => __('Sub Test Execution')],
    ['value' => '10015', 'label' => __('User feedback')],
  ];
 }
}