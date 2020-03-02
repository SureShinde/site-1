<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate;

class NewAction extends \Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
