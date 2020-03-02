<?php

namespace Pektsekye\OptionExtended\Model\ResourceModel\Template\Option;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     */
    public function _construct()
    {
        $this->_init('Pektsekye\OptionExtended\Model\Template\Option', 'Pektsekye\OptionExtended\Model\ResourceModel\Template\Option');
    }


    public function joinTitle()
    {
        $this->getSelect()
            ->join(array('default_option_title'=>$this->getTable('optionextended_template_option_title')),
                '`default_option_title`.option_id=`main_table`.option_id AND `default_option_title`.store_id=0', 
                array('title'));

        return $this;
    }     
     
    public function joinPrice()
    {
        $this->getSelect()
            ->joinLeft(array('default_option_price'=>$this->getTable('optionextended_template_option_price')),
                '`default_option_price`.option_id=`main_table`.option_id AND `default_option_price`.store_id=0', 
                array('price' => 'FORMAT(price, 2)', 'price_type'));

        return $this;
    }  
      
    public function joinNote()
    {
        $this->getSelect()
            ->join(array('default_option_note'=>$this->getTable('optionextended_template_option_note')),
                '`default_option_note`.option_id=`main_table`.option_id AND `default_option_note`.store_id=0', 
                array('note'));

        return $this;
    } 
                  	 
}
