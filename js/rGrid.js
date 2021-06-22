function rGrid() {
    this.genElement = function(key,els) {
        var theEls = "";
        var width=els._length*10;
        switch(els._type) {
            case 'text':
            case 'txt':
                theEls += "<input class='myinputtext' type='text' ";
                theEls += "id='el_"+els._id+"_"+key+"' name='"+els._id+"_"+key+"' ";
                theEls += "maxlength='"+els._length+"' ";
                theEls += "style='width:"+width+"px;text-align:"+els._align+";' ";
                for(i in els._attr) {
                    theEls += i+"=\""+els._attr[i]+"\" ";
                }
                theEls += "/>";
                break;
            case 'textnum':
            case 'textnumber':
            case 'textnumeric':
                theEls += "<input class='myinputtextnumber' type='text' ";
                theEls += "id='el_"+els._id+"_"+key+"' name='"+els._id+"_"+key+"' ";
                theEls += "maxlength='"+els._length+"' ";
                theEls += "style='width:"+width+"px;text-align:right;' ";
                for(i in els._attr) {
                    theEls += i+"=\""+els._attr[i]+"\" ";
                }
                theEls += "/>";
                break;
            case 'select':
	    case 'dropdown':
                theEls += "<select id='el_"+els._id+"_"+key+"' name='"+els._id+"_"+key+"' ";
                theEls += "style='width:"+width+"px'>";
                for(i in els._refer) {
                    theEls += "<option value='"+i+"'>"+els._refer[i]+"</option>";
                }
                theEls += "</select>";
                break;
            default:
                break;
        }
        return theEls;
    }
    
    // Make Row to Editable Element
    this.editElement = function(key,elsID) {
        // Get Object-based Element
        var elsObj = document.getElementById(elsID);
        eval("var els = "+elsObj.innerHTML);
        // Transform text to Element
        for(i in els) {
            var tmp = document.getElementById(i+"_"+key);
            tmp.innerHTML = this.genElement(key,els[i]);
        }
        // Switch to Edit Mode
        document.getElementById('makeEdit_'+key).style.display='none';
        document.getElementById('editData_'+key).style.display='';
        document.getElementById('delDataTd_'+key).style.display='';
        document.getElementById('delData_'+key).style.display='';
    }
}

var theRGrid = new rGrid();