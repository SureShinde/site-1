<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Value\Helper\Form\Text;

class Content extends \Magento\Backend\Block\Widget
{

    protected $_template = 'optiontemplate/helper/text.phtml';
    
    
    protected $_options = array();
    protected $_rowIdIsset  = array();
    protected $_rowIdsByOption  = array();
    protected $_optionByChild  = array();
    protected $_previousParent  = array();
    
    protected $_oxTemplateOption;      
    protected $_coreRegistry = null;        
    protected $_jsonEncoder;


    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Pektsekye\OptionExtended\Model\Template\Option $templateOption,            
        \Magento\Framework\Registry $registry,        
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = array()
    ) {  
        $this->_oxTemplateOption = $templateOption;     
        $this->_coreRegistry = $registry;     
        $this->_jsonEncoder = $jsonEncoder;       
        parent::__construct($context, $data);
    }    
    
    
    
    public function _construct()
    {		
      parent::_construct();

      $value = $this->_coreRegistry->registry('current_value');      
      $model = $this->_oxTemplateOption->getResource();      

      $options = array();
      $rowIdIsset  = array();
      $rowIdsByOption = array();
      $optionByChild = array();
        
      $rows = $model->getChildrenOptionData($value->getTemplateId());        
      foreach ($rows as $row){
        $id = (int) $row['option_id'];
        $rowId = (int) $row['row_id'];   
        $options[$id]['title']  = $row['title'];
        if (!is_null($row['row_id'])){
          $options[$id]['row_id'] = $rowId;       
          if ($id != $value->getOptionId())
            $rowIdIsset[$rowId] = 1;
          $rowIdsByOption[$id][] = $rowId;
        }                                        
      } 

      $rows = $model->getChildrenValueData($value->getTemplateId());        
      foreach ($rows as $row){             
        $oId = (int) $row['option_id'];
        $vId = (int) $row['value_id'];        
        $rowId = (int) $row['row_id'];
        $options[$oId]['values'][$vId]['title'] = $row['title']; 
        $options[$oId]['values'][$vId]['row_id'] = $rowId;
        if ($oId != $value->getOptionId())        
          $rowIdIsset[$rowId] = 1;      
        $rowIdsByOption[$oId][] = $rowId;
        if (!empty($row['children']))
          foreach(explode(',', $row['children']) as $c)                                             
            $optionByChild[(int)$c] = $oId;
      } 

      $this->_rowIdIsset  = $rowIdIsset;
      $this->_rowIdsByOption = $rowIdsByOption;
      $this->_optionByChild = $optionByChild; 
       
      $parent = $this->getAllParent($value->getOptionId());
      
      foreach ($options as $oId => $v){
        if ($oId != $value->getOptionId() && (isset($v['row_id']) || isset($v['values']))){
          $oP = $this->getParent($oId);
          if(!in_array($oId, $parent) && ($oP == null || $oP == $value->getOptionId()))
            $this->_options[$oId] = $v;
        }    
      }     
                                       
    }


    public function getAllParent($optionId)
	  {
		  if (isset($this->_previousParent[$optionId]))
			  return array();
		  $this->_previousParent[$optionId] = 1;

		  $parent = array();
		  $p = $this->getParent($optionId);
      if (!is_null($p)){
		    $parent[] = $p;
			  $pp = $this->getAllParent($p);
			  if (count($pp) > 0)
				  $parent = array_merge($parent, $pp);		    
			}	

		  return $parent;
	  }	
	
	
    public function getParent($optionId){
      if (isset($this->_rowIdsByOption[$optionId])){
    		$r = $this->_rowIdsByOption[$optionId];
		    $l = count($r);
		    for ($i=0;$i<$l;$i++)
			    if (isset($this->_optionByChild[$r[$i]]))
				    return $this->_optionByChild[$r[$i]];
			}	  
		  return null;
	  }


    public function getOptions()
    {      
        return $this->_options;
    }
    
    public function getRowIdIsset()
    {      
        return $this->_jsonEncoder->encode($this->_rowIdIsset);
    } 
        
     public function getRowIdsByOption()
    {
        return $this->_jsonEncoder->encode($this->_rowIdsByOption);
    }
    
    
}
