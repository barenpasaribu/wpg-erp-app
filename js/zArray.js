Array.prototype.inArray = function(){ 
  var i; 
  for(i=0; i < this.length; i++){ 
    if(this[i] === value) 
      return true; 
  }; 
  return false; 
}; 