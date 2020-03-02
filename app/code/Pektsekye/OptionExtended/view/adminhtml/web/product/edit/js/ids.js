
define([
    "jquery"
],function ($) {   

  return {


    optionIds : [],
    optionByChild : [],

    selectIdByRowId    : [], 

    parentRowIdsOfRowId : [],
  
    rowIds : [],
    rowsData : [],
    rowIdIsset : [],
    rowIdByOption : [],
    rowIdsByOption : [], 
    rowIdsByOptionIsset : [],
    rowIdBySelectId : [],
              
    childrenByRowId : [],
    optionByRowId : [],
  
    rowsToActivate : [],
  
    setOptionIds : function(optionId, rowId, group){
      if (group == 'select'){
        if (this.rowIdsByOption[optionId] == undefined)  	
          this.rowIdsByOption[optionId] = [];
        if (this.rowIdsByOptionIsset[optionId] == undefined)
          this.rowIdsByOptionIsset[optionId] = [];	        
      } else { 
        this.rowIdIsset[rowId] = 1;             
        this.rowIdByOption[optionId] = rowId;
        this.optionByRowId[rowId] = optionId;        		            
      }
    },
  

    setOptionValueIds : function(optionId, selectId, rowId){
      this.rowIdIsset[rowId] = 1;							
      this.selectIdByRowId[rowId] = selectId;		
      this.rowIdsByOption[optionId].push(rowId);
      this.rowIdsByOptionIsset[optionId][rowId] = 1;		
      this.rowIdBySelectId[selectId] = rowId;
      this.optionByRowId[rowId] = optionId;      	
    },	
  
  
    unsetOptionIds : function(optionId){
      var rowId;
      this.without(this.optionIds, optionId);
      if (this.isSelect(optionId)){
        var l = this.rowIdsByOption[optionId].length;
        while (l--){
          rowId = this.rowIdsByOption[optionId][l];
          this.without(this.rowIds, rowId);			
          delete this.rowIdIsset[rowId];			
          this.unsetChildren(rowId);			
        }					
        this.rowIdsByOption[optionId] = [];
        this.rowIdsByOptionIsset[optionId] = null;			
      } else {
        rowId = this.rowIdByOption[optionId];
        this.without(this.rowIds, rowId);  		
        delete this.rowIdIsset[rowId];			
        this.unsetChildren(rowId);			
        this.rowIdByOption[optionId] = null;
        this.rowIdsByOptionIsset[optionId] = null;			
      }
    },
  
  
    unsetOptionType : function(optionId, group){  
      if (group == 'select'){
        var l = this.rowIdsByOption[optionId].length;
        while (l--){
          rowId = this.rowIdsByOption[optionId][l];
          this.without(this.rowIds, rowId);			
          delete this.rowIdIsset[rowId];			
          this.unsetChildren(rowId);			
        }					
        this.rowIdsByOption[optionId] = [];
        this.rowIdsByOptionIsset[optionId] = null;			
      } else {
        rowId = this.rowIdByOption[optionId];
        this.without(this.rowIds, rowId);  		
        delete this.rowIdIsset[rowId];			
        this.unsetChildren(rowId);			
        this.rowIdByOption[optionId] = null;
        this.rowIdsByOptionIsset[optionId] = null;			
      }			
    },	
  
    unsetOptionValueIds : function(optionId, rowId){    
      this.without(this.rowIds, rowId);
      delete this.rowIdIsset[rowId];		
      this.without(this.rowIdsByOption[optionId], rowId);
      delete this.rowIdsByOptionIsset[optionId][rowId];		
    
      this.unsetChildren(rowId);			
    },
  
  
    unsetChildren : function(rowId){
      var rId,vId;

      this.setChildrenOfRow(rowId, []);

      if (this.parentRowIdsOfRowId[rowId] != undefined){

        var l = this.parentRowIdsOfRowId[rowId].length;		    
        while (l--){
          rId = this.parentRowIdsOfRowId[rowId][l];
                 
          this.without(this.childrenByRowId[rId], rowId);           
   
          vId = this.selectIdByRowId[rId];
          if ($('#ox_'+vId+'_children').length == 0){
            var widget = this;	
            var jqxhr = this.loadSection(this.optionByRowId[rId]);
            if (jqxhr){
              jqxhr.always(function(){widget.updateChildrenField(rId);});
            }  
          } else {
            this.updateChildrenField(rId);
          }
        }      
      
      }	
    },
  
  
    updateChildrenField : function(rowId){
      var vId = this.selectIdByRowId[rowId];
      var cStr = this.childrenByRowId[rowId].join(',');                
      $('#ox_'+vId+'_children').val(cStr);
    },	
  
  
    setChildrenOfRow : function(rowId, ids){
      var l;
      var previousIds = this.childrenByRowId[rowId] != undefined ? this.childrenByRowId[rowId].slice(0) : []; 
        
      this.childrenByRowId[rowId] = ids;
    
      l = previousIds.length;	     
      while (l--)
        if (ids.indexOf(previousIds[l]) == -1)
          this.without(this.parentRowIdsOfRowId[previousIds[l]], rowId);
            
      l = ids.length;
      while (l--){
        if (this.parentRowIdsOfRowId[ids[l]] == undefined)
          this.parentRowIdsOfRowId[ids[l]] = [];
        this.parentRowIdsOfRowId[ids[l]].push(rowId);
      }  
          
    }	
    
  };
    
});

