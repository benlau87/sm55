<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\RewardPoints\Block\Order;

/**
 * Class Totals
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Totals extends \Magento\Framework\View\Element\AbstractBlock
{
    /**
     * @var \Magento\Framework\DataObject
     */
    private $dataObject;

    /**
     * Totals constructor.
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\DataObject $dataObject
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Framework\DataObject $dataObject,
        array $data = []
    ) {
        $this->dataObject = $dataObject;
        parent::__construct($context, $data);
    }

    public function initTotals()
    {

        $orderTotalsBlock = $this->getParentBlock();
        $order = $orderTotalsBlock->getOrder();
        $points = $order->getRewardPointsAmount();

        if ($points > 0) {
            $this->dataObject->addData([
                'code' => 'reward_total',
                'label' => __('Reward Points'),
                'value' => (-1) * $points,
            ]);

            $orderTotalsBlock->addTotal($this->dataObject, 'subtotal');
        }
    }
}
