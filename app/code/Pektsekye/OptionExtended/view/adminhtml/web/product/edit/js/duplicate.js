
define([
    "jquery"
],function ($) {   

  return {


    duplicate : function(optionId){
  
      if ($('#'+optionId+'-content fieldset').length == 0){
        var widget = this;	
        var jqxhr = this.loadSection(optionId);
        if (jqxhr){
          jqxhr.always(function(){widget.duplicate(optionId);});
        }      
        return;
      }
    
      var type = $('#product_option_'+optionId+'_type').val();	
      if (type){

        $('#add_new_defined_option').trigger('click');	
  
        $('#product_option_'+this.lastOptionId+'_title').val($('#product_option_'+optionId+'_title').val()).trigger('change');
        $('#product_option_'+this.lastOptionId+'_type')[0].selectedIndex = $('#product_option_'+optionId+'_type')[0].selectedIndex;
      
        $('#product_option_'+this.lastOptionId+'_type').trigger('change');
      
        $('#product_option_'+this.lastOptionId+'_required')[0].checked = $('#product_option_'+optionId+'_required')[0].checked;  
        $('#product_option_'+this.lastOptionId+'_is_require')[0].selectedIndex = $('#product_option_'+optionId+'_is_require')[0].selectedIndex;    
        $('#product_option_'+this.lastOptionId+'_sort_order').val(parseInt($('#product_option_'+optionId+'_sort_order').val()) + 1);
        
        $('#ox_note_'+this.lastOptionId).val($('#ox_note_'+optionId).val());	
        
        if (type == 'drop_down' || type == 'radio' || type == 'checkbox' || type == 'multiple'){
        
          $('#ox_layout_'+this.lastOptionId)[0].selectedIndex = $('#ox_layout_'+optionId)[0].selectedIndex;	
          $('#ox_popup_'+this.lastOptionId)[0].checked = $('#ox_popup_'+optionId)[0].checked;
          $('#ox_popup_'+this.lastOptionId).disabled = $('#ox_popup_'+optionId).disabled;		
          $('#ox_sd_all_'+this.lastOptionId)[0].checked = $('#ox_sd_all_'+optionId)[0].checked;
          
          var selectId,rowId,value;			
          var l = this.rowIdsByOption[optionId].length;	
          for (var i=0;i<l;i++){
            rowId = this.rowIdsByOption[optionId][i];
            selectId = this.selectIdByRowId[rowId];
            
              $('#product_option_'+this.lastOptionId+'_add_select_row').trigger('click');
                        
              $('#product_option_'+this.lastOptionId+'_select_'+this.lastSelectId+'_title').val($('#product_option_'+optionId+'_select_'+selectId+'_title').val());				
              $('#product_option_'+this.lastOptionId+'_select_'+this.lastSelectId+'_price').val($('#product_option_'+optionId+'_select_'+selectId+'_price').val());
              $('#product_option_'+this.lastOptionId+'_select_'+this.lastSelectId+'_price_type')[0].selectedIndex = $('#product_option_'+optionId+'_select_'+selectId+'_price_type')[0].selectedIndex;	
              $('#product_option_'+this.lastOptionId+'_select_'+this.lastSelectId+'_sku').val($('#product_option_'+optionId+'_select_'+selectId+'_sku').val());	
              $('#product_option_'+this.lastOptionId+'_select_'+this.lastSelectId+'_sort_order').val($('#product_option_'+optionId+'_select_'+selectId+'_sort_order').val());	

              $('#ox_delete_image_'+this.lastSelectId).val($('#ox_delete_image_'+selectId).val());		        
              $('#ox_image_saved_as_'+this.lastSelectId).val($('#ox_image_saved_as_'+selectId).val());
              $('#ox_image_input_'+this.lastSelectId).val($('#ox_image_input_'+selectId).val());
              if ($('#ox_image_placeholder_'+selectId).css('display') != 'none') {
                $('#ox_image_uploader_'+this.lastSelectId).hide();                                      
                $('#ox_image_placeholder_'+this.lastSelectId).prop('src', $('#ox_image_placeholder_'+selectId).prop('src')).show();
                $('#ox_delete_image_button_'+this.lastSelectId).show();  			      
              }
              $('#ox_description_'+this.lastSelectId).val($('#ox_description_'+selectId).val());					
              $('#ox_sd_'+this.lastSelectId)[0].checked = $('#ox_sd_'+selectId)[0].checked;
              if ($('#ox_sd_'+selectId)[0].checked)
                this.addSdId(this.lastOptionId, this.lastRowId);				    
          }		

      
        } else {
    
          $('[name="product[options]['+this.lastOptionId+'][price]"]').val($('[name="product[options]['+optionId+'][price]"]').val());			
          $('[name="product[options]['+this.lastOptionId+'][price_type]"]')[0].selectedIndex = $('[name="product[options]['+optionId+'][price_type]"]')[0].selectedIndex;
          $('[name="product[options]['+this.lastOptionId+'][sku]"]').val($('[name="product[options]['+optionId+'][sku]"]').val());
        
          switch(type){
            case 'field' :	
            case 'area'  :
              $('[name="product[options]['+this.lastOptionId+'][max_characters]"]').val($('[name="product[options]['+optionId+'][max_characters]"]').val());
              break;	
            case 'file'  :
              $('[name="product[options]['+this.lastOptionId+'][file_extension]"]').val($('[name="product[options]['+optionId+'][file_extension]"]').val());
              $('[name="product[options]['+this.lastOptionId+'][image_size_x]"]').val($('[name="product[options]['+optionId+'][image_size_x]"]').val());
              $('[name="product[options]['+this.lastOptionId+'][image_size_y]"]').val($('[name="product[options]['+optionId+'][image_size_y]"]').val());			          											
          }	      
      
        }
        
        $('#option_'+optionId).after($('#option_'+this.lastOptionId));
     //   $.mage.customOptions.prototype._updateOptionBoxPositions.apply(this.element);

      }
    
    }
  
   
  };
    
});

