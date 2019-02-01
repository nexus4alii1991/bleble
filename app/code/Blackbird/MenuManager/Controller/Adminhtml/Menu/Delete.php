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
namespace Blackbird\MenuManager\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action;
use Magento\Framework\Api\FilterBuilderFactory;
use Magento\Framework\Api\Search\FilterGroupBuilderFactory;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Blackbird\MenuManager\Api\MenuRepositoryInterface;
use Blackbird\MenuManager\Api\NodeRepositoryInterface;

class Delete extends Action
{
    /**
     * @var MenuRepositoryInterface
     */
    private $menuRepository;
    /**
     * @var NodeRepositoryInterface
     */
    private $nodeRepository;
    /**
     * @var FilterBuilderFactory
     */
    private $filterBuilderFactory;
    /**
     * @var FilterGroupBuilderFactory
     */
    private $filterGroupBuilderFactory;
    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    public function __construct(
        Action\Context $context,
        MenuRepositoryInterface $menuRepository,
        NodeRepositoryInterface $nodeRepository,
        FilterBuilderFactory $filterBuilderFactory,
        FilterGroupBuilderFactory $filterGroupBuilderFactory,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    ) {
        parent::__construct($context);
        $this->menuRepository = $menuRepository;
        $this->nodeRepository = $nodeRepository;
        $this->filterBuilderFactory = $filterBuilderFactory;
        $this->filterGroupBuilderFactory = $filterGroupBuilderFactory;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
    }


    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        try {
            $menu = $this->menuRepository->getById($id);
            $this->menuRepository->deleteById($id);

            $filterBuilder = $this->filterBuilderFactory->create();
            $filter = $filterBuilder->setField('menu_id')->setValue($id)->setConditionType('eq')->create();

            $filterGroupBuilder = $this->filterGroupBuilderFactory->create();
            $filterGroup = $filterGroupBuilder->addFilter($filter)->create();

            $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
            $searchCriteria = $searchCriteriaBuilder->setFilterGroups([$filterGroup])->create();

            $nodes = $this->nodeRepository->getList($searchCriteria);
            foreach ($nodes->getItems() as $node) {
                $this->nodeRepository->delete($node);
            }
            $this->messageManager->addSuccessMessage(__("Menu %1 and it's nodes removed", $menu->getTitle()));
        } catch (CouldNotDeleteException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $redirect = $this->resultRedirectFactory->create();
        $redirect->setPath('*/*/index');
        return $redirect;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Blackbird_MenuManager::menus');
    }
}