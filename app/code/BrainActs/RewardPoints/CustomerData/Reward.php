<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\RewardPoints\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use BrainActs\RewardPoints\Model\History as PointsHistory;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Reward
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Reward implements SectionSourceInterface
{

    /**
     * @var \BrainActs\RewardPoints\Model\ResourceModel\History\Collection
     */
    private $points;

    /**
     * @var \BrainActs\RewardPoints\Model\ResourceModel\History\CollectionFactory
     */
    private $pointsCollectionFactory;

    /**
     * @var
     */
    private $isExpirationEnable = null;

    /**
     * @var int
     */
    private $totalPoints = 0;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Reward constructor.
     * @param \BrainActs\RewardPoints\Model\ResourceModel\History\CollectionFactory $pointsCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \BrainActs\RewardPoints\Model\ResourceModel\History\CollectionFactory $pointsCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $_scopeConfig
    )
    {
        $this->pointsCollectionFactory = $pointsCollectionFactory;
        $this->customerSession = $customerSession;
        $this->_scopeConfig = $_scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        $counter = $this->getCounter();
        return [
            'counter' => $counter
        ];
    }

    /**
     * @return string
     */
    private function getCounter()
    {
        //$this->wishlistHelper->getItemCount()
        return $this->createCounter($this->getPoints());
    }

    /**
     * Create button label based on wishlist item quantity
     *
     * @param int $count
     * @return \Magento\Framework\Phrase|null
     */
    private function createCounter($count)
    {
        if ($count > 1) {
            return __('(%1 points)', $count);
        } elseif ($count == 1) {
            return __('(1 point)');
        }
        return null;
    }

    /**
     * @return $this|bool|\BrainActs\RewardPoints\Model\ResourceModel\History\Collection
     */
    public function getPoints()
    {
        if (!($customerId = $this->customerSession->getCustomerId())) {
            return false;
        }

        if (!$this->points) {
            /** @var  points */
            $this->points = $this->pointsCollectionFactory->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'customer_id',
                ['eq' => $customerId]
            )->addFieldToFilter(
                'is_deleted',
                ['eq' => 0]
            )->addFieldToFilter(
                'is_expired',
                ['eq' => 0]
            )->setOrder(
                'created_at',
                'desc'
            );

            if ($this->isExpirationEnabled()) {
                $this->points->addExpiredField($this->getExpirationDays());
            }
            $this->calcTotal();
        }

        return $this->totalPoints;
    }

    /**
     * Check if is expiration logic is enable
     * @return bool
     */
    public function isExpirationEnabled()
    {
        if ($this->isExpirationEnable != null) {
            return $this->isExpirationEnable;
        }

        $isExpirationEnable = (bool)$this->_scopeConfig
            ->getValue(PointsHistory::POINTS_EXPIRATION_XML, ScopeInterface::SCOPE_STORE);
        $this->isExpirationEnable = $isExpirationEnable;
        if ($isExpirationEnable) {
            return $this->isExpirationEnable;
        }

        return $this->isExpirationEnable;
    }

    private function calcTotal()
    {
        foreach ($this->points as $item) {
            $this->totalPoints += $item->getPoints();
        }
    }
}
