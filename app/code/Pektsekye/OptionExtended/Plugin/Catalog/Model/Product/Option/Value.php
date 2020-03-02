<?php

namespace Pektsekye\OptionExtended\Plugin\Catalog\Model\Product\Option;

class Value
{

    public function aroundSaveValues(\Magento\Catalog\Model\Product\Option\Value $subject, \Closure $proceed)
    {    
        foreach ($subject->getValues() as $value) {
            $subject->setData(
                $value
            )->setData(
                'option_id',
                $subject->getOption()->getId()
            )->setData(
                'store_id',
                (int) $subject->getOption()->getStoreId()
            )->setData(
                'price',
                isset($value['price']) && !empty($value['price']) ? $value['price'] : '0.00' //make it possible to delete option value price (Magento 2.2.1 bug)
            );
            
            $subject->isDeleted(false); //don't delete other option values if one option value is deleted (Magento 2.2.1 bug)            

            if ($subject->getData('is_delete') == '1') {
                if ($subject->getId()) {
                    $subject->deleteValues($subject->getId());
                    $subject->delete();
                }
            } else {
                $subject->save();
            }
        }
        //eof foreach()
        return $subject;
    }


}
