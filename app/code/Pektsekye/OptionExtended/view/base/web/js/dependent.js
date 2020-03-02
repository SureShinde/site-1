
define([
    "jquery",
    "jquery/ui"
],function ($) {   

  return {

  
    oIds : [],
    oldV : [],  
    oldO : [],
    childrenVals : [],
    indByValue : [],
    valsByOption : [],
    optionByValue : [],
    univValsByOption : [],
    childOIdsByO : [],
    previousIds : [],
    childrenByOption : [],


  
    loadDependency : function(element, optionId){
     
       if (!this.valsByOption[optionId]){   
        this.oldO[optionId].visible = true; 
        this.valsByOption[optionId] = [];
        this.oIds.push(optionId);
        this.isNewOption = true;        
        var c = 0;        
      }

      if (element[0].type == 'radio' || element[0].type == 'checkbox') {  
    
        element[0].checked = false;      
        this.setVars(optionId, element, c, null);         
      
      } else if ((element[0].type == 'select-one' && !element.hasClass('datetime-picker')) || element[0].type == 'select-multiple') {  
    
        var options = element[0].options;
        for (var i = 0, len = options.length; i < len; ++i){
          options[i].selected = false;      
          this.setVars(optionId, element, i, options[i]);
        }   
        
      }
    
      c++;
    },


    setVars : function(optionId, element, i, option){
    
      var value = option ? option.value : element.val();
      if (value){
        var valueId = parseInt(value);          
        this.indByValue[valueId] = i;
        this.valsByOption[optionId].push(valueId);
        this.optionByValue[valueId] = optionId;
        if (!this.oldV[valueId])
          this.oldV[valueId] = {};
        this.oldV[valueId].visible = true;
        this.oldV[valueId].selected = false;      
        if (option)
          this.oldV[valueId].name = option.text;      
        
        if (this.config[1][valueId][2].length > 0){           
          if (!this.childOIdsByO[optionId])
            this.childOIdsByO[optionId] = [];
          this.childOIdsByO[optionId] = this.childOIdsByO[optionId].concat(this.config[1][valueId][2]);             
        } 
        if (this.config[1][valueId][3].length > 0){             
          this.childrenVals = this.childrenVals.concat(this.config[1][valueId][3]);               
          if (!this.childrenByOption[optionId])
            this.childrenByOption[optionId] = [];
          this.childrenByOption[optionId] = this.childrenByOption[optionId].concat(this.config[1][valueId][3]);
        }   
      }
    }, 
  
  
    setDependency : function(){
      var l = this.oIds.length; 
      for (var i=0;i<l;i++){
        var ll = this.valsByOption[this.oIds[i]].length;
        while (ll--){
          if (this.childrenVals.indexOf(this.valsByOption[this.oIds[i]][ll]) == -1){
            if (!this.univValsByOption[this.oIds[i]])
              this.univValsByOption[this.oIds[i]] = [];
            this.univValsByOption[this.oIds[i]].push(this.valsByOption[this.oIds[i]][ll]);
          }
        }
        var ids = this.getChildrenOptionIds(this.oIds[i]);
        if (ids.length > 0)
            this.childOIdsByO[this.oIds[i]] = ids;      
      }

      while (l--) 
        if (this.childOIdsByO[this.oIds[l]])
          this.reloadOptions(this.oIds[l], [], []);   
    },
  
  
    observeRadio : function(optionId, valueId, event, process){
      if (process == "updatingPrice")
        return;    
      if (this.childOIdsByO[optionId]){
        if (!valueId)
          this.reloadOptions(optionId, [], [])      
        else
          this.reloadOptions(optionId, this.config[1][valueId][2], this.config[1][valueId][3]); 
      }
      this.oldO[optionId].value = valueId;  
      this.observeRadioImage(optionId, valueId);
      
      if (process != "selectingDefault"){
        this.config[0][optionId][3] = [];		
        if (valueId)		
          this.config[0][optionId][3].push(parseInt(valueId));      
        this.selectDefault(optionId);		   
      }          
    },
  
  
    observeCheckbox : function(element, optionId, valueId, event, process){
      if (process == "updatingPrice")
        return;  
          
      var selectedIds = [];	        
        
      var vId;         
      var cOIds = [];
      var cVIds = [];    
      var l = this.valsByOption[optionId].length;
      while (l--){
        vId = this.valsByOption[optionId][l]; 
        if (this.oldV[vId].element[0].checked){
          if (this.config[1][vId][2].length > 0)       
            cOIds = cOIds.concat(this.config[1][vId][2]);  
          if (this.config[1][vId][3].length > 0)
            cVIds = cVIds.concat(this.config[1][vId][3]);
          selectedIds.push(vId);                          
        }
      } 
        
      if (this.childOIdsByO[optionId]){               
        this.reloadOptions(optionId, this.unique(cOIds), this.unique(cVIds));
      }
      
      this.observeCheckboxImage(element, optionId, valueId);
      
      if (process != "selectingDefault"){	
        this.config[0][optionId][3] = selectedIds;      
        this.selectDefault(optionId);		   
      }          
    },
  
  
    observeSelectOne : function(element, optionId, event, process){
      if (process == "updatingPrice")
        return;    
      var valueId = element.val();      
      if (this.childOIdsByO[optionId]){   
        if (!valueId){
          this.reloadOptions(optionId, [], []);
        } else {
          this.reloadOptions(optionId, this.config[1][valueId][2], this.config[1][valueId][3]); 
        } 
      }
      this.oldO[optionId].value = valueId;        
      this.observeSelectOneImage(element, optionId);
       
      if (process != "selectingDefault"){
        this.config[0][optionId][3] = [];		
        if (valueId)		
          this.config[0][optionId][3].push(parseInt(valueId));      
        this.selectDefault(optionId);		   
      }         
    },
  
  
    observeSelectMultiple : function(element, optionId, event, process){
      if (process == "updatingPrice")
        return;  
        
      var selectedIds = [];	        
          
      var vId,option;          
      var cOIds = [];
      var cVIds = []; 
                
      var l = element[0].options.length;
      while (l--){
        option = element[0].options[l];
        vId = option.value;      
        if (option.selected){
          if (this.config[1][vId][2].length > 0)       
            cOIds = cOIds.concat(this.config[1][vId][2]);            
          if (this.config[1][vId][3].length > 0)
            cVIds = cVIds.concat(this.config[1][vId][3]);
          selectedIds.push(parseInt(vId));                                  
        }
        this.oldV[vId].selected = option.selected;     
      } 
      if (this.childOIdsByO[optionId]){     
        this.reloadOptions(optionId, this.unique(cOIds), this.unique(cVIds));
      }
      
      this.observeSelectMultipleImage(element, optionId); 
      
      if (process != "selectingDefault"){	
        this.config[0][optionId][3] = selectedIds;      
        this.selectDefault(optionId);		   
      }         
    },


    reloadOptions : function(id, optionIds, valueIds){
      var a = [];
      var l = valueIds.length;
      while (l--){
        if (!a[this.optionByValue[valueIds[l]]])
          a[this.optionByValue[valueIds[l]]] = [];        
        a[this.optionByValue[valueIds[l]]].push(valueIds[l]);
      }

      l = this.childOIdsByO[id].length;
      while (l--){
        if (a[this.childOIdsByO[id][l]]){
          if (this.univValsByOption[this.childOIdsByO[id][l]])
            a[this.childOIdsByO[id][l]] = a[this.childOIdsByO[id][l]].concat(this.univValsByOption[this.childOIdsByO[id][l]]);          
          this.reloadValues(this.childOIdsByO[id][l], a[this.childOIdsByO[id][l]]);         
        } else if(this.univValsByOption[this.childOIdsByO[id][l]]) {      
          this.reloadValues(this.childOIdsByO[id][l], this.univValsByOption[this.childOIdsByO[id][l]]);
        } else if (optionIds.indexOf(this.childOIdsByO[id][l]) != -1){
          this.showOption(this.childOIdsByO[id][l], this.oldO[this.childOIdsByO[id][l]].element);         
        } else {
          if (this.oldO[this.childOIdsByO[id][l]].element == undefined ||  this.oldO[this.childOIdsByO[id][l]].element[0].type == 'select-one' || this.oldO[this.childOIdsByO[id][l]].element[0].type == 'select-multiple')           
            this.reloadValues(this.childOIdsByO[id][l], []);    
          this.hideOption(this.childOIdsByO[id][l]);          
        } 
      } 
    },  

  
    showOption : function(id, element){
      if (!this.oldO[id].visible){
    
        this.oldO[id].dd.show();
      
        if (element[0].type == 'file'){
          var disabled = false;
          /*
          if (this.inPreconfigured){
            var inputBox = element.up('.input-box');
            if (!inputBox.visible()){
              var inputFileAction = inputBox.select('input[name="options_'+ id +'_file_action"]')[0];
              inputFileAction.value = 'save_old';
              disabled = true;
            } 
          }
          */   
          element[0].disabled = disabled;        
        }
      
        this.oldO[id].visible = true;
      }  
    },
  
  
    hideOption : function(id){
      if (this.oldO[id].visible){
    
        if (this.dependecyIsSet){
          var element = this.oldO[id].element ? this.oldO[id].element : this.oldO[id].firstelement;
          if (element.hasClass('datetime-picker')){
            element[0].selectedIndex = 0; 
          } else if (element[0].type == 'text' || element[0].type == 'textarea') {        
            element.val('');
          } else if (element[0].type == 'file') {
  /*
            if (this.inPreconfigured) {
              var inputBox = element.up('.input-box');
              if (!inputBox.visible()){
                var inputFileAction = inputBox.select('input[name="options_'+ id +'_file_action"]')[0];
                inputFileAction.value = '';                             
              }                 
            }
            */
            element[0].disabled = true;
          }
        }
      
        this.oldO[id].dd.hide();
        this.oldO[id].visible = false;
      }  
    },


  
    reloadValues : function(id, ids){

      var l = this.valsByOption[id].length;
    
      if (l == 0)
        return;  
      
      if (this.oldO[id].element != undefined){
        this.clearSelect(id);        
        for (var i=0;i<l;i++){   
          if (ids.indexOf(this.valsByOption[id][i]) != -1)      
              this.showValue(id, this.valsByOption[id][i]);   
        }
        this.oldO[id].element.trigger('change', ["updatingPrice"]);                                 
      } else {
        for (var i=0;i<l;i++){      
          if (ids.indexOf(this.valsByOption[id][i]) != -1){   
            if (!this.oldV[this.valsByOption[id][i]].visible)
              this.showValue(id, this.valsByOption[id][i], this.oldV[this.valsByOption[id][i]].element);
            else 
              this.resetValue(id, this.valsByOption[id][i], this.oldV[this.valsByOption[id][i]].element);
          } else if (ids.indexOf(this.valsByOption[id][i]) == -1 && this.oldV[this.valsByOption[id][i]].visible) {
            this.hideValue(id, this.valsByOption[id][i], this.oldV[this.valsByOption[id][i]].element);
          }
        }   
      }
  
    },
  
  
    showValue : function(optionId, valueId, element){
      if (element){       
        element.closest('.field').show();  
        this.showOption(optionId, element);       
      } else {
        var ind = this.oldO[optionId].element[0].options.length;     
        this.oldO[optionId].element[0].options[ind] = new Option(this.oldV[valueId].name, valueId);
        this.indByValue[valueId] = ind;
        this.showOption(optionId, this.oldO[optionId].element);
        if (this.oldO[optionId].element[0].type == 'select-one')
          this.showPickerImage(optionId, valueId);      
      } 
      this.oldV[valueId].visible = true;
    },

    clearSelect : function(optionId){  
      var l = this.valsByOption[optionId].length;

      for (var i=0;i<l;i++){
        if (this.oldV[this.valsByOption[optionId][i]].selected || this.oldO[optionId].value)
          this.resetImage(optionId, this.valsByOption[optionId][i], this.oldO[optionId].element[0].type);       
        this.indByValue[this.valsByOption[optionId][i]] = null;
        this.oldV[this.valsByOption[optionId][i]].visible = false;            
      }
    
      if (this.oldO[optionId].element[0].type == 'select-one'){
        while (l--) 
          this.hidePickerImage(optionId, this.valsByOption[optionId][l]);
        this.oldO[optionId].element[0].options.length = 1;                                         
      } else {
        this.oldO[optionId].element[0].options.length = 0;
      }   
     
    },
  
    hideValue : function(optionId, valueId, element){
      this.resetValue(optionId, valueId, element);
      if (element){
        element.closest('.field').hide();
      } else {
        var ind = this.indByValue[valueId];
        this.oldO[optionId].element[0].options[ind] = null;
        this.indByValue[valueId] = null;
        if (this.oldO[optionId].element[0].type == 'select-one')
          this.hidePickerImage(optionId, valueId);
      } 
      this.oldV[valueId].visible = false;
    },  
  
  
    resetValue : function(optionId, valueId, element){
      if (element){
         if (element[0].checked){  
          this.resetImage(optionId, valueId, element[0].type);       
          element[0].checked = false;
          if (element[0].type == 'radio'){
            var noneOption = $('#options_'+optionId);
            if (noneOption.length == 0){
              element.before($('<input type="radio" style="display:none;" id="options_'+optionId+'" name="options['+optionId+']" value="">'));
              noneOption = $('#options_'+optionId);
              var priceOptions = $('#product_addtocart_form').data('magePriceOptions');
              if (priceOptions)
                noneOption.change($.proxy(priceOptions._onOptionChanged, priceOptions));              
            }                  
            noneOption[0].checked = true;
            noneOption.trigger('change', ["updatingPrice"]);
          } else {
            element.trigger('change', ["updatingPrice"]);
          }          
        }
      } else {
        var ind = this.indByValue[valueId];
        if ((this.oldV[valueId] && this.oldV[valueId].selected) || this.oldO[optionId].value){
          this.resetImage(optionId, valueId, this.oldO[optionId].element[0].type);
          if (this.oldO[optionId].element[0].type == 'select-one'){
            this.oldO[optionId].element[0].selectedIndex = 0;
          } else {
            this.oldO[optionId].element[0].options[ind].selected = false;
            this.oldV[valueId].selected = false;
          }
        }
      } 
    },  
  
  
    getChildrenOptionIds : function(id){
      if (this.previousIds[id])
        return [];
      this.previousIds[id] = true;
      if (!this.childrenByOption[id] && !this.childOIdsByO[id])
        return [];    
      var optionIds = [];
      if (this.childOIdsByO[id]){
        this.childOIdsByO[id] = this.unique(this.childOIdsByO[id]);
        optionIds = optionIds.concat(this.childOIdsByO[id]);
      }
      if (this.childrenByOption[id]){   
        var ids = this.unique(this.childrenByOption[id]);   
        var l = ids.length;
        while (l--)
          if (optionIds.indexOf(this.optionByValue[ids[l]]) == -1)
            optionIds.push(this.optionByValue[ids[l]]);
      }
      var l = optionIds.length;
      while (l--){
        var ids = this.getChildrenOptionIds(optionIds[l]);
        if (ids.length > 0){
            this.childOIdsByO[optionIds[l]] = ids;
            optionIds = optionIds.concat(ids);
        }   
      }
      return optionIds;   
    },
  
  
    unique : function(a){
      var l=a.length,b=[],c=[];
      while (l--)
        if (c[a[l]] == undefined) b[b.length] = c[a[l]] = a[l];
      return b;
    } 
  
	 
  };
    
});