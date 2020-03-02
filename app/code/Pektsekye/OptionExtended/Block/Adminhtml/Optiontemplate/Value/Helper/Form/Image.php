<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Value\Helper\Form;

class Image extends \Magento\Framework\Data\Form\Element\AbstractElement
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


    public function getElementHtml()
    {
        $content = $this->_layout->createBlock('Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Value\Helper\Form\Image\Content');
        return $content->toHtml();
    }


}
