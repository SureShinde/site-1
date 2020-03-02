<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Option\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{

    protected $_coreRegistry = null;
    protected $_coreOption;      
    
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\Option $coreOption,          
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_coreOption   = $coreOption;         
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }
    
    
    
    public function _construct()
    {
        parent::_construct();
        $this->setId('optionextended_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Option Information'));
    }



    protected function _beforeToHtml()
    {
        $this->addTab('general', array(
            'label'     => __('General Information'),
            'content'   => $this->getChildHtml('main'),
            'active'    => true
        ));
        
        $option = $this->_coreRegistry->registry('current_option');
        $group  = $this->_coreOption->getGroupByType($option->getType());      
        if (!is_null($option->getId()) && $group == 'select'){
          $this->addTab('values', array(
              'label'     => __('Values'),
              'content'   => $this->getChildHtml('values')         
          ));        
        }
        
        $this->_updateActiveTab();
        return parent::_beforeToHtml();
    }

    protected function _updateActiveTab()
    {
        $tabId = $this->getRequest()->getParam('tab');
        if ($tabId) {
            $tabId = preg_replace("#{$this->getId()}_#", '', $tabId);
            if ($tabId) {
                $this->setActiveTab($tabId);
            }
        }
    }
}
