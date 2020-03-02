<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Ox;

abstract class Pickerimage extends \Magento\Backend\App\AbstractAction
{

    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,       
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
    
    
    protected function _isAllowed()
    {
       return $this->_authorization->isAllowed('Pektsekye_OptionExtended::pickerimage');
    }   
}
