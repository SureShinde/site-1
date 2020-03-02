<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Option\Edit;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\Form as DataForm;

class Form extends Generic
{

  protected $_wysiwygConfig;

  public function __construct(
      \Magento\Backend\Block\Widget\Context $context,
      \Magento\Framework\Registry $registry,
      \Magento\Framework\Data\FormFactory $formFactory,
      \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
      array $data = array()
  ) {
      $this->_wysiwygConfig = $wysiwygConfig;
      parent::__construct($context, $registry, $formFactory, $data);
  }
  
  
  protected function _prepareLayout()
  {
      parent::_prepareLayout();
      if ($this->_wysiwygConfig->isEnabled()) {
  //        $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
     }
  }    

  
  protected function _prepareForm()
  {
      /** @var DataForm $form */
      $form = $this->_formFactory->create(
          array('data' => array('id' => 'edit_form', 'action' => $this->getUrl('*/*/save', array('_current'=>true)), 'method' => 'post'))
      );
      $form->setUseContainer(true);
      $this->setForm($form);
      return parent::_prepareForm();
  } 


 
}
