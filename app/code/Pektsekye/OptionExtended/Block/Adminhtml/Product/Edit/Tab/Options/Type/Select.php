<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Product\Edit\Tab\Options\Type;

class Select extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options\Type\AbstractType
{
    /**
     * @var string
     */
    protected $_template = 'product/edit/options/type/select.phtml';

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setCanEditPrice(true);
        $this->setCanReadPrice(true);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->addChild(
            'add_select_row_button',
            'Magento\Backend\Block\Widget\Button',
            array(
                'label' => __('Add New Row'),
                'class' => 'add add-select-row',
                'id' => 'product_option_<%- data.option_id %>_add_select_row'
            )
        );

        return parent::_prepareLayout();
    }



    /**
     * @return string
     */
    public function getUploadUrl()
    {
      return $this->_urlBuilder->addSessionParam()->getUrl('catalog/product_gallery/upload');
    }



    /**
     * @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_select_row_button');
    }

    /**
     * @return string
     */
    public function getPriceTypeSelectHtml($extraParams = '')
    {
        $this->getChildBlock(
            'option_price_type'
        )->setData(
            'id',
            'product_option_<%- data.id %>_select_<%- data.select_id %>_price_type'
        )->setName(
            'product[options][<%- data.id %>][values][<%- data.select_id %>][price_type]'
        );

        return parent::getPriceTypeSelectHtml($extraParams);
    }
}
