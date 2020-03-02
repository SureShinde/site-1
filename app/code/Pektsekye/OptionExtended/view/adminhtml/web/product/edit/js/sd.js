
define([
    "jquery"
],function ($) {   

  return {
  
    /*
    getSdColumnInput : function(type, optionId){
      var input;	
      if (type == 'drop_down' || type == 'radio'){
        input = '<input onclick="optionExtended.uncheckAllRadio('+optionId+')" type="radio" name="ox_'+optionId+'_sd" id="ox_'+optionId+'_sd" checked="checked" class="radio" value=""/>';
      } else {
        input = '<input onclick="optionExtended.checkAllCheckboxes(this,'+optionId+')" type="checkbox" name="ox_'+optionId+'_sd[]" id="ox_'+optionId+'_sd" class="checkbox"  title="'+this.selectAll+'" value=""/>';			
      }	
      return input;
    },
  
    getSdCellInput: function(optionId, selectId, rowId, type, checked){	    
      var input;
      if (checked == undefined) 
        checked = '';
      else if (checked == 1) 
        checked = 'checked="checked"';
                    
      if (type == 'drop_down' || type == 'radio'){
        input = '<input onclick="optionExtended.onRadioCheck('+optionId+','+selectId+')" type="radio" class="radio optionextended-sd-input" name="ox_'+optionId+'_sd" id="optionextended_value_'+selectId+'_sd" title="'+this.sdTitle+'" value="" '+checked+'/>';				  			              
      } else {
        input = '<input onclick="optionExtended.onCheckboxCheck(this,'+optionId+','+selectId+')" type="checkbox" class="checkbox optionextended-sd-input" name="ox_'+optionId+'_sd[]" id="optionextended_value_'+selectId+'_sd" title="'+this.sdTitle+'" value="" '+checked+'/>';				  			            
      }	

      return input;         
    },  

  */
    uncheckAllRadio : function(optionId){
      $('#ox_sd_field_'+optionId).val('');     
    },	


    checkAllCheckboxes : function(input, optionId){
      var rId,sId;
      var sd = $('#ox_sd_field_'+optionId)[0];
      var l = this.rowIdsByOption[optionId].length;	      
      if (input.checked){
        sd.value = this.rowIdsByOption[optionId].join(','); 
        for (var i=0;i<l;i++){      
          rId = this.rowIdsByOption[optionId][i];					
          sId = this.selectIdByRowId[rId];	
          $('#ox_sd_'+ sId)[0].checked = true;
        }  	      
      } else {
        sd.value = '';  
        for (var i=0;i<l;i++){      
          rId = this.rowIdsByOption[optionId][i];					
          sId = this.selectIdByRowId[rId];	
          $('#ox_sd_'+ sId)[0].checked = false;
        } 
      }
    },
  
  
    onRadioCheck : function(optionId, selectId){
      $('#ox_sd_field_'+optionId)[0].value = this.rowIdBySelectId[selectId];    
    },
  
  
    onCheckboxCheck : function(input, optionId, selectId){
      var ids = [];
      var sd = $('#ox_sd_field_'+optionId)[0];
      if (sd.value != ''){
        var s = '['+sd.value+']';
        ids = $.parseJSON(s);
      }    
    
      if (input.checked)
        ids.push(this.rowIdBySelectId[selectId]);      
      else 
        this.without(ids, this.rowIdBySelectId[selectId]);
     
      sd.value = ids.join(','); 
    },  
  
  
    addSdId : function(optionId, rowId){ 
      var sd = $('#ox_sd_field_'+optionId)[0];	       
      sd.value += (sd.value != '' ? ',' : '') + rowId;
    },
    
    
    deleteSdId : function(optionId, rowId){
      var sd = $('#ox_sd_field_'+optionId)[0];
      if (sd.value != ''){
        var s = '['+sd.value+']';
        var ids = $.parseJSON(s);	       
        this.without(ids, rowId);
        sd.value = ids.join(','); 
      }
    },
  
  
    reloadSd : function(optionId, type){	 	 
      
        var sdInputType,arraySign,onclick,input;
          
        if (type == 'radio' || type == 'drop_down'){	
          sdInputType = 'radio';	
          arraySign = '';
          onclick = 'onRadioCheck(';				    				  			  			
        } else {    			      														
          sdInputType = 'checkbox';
          arraySign = '[]';
          onclick = 'onCheckboxCheck(this,';				  				  			
        }			  

        var element = $('#ox_sd_all_'+optionId);
        if (element.length && ((sdInputType == 'radio' && element[0].type != 'radio') || (sdInputType == 'checkbox' && element[0].type != 'checkbox'))){
      
          var defaultInput;	    
          if (sdInputType == 'radio')
            defaultInput = '<input onclick="optionExtended.uncheckAllRadio('+optionId+')" type="radio" name="ox_sd_'+optionId+'" id="ox_sd_all_'+optionId+'" checked="checked" class="radio" value=""/>';
          else 
            defaultInput = '<input onclick="optionExtended.checkAllCheckboxes(this,'+optionId+')" type="checkbox" name="ox_sd_'+optionId+'[]" id="ox_sd_all_'+optionId+'" class="checkbox"  title="'+this.selectAll+'" value=""/>';			 
          
          element.replaceWith(defaultInput); 
                    
          var l = this.rowIdsByOption[optionId].length;	      
          for (var i=0;i<l;i++){             
            rId = this.rowIdsByOption[optionId][i];					
            sId = this.selectIdByRowId[rId];
            input = '<input onclick="optionExtended.'+ onclick + optionId +','+sId+')" type="'+sdInputType+'" class="'+sdInputType+' optionextended-sd-input" name="ox_sd_'+optionId+arraySign+'" id="ox_sd_'+sId+'" title="'+this.sdTitle+'" value="" />';	        					            
            $('#ox_sd_'+ sId).replaceWith(input);         
          }
        
          $('#ox_sd_field_'+optionId).val(''); 
                
        } 
       
       
    }
   
  };
    
});


