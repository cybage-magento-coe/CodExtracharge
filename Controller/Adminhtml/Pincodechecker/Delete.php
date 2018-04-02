<?php


namespace Cybage\CodExtracharge\Controller\Adminhtml\Pincodechecker;

class Delete extends \Cybage\CodExtracharge\Controller\Adminhtml\Pincodechecker
{

    public function __construct(
        \Cybage\CodExtracharge\Model\Pincodechecker $pincodechecker
    ) {
        $this->pincodechecker = $pincodechecker;
    }
    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('pincode_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->pincodechecker;
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccess(__('You deleted the Pincode.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['pincode_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a Pincode to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
