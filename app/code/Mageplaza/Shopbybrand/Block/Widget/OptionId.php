<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Shopbybrand
 * @copyright   Copyright (c) 2017 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Shopbybrand\Block\Widget;

use Magento\Framework\View\Element\Template\Context;
use Mageplaza\Shopbybrand\Helper\Data as Helper;
use Mageplaza\Shopbybrand\Model\BrandFactory;
use Magento\Widget\Block\BlockInterface;

/**
 * Class OptionId
 *
 * @package Mageplaza\Shopbybrand\Block\Brand
 */
class OptionId extends AbstractBrand implements BlockInterface
{
    /**
     * @var string
     */
    protected $_template = "widget/brandlist.phtml";

    /**
     * @type \Mageplaza\Shopbybrand\Model\BrandFactory
     */
    protected $_brandFactory;

    /**
     * OptionId constructor.
     *
     * @param Context $context
     * @param Helper $helper
     * @param BrandFactory $brandFactory
     */
    public function __construct(Context $context, Helper $helper,
        BrandFactory $brandFactory
    ) {
        $this->_brandFactory = $brandFactory;
        parent::__construct($context, $helper);
    }

    /**
     * @return string
     */
    public function getOptionIds()
    {
        $str = $this->getData('option_id');
        if (strpos($str, ",") !== false) {
            $str = str_replace(",", " ", $str);
        } elseif (strpos($str, "-") !== false) {
            $str = str_replace("-", " ", $str);
        }
        $optionIDs = explode(" ", $str);
        return implode(",", array_diff($optionIDs, array('')));
    }

    /**
     * get brand by option IDs
     *
     * @return mixed
     */
    public function getCollection()
    {
        $collection = $this->_brandFactory->create()->getBrandCollection(
            null, ['main_table.option_id' => ['in' => $this->getOptionIds()]]
        );

        return $collection;
    }

}
