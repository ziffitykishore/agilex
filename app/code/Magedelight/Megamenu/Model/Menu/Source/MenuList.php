<?php

/**
 * Magedelight
 * Copyright (C) 2017 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Megamenu
 * @copyright Copyright (c) 2017 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Megamenu\Model\Menu\Source;

use Magento\Framework\App\Request\Http;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\StoreRepository;

/**
 * Class MenuList
 *
 * @package Magedelight\Megamenu\Model\Menu\Source
 */
class MenuList implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @var \Magedelight\Megamenu\Model\Menu
     */
    private $megamenuMenu;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * MenuList constructor.
     * @param \Magedelight\Megamenu\Model\Menu $megamenuMenu
     * @param Http $request
     * @param StoreManagerInterface $storeManager
     * @param StoreRepository $storeRepository
     */
    public function __construct(
        \Magedelight\Megamenu\Model\Menu $megamenuMenu,
        Http $request,
        StoreManagerInterface $storeManager,
        StoreRepository $storeRepository
    ) {
        $this->megamenuMenu = $megamenuMenu;
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->storeRepository = $storeRepository;
    }

    /**
     * @param \Magedelight\Megamenu\Model\Menu $megamenuMenu
     * @return string
     */
    public function getStores()
    {
        $store = $this->request->getParam('store');
        $website = $this->request->getParam('website');

        $allStores = [];
        if (isset($store) and ! empty($store)) {
            $allStores[] = $store;
        } elseif (isset($website) and ! empty($website)) {
            $website = $this->storeManager->getWebsite($website);
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                foreach ($stores as $store) {
                    $allStores[] = $store->getStoreId();
                }
            }
        } else {
            $allStores[] = 0;
        }
        $allStores[] = 0;
        return $allStores;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $storeId = $this->getStores();
        $menus = $this->megamenuMenu->getCollection();
        $menus->addFieldToFilter('is_active',['eq' => 1]);
        if ($this->request->getParam('website')) {
            $menus->getSelect()->join(
                ['u' => $menus->getTable('megamenu_menus_store')],
                'u.menu_id = main_table.menu_id',
                ['c' =>new \Zend_Db_Expr("group_concat(u.store_id)")]
            );
            $qry = '';
             foreach ($storeId as $_storeId) {
                 if($_storeId != 0) {
                    $temp[] =  "find_in_set(".$_storeId.", c)";
                    $qry = implode(" AND ", $temp);
                }

                if($_storeId == 0) {
                    $qry .= " OR find_in_set(".$_storeId.", c)";
                }
            }
            $menus->getSelect()->group('main_table.menu_id')->having(new \Zend_Db_Expr($qry) );
           
        } else {
            $menus->getSelect()->join(
                ['u' => $menus->getTable('megamenu_menus_store')],
                'u.menu_id = main_table.menu_id',
                []
            );
            $menus->addFieldToFilter(
                'u.store_id',
                [
                        ['in' => $storeId],
                    ]
            );
            $menus->getSelect()->group('main_table.menu_id');
        }

        $sourceArray = [];
        $count = 0;
        if (isset($menus) and ! empty($menus)) {
            foreach ($menus as $menu) {
                $sourceArray[$count]['value'] = $menu->getMenuId();
                $sourceArray[$count]['label'] = $menu->getMenuName();
                $count++;
            }
        }
        return $sourceArray;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [0 => __('No'), 1 => __('Yes')];
    }
}
