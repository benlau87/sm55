<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\RewardPoints\Block\Adminhtml\Order\Creditmemo\View;

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
     * Source object
     *
     * @var \Magento\Framework\DataObject
     */
    private $order;

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
        /** @var $parent \Magento\Sales\Block\Adminhtml\Order\Invoice\Totals */
        $parent = $this->getParentBlock();
        $this->source = $parent->getSource();
        $this->order = $parent->getOrder();

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
        $amount = $this->order->getRewardPointsAmount() * (-1);

        return $this->priceCurrency->format($amount);
    }

    /**
     * @return array
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * @return array
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }
}
