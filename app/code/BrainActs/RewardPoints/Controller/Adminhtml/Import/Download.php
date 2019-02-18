<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace BrainActs\RewardPoints\Controller\Adminhtml\Import;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\ImportExport\Controller\Adminhtml\Import as ImportController;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Download
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Download extends \Magento\ImportExport\Controller\Adminhtml\Import\Download
{

    /**
     * Download sample file action
     *
     * @return \Magento\Framework\Controller\Result\Raw
     * @throws \Exception
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function execute()
    {
        $fileName = $this->getRequest()->getParam('filename');

        if ($fileName!='reward_points') {
            return parent::execute();
        }

        $fileName .= '.csv';

        $moduleSampleFiles = 'BrainActs_RewardPoints';
        $dirModule = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, $moduleSampleFiles);
        $fileAbsolutePath = $dirModule . '/Files/Sample/' . $fileName;
        $readDirectory = $this->readFactory->create($dirModule);
        $filePath = $readDirectory->getRelativePath($fileAbsolutePath);

        if (!$readDirectory->isFile($filePath)) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $this->messageManager->addError(__('There is no sample file for this entity.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/import');
            return $resultRedirect;
        }

        $fileSize = isset($readDirectory->stat($filePath)['size'])
            ? $readDirectory->stat($filePath)['size'] : null;

        $this->fileFactory->create(
            $fileName,
            null,
            DirectoryList::VAR_DIR,
            'application/octet-stream',
            $fileSize
        );

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        $resultRaw->setContents($readDirectory->readFile($filePath));
        return $resultRaw;
    }
}
