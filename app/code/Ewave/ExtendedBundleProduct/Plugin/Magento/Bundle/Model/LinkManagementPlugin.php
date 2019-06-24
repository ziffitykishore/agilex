<?php
namespace Ewave\ExtendedBundleProduct\Plugin\Magento\Bundle\Model;

use Ewave\ExtendedBundleProduct\Preferences\Magento\Bundle\Model\LinkManagement as Subject;
use Ewave\ExtendedBundleProduct\Helper\Data as Helper;

/**
 * Class LinkManagementPlugin
 */
class LinkManagementPlugin
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @param Helper $helper
     */
    public function __construct(
        Helper $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param Subject $subject
     * @param \Magento\Bundle\Model\Selection $selectionModel
     * @param \Magento\Bundle\Api\Data\LinkInterface $productLink
     * @param string $linkedProductId
     * @param string $parentProductId
     * @return null
     */
    public function beforeMapProductLinkToSelectionModelPublic(
        Subject $subject,
        \Magento\Bundle\Model\Selection $selectionModel,
        \Magento\Bundle\Api\Data\LinkInterface $productLink,
        $linkedProductId,
        $parentProductId
    ) {
        if (is_array($productLink->getConfigurableOptions())) {
            $this->helper->setConfigurableOptions($selectionModel, $productLink->getConfigurableOptions());
        }
        return null;
    }
}
