<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{


    protected $_coreRegistry = null;
    
    
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }
    

    protected function _construct()
    {
        parent::_construct();
        $this->setId('optiontemplate_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Template Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('main', array(
            'label'     => __('General Information'),
            'content'   => $this->getChildHtml('main'),
            'active'    => true
        ));

        $this->addTab('products', array(
            'label'     => __('Products'),
            'content'   => $this->getChildHtml('products'),
        ));
        
        $template = $this->_coreRegistry->registry('current_template');
        if (!is_null($template->getId())){
          $this->addTab('options', array(
              'label'     => __('Options'),
              'content'   => $this->getChildHtml('options'),
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
