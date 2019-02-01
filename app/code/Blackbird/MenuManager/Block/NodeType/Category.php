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
namespace Blackbird\MenuManager\Block\NodeType;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Profiler;

class Category extends AbstractNodeTypeFront
{
    protected $nodes;

    protected $categoryUrls;
    /**
     * @var ResourceConnection
     */
    private $connection;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $_categoryRepositoryInterface;

    /**
     * Category constructor.
     * @param Context $context
     * @param ResourceConnection $connection
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepositoryInterFace
     * @param array $data
     */
    public function __construct(
        Context $context,
        ResourceConnection $connection,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepositoryInterFace,
        $data = [])
    {
        $this->_categoryRepositoryInterface = $categoryRepositoryInterFace;
        parent::__construct($context, $data);
    }


    public function fetchData(array $nodes)
    {
        $localNodes = [];
        $categoryIds = [];
        foreach ($nodes as $node) {
            $localNodes[$node->getId()] = $node;
            $categoryIds[] = (int)$node->getContent();
        }
        $this->nodes = $localNodes;
    }


    /**
     * define the url of the node
     *
     * @param $node
     * @return mixed
     */
    public function getUrlNode($node)
    {
        try {
            $category = $this->_categoryRepositoryInterface->get($node->getEntityId());
            $url = $category->getUrl();
        } catch (NoSuchEntityException $e){
            $url = $this->_storeManager->getStore()->getBaseUrl();
        }

        $this->setUrl($url);
        return $url;
    }


    /**
     * @param $nodeId
     * @param $level
     * @param $classes
     * @param $childrenHtml
     * @param $childrenArray
     * @return string
     */
    public function getHtml($nodeId, $level, $classes, $childrenHtml, $childrenArray, $storeId)
    {
        $node = $this->nodes[$nodeId];
        $node->setUrlPath($this->getUrlNode($node));

        $url = $this->getData('url');
        $classes = $this->getIsActiveClass($url, $node, $classes, $childrenArray);

        return parent::getHtml($nodeId, $level, $classes, $childrenHtml, $childrenArray, $storeId);
    }
}