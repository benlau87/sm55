<?php

namespace Benlau\CategoryFooterWidget\Block\Widget;

class CategoryFooterWidget extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    protected $_template = 'widget/categorywidget.phtml';

    /**
     * \Magento\Catalog\Model\CategoryFactory $categoryFactory
     */
    protected $_categoryFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
    )
    {
        $this->_categoryFactory = $categoryFactory;
        parent::__construct($context);
    }

    /**
     * @param $catId
     *
     * @return \Magento\Catalog\Model\Category
     */
    protected function _getCatData($catId)
    {
        $catModel = $this->_categoryFactory->create();

        return $catModel->load($catId);
    }

    /**
     * Retrieve current store categories
     *
     * @return \Magento\Framework\Data\Tree\Node\Collection|\Magento\Catalog\Model\Resource\Category\Collection|array
     */
    public function getCategoryCollection()
    {
        $rootCatID = NULL;
        if ($this->getData('parentcat') > 0)
            $rootCatID = $this->getData('parentcat');
        else
            $rootCatID = $this->_storeManager->getStore()->getRootCategoryId();


        $category = $this->_getCatData($rootCatID);
        $childCategories = $category->getChildrenCategories();
        return $childCategories;
    }

    /**
     * Retrieve sub categories
     *
     * @return \Magento\Framework\Data\Tree\Node\Collection|\Magento\Catalog\Model\Resource\Category\Collection|array
     */
    public function getCategoryChildren($childID)
    {
        $category = $this->_getCatData($childID);

        $childCategories = $category->getChildrenCategories();
        return $childCategories;
    }

    public function getDisplayStyle()
    {
        if ($this->getData('display_style') == 'footer')
            return 'footer';
        elseif ($this->getData('display_style') == 'list')
            return 'list';
    }

    public function getCategoryDepth()
    {
        if ($this->getData('depth') == '1')
            return 1;
        elseif ($this->getData('depth') == '2')
            return 2;
    }

    public function showIcon()
    {
        if ($this->getData('show_icon') == '1')
            return true;
        else
            return false;
    }

    public function showHeading()
    {
        $showHeading = false;
        if ($this->getData('show_heading') == 1) {
            $categoryId = $this->getData('parentcat');
            $category = $this->_categoryFactory->create()->load($categoryId);
            $categoryName = $category->getName();
            $categoryLink = $category->getUrl();

            $showHeading = array('id' => $categoryId, 'title' => $categoryName, 'url' => $categoryLink);
            return $showHeading;
        }

        return $showHeading;
    }

    public function getCategoryIcon($catId)
    {
        $catData = $this->_getCatData($catId);

        $iconHtml = $catData->getData('smartmenu_cat_icon');
        if (empty($iconHtml)) {
            $iconImage = $catData->getData('smartmenu_cat_imgicon');
            if (!empty($iconImage)) {
                $iconHtml = '<img alt="' . $catData->getData('name') . '" src="' . $this->_storeManager->getStore()->getBaseUrl() . 'pub/media/catalog/category/' . $iconImage . '">';
            }
        }

        return $iconHtml;
    }
}