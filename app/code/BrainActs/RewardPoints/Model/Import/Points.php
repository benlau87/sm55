<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\RewardPoints\Model\Import;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\ImportExport\Model\Import;

/**
 * Class Locator
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
//@codingStandardsIgnoreFile
class Points extends AbstractEntity
{

    const ENTITY_TYPE_CODE = 'reward_points';

    /** COLUMNS */
    const COL_EMAIL = 'email';

    const COL_POINTS = 'points';

    const COL_REASON = 'reason';

    /**#@+
     * Keys which used to build result data array for future update
     */
    const ENTITIES_TO_CREATE_KEY = 'entities_to_create';

    const ENTITIES_TO_UPDATE_KEY = 'entities_to_update';

    const ATTRIBUTES_TO_SAVE_KEY = 'attributes_to_save';

    /** ERRORS */

    const ERROR_COL_NAME_IS_EMPTY = 'nameIsEmpty';

    const ERROR_VALUE_IS_REQUIRED = 'fieldIsRequired';

    const ERROR_INVALID_STORE = 'invalidStore';

    /** PROPERTIES */
    /**
     * If we should check column names
     *
     * @var bool
     */
    protected $needColumnCheck = true;

    /**
     * Need to log in import history
     *
     * @var bool
     */
    protected $logInHistory = true;

    /**
     * Id of next entity row
     *
     * @var int
     */
    private $nextEntityId;

    /**
     * Entity DB table name.
     *
     * @var string
     */
    private $entityTable;

    /**
     * Locator model
     *
     * @var \BrainActs\StoreLocator\Model\Locator
     */
    private $historyModel;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * Customers information from import file
     *
     * @var array
     */
    private $newLocations = [];

    /**
     * All stores code-ID pairs.
     *
     * @var array
     */
    private $storeCodeToId = [];

    /**
     * Valid column names
     *
     * @array
     */
    protected $validColumnNames = [
        self::COL_EMAIL,
        self::COL_POINTS,
        self::COL_REASON,
    ];

    /**
     * Permanent entity columns.
     *
     * @var string[]
     */
    protected $_permanentAttributes = [
        self::COL_EMAIL,
        self::COL_POINTS,
        self::COL_REASON,
    ];

    /**
     * Rules for fields
     * @var array
     */
    private $fieldsParams = [
        self::COL_EMAIL => ['is_required' => true, 'type' => 'varchar'],
        self::COL_POINTS => ['is_required' => true, 'type' => 'varchar'],
        self::COL_REASON => ['is_required' => false, 'type' => 'varchar'],
    ];

    /**
     * Points constructor.
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\ImportExport\Helper\Data $importExportData
     * @param \Magento\ImportExport\Model\ResourceModel\Import\Data $importData
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @param \BrainActs\RewardPoints\Model\HistoryFactory $historyFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Stdlib\StringUtils $string,
        ProcessingErrorAggregatorInterface $errorAggregator,
        \BrainActs\RewardPoints\Model\HistoryFactory $historyFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {

        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->string = $string;
        $this->errorAggregator = $errorAggregator;
        $this->storeManager = $storeManager;
        $this->customerRepository = $customerRepository;

        foreach ($this->errorMessageTemplates as $errorCode => $message) {
            $this->getErrorAggregator()->addErrorMessageTemplate($errorCode, $message);
        }

        $this->_dataSourceModel = $importData;
        $this->_connection = $resource->getConnection();

        $this->addMessageTemplate(
            self::ERROR_VALUE_IS_REQUIRED,
            __('\'%s\' is empty')
        );

        $this->addMessageTemplate(
            self::ERROR_INVALID_STORE,
            __('Invalid value in Store column (store does not exists?)')
        );

        $this->historyModel = $historyFactory->create();
        /** @var $locatorResource \BrainActs\StoreLocator\Model\ResourceModel\Locator */
        $locatorResource = $this->historyModel->getResource();
        $this->entityTable = $locatorResource->getMainTable();
    }

    /**
     * Initialize stores data
     *
     * @param bool $withDefault
     * @return $this
     */
    private function initStores($withDefault = false)
    {
        /** @var $store \Magento\Store\Model\Store */
        foreach ($this->storeManager->getStores($withDefault) as $store) {
            $this->storeCodeToId[$store->getCode()] = $store->getId();
        }
        return $this;
    }

    /**
     * Import data rows.
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _importData()
    {
        $this->savePointsHistory();
        return true;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function savePointsHistory()
    {
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $entitiesToCreate = [];
            $entitiesToUpdate = [];
            $entitiesToDelete = [];
            $attributesToSave = [];
            foreach ($bunch as $rowNumber => $rowData) {
                if (!$this->validateRow($rowData, $rowNumber)) {
                    continue;
                }

                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNumber);
                    continue;
                }

                $processedData = $this->_prepareDataForUpdate($rowData);
                $entitiesToCreate = array_merge($entitiesToCreate, $processedData[self::ENTITIES_TO_CREATE_KEY]);
                $entitiesToUpdate = array_merge($entitiesToUpdate, $processedData[self::ENTITIES_TO_UPDATE_KEY]);
                foreach ($processedData[self::ATTRIBUTES_TO_SAVE_KEY] as $tableName => $customerAttributes) {
                    if (!isset($attributesToSave[$tableName])) {
                        $attributesToSave[$tableName] = [];
                    }
                    $attributesToSave[$tableName] = array_diff_key(
                            $attributesToSave[$tableName],
                            $customerAttributes
                        ) + $customerAttributes;
                }
            }

            $this->updateItemsCounterStats($entitiesToCreate, $entitiesToUpdate, $entitiesToDelete);

            /**
             * Save prepared data
             */
            if ($entitiesToCreate || $entitiesToUpdate) {
                $this->saveHistoryEntities($entitiesToCreate, $entitiesToUpdate);
            }
        }

        return true;
    }

    /**
     * EAV entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return self::ENTITY_TYPE_CODE;
    }

    /**
     * Validate data row.
     *
     * @param array $rowData
     * @param int $rowNum
     * @return boolean
     */
    public function validateRow(array $rowData, $rowNum)
    {
        if (isset($this->_validatedRows[$rowNum])) {
            // check that row is already validated
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }
        $this->_validatedRows[$rowNum] = true;
        $this->_processedEntitiesCount++;
        $this->_validateRowForUpdate($rowData, $rowNum);

        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }

    /**
     * Validate row data for add/update behaviour
     *
     * @param array $rowData
     * @param int $rowNumber
     * @return void
     */
    private function _validateRowForUpdate(array $rowData, $rowNumber)
    {

        // check simple fields
        foreach ($this->fieldsParams as $fieldName => $fieldParams) {
            if (isset($rowData[$fieldName]) && strlen($rowData[$fieldName])) {
                $this->isAttributeValid($fieldName, $fieldParams, $rowData, $rowNumber);
            } elseif ($fieldParams['is_required'] === true) {
                $this->addRowError(self::ERROR_VALUE_IS_REQUIRED, $rowNumber, $fieldName);
            }
        }


    }

    /**
     * Prepare locator data for update
     *
     * @param array $rowData
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function _prepareDataForUpdate(array $rowData)
    {
        $entitiesToCreate = [];
        $entitiesToUpdate = [];
        $attributesToSave = [];

        // entity table data
        $now = new \DateTime();
        if (empty($rowData['created_at'])) {
            $createdAt = $now;
        } else {
            $createdAt = (new \DateTime())->setTimestamp(strtotime($rowData['created_at']));
        }

        $newLocator = false;
        $entityId = $this->getLocatorId();
        if (!$entityId) {
            // create
            $newLocator = true;
            $entityId = $this->_getNextEntityId();
            $this->newLocations[$rowData[self::COL_EMAIL]] = $entityId;
        }

        $entityRow = [
            'created_at' => $createdAt->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT),
            'updated_at' => $now->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT),
            'history_id' => $entityId,
        ];

        //base field values
        foreach (array_intersect_key($rowData, $this->fieldsParams) as $attributeCode => $value) {
            if ($newLocator && !strlen($value)) {
                continue;
            }
            $entityRow[$attributeCode] = $value;
        }

        if ($newLocator) {
            // create
            $entitiesToCreate[] = $entityRow;
        } else {
            // edit
            $entitiesToUpdate[] = $entityRow;
        }

        return [
            self::ENTITIES_TO_CREATE_KEY => $entitiesToCreate,
            self::ENTITIES_TO_UPDATE_KEY => $entitiesToUpdate,
            self::ATTRIBUTES_TO_SAVE_KEY => $attributesToSave
        ];
    }

    /**
     *
     * @return bool
     */
    private function getLocatorId()
    {
        return false;
    }

    /**
     * Retrieve next locator entity id
     *
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function _getNextEntityId()
    {
        if (!$this->nextEntityId) {
            $this->nextEntityId = $this->_resourceHelper->getNextAutoincrement($this->entityTable);
        }
        return $this->nextEntityId++;
    }

    /**
     * Update proceed items counter
     *
     * @param array $created
     * @param array $updated
     * @param array $deleted
     * @return $this
     */
    private function updateItemsCounterStats(array $created = [], array $updated = [], array $deleted = [])
    {
        $this->countItemsCreated = count($created);
        $this->countItemsUpdated = count($updated);
        $this->countItemsDeleted = count($deleted);
        return $this;
    }

    /**
     * @param array $entitiesToCreate
     * @param array $entitiesToUpdate
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function saveHistoryEntities(array $entitiesToCreate, array $entitiesToUpdate)
    {

        if ($entitiesToCreate) {
            foreach ($entitiesToCreate as $item) {
                try{
                    $customer = $this->customerRepository->get($item[self::COL_EMAIL]);
                    unset($item['email']);
                    $item['customer_id'] = $customer->getId();
                    $item['customer_name'] = implode(' ', [$customer->getFirstname(), $customer->getLastname()]);
                    $item['rule_name'] = 'Custom Update By Manager';
                    if ($item[self::COL_POINTS] >= 0) {
                        $item['type_rule'] = 1;
                    } else {
                        $item['type_rule'] = 2;
                    }
                    $this->_connection->insert($this->entityTable, $item);
                }catch(NoSuchEntityException $e){

                }
            }
        }

        return $this;
    }
}
