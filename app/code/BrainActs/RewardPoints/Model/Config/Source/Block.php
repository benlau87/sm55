<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\RewardPoints\Model\Config\Source;

/**
 * Class Block
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Block implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Cms\Api\BlockRepositoryInterface
     */
    private $blockRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaInterface
     */
    private $searchCriteria;

    /**
     * Block constructor.
     * @param \Magento\Cms\Api\BlockRepositoryInterface $blockRepository
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     */
    public function __construct(
        \Magento\Cms\Api\BlockRepositoryInterface $blockRepository,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    ) {
        $this->blockRepository = $blockRepository;
        $this->searchCriteria = $searchCriteria;
    }

    /**
     * Options getter
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function toOptionArray()
    {
        $collection = $this->blockRepository->getList($this->searchCriteria);
        $options = [];
        $options[] = [
            'value' => '',
            'label' => __('N/A')
        ];
        foreach ($collection->getItems() as $block) {
            $options[] = [
                'value' => $block->getId(),
                'label' => $block->getTitle()
            ];
        }

        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [0 => __('No'), 1 => __('Yes')];
    }
}
