<?php

namespace Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Value\Edit;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\Form as DataForm;

class Form extends Generic
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
  
  
  protected function _prepareLayout()
  {
      parent::_prepareLayout();
      if ($this->_wysiwygConfig->isEnabled()) {
   //       $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
       //   $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);        
       //   $this->getLayout()->getBlock('js')->append($this->getLayout()->createBlock('core/template','catalog_wysiwyg_js', array('template'=>'catalog/wysiwyg/js.phtml')));           
      }
  }    


  protected function _prepareForm()
  {
      /** @var DataForm $form */
      $form = $this->_formFactory->create(
          array('data' => array('id' => 'edit_form', 'action' => $this->getUrl('*/*/save', array('_current'=>true)), 'method' => 'post'))
      );

      $fieldset = $form->addFieldset('optionextended_form', array('legend'=>__('General Information')));

      $disabled = false;
      $useDefaultHtml = '';
      if (!is_null($this->_coreRegistry->registry('current_value')->getId()) && $this->_coreRegistry->registry('current_value')->getStoreId() != 0){
        $checked = '';
        if (is_null($this->_coreRegistry->registry('current_value')->getStoreTitle())){
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

      $disabled = false;
      $useDefaultHtml = '';
      if (!is_null($this->_coreRegistry->registry('current_value')->getId()) && $this->_coreRegistry->registry('current_value')->getStoreId() != 0){
        $checked = '';
        if (is_null($this->_coreRegistry->registry('current_value')->getStorePrice())){
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



      $fieldset->addField('sort_order', 'text', array(
          'name'      => 'sort_order',
          'label'     => __('Sort Order')      
      ));


              
      $fieldset->addType('text', 'Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Value\Helper\Form\Text'); 
               
      $fieldset->addField('children', 'text', array(
          'id'        => 'optionextended_children',
          'name'      => 'children',
          'label'     => __('Children'),
          'onblur'    => 'optionExtended.checkChildren(this)',
          'style'     => 'width:235px'      
      ));


    
      $fieldset->addType('image', 'Pektsekye\OptionExtended\Block\Adminhtml\Optiontemplate\Value\Helper\Form\Image');                    
      
      $fieldset->addField('image', 'image', array(
          'name'      => 'image',
          'label'     => __('Image')                 
      ));



      $disabled = false;
      $html = '';      
      if (!is_null($this->_coreRegistry->registry('current_value')->getId()) && $this->_coreRegistry->registry('current_value')->getStoreId() != 0){
        $checked = '';
        if (is_null($this->_coreRegistry->registry('current_value')->getStoreDescription())){
          $checked = 'checked="checked"';
          $disabled = true;          
        } 
       $html = '<input type="checkbox" id="description_use_default" class="checkbox" name="description_use_default" onclick="toggleValueElements(this, this.parentNode)" value="1" '.$checked.'/>&nbsp;'.
                         '<label class="normal" for="description_use_default">'.__('Use Default').'</label>';  
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
                      ) . '\', \'description\')'
                  )
              )
          )->toHtml();
          $editor .= <<<HTML
<script type="text/javascript">
require([
    'jquery',
    'mage/adminhtml/wysiwyg/tiny_mce/setup'
], function(jQuery){
  jQuery('#description')
    .addClass('wysiwyg-editor')
    .data(
        'wysiwygEditor',
        new tinyMceWysiwygSetup(
            'description',
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


      $fieldset->addField('description', 'textarea', array(
          'name'      => 'description',
          'label'     => __('Description'),
          'disabled'  => $disabled,              
          'after_element_html' => $html                 
      ));


      $fieldset->addField('row_id', 'hidden', array(
          'name' => 'row_id'              
      ));

      $data = $this->_coreRegistry->registry('current_value')->getData();
      if ($this->_catalogData->isUrlDirectivesParsingAllowed()) {
        if (isset($data['description'])){
          $data['description'] = $this->_catalogData->getPageTemplateProcessor()->filter($data['description']);            
        }  
      }         
      $form->setValues($data);

      $form->setUseContainer(true); 
      $this->setForm($form);
          
      return parent::_prepareForm();
  }

}
