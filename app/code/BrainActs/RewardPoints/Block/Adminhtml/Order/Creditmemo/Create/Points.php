<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\RewardPoints\Block\Adminhtml\Order\Creditmemo\Create;

use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class Points
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Points extends \Magento\Backend\Block\Template
{

    /**
     * Source object
     *
     * @var \Magento\Framework\DataObject
     */
    private $source;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Tax\Model\Config $taxConfig,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->_taxConfig = $taxConfig;
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context, $data);
    }

    /**
     * Initialize creditmemo agjustment totals
     *
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->source = $parent->getSource();
        $total = new \Magento\Framework\DataObject(['code' => 'rewards', 'block_name' => $this->getNameInLayout()]);
        $parent->addTotal($total);
        return $this;
    }

    /**
     * Get source object
     *
     * @return \Magento\Framework\DataObject
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Get label for shipping total based on configuration settings
     *
     * @return string
     */
    public function getLabel()
    {
        return __('Reward Points');
    }

    public function getPointsValue()
    {
        $source = $this->getSource();
        $amount = $source->getRewardPoints() * (-1);

        return $this->priceCurrency->format($amount);
    }
}
