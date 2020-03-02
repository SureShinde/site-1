<?php

namespace Pektsekye\OptionExtended\Plugin\Catalog\Ui\DataProvider\Product\Form\Modifier;

use Magento\Ui\Component\Container;

class CustomOptions
{


    protected $locator;
    
    
    public function __construct(
        \Magento\Catalog\Model\Locator\LocatorInterface $locator
    ) {
        $this->locator = $locator;
    }



    public function afterModifyData(\Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions $subject, $data)
    {     
        $data[$this->locator->getProduct()->getId()][$subject::DATA_SOURCE_DEFAULT][$subject::FIELD_ENABLE] = 0; 
        $data[$this->locator->getProduct()->getId()][$subject::DATA_SOURCE_DEFAULT][$subject::GRID_OPTIONS_NAME] = [];
               
        return $data;   
    }


    public function afterModifyMeta(\Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions $subject, $meta)
    {   
        $meta[$subject::GROUP_CUSTOM_OPTIONS_NAME]['children'] = [$subject::CONTAINER_HEADER_NAME => $this->getHeaderContainerConfig(10)];              
       
        return $meta;   
    }


    protected function getHeaderContainerConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => null,
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
                        'template' => "Pektsekye_OptionExtended/form/components/js",                         
                        'sortOrder' => $sortOrder,
                        'content' => '',
                        'idColumn' => 'aaa'
                    ],
                ],
            ],
        ];
    }  

}
