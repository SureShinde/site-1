<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Value\Helper\Form;

class Text extends \Magento\Framework\Data\Form\Element\Text
{

    protected $_layout;
    
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\View\LayoutInterface $layout,
        array $data = array()
    ) {
        $this->_layout = $layout;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }


    public function getAfterElementHtml()
    {
        $content = $this->_layout->createBlock('Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Value\Helper\Form\Text\Content');
        return $content->toHtml();
    }
      
      
    public function getHtmlAttributes()
    {
        return array('type', 'title', 'class', 'style', 'onclick', 'onchange', 'onkeyup', 'onblur', 'disabled', 'readonly', 'maxlength', 'tabindex');
    }

}
