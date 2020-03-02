<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Grid\Renderer;

class Text extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    public function render(\Magento\Framework\DataObject $row)
    {
        $text = '';    
        $productIds = $this->getColumn()->getProductIds();
        if (isset($productIds[$row->getTemplateId()])){               
          $text  = $productIds[$row->getTemplateId()]['product_ids'];
          $count = $productIds[$row->getTemplateId()]['product_count'];

          if (strlen($text) > 20)
            $text = substr($text ,0,15) . '... (' . $count . ')';
        }
        return $text;
    }

}
