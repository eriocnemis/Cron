<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Eriocnemis\Cron\Controller\Adminhtml\Job;

use \Magento\Framework\App\ResponseInterface;
use \Magento\Framework\Exception\LocalizedException;
use \Eriocnemis\Cron\Controller\Adminhtml\Job as Action;

/**
 * Job change status controller
 */
class ChangeStatus extends Action
{
    /**
     * Change job status action
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $ids = (array)$this->getRequest()->getParam('job_ids');
        if (!count($ids)) {
            $this->messageManager->addError(
                __('Please correct the jobs you requested.')
            );
            return $this->_redirect('*/*/*');
        }

        try {
            $status = (int)$this->getRequest()->getParam('status');
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('name', ['in' => $ids]);
            /** @var \Eriocnemis\Cron\Model\Job $job */
            foreach ($collection as $job) {
                $job->setStatus($status)->save();
            }

            $this->messageManager->addSuccess(
                __('You updated a total of %1 records.', count($ids))
            );
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __('Something went wrong while updating the job(s) status. Please review the log and try again.')
            );
            $this->logger->critical($e);
        }
        $this->_redirect('*/*/index', ['_current' => true]);
    }
}
