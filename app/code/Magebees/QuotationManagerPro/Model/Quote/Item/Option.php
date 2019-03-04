<?php

namespace Magebees\QuotationManagerPro\Model\Quote\Item;


/**
 * Item option model
 *
 * @method int getItemId()
 * @method \Magebees\QuotationManagerPro\Model\Quote\Item\Option setItemId(int $value)
 * @method int getProductId()
 * @method \Magebees\QuotationManagerPro\Model\Quote\Item\Option setProductId(int $value)
 * @method string getCode()
 * @method \Magebees\QuotationManagerPro\Model\Quote\Item\Option setCode(string $value)
 * @method \Magebees\QuotationManagerPro\Model\Quote\Item\Option setValue(string $value)
 */
class Option extends \Magento\Framework\Model\AbstractModel implements
    \Magento\Catalog\Model\Product\Configuration\Item\Option\OptionInterface
{
    /**
     * @var \Magento\Quote\Model\Quote\Item
     */
    protected $_item;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;
	
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magebees\QuotationManagerPro\Model\ResourceModel\Quote\Item\Option::class);
    }
	
    /**
     * Checks that item option model has data changes
     *
     * @return boolean
     */
    protected function _hasModelChanged()
    {
        if (!$this->hasDataChanges()) {
            return false;
        }

        return $this->_getResource()->hasDataChanged($this);
    }

    /**
     * Set quote item
     *
     * @param   \Magento\Quote\Model\Quote\Item $qitem
     * @return $this
     */
    public function setItem($qitem)
    {
        $this->setItemId($qitem->getId());
        $this->_item = $qitem;
        return $this;
    }

    /**
     * Get option item
     *
     * @return \Magento\Quote\Model\Quote\Item
     */
    public function getItem()
    {
        return $this->_item;
    }

    /**
     * Set option product
     *
     * @param \Magento\Catalog\Model\Product $qproduct
     * @return $this
     */
    public function setProduct($qproduct)
    {
        $this->setProductId($qproduct->getId());
        $this->_product = $qproduct;
        return $this;
    }

    /**
     * Get option product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_product;
    }

    /**
     * Get option value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->getData('value');
    }

    /**
     * Initialize item identifier before save data
     *
     * @return $this
     */
    public function beforeSave()
    {
        if ($this->getItem()) {
            $this->setItemId($this->getItem()->getId());
        }
        return parent::beforeSave();
    }

    /**
     * Clone option object
     *
     * @return $this
     */
    public function __clone()
    {
        $this->setId(null);
        $this->_item = null;
        return $this;
    }
}