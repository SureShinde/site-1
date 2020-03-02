<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate\Value;

class NewAction extends \Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate\Value
{


  public function execute()
  {
		$this->_forward('edit');
	}
}
