<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\RewardPoints\Model\Order\Creditmemo\Total;

use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;

/**
 * Class Points
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Points extends AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return $this|AbstractTotal
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {

        $creditmemo->setRewardPoints(0);

        $order = $creditmemo->getOrder();

        $points = $order->getRewardPointsAmount();

        $creditmemo->setRewardPoints($points);

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $points);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $points);

        return $this;
    }
}
