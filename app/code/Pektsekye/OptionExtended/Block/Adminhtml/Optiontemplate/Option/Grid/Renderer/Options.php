<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Option\Grid\Renderer;

class Options extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    public function render(\Magento\Framework\DataObject $row)
    {
        if (!is_null($row->getRowId()))
          return '';
          
        $options = $this->getColumn()->getOptions();
        if (!empty($options) && is_array($options)) {
            $value = $row->getData($this->getColumn()->getIndex());
            if (isset($options[$value]))
              return $options[$value];
        }
        
        return '';   
    }


}
