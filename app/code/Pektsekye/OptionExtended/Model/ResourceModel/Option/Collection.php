<?php

namespace Pektsekye\OptionExtended\Model\ResourceModel\Option;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     */
    public function _construct()
    {
        $this->_init('Pektsekye\OptionExtended\Model\Option', 'Pektsekye\OptionExtended\Model\ResourceModel\Option');
    }


    public function joinNotes($storeId)
    {
        $this->getSelect()->joinLeft(array('option_note_default' => $this->getTable('optionextended_option_note')),
                '`main_table`.`ox_option_id` = `option_note_default`.`ox_option_id` and `option_note_default`.`store_id` = "0"',
                array())
            ->from('', array('default_note' => 'option_note_default.note'));

        if ($storeId !== null) {
            $this->getSelect()
                ->from('', array('store_note' => 'option_note.note', 'note' => 'IFNULL(`option_note`.`note`, `option_note_default`.`note`)'))
                ->joinLeft(array('option_note' => $this->getTable('optionextended_option_note')),
                    '`main_table`.`ox_option_id` = `option_note`.`ox_option_id` and `option_note`.`store_id` = "' . $storeId . '"',
                    array());
        }
        return $this;
    }

}
