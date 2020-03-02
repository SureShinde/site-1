<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Option\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic
{

  protected $_wysiwygConfig;
  protected $_catalogData = null;
      
  public function __construct(
      \Magento\Backend\Block\Widget\Context $context,
      \Magento\Framework\Registry $registry,
      \Magento\Framework\Data\FormFactory $formFactory,
      \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
      \Magento\Catalog\Helper\Data $catalogData,      
      array $data = array()
  ) {
      $this->_wysiwygConfig = $wysiwygConfig;
      $this->_catalogData = $catalogData;      
      parent::__construct($context, $registry, $formFactory, $data);
  }
  
    
  protected function _prepareForm()
  {
      $form = $this->_formFactory->create();
      $fieldset = $form->addFieldset('optionextended_form', array('legend'=>__('General Information')));
      
      $disabled = false;
      $useDefaultHtml = '';
      if (!is_null($this->_coreRegistry->registry('current_option')->getId()) && $this->_coreRegistry->registry('current_option')->getStoreId() != 0){
        $checked = '';
        if (is_null($this->_coreRegistry->registry('current_option')->getStoreTitle())){
          $checked = 'checked="checked"';
          $disabled = true;          
        } 
       $useDefaultHtml = '<input type="checkbox" id="title_use_default" class="checkbox" name="title_use_default" onclick="toggleValueElements(this, this.parentNode.parentNode)" value="1" '.$checked.'/>&nbsp;'.
                         '<label class="normal" for="title_use_default">'.__('Use Default').'</label>';  
      }     
      
      $fieldset->addField('title', 'text', array(
          'name'      => 'title',    
          'label'     => __('Title'),
          'disabled'  => $disabled,              
          'required'  => true,
          'after_element_html' => $useDefaultHtml 
      ));


      $fieldset->addField('type', 'select', array(
          'name'      => 'type',
          'label'     => __('Type'),
          'options'   => array(
                          "" => __('-- Please Select --'),          
                          "field" => __('Field'),
                          "area" => __('Area'),            
                          "file" => __('File'),            
                          "drop_down" => __('Drop-down'),
                          "radio" => __('Radio Buttons'),
                          "checkbox" => __('Checkbox'),
                          "multiple" => __('Multiple Select'),
                          "date" => __('Date'),
                          "date_time" => __('Date & Time'),
                          "time" => __('Time')
                         ),
          'required'  => true,
          'onchange' => 'optionExtended.onTypeChange();'                          
      ));


      $fieldset->addField('is_require', 'select', array(
          'name'      => 'is_require',
          'label'     => __('Required'),
          'values'    =>  array(array('value' => 0, 'label' => __('No')), array('value' => 1, 'label' => __('Yes'))),
          'value' => 1        
      ));


      $fieldset->addField('sort_order', 'text', array(
          'name'      => 'sort_order',
          'label'     => __('Sort Order')      
      ));


      $fieldset->addField('code', 'text', array(
          'name'      => 'code',
          'label'     => __('Code')     
      ));


      $disabled = false;
      $html = '';      
      if (!is_null($this->_coreRegistry->registry('current_option')->getId()) && $this->_coreRegistry->registry('current_option')->getStoreId() != 0){
        $checked = '';
        if (is_null($this->_coreRegistry->registry('current_option')->getStoreNote())){
          $checked = 'checked="checked"';
          $disabled = true;          
        } 
       $html = '<input type="checkbox" id="note_use_default" class="checkbox" name="note_use_default" onclick="toggleValueElements(this, this.parentNode)" value="1" '.$checked.'/>&nbsp;'.
                         '<label class="normal" for="note_use_default">'.__('Use Default').'</label>';  
      }
      
      if ($this->_wysiwygConfig->isEnabled()) {
          $editor = $this->_layout->createBlock(
              'Magento\Backend\Block\Widget\Button',
              '',
              array(
                  'data' => array(
                      'label' => __('WYSIWYG Editor'),
                      'type' => 'button',
                      'disabled' => $disabled,
                      'class' => $disabled ? 'disabled action-wysiwyg' : 'action-wysiwyg',
                      'onclick' => 'catalogWysiwygEditor.open(\'' . $this->getUrl(
                          'catalog/product/wysiwyg'
                      ) . '\', \'note\')'
                  )
              )
          )->toHtml();
          $editor .= <<<HTML
<script type="text/javascript">
require([
    'jquery',
    'mage/adminhtml/wysiwyg/tiny_mce/setup'
], function(jQuery){

  jQuery('#note')
    .addClass('wysiwyg-editor')
    .data(
        'wysiwygEditor',
        new tinyMceWysiwygSetup(
            'note',
             {
                settings: {
                    theme_advanced_buttons1 : 'bold,italic,|,justifyleft,justifycenter,justifyright,|,' +
                        'fontselect,fontsizeselect,|,forecolor,backcolor,|,link,unlink,image,|,bullist,numlist,|,code',
                    theme_advanced_buttons2: null,
                    theme_advanced_buttons3: null,
                    theme_advanced_buttons4: null,
                    theme_advanced_statusbar_location: null
                }
            }
        ).turnOn()
    );
});  
</script>
HTML;
        $html = $editor . '&nbsp;&nbsp;&nbsp;' . $html;
      }      



      $fieldset->addField('note', 'textarea', array(
          'name'      => 'note',
          'label'     => __('Note'),
          'disabled'  => $disabled,              
          'after_element_html' => $html                 
      ));


      $disabled = false;
      $useDefaultHtml = '';
      if (!is_null($this->_coreRegistry->registry('current_option')->getId()) && $this->_coreRegistry->registry('current_option')->getStoreId() != 0){
        $checked = '';
        if (is_null($this->_coreRegistry->registry('current_option')->getStorePrice())){
          $checked = 'checked="checked"';
          $disabled = true;          
        } 
       $useDefaultHtml = '<input type="checkbox" id="price_use_default" class="checkbox" name="price_use_default" onclick="toggleValueElements(this, this.parentNode.parentNode)" value="1" '.$checked.'/>&nbsp;'.
                         '<label class="normal" for="price_use_default">'.__('Use Default').'</label>';  
      }
      
      $fieldset->addField('price', 'text', array(
          'name'      => 'price',
          'label'     => __('Price'),
          'disabled'  => $disabled,              
          'after_element_html' => $useDefaultHtml                 
      ));

      
      $fieldset->addField('price_type', 'select', array(
          'name'      => 'price_type',
          'label'     => __('Price Type'),
          'options'   => array(
                           'fixed'   => __('Fixed'),
                           'percent' => __('Percent')
                         )                 
      ));     

       
      $fieldset->addField('sku', 'text', array(
          'name'      => 'sku',
          'label'     => __('Sku')      
      ));

            
      $fieldset->addField('max_characters', 'text', array(
          'name'      => 'max_characters',
          'label'     => __('Max Characters')      
      ));


      $fieldset->addField('file_extension', 'text', array(
          'name'      => 'file_extension',
          'label'     => __('File Extensions')      
      ));


      $fieldset->addField('image_size_x', 'text', array(
          'name'      => 'image_size_x',
          'label'     => __('Image Size X')      
      ));


      $fieldset->addField('image_size_y', 'text', array(
          'name'      => 'image_size_y',
          'label'     => __('Image Size Y')      
      ));


      $fieldset->addField('layout', 'select', array(
          'name'      => 'layout',
          'label'     => __('Layout'),
          'options'   => array(
                           'above'      =>__('Above Option'),
                           'before'     =>__('Before Option'),
                           'below'      =>__('Below Option'),
                           'grid'       =>__('Grid'),
                           'gridcompact'=>__('Grid Compact'),                           
                           'list'       =>__('List'),
                           'swap'       =>__('Main Image'),
                           'picker'     =>__('Color Picker'),
                           'pickerswap' =>__('Picker & Main')
                         ),
          'onchange' => 'optionExtended.changePopup(this.value);'                                                         
      ));                                   


      $fieldset->addField('popup', 'checkbox', array(
          'name'      => 'popup',
          'label'     => __('Popup'),
          'value'     =>1,
          'checked'   => $this->_coreRegistry->registry('current_option')->getPopup() == 1     
      )); 

               
      $fieldset->addField('row_id', 'hidden', array(
          'name' => 'row_id'              
      )); 


      $rows = $this->_coreRegistry->registry('current_option')->getValueTitles();
      
      $options = array('' => '');
      foreach($rows as $row)
        $options[$row['row_id']] = $row['title'];
      
      $fieldset->addField('sd', 'select', array(
          'name'     => 'sd',
          'label'    => __('Selected By Default'),
          'values'   => $options                                                        
      ));         

      $options = array(array('value' => '-1', 'label' => ''));
      foreach($rows as $row)
        $options[] = array('value' => $row['row_id'], 'label' => $row['title']);
      
      $fieldset->addField('sd_multiple', 'multiselect', array(
          'name'      => 'sd_multiple[]',
          'label'     => __('Selected By Default'),
          'values'    => $options                                                    
      ));

      
      if (!is_null($this->_coreRegistry->registry('current_option')->getId())){
        $data = $this->_coreRegistry->registry('current_option')->getData();
        if ($this->_catalogData->isUrlDirectivesParsingAllowed()) {
          if (isset($data['note'])){
            $data['note'] = $this->_catalogData->getPageTemplateProcessor()->filter($data['note']);            
          }  
        }         
        $form->setValues($data);
      }
      
                   
      $this->setForm($form);    
      return parent::_prepareForm();
  }
 
    
}
