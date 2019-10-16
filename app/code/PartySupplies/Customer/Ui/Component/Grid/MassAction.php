<?php

namespace PartySupplies\Customer\Ui\Component\Grid;

use PartySupplies\Customer\Helper\Constant;
use Magento\Framework\App\Request\Http;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class MassAction extends \Magento\Ui\Component\MassAction
{
    const MASS_ACTION_DELETE = 'delete';
    
    const MASS_ACTION_EDIT = 'edit';
    
    /**
     * @var Http
     */
    protected $request;
    
    /**
     *
     * @param ContextInterface $context
     * @param Http $request
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        Http $request,
        $components,
        array $data
    ) {
        parent::__construct($context, $components, $data);
        $this->request = $request;
    }
    
    /**
     * To prepare mass action configuration
     *
     */
    public function prepare()
    {
        parent::prepare();
        $config = $this->getConfiguration();

        if ($this->request->getParam('account_type') !== null
            && $this->request->getParam('account_type') === Constant::COMPANY
        ) {
            $allowedActions = [];
            foreach ($config['actions'] as $action) {
                if ($action['type'] === self::MASS_ACTION_EDIT
                    || $action['type'] === self::MASS_ACTION_DELETE
                ) {
                    $allowedActions[] = $action;
                }
            }
            $config['actions'] = $allowedActions;
        }

        $this->setData('config', $config);
    }
}
