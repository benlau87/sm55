<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\RewardPoints\Model\Sales\Pdf;

use Magento\Sales\Model\Order\Pdf\Total\DefaultTotal;

/**
 * Class RewardPoints
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class RewardPoints extends DefaultTotal
{
    /**
     * Get Total amount from source
     *
     * @return float
     */
    public function getAmount()
    {
        $amount = $this->getSource()->getOrder()->getDataUsingMethod($this->getSourceField());
        return (-1) * $amount;
    }
}
