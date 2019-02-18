<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\RewardPoints\Block;

/**
 * Class Link
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Link extends \Magento\Framework\View\Element\Html\Link
{
    const SORT_ORDER = 'sortOrder';
    /**
     * Template name
     *
     * @var string
     */
    protected $_template = 'BrainActs_RewardPoints::link.phtml'; //@codingStandardsIgnoreLine

    /**
     * @var \BrainActs\RewardPoints\Helper\Data
     */
    public $pointsHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \BrainActs\RewardPoints\Helper\Data $pointsHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \BrainActs\RewardPoints\Helper\Data $pointsHelper,
        array $data = []
    ) {
        $this->pointsHelper = $pointsHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    protected function _toHtml()//@codingStandardsIgnoreLine
    {
        if ($this->pointsHelper->isEnabled()) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('points/history');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Reward Points');
    }

    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }
}
