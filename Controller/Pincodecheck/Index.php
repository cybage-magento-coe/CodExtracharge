<?php

namespace Cybage\CodExtracharge\Controller\Pincodecheck;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Prince\PincodeChecker\Helper\Data
     */
    protected $pincodeHelper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Prince\PincodeChecker\Helper\Data $pincodeHelper
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Cybage\CodExtracharge\Helper\Pincode $pincodeHelper,
        \Magento\Framework\Controller\Result\JsonFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->pincodeHelper = $pincodeHelper;
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if ($this->getRequest()->isAjax()) {
            $pincode = $this->getRequest()->getParam('p', false);
            $id = $this->getRequest()->getParam('id', false);

            $pincodeStatus = $this->pincodeHelper->getPincodeStatus($pincode);
            $productStatus = $this->pincodeHelper->getProductPincodeStatus($id, $pincode);

            if ($productStatus) {
                $message = $this->pincodeHelper->getMessage(false, $pincode);
            } else {
                $message = $this->pincodeHelper->getMessage($pincodeStatus, $pincode);
            }
            $resultJson = $this->resultPageFactory->create();
            
            return $resultJson->setData($message);
        }
        return false;
    }
}
