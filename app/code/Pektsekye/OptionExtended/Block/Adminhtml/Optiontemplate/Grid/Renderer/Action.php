<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Grid\Renderer;

class Action extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    public function render(\Magento\Framework\DataObject $row)
    {
        $count = 0;
        $optionCount = $this->getColumn()->getOptionCount();
        if (isset($optionCount[$row->getTemplateId()]))        
          $count = (int) $optionCount[$row->getTemplateId()];

        $actions = array(
                          array(
                              '@'	=>  array(
                                  'href'  => $this->getUrl('*/*/edit', array('template_id' => $row->getTemplateId()))
                              ),
                              '#'	=> __('Edit')          
                          ),        
                          array(
                              '@'	=>  array(
                                  'href'  => $this->getUrl('*/optiontemplate_option/index', array('template_id' => $row->getTemplateId()))
                              ),
                              '#'	=> __('View Options') . ' ('. $count . ')'           
                          )        
        );


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
