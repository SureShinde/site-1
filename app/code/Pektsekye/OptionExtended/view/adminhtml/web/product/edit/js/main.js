
var optionExtended;


define([
    "jquery",
    "mage/template",
    "jquery/file-uploader"            
],function($, mageTemplate) {
  "use strict";

  return {
  
    lastSortOrder : 0,  		    	
    lastOptionId  : 0,
    lastRowId     : 0,
    lastSelectId  : 0,    
    delButtonObserved : {},
               
    options: {						
      
    },	
    
    _create: function(){
      $.extend(this, this.options);  
         
      this.sectionTmpl = mageTemplate('#ox-custom-option-base-template');
      this.contentTmpl = mageTemplate('#ox-custom-option-content-template');    
      this.addAccordion();
      
      this._initCustomOptions();
      
      this.loadOptionsForResave();      
    },
    
      
      
    loadOptionsForResave : function(){      
      var l = this.oIdsToResave.length;
      while (l--)
        this.loadSection(this.oIdsToResave[l]);           
    },
	


    addAccordion : function(){
      var l = this.sectionTitles.length;
      for (var i=0;i<l;i++){
        this.addSection(this.sectionTitles[i].id, this.sectionTitles[i].title);      
      }
    },



    addSection : function(optionId, optionTitle){

      var sectionTmpl = this.sectionTmpl({data: {id: optionId, title: optionTitle}});
      
      $(sectionTmpl)
          .appendTo(this.element.find('#product_options_container_top'))
          .find('#'+optionId+'-content')
          .collapsable()
          .on('show', $.proxy(this.loadSection, this, optionId)); 
    },



    loadSection : function(optionId){
    
      var wrapper = $('#ox_wrapper_title_'+optionId);
      if (wrapper.hasClass("loading") || $('#'+optionId+'-content fieldset').length)
        return null;
          
      wrapper.addClass("loading");
      
      var widget = this;
      
      var jqxhr = $.ajax({
              type: 'POST',
              url: this.loadOptionUrl,
              async: true,
              data: {isAjax:true, form_key: FORM_KEY, product_id: this.productId, option_id: optionId, store_id: this.storeId},
              dataType: 'json'
          }).success(function (result) {
              if (!result.error){      
                widget.addSectionContent(result);              
              }        
          }).always(function () {
              wrapper.removeClass("loading");          
          });
            
      this._bindCheckboxHandlers();
      
      return jqxhr;
    },



    importOptions : function(productId){
      var widget = this;
      $.ajax({
          type: 'POST',
          url: this.importOptionsUrl,
          async: false,
          data: {isAjax:true, form_key: FORM_KEY, product_id: productId, last_option_id: this.lastOptionId, last_value_id: this.lastValueId, last_row_id: this.lastRowId, last_sort_order: this.lastSortOrder},
          dataType: 'json'
      }).success(
          function (data) {
            if (!data.error){

              widget.rowIds = widget.rowIds.concat(data.ids.rowIds);
              $.extend(widget.rowIdIsset, data.ids.rowIdIsset);
              $.extend(widget.rowIdByOption, data.ids.rowIdByOption);
              $.extend(widget.optionByRowId, data.ids.optionByRowId);
              widget.optionIds = widget.optionIds.concat(data.ids.optionIds);                
              $.extend(widget.optionTypes, data.ids.optionTypes);
              $.extend(widget.optionTitles, data.ids.optionTitles);             
              widget.lastSortOrder = data.ids.lastSortOrder;     
              $.extend(widget.parentRowIdsOfRowId, data.ids.parentRowIdsOfRowId);
              $.extend(widget.selectIdByRowId, data.ids.selectIdByRowId);
              $.extend(widget.rowIdsByOption, data.ids.rowIdsByOption);
              $.extend(widget.rowIdsByOptionIsset, data.ids.rowIdsByOptionIsset);                                          
              $.extend(widget.rowIdBySelectId, data.ids.rowIdBySelectId);                 
              $.extend(widget.childrenByRowId, data.ids.childrenByRowId);      
              $.extend(widget.valueTitles, data.ids.valueTitles);   
              widget.lastRowId = data.ids.lastRowId;   
              widget.lastOptionId = data.ids.lastOptionId;                                                                                            

              var oId,title;             
              var l = data.sectionTitles.length;
              for (var i=0;i<l;i++){
                oId = data.sectionTitles[i].id;
                title = data.sectionTitles[i].title;
                
                widget.addSection(oId, title);
                widget.addSectionContent(data.optionData[oId]);
                                
                $('#'+oId+'-content').trigger("show");
                                
                $('#product_option_' + oId + '_option_id').val(0);
                $('#option_' + oId + ' input[name$="option_type_id]"]').val(-1);                
              }
              
              $('#import-container').modal('closeModal');
            }
          }
        );    
    
    },



    onTypeChange : function(event, data){
   
      var currentElement = $(event.target);
      var group = currentElement.find('[value="' + currentElement.val() + '"]').closest('optgroup').attr('data-optgroup-name');
      var parentId = '#' + currentElement.closest('.fieldset-alt').attr('id');
                  
      if (typeof group === 'undefined')
        return;

      var optionId = parseInt($(parentId + '_id').val()); 
        
      group = group.toLowerCase(); 
      
      var rowId; 
      
      if (data && data.row_id) {  
           
        rowId = data.row_id;
        
        if (rowId > this.lastRowId)
          this.lastRowId = rowId
          
      } else if (group != 'select' && $('#ox_row_id_option_'+optionId).val() == '') {
            
        this.lastRowId++;
                  
        rowId = this.lastRowId;
           
        $('#ox_row_id_option_'+optionId).val(rowId).after(rowId);        
      }
		
           
      this.setOptionIds(optionId, rowId, group);
			this.reloadLayoutSelect(optionId);
			
      if (group == 'select'){
        $('#ox_layout_div_'+optionId).show();
        $('#ox_popup_div_'+optionId).show();
      } else {
        $('#ox_layout_div_'+optionId).hide();
        $('#ox_popup_div_'+optionId).hide();      
      }
			
      if (this.optionIds.indexOf(optionId) == -1)
			  this.optionIds.push(optionId);
	/*		  
      if (this.delButtonObserved[optionId] == undefined){			   			
        var widget = this;	
        $('#product_option_'+optionId+'_delete').click(function(){widget.deleteOption(optionId);});
        this.delButtonObserved[optionId] = 1;	
      }	*/	 			       
    },



    addSectionContent : function(event){
    
      var data = {};
      var element = event.target || event.srcElement || event.currentTarget;
      if (typeof element !== 'undefined') {
          data.id = this.lastOptionId + 1;
          data.type = '';
          data.option_id = 0;
          data.sort_order = this.lastSortOrder + 1;
          this.lastSortOrder++;
          this.lastOptionId++;                
      } else {
          data = event;
      }
      
      var content = this.contentTmpl({data:data});
      $(content).appendTo($('#'+data.id+'-content'));
      if (data.type)
        $('#' + this.options.fieldId + '_' + data.id + '_type').val(data.type).trigger('change', data);

      $('#' + this.options.fieldId + '_' + data.id + '_required').prop('checked', data.is_require > 0).trigger('change');
      
      //this.options.itemCount++;
      $('#' + this.options.fieldId + '_' + data.id + '_title').trigger('change');
     // if (['drop_down','radio','checkbox','multiple'].indexOf(result.type) = -1){
     // }  
      
      var popup = $('#ox_popup_'+data.id);
      if (data.layout == 'swap'){
        popup[0].disabled = true;
      } else if (data.popup > 0){
        popup.prop('checked', true);        
      }    

      if (data.layout) {
        $('#ox_layout_'+data.id).val(data.layout);
      }

      tinyMCE.editors['ox_note_'+data.id] = {save:function(){},load:function(){}}; 
      $('#ox_affect_product_custom_options').val(1);
    },
    
 
 
 
    addRow : function(event, optionId, selectId){
      var rowId;
      
      var data = {};
      var element = event.target || event.srcElement || event.currentTarget;
      if (typeof element !== 'undefined') {
      
        this.lastRowId++;
        
        rowId = this.lastRowId;    
        
        $('#ox_row_id_'+selectId).val(rowId).after(rowId); 

        this.setOptionIds(optionId, null, 'select');             
        this.setOptionValueIds(optionId, selectId, rowId);	         
                           
      } else {                  
        data = event;
        if (this.lastRowId < data.row_id){
          this.lastRowId = data.row_id;
        }
        rowId = data.row_id;
        selectId = data.select_id;

        this.updateChildren($('#ox_'+selectId+'_children')[0], optionId, selectId);           
      }

      var maxFileSize = this.maxFileSize;
      var maxWidth = this.maxWidth;
      var maxHeight = this.maxHeight;

      $('#ox_image_file_'+selectId).fileupload({
          dataType: 'json',
          dropZone: '[data-tab-panel=image-management]',
          sequentialUploads: true,
          acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
          maxFileSize: maxFileSize,
          add: function(e, data) {
              $.each(data.files, function (index, file) {
                  data.fileId = Math.random().toString(33).substr(2, 18);
                  var progressTmpl = $('#ox_image_uploader_'+selectId+'-template').children(':first').clone();
                  progressTmpl.attr('id', data.fileId);
                  var fileInfoHtml = progressTmpl.html().replace('{{size}}', byteConvert(file.size))
                      .replace('{{name}}', file.name);
                  progressTmpl.html(fileInfoHtml) ;

                  progressTmpl.appendTo('#ox_image_uploader_'+selectId);

              });
              $(this).fileupload('process', data).done(function () {
                  data.submit();
              });
              $('#ox_image_browse_button_'+selectId).hide();              
          },
          done: function(e, data) {
              if (data.result && !data.result.error) {
                 $('#ox_image_uploader_'+selectId).hide();                        
                 $('#ox_image_input_'+selectId).val(data.result.file);
			           $('#ox_image_saved_as_'+selectId).val('');                 
                 $('#ox_image_placeholder_'+selectId).prop('src', data.result.url).show();
                 $('#ox_delete_image_button_'+selectId).show();                 
              } else {
                  $('#' + data.fileId)
                      .delay(2000)
                      .hide('highlight');
                  alert($.mage.__('File extension not known or unsupported type.'));
              }
              $('#' + data.fileId).remove();
              $('#ox_image_browse_button_'+selectId).show();              
          },
          progress: function(e, data) {
              var progress = parseInt(data.loaded / data.total * 100, 10);
              var progressSelector = '#' + data.fileId + ' .progressbar-container .progressbar';
              $(progressSelector).css('width', progress + '%');
          },
          fail: function(e, data) {
              var progressSelector = '#' + data.fileId;
              $(progressSelector).removeClass('upload-progress').addClass('upload-failure')
                  .delay(2000)
                  .hide('highlight')
                  .remove();
              $('#ox_image_browse_button_'+selectId).show();                  
          }
      });
  
      $('#ox_image_file_'+selectId).fileupload('option', {
          process: [
              {
                  action: 'load',
                  fileTypes: /^image\/(gif|jpeg|png)$/
              },
              {
                  action: 'resize',
                  maxWidth: maxWidth,
                  maxHeight: maxHeight
              },
              {
                  action: 'save'
              }
          ]
      });

      tinyMCE.editors['ox_description_'+selectId] = {save:function(){},load:function(){}}; 
     /*       
      var widget = this;	
      $('#product_option_'+optionId+'_select_'+selectId+'_delete').click(function(){widget.deleteRow(optionId, rowId);});
      */
      this.lastSelectId = selectId;      
    },
    
   
    
    deleteOption : function(optionId){      
     this.unsetOptionIds(optionId); 		
    },
  
    
    deleteRow : function(optionId, rowId){
      this.unsetOptionValueIds(optionId, rowId);
      this.deleteSdId(optionId, rowId);
    },
   
    
    without : function(a, v){
      var i = a.indexOf(v);
      if (i != -1)
        a.splice(i, 1);
    }
    	    		
  };


});

