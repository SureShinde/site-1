<?php

namespace Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate;

class TemplateData extends \Pektsekye\OptionExtended\Controller\Adminhtml\Optiontemplate
{


  public function execute()
  {
    $templateId = (int) $this->getRequest()->getParam('template_id');

    $data = $this->_oxTemplate->getOptionsData($templateId);
    $this->getResponse()->setBody($this->_jsonEncoder->encode($data));     
  } 

}
