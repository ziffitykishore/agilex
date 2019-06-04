<?php

namespace Ziffity\Pickupdate\Controller\Adminhtml\Pickupdate;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Ziffity\Pickupdate\Controller\Adminhtml\Pickupdate
{
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            $data = $this->getRequest()->getPostValue();
            $orderId = $this->getRequest()->getParam('order_id');

            try {
                $order = $this->orderFactory->create();
                $this->orderResource->load($order, $orderId);

                if (is_array($data) && !empty($data)) {
                    if (array_key_exists('clear', $data)) {
                        $data['date'] = '0000-00-00';
                    }

                    $pickupDate = $this->model->create();
                    $this->resourceModel->load($pickupDate, $orderId, 'order_id');

                    $wasDate = $pickupDate->getDate();
                    $wasTime = $pickupDate->getTime();

                    if ($pickupDate->prepareForSave($data, $order)) {
                        $this->resourceModel->save($pickupDate);
                    }

                    $email = $order->getCustomerEmail();
                    if (array_key_exists('notify', $data) && $email) {
                        if ($wasDate != $pickupDate->getDate() || $wasTime != $pickupDate->getTime()) {
                            $value = $this->date->date($this->pickupHelper->getPhpFormat(), $pickupDate->getDate());
                            $pickupDate->setDate($value);

                            $templateId = $this->pickupHelper->getDefaultScopeValue('general/email_template');
                            $templateId = $templateId ? $templateId : 'pickupdate_general_email_template';

                            $sender = $this->pickupHelper->getDefaultScopeValue('general/notification_sender');
                            $storeId = $order->getStoreId();

                            $vars = [
                                'pickup' => $pickupDate,
                                'was_date' => $wasDate,
                                'was_time' => $wasTime,
                                'order' => $order
                            ];

                            $this->transportBuilder
                                ->setTemplateIdentifier($templateId)
                                ->setTemplateOptions(['area' => 'frontend', 'store' => $storeId])
                                ->setTemplateVars($vars)
                                ->setFrom($sender)
                                ->addTo($email);

                            $transport = $this->transportBuilder->getTransport();
                            $transport->sendMessage();
                        }

                    }
                    $this->messageManager->addSuccessMessage(__('Record has been successfully saved'));
                } else {
                    throw new LocalizedException(__('The wrong date interval is specified.'));
                }
                $this->_redirect('sales/order/view', ['order_id' => $orderId]);
                return;

            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_redirect('sales/order/view', ['order_id' => $orderId]);
                return;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving data. Please review the error log.')
                );
                $this->logInterface->critical($e);
                $this->session->setPageData($data);
                $this->_redirect('sales/order/view', ['order_id' => $orderId]);
                return;
            }
        }
    }
}