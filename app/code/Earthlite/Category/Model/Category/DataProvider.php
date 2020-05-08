<?php
declare(strict_types=1);
namespace Earthlite\Category\Model\Category;

/**
 * class DataProvider
 */
class DataProvider extends \Magento\Catalog\Model\Category\DataProvider
{
    /**
     * @return []
     */
    protected function getFieldsMap()
    {
        $fields = parent::getFieldsMap();
        $fields['content'][] = 'banner_image';
        return $fields;
//        return [
//            'general' => [
//                    'parent',
//                    'path',
//                    'is_active',
//                    'include_in_menu',
//                    'name',
//                ],
//            'content' => [
//                    'image',
//                    'description',
//                    'landing_page',
//                ],
//            'display_settings' => [
//                    'display_mode',
//                    'is_anchor',
//                    'available_sort_by',
//                    'use_config.available_sort_by',
//                    'default_sort_by',
//                    'use_config.default_sort_by',
//                    'filter_price_range',
//                    'use_config.filter_price_range',
//                ],
//            'search_engine_optimization' => [
//                    'url_key',
//                    'url_key_create_redirect',
//                    'url_key_group',
//                    'meta_title',
//                    'meta_keywords',
//                    'meta_description',
//                ],
//            'assign_products' => [
//                ],
//            'design' => [
//                    'custom_use_parent_settings',
//                    'custom_apply_to_products',
//                    'custom_design',
//                    'page_layout',
//                    'custom_layout_update',
//                    'custom_layout_update_file'
//                ],
//            'schedule_design_update' => [
//                    'custom_design_from',
//                    'custom_design_to',
//                ],
//            'category_view_optimization' => [
//                ],
//            'category_permissions' => [
//                ],
//        ];
    }

}