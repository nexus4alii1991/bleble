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

namespace Mageplaza\Shopbybrand\Model\ResourceModel;

/**
 * Class Category
 * @package Mageplaza\Shopbybrand\Model\ResourceModel
 */
class Category extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	public $helperData;

	public $date;

	public $formData;

	public function __construct(
		\Mageplaza\Shopbybrand\Helper\Data $helperData,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Mageplaza\Shopbybrand\Controller\Adminhtml\Category\Save $formData,
		\Magento\Framework\Model\ResourceModel\Db\Context $context
	)
	{
		$this->helperData = $helperData;
		$this->date       = $date;
		$this->formData   = $formData;
		parent::__construct($context);
	}

	/**
	 * Initialize resource
	 *
	 * @return void
	 */

	public function _construct()
	{
		$this->_init('mageplaza_shopbybrand_category', 'cat_id');
	}

	/**
	 * @param \Magento\Framework\Model\AbstractModel $object
	 * @return $this
	 */
	protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
	{
		parent::_beforeSave($object);

		if (is_array($object->getStoreIds())) {
			$object->setStoreIds(implode(',', $object->getStoreIds()));
		}
		$object->setUpdatedAt($this->date->date());
		if ($object->isObjectNew()) {
			$object->setCreatedAt($this->date->date());
		}

		//Check Url Key

		if ($object->isObjectNew()) {
			$count   = 0;
			$objName = $object->getName();
			if ($object->getUrlKey()) {
				$urlKey = $object->getUrlKey();
			} else {
				$urlKey = $this->generateUrlKey($objName, $count);
			}
			while ($this->checkUrlKey($urlKey)) {
				$count++;
				$urlKey = $this->generateUrlKey($urlKey, $count);
			}
			$object->setUrlKey($urlKey);
		} else {
			$objectId = $object->getId();
			$count    = 0;
			$objName  = $object->getName();
			if ($object->getUrlKey()) {
				$urlKey = $object->getUrlKey();
			} else {
				$urlKey = $this->generateUrlKey($objName, $count);
			}
			while ($this->checkUrlKey($urlKey, $objectId)) {
				$count++;
				$urlKey = $this->generateUrlKey($urlKey, $count);
			}

			$object->setUrlKey($urlKey);
		}
		return $this;
	}

	/**
	 * after save callback
	 *
	 * @param \Magento\Framework\Model\AbstractModel|\Mageplaza\Shopbybrand\Model\Category $object
	 * @return $this
	 */
	protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
	{
		$this->saveBrandRelation($object);
		return parent::_afterSave($object); // TODO: Change the autogenerated stub

	}

	/**
	 * Insert, Delete , Update Brand-Category relation function
	 * @param \Mageplaza\Shopbybrand\Model\Category $cat
	 * @return $this
	 */
	protected function saveBrandRelation(\Mageplaza\Shopbybrand\Model\Category $cat)
	{
		$oldOptionIds = [];
		$adapter      = $this->getConnection();
		$data         = $this->formData->getData();
		$id           = $cat->getCatId();
		$sql          = "select * from " . $this->getTable('mageplaza_shopbybrand_brand_category') . " where cat_id = " . $id;
		$oldRecord    = $this->getConnection()->fetchAll($sql);
		foreach ($oldRecord as $item) {
			$oldOptionIds[] = $item['option_id'];
		}
		if (!empty($data['brand_category'])) {
			$optionIds = $data['brand_category'];
			if (empty($oldRecord)) {
				$insert = [];
				foreach ($optionIds as $optionId) {
					$insert[] = [
						'cat_id'    => $id,
						'option_id' => $optionId
					];

				}
				$adapter->insertMultiple($this->getTable('mageplaza_shopbybrand_brand_category'), $insert);
			}
			if (!empty($oldRecord)) {
				$updateOptionIds = array_diff($optionIds, $oldOptionIds);
				$deleteOptionIds = array_diff($oldOptionIds, $optionIds);
				if (!empty($updateOptionIds)) {
					$update = [];
					foreach ($updateOptionIds as $optionId) {
						$update[] = [
							'cat_id'    => $id,
							'option_id' => $optionId
						];
					}
					$adapter->insertMultiple($this->getTable('mageplaza_shopbybrand_brand_category'), $update);
				}
				if (!empty($deleteOptionIds)) {
					foreach ($deleteOptionIds as $optionId) {
						$sql = "Delete FROM " . $this->getTable('mageplaza_shopbybrand_brand_category') . " WHERE option_id = " . $optionId . " AND cat_id = " . $id;
						$adapter->query($sql);
					}
				}
			}
		}

		return $this;
	}

	/**
	 * Auto Generate Url Key function ( depends on Category Name if leave UrlKey empty )
	 * @param $name
	 * @param $count
	 * @return string
	 */

	public function generateUrlKey($name, $count)
	{
		return $this->helperData->generateUrlKey($name, $count);
	}

	/**
	 * Check Url Key function to avoid duplicate
	 * @param $url
	 * @param null $id
	 * @return string
	 */

	public function checkUrlKey($url, $id = null)
	{
		$adapter = $this->getConnection();
		if ($id) {
			$select           = $adapter->select()
				->from($this->getMainTable(), '*')
				->where('url_key = :url_key')
				->where('cat_id != :cat_id');
			$binds['url_key'] = (string)$url;
			$binds ['cat_id'] = (int)$id;
		} else {
			$select = $adapter->select()
				->from($this->getMainTable(), '*')
				->where('url_key = :url_key');
			$binds  = ['url_key' => (string)$url];
		}
		$result = $adapter->fetchOne($select, $binds);

		return $result;
	}

}
