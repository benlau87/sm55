<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\RewardPoints\Block\Adminhtml\Sales\Order\Invoice;

/**
 * Class Points
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Points extends \Magento\Framework\View\Element\Template
{
    private $order;

    private $source;

    /**
     * @return bool
     */
    public function displayFullSummary()
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return mixed
     */
    public function getStore()
    {
        return $this->order->getStore();
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return mixed
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * @return mixed
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }

    /**
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->order = $parent->getOrder();
        $this->source = $parent->getSource();

        $value = $this->order->getRewardPointsAmount();
        $fee = new \Magento\Framework\DataObject(
            [
                'code' => 'reward_points',
                'strong' => false,
                'value' => -$value,
                'base_value' => -$value,
                'label' => __('Reward Points'),
            ]
        );
        $parent->addTotal($fee, 'reward_points');
        return $this;
    }
}
