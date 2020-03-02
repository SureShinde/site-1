<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Ox;

abstract class Export extends \Magento\Backend\App\AbstractAction
{

    protected $resultPageFactory;
    protected $_fileFactory;
    

    public function __construct(
        \Magento\Backend\App\Action\Context $context,       
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory        
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_fileFactory = $fileFactory;        
        parent::__construct($context);
    }
    
    
    protected function _isAllowed()
    {
       return $this->_authorization->isAllowed('Pektsekye_OptionExtended::export');
    }   
}
