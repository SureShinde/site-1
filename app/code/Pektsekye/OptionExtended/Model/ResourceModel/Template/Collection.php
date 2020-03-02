<?php

namespace Pektsekye\OptionExtended\Model\ResourceModel\Template;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     */
    public function _construct()
    {
        $this->_init('Pektsekye\OptionExtended\Model\Template', 'Pektsekye\OptionExtended\Model\ResourceModel\Template');
    }


}
