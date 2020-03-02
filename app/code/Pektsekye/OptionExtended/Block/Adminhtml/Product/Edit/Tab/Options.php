<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Product\Edit\Tab;

use Magento\Backend\Block\Widget;

class Options extends Widget
{

    protected $_template = 'product/edit/options.phtml';
    protected $_coreRegistry;
  

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $coreRegistry,        
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;       
        parent::__construct($context, $data);
    }
   
 
    protected function _prepareLayout()
    {
        $this->addChild(
            'add_button',
            'Magento\Backend\Block\Widget\Button',
            array('label' => __('Add New Option'), 'class' => 'add', 'id' => 'add_new_defined_option')
        );

        $this->addChild('options_box', 'Pektsekye\OptionExtended\Block\Adminhtml\Product\Edit\Tab\Options\Option');

        $this->addChild(
            'import_button',
            'Magento\Backend\Block\Widget\Button',
            array('label' => __('Import Options'), 'class' => 'add', 'id' => 'import_new_defined_option')
        );
       
        $this->addChild(
            'option_template',
            'Pektsekye\OptionExtended\Block\Adminhtml\Product\Edit\Tab\Options\Template',
            array('product_id' => $this->getProduct()->getId())
        );        

        return parent::_prepareLayout();
    }


    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', $this->_coreRegistry->registry('product'));
        }
       
        return $this->getData('product');
    }



    public function getOptionTemplateHtml()
    {
        return $this->getChildHtml('option_template');
    }
    
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }



    public function getOptionsBoxHtml()
    {
        return $this->getChildHtml('options_box');
    }
 
}
