
define([
    "jquery",
    "mage/template"
],function ($, mageTemplate) {   

  return {


    applyTemplate : function(){
      var tSelect = $('#ox_template_select');
      var templateId = tSelect.val();
      var tField = $('#ox_template_ids');
      var ids = tField.val().split(',');
      if (ids.indexOf(templateId) == -1){	  
        var value = tField.val() + (tField.val() != '' ? ',' : '') + templateId;
        tField.val(value);
        var title = tSelect[0].options[tSelect[0].selectedIndex].text;
        var rowTmpl = mageTemplate('#ox_optiontemplate_row_template');
        var rowHtml = rowTmpl({'title':title,'template_id':templateId});
      
        $('#ox_template_table').append(rowHtml);			  	    
      }
    },
  
    removeTemplate : function(templateId){
      var tField = $('#ox_template_ids');
      var ids = tField.val().split(',');
      this.without(ids, templateId + '');

      tField.val(ids.join(','));

      $('#ox_template_table_row_' + templateId).remove();	
    },



    insertTemplateOptions : function(){

      var templateId = $('#ox_template_select').val();
    
      var table = $('#ox_template_table');
      if (table.hasClass("loading"))
        return null;
        
      table.addClass("loading");
    
      var widget = this;    
      $.ajax({
          type: 'POST',
          url: this.templateDataUrl,
          async: true,
          data: {isAjax: 'true', form_key: FORM_KEY, 'template_id': templateId, 'product_id': this.productId, 'store': this.storeId},
          dataType: 'json'     
      }).success(function (data) {
          if (!data.error){           
            widget._insertTemplateOptions(data);
          }         
      }).always(function () {
          table.removeClass("loading");          
      });      
    },



    _insertTemplateOptions : function(templateData){
      var	option,oId,value,selectId,children,l,ll,i,ii,vId;
      var newRowIds = [];
      var toAddChildren = [];

      l = templateData.length;
      for (i=0;i<l;i++){
  
        option = templateData[i];
    
        $('#add_new_defined_option').click();

        oId = this.lastOptionId;

        $('#product_option_'+oId+'_title').val(option.title).trigger('change');

        $('#product_option_'+oId+'_type')[0].selectedIndex = option.typeIndex;
        $('#product_option_'+oId+'_type').trigger('change');

        $('#product_option_'+oId+'_required')[0].checked = option.isRequired; 
        $('#product_option_'+oId+'_is_require').val(option.isRequired ? 1 : 0);            
      
        $('#ox_note_'+oId).val(option.note);	

      
        if (option.type == 'drop_down' || option.type == 'radio' || option.type == 'checkbox' || option.type == 'multiple'){
      
          $('#ox_layout_'+oId)[0].selectedIndex = option.layoutIndex;	
          $('#ox_popup_'+oId)[0].checked = option.popupChecked;
          $('#ox_popup_'+oId)[0].disabled = option.popupDisabled;
    
          ll = option.values.length;	
          for (ii=0;ii<ll;ii++){
            value = option.values[ii];
              
            $('#product_option_'+oId+'_add_select_row').click();

            vId = this.lastSelectId;		        

            newRowIds[value.rowId] = this.lastRowId;
            if (value.children.length > 0)
              toAddChildren.push([this.lastRowId, i, ii]);
          
            if (value.imageUrl){
              $('#ox_image_uploader_'+vId).hide();                        
              $('#ox_image_input_'+vId).val('');
              $('#ox_image_saved_as_'+vId).val(value.imageSavedAs);                 
              $('#ox_image_placeholder_'+vId).prop('src', value.imageUrl).show();
              $('#ox_delete_image_button_'+vId).show();             
            }
          
            $('#product_option_'+oId+'_select_'+vId+'_title').val(value.title);				
            $('#product_option_'+oId+'_select_'+vId+'_price').val(value.price);				
            $('#product_option_'+oId+'_select_'+vId+'_price_type')[0].selectedIndex = value.priceTypeIndex;	
            $('#product_option_'+oId+'_select_'+vId+'_sku').val(value.sku);	
          
            $('#ox_description_'+vId).val(value.description);
          
            if (value.sdIsChecked){						
              $('#ox_sd_'+vId)[0].checked = true;
              this.addSdId(oId, this.lastRowId);
            } 				      
          }		
  
        } else {
  
          $('[name="product[options]['+oId+'][price]"]').val(option.price);			
          $('[name="product[options]['+oId+'][price_type]"]')[0].selectedIndex = option.priceTypeIndex;
          $('[name="product[options]['+oId+'][sku]"]').val(option.sku);
        
          switch(option.type){
            case 'field' :	
            case 'area'  :
              $('[name="product[options]['+oId+'][max_characters]"]').val(option.maxCharacters);
              break;	
            case 'file'  :
              $('[name="product[options]['+oId+'][file_extension]"]').val(option.fileExtension);
              $('[name="product[options]['+oId+'][image_size_x]"]').val(option.imageSizeX);  
              $('[name="product[options]['+oId+'][image_size_y]"]').val(option.imageSizeY);       											
          }	          
          newRowIds[option.rowId] = this.lastRowId;
        }  
  
      }
    
      l = toAddChildren.length;
      while (l--){
        rowId = toAddChildren[l][0];
        i = toAddChildren[l][1];
        ii = toAddChildren[l][2];        
        children = templateData[i]['values'][ii].children;
        ll = children.length;
        while (ll--)
          children[ll] = newRowIds[children[ll]];
        $('#ox_'+this.selectIdByRowId[rowId]+'_children').val(children.join(','));
        this.setChildrenOfRow(rowId, children);
      }
    
      $.mage.customOptions.prototype._updateOptionBoxPositions.apply(this.element);
  
        
    } 
  
	 			
  };
    
});



