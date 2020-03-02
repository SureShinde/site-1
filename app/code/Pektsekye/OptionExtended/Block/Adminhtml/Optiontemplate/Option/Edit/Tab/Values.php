<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Option\Edit\Tab;

use Magento\Backend\Block\Widget;

class Values extends  Widget
{

    protected $_template = 'optiontemplate/option/edit/tab/values.phtml';
    
    protected $_oxTemplateOption;    
    protected $_coreRegistry;    
    
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Pektsekye\OptionExtended\Model\Template\Option $templateOption,           
        array $data = array()
    ) {
        $this->_oxTemplateOption = $templateOption;    
        $this->_coreRegistry = $coreRegistry;         
        parent::__construct($context, $data);
    }
     
   
     
    public function getValuesUrl()
    {
        return $this->getUrl('*/optiontemplate_value/index', array('template_id' => (int) $this->_coreRegistry->registry('current_option')->getTemplateId(), 'option_id' => (int) $this->_coreRegistry->registry('current_option')->getId()));                          
    }

    public function getValueCount()
    {
        return (int) $this->_oxTemplateOption->getResource()->getValueCount((int) $this->_coreRegistry->registry('current_option')->getId());                          
    }

}
