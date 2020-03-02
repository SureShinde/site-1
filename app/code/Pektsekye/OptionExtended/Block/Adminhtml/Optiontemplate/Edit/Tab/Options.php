<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Edit\Tab;

use Magento\Backend\Block\Widget;

class Options extends  Widget
{

    protected $_template = 'optiontemplate/edit/tab/options.phtml';
    
    protected $_oxTemplate;    
    protected $_coreRegistry;    
    
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Pektsekye\OptionExtended\Model\Template $template,           
        array $data = array()
    ) {
        $this->_oxTemplate = $template;    
        $this->_coreRegistry = $coreRegistry;         
        parent::__construct($context, $data);
    }
     
     
     
    public function getOptionsUrl()
    {
        return $this->getUrl('*/optiontemplate_option/index', array('template_id' => (int) $this->_coreRegistry->registry('current_template')->getId()));                          
    }



    public function getOptionCount()
    {
        return (int) $this->_oxTemplate->getResource()->getOptionCount((int) $this->_coreRegistry->registry('current_template')->getId());                          
    }

}
