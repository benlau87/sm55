<?php
namespace Benlau\CategoryWidget\Block\Widget;

class CategoryWidget extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    protected $_template = 'widget/categorywidget.phtml';

    const DEFAULT_IMAGE_WIDTH = 168;
    const DEFAULT_IMAGE_HEIGHT = 168;
    
    /**
    * \Magento\Catalog\Model\CategoryFactory $categoryFactory
    */
    private $registry;
    protected $_categoryFactory;
    protected $categoryRepository;

    /**
    * @param \Magento\Framework\View\Element\Template\Context $context
    * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
    * @param array $data
    */
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
    \Magento\Framework\Registry $registry,
    \Magento\Catalog\Model\CategoryFactory $categoryFactory,
    \Magento\Catalog\Model\CategoryRepository $categoryRepository
    ) {
        $this->registry = $registry;
        $this->_categoryFactory = $categoryFactory;
        $this->categoryRepository = $categoryRepository;
        parent::__construct($context);
    }

    /**
     * Retrieve current store categories
     *
     * @return mixed
     */
    public function getCategoryCollection()
    {
        $category = $this->registry->registry('current_category');

        $rootCatID = $category->getId();

        $category->load($rootCatID);
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
        $categoryObj = $this->categoryRepository->get($childID);

        $categoryObj->load($childID);
        $childCategories = $categoryObj->getChildrenCategories();
        return $childCategories;
    }
    
    /**
    * Get the width of product image
    * @return int
    */
    public function getImageWidth() {
        if($this->getData('imagewidth')==''){
            return DEFAULT_IMAGE_WIDTH;
        }
        return (int) $this->getData('imagewidth');
    }

    /**
    * Get the height of product image
    * @return int
    */
    public function getImageHeight() {
        if($this->getData('imageheight')==''){
            return DEFAULT_IMAGE_HEIGHT;
        }
        return (int) $this->getData('imageheight');
    }
    
    public function canShowImage(){
        if($this->getData('image') == 'image')
            return true;
        elseif($this->getData('image') == 'no-image')
            return false;
    }

    public function getCategorymodel($id)
    {
        $_category = $this->_categoryFactory->create();
        $_category->load($id);
        return $_category;
    }
}