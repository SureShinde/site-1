
define([
    "jquery"
],function ($) {   

  return {

    
    isSelect : function(optionId){
      var input = $('#product_option_'+optionId+'_type');
      var type = input.length && input.val() != '' ? input.val() : this.optionTypes[optionId];	    
  
      switch(type){
        case "radio":							
        case "checkbox":							
        case "drop_down":						
        case "multiple":	
          return true;				
      }	  	
       return false;		
    },
  
    optionIsNew : function (optionId)
    {
      return this.optionTypes[optionId] == undefined;
    },
  
    rowIsNew : function (rowId)
    {
      return this.valueTitles[rowId] == undefined;
    },
    
    arrayToInt : function (a){
      var t = [];
      var l = a.length;
      for(var i=0;i<l;i++)
        if (a[i] != '')
          t.push(parseInt(a[i]));
      return t;
    },	
     
    unique : function(a){
      var l=a.length,b=[],c=[];
      while (l--)
        if (c[a[l]] == undefined) b[b.length] = c[a[l]] = a[l];
      return b;
    }	
   
  };
    
});


