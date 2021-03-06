<?php
/**
 * Blackbird MenuManager Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category            Blackbird
 * @package		Blackbird_MenuManager
 * @copyright           Copyright (c) 2016 Blackbird (http://black.bird.eu)
 * @author		Blackbird Team
 */
namespace Blackbird\MenuManager\Block\Adminhtml\Catalog\Product\Widget;


class Chooser extends \Magento\CatalogRule\Block\Adminhtml\Promo\Widget\Chooser\Sku
{
    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'menumanager/product_widget/chooser',
            ['_current' => true, 'current_grid_id' => $this->getId(), 'collapse' => null]
        );
    }

}
