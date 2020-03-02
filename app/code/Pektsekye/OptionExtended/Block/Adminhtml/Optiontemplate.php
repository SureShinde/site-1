<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml;

class Optiontemplate extends \Magento\Backend\Block\Widget\Grid\Container
{



    protected function _construct()
    {
        $this->_controller = 'adminhtml_optiontemplate';
        $this->_blockGroup = 'Pektsekye_OptionExtended';
        $this->_headerText = __('Option Templates');
        $this->addButtonLabel = __('Add Template');
        parent::_construct();
    }





}
