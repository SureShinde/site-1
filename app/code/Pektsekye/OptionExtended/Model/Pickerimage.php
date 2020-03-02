<?php

namespace Pektsekye\OptionExtended\Model;

class Pickerimage extends \Magento\Framework\Model\AbstractModel
{    

    public function _construct()
    {
       $this->_init('Pektsekye\OptionExtended\Model\ResourceModel\Pickerimage');
    }


    public function getImageData()
    {        
      return $this->getResource()->getImageData();                            
    } 


    public function saveImages($images)
    {    
      $this->getResource()->saveImages($images);      
    }

    
}
