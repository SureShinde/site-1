<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Option;

class Import extends \Magento\Backend\Block\Widget\Form\Container
{


  protected function _construct()
  {  
    $this->_controller = 'adminhtml_optiontemplate_option';
    $this->_blockGroup = 'Pektsekye_OptionExtended';   
    $this->_mode = 'import';  
      
    $this->_backButtonLabel = __('Back to Options');

    parent::_construct();

    $this->removeButton('reset');
    $this->updateButton('save', 'label', __('Import Options'));        

    $this->_formScripts[] = "
            productGridCheckboxCheck = function(grid, element, checked) {
              $('product_id').value = element.value;							
            };        
            productGridRowClick = function(grid, event) {
                var trElement = Event.findElement(event, 'tr');
                var isInput = Event.element(event).tagName == 'INPUT';
                if (trElement) {
                    var radio = Element.select(trElement, 'input');
                    if (radio[0]) {
                        var checked = isInput ? radio[0].checked : !radio[0].checked;
                        grid.setCheckboxChecked(radio[0], checked);
                    }
                }
            };                 
         "; 
            
  }
  
  public function getHeaderText()
  {
      return __('Choose product to import options from');
  }
    
  public function getBackUrl()
  {
      return $this->getUrl('*/optiontemplate_option/index', array('_current'=>true));
  } 
    
}
