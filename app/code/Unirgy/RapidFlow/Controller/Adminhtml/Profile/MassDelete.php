<?php

namespace Unirgy\RapidFlow\Controller\Adminhtml\Profile;


use Unirgy\RapidFlow\Model\Profile;

class MassDelete extends AbstractProfile
{

    public function execute()
    {
        $profileIds = $this->getRequest()->getParam('profiles');
        if(!is_array($profileIds)) {
            $this->messageManager->addError(__('Please select profile(s)'));
        } else {
            try {
                foreach ($profileIds as $profileId) {
                    $profile = $this->_profile->load($profileId);
                    $profile->delete();
                }
                $this->messageManager->addSuccess(
                    __('Total of %1 record(s) were successfully deleted', count($profileIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}
