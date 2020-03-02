<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Option\Grid\Renderer;

class Action extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    protected $_productOption;

    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Catalog\Model\Product\Option $productOption,
        array $data = array()
    ) {
        $this->_productOption = $productOption;
        parent::__construct($context, $data);
    }
    

    public function render(\Magento\Framework\DataObject $row)
    {
        $count = 0;
        $valueCount = $this->getColumn()->getValueCount();
        if (isset($valueCount[$row->getOptionId()]))
          $count = (int) $valueCount[$row->getOptionId()]; 
          
        $actions = array();        
        if (is_null($this->getColumn()->getOnlyValues())){                                 
          $actions[] = array(
                          '@'	=>  array(
                              'href'  => $this->getUrl('*/*/edit', array('option_id' => $row->getOptionId(), 'template_id' => $this->getRequest()->getParam('template_id')))
                          ),
                          '#'	=> __('Edit')           
                      );
        }
        
        $group  = $this->_productOption->getGroupByType($row->getType());
                  
        if ($group == 'select'){        
           $actions[] = array(
                            '@'	=>  array(
                                'href'  => $this->getUrl('*/optiontemplate_value/index', array('option_id' => $row->getOptionId(), 'template_id' => $this->getRequest()->getParam('template_id')))
                            ),
                            '#'	=> __('View Values') . ' ('. $count . ')'           
                        ); 
        }   
        
        return $this->_actionsToHtml($actions);
    }

    protected function _getEscapedValue($value)
    {
        return addcslashes(htmlspecialchars($value),'\\\'');
    }

    protected function _actionsToHtml(array $actions)
    {
        $html = array();
        $attributesObject = new \Magento\Framework\DataObject; 
        foreach ($actions as $action) {
            $attributesObject->setData($action['@']);
            $html[] = '<a ' . $attributesObject->serialize() . '>' . $action['#'] . '</a>';
        }
        $value = implode('<span class="separator">&nbsp;|&nbsp;</span>', $html);
        return '<span class="nobr">' . $value . '</span>';
    }

}
