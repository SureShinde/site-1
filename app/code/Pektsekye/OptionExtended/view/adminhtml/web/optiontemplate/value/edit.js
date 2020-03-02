var optionExtended = {};

require([
    "jquery",
    "jquery/ui"
], function ($) { 

  $.extend(optionExtended, {

	
	  showSelect : function(type){
	    		
		  if (this.hasOptions){
		  
		    var select,i,l,ll,selected;
        var childrenField = $('#children')[0];

        if (this.rowIdIsSelected == undefined)
          this.checkChildren(childrenField);


		    if (type == 'detailed'){
          select = $('#detailed_select')[0];
          select.options[0].selected = false;
          l = select.options.length;
          for (i=1;i<l;i++){
            if (this.rowIdIsSelected[select.options[i].value] != undefined) 
              select.options[i].selected = true;
            else
              select.options[i].selected = false;
          }        
        } else {
          select = $('#short_select')[0];
          select.options[0].selected = false;          
          l = select.options.length;
          for (i=1;i<l;i++){
        		selected = true;
            ll = this.rowIdsByOption[select.options[i].value].length;	
            while (ll--){		
	            if (this.rowIdIsSelected[this.rowIdsByOption[select.options[i].value][ll]] == undefined){
                selected = false;
                break;
              }  
            }       						
            select.options[i].selected = selected;
          }         
        }
        
        $(childrenField).hide();
        $(select).show();
        select.focus();
			  $('#show_link').hide();

		  }

	  },

	
	
	  showInput : function(type){
      var select;      	
		  var input = $('#children')[0];		  	
		  						
		  if (type == 'detailed'){		
			  select = $('#detailed_select');
			  var ids = select.val();
			  if (ids[0] == '')
			    ids.shift();
        this.resetSelected(ids);  		  			  
	      input.value = ids.join(',');								  					
		  } else {		
        select = $('#short_select');
        if (this.childrenShortSelectWasChanged != undefined){        	
		      var a = select.val();
          var ids = [];		      	  
		      var l = a.length;
	        for (var i=0;i<l;i++)
		        if (a[i] != ''){
				      ids = ids.concat(this.rowIdsByOption[a[i]]);}
				  this.resetSelected(ids);           			      	           
	        input.value = ids.join(',');
          delete this.childrenShortSelectWasChanged;
        }	    				
		  }  	
   					
		  select.hide();      		
		  $(input).show();					       					
		  $('#show_link').show();	
	  },


	
	  checkChildren : function(input){
	  
      var value = input.value;     
		  var ids = [];
		  
		  if (value != '') {						
		    var s = '['+value+']';
		    try {
			    var ch = $.parseJSON(s);
			    var t = [];
		      var l = ch.length;
		      for (var i=0;i<l;i++){
			      if (this.rowIdIsset[ch[i]] != undefined && t[ch[i]] == undefined){
              ids.push(ch[i]);
              t[ch[i]] = 1;           
            }
          }  
	        input.value = ids.join(',');                        		
        } catch (e){
			    input.value = '';      
        }            
		  }
		  
		  this.resetSelected(ids);
	  },

	
	  resetSelected : function(ids){
		  this.rowIdIsSelected = [];	  
      var l = ids.length;
      while (l--)
        this.rowIdIsSelected[ids[l]] = 1;
	  },

	  
	  onChildrenShortSelectChange : function(){
      this.childrenShortSelectWasChanged = 1; 	
	  },		
    
    
    
    loadUploader   : function(){

      var maxFileSize = this.maxFileSize;
      var maxWidth = this.maxWidth;
      var maxHeight = this.maxHeight;

      $('#ox_image_file').fileupload({
          dataType: 'json',
          dropZone: '[data-tab-panel=image-management]',
          sequentialUploads: true,
          acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
          maxFileSize: maxFileSize,
          add: function(e, data) {
              $.each(data.files, function (index, file) {
                  data.fileId = Math.random().toString(33).substr(2, 18);
                  var progressTmpl = $('#ox_image_uploader-template').children(':first').clone();
                  progressTmpl.attr('id', data.fileId);
                  var fileInfoHtml = progressTmpl.html().replace('{{size}}', byteConvert(file.size))
                      .replace('{{name}}', file.name);
                  progressTmpl.html(fileInfoHtml) ;

                  progressTmpl.appendTo('#ox_image_uploader');

              });
              $(this).fileupload('process', data).done(function () {
                  data.submit();
              });
              $('#ox_image_browse_button').hide();              
          },
          done: function(e, data) {
              if (data.result && !data.result.error) {
                 $('#ox_image_uploader').hide();                        
                 $('#ox_image_input').val(data.result.file);
                 $('#ox_image_placeholder').prop('src', data.result.url).show();
                 $('#ox_delete_image_button').show();                 
              } else {
                  $('#' + data.fileId)
                      .delay(2000)
                      .hide('highlight');
                  alert($.mage.__('File extension not known or unsupported type.'));
              }
              $('#' + data.fileId).remove();
              $('#ox_image_browse_button').show();              
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
              $('#ox_image_browse_button').show();                   
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
    
    deleteImage : function(selectId){
      $('#ox_delete_image').val(1);
      $('#ox_image_input').val('');
      $('#ox_image_placeholder').hide();
      $('#ox_delete_image_button').hide();
      $('#ox_image_uploader').show();    
    }	    
	
  });


});


