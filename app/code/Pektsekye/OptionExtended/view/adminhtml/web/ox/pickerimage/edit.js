
define([
    'jquery',
    'mage/template', 
    'mage/translate',
    "jquery/file-uploader",
    'jquery/ui'           
], function($, mageTemplate){
  "use strict";
  $.widget("pektsekye.pickerImages", {
          
           
  options: {						
  
  },	

  _create: function(){
		$.extend(this, this.options);
		 
    this.imageRowTmpl = mageTemplate('#image-row-template');		 
		 
    this._on({
    'click .ox-add-image': function(event){
        this.addRow(event);
      },
    'click .delete': function(event){
        var imageId = $(event.target).closest('tr').prop('id').replace('ox_image_row_', '');
        this.deleteImage(imageId);
      }    
    }); 
    
    var l = this.imagesData.length;
    for(var i=0;i<l;i++)
      this.addRow(this.imagesData[i]); 
  },


  addRow : function(event){ 

    var data = {};
    var element = event.target || event.srcElement || event.currentTarget;
    if (typeof element !== 'undefined') {
      data.id = this.lastImageId + 1;      	         
      this.lastImageId++;                         
    } else {                  
      data = event;
      if (this.lastImageId < data.id)
        this.lastImageId = data.id;           
    }

    var imageRowTmpl = this.imageRowTmpl({data: data});
    
    $('#ox_add_image_row').before($(imageRowTmpl));

    this.addUploader(data.id);	    	
  },


  addUploader : function(imageId){

      var maxFileSize = this.maxFileSize;
      var maxWidth = this.maxWidth;
      var maxHeight = this.maxHeight;

      $('#ox_image_file_'+imageId).fileupload({
          dataType: 'json',
          dropZone: '[data-tab-panel=image-management]',
          sequentialUploads: true,
          acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
          maxFileSize: maxFileSize,
          add: function(e, data) {
              $.each(data.files, function (index, file) {
                  data.fileId = Math.random().toString(33).substr(2, 18);
                  var progressTmpl = $('#ox_image_uploader-template_'+imageId).children(':first').clone();
                  progressTmpl.attr('id', data.fileId);
                  var fileInfoHtml = progressTmpl.html().replace('{{name}}', file.name);
                  progressTmpl.html(fileInfoHtml) ;

                  progressTmpl.appendTo('#ox_image_uploader_'+imageId);

              });
              $(this).fileupload('process', data).done(function () {
                  data.submit();
              });
              $('#ox_image_browse_button_'+imageId).hide();               
          },
          done: function(e, data) {
              if (data.result && !data.result.error) {
                 $('#ox_image_uploader_'+imageId).hide();                        
                 $('#ox_image_input_'+imageId).val(data.result.file);
			           $('#ox_image_saved_as_'+imageId).val('');                  
                 $('#ox_image_placeholder_'+imageId).prop('src', data.result.url).show();
                 $('#ox_delete_image_button_'+imageId).show();                 
              } else {
                  $('#' + data.fileId)
                      .delay(2000)
                      .hide('highlight');
                  alert($.mage.__('File extension not known or unsupported type.'));
              }
              $('#' + data.fileId).remove();
              $('#ox_image_browse_button_'+imageId).show();              
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
              $('#ox_image_browse_button_'+imageId).show();                  
          }
      });
  
      $('#ox_image_file').fileupload('option', {
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
   

  },


  deleteImage : function(imageId){  
    $('#ox_delete_image_'+imageId).val(1);
    $('#ox_image_input_'+imageId).val('');
    $('#ox_image_saved_as_'+imageId).val('');                
    $('#ox_value_title_'+imageId).val('');
    $('#ox_image_row_'+imageId).hide();    
  }
		
}); 


}); 


