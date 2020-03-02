<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Value\Grid\Renderer;

class Image extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    protected $_product; 
    protected $_imageHelper;     
    
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Catalog\Model\Product $product,
        \Magento\Catalog\Helper\Image $imageHelper,             
        array $data = array()
    ) {
        $this->_product = $product;   
        $this->_imageHelper = $imageHelper;          
        parent::__construct($context, $data);
    }


    public function render(\Magento\Framework\DataObject $row)
    {
      $image = $row->getData($this->getColumn()->getIndex());
      if (!empty($image)){
        $url = $this->_imageHelper->init($this->_product, 'product_page_image_small', array('type'=>'thumbnail'))->resize(40)->setImageFile($image)->getUrl();
        return '<img src="'. $url .'" >';
      }
      return $image;          
    }

}
