<?php

namespace Pektsekye\OptionExtended\Plugin\Catalog\Model\Product\Option;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\CouldNotSaveException;

class Repository
{

    protected $_oxOption;   
    
    public function __construct(
        \Pektsekye\OptionExtended\Model\Option $oxOption
    ) {
        $this->_oxOption = $oxOption;    
    } 


    public function aroundDuplicate(\Magento\Catalog\Model\Product\Option\Repository $subject, \Closure $proceed, $originalProduct, $duplicate)
    {    
        $result = $proceed($originalProduct, $duplicate);

        $this->_oxOption->getResource()->duplicate((int) $originalProduct->getId(), (int) $duplicate->getId());
      
        return $result;
    }
    
    
}
