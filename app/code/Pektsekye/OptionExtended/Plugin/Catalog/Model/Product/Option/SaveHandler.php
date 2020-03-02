<?php

namespace Pektsekye\OptionExtended\Plugin\Catalog\Model\Product\Option;

use Magento\Catalog\Api\ProductCustomOptionRepositoryInterface as OptionRepository;

class SaveHandler
{

    protected $optionRepository;


    public function __construct(
        OptionRepository $optionRepository
    ) {
        $this->optionRepository = $optionRepository;
    }
    
    
    public function aroundExecute(\Magento\Catalog\Model\Product\Option\SaveHandler $subject, \Closure $proceed, $entity, $arguments = [])
    {    

        if ($entity->getOptions() && $entity->getAffectProductCustomOptions() == 1) { //do not save options when Custom Options section was not opened
            foreach ($entity->getOptions() as $option) {

                if ($option->getData('is_delete') == '1'){
                  $this->optionRepository->delete($option);
                  continue;
                }
                
                $this->optionRepository->save($option);
            }
        } 
        
        return $entity;
    }

}
