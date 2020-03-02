<?php

namespace Pektsekye\OptionExtended\Model\ResourceModel\Value;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     */
    public function _construct()
    {
        $this->_init('Pektsekye\OptionExtended\Model\Value', 'Pektsekye\OptionExtended\Model\ResourceModel\Value');
    }

    public function joinDescriptions($storeId)
    {
        $this->getSelect()->joinLeft(array('value_description_default' => $this->getTable('optionextended_value_description')),
                '`main_table`.`ox_value_id` = `value_description_default`.`ox_value_id` and `value_description_default`.`store_id` = "0"',
                array())
            ->from('', array('default_description' => 'value_description_default.description'));

        if ($storeId !== null) {
            $this->getSelect()
                ->from('', array('store_description' => 'value_description.description', 'description' => 'IFNULL(`value_description`.`description`, `value_description_default`.`description`)'))
                ->joinLeft(array('value_description' => $this->getTable('optionextended_value_description')),
                    '`main_table`.`ox_value_id` = `value_description`.`ox_value_id` and `value_description`.`store_id` = "' . $storeId . '"',
                    array());
        }
        return $this;
    }

}
