<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace BrainActs\RewardPoints\Model\Import\Behavior;

/**
 * Class Custom
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Custom extends \Magento\ImportExport\Model\Source\Import\AbstractBehavior
{
    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'add_update' => __('Add')
        ];
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return 'custom';
    }
}
