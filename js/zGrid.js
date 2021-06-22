/* Class zElement
 * Kelas untuk manajemen zElement
 */
function zElement(cId,cName,cType,cCont,cAlign,cLength,cRefer) {
    // Default Value
    typeof cType=='undefined' ? this.type = 'text' : this.type = cType;
    typeof cCont=='undefined' ? this.content = '' : this.content = cCont;
    typeof cAlign=='undefined' ? this.align = 'L' : this.align = cAlign;
    typeof cLength=='undefined' ? this.length = 40 : this.length = cLength;
    typeof cRefer=='undefined' ? this.refer = new Array() : this.refer = cRefer;
    
    // Init Attr
    this.id = cId;
    this.name = cName;
    this.attr = new Object();
    
    // Align Long
    switch(this.align) {
        case "L":
        case "l":
        case "left":
            this.alignlong = "left";
            break;
        case "R":
        case "r":
        case "right":
            this.alignlong = "right";
            break;
        case "C":
        case "c":
        case "center":
            this.alignlong = "center";
            break;
        default:
            this.alignlong = "left";
            break;
    }
    
    /* Function addAttribute
     * Fungsi untuk menambah atribut
     */
    this.addAttribute = function(key,cont) {
        this.attr[key] = cont;
    }
    
    /* Function removeAttribute
     * Fungsi untuk menghapus atribut
     */
    this.removeAttribute = function(key) {
        delete this.attr[key];
    }
    
    /* Function renderElement
     * Fungsi untuk membuat element daam format HTML
     */
    this.renderElement = function(numRow) {
        // Init
        var els;
        typeof numRow=='undefined' ? numRow = 0 : null;
        
        // Render By Type
        switch(cType) {
            case 'text':
            case 'txt':
                els = "<input type='text' id='"+this.id+"_"+numRow+"' ";
                els += "name='"+this.id+"_"+numRow+"' ";
                els += "class='myinputtext' "
                els += "maxlength='"+this.length+"' value='"+this.content+"' ";
                els += "style='margin:0;width:"+(this.length*6.5)+"px;text-align:"+this.alignlong+"' ";
                els += "/>";
                break;
            case 'textnum':
            case 'txtnum':
                els = "<input type='text' id='"+this.id+"_"+numRow+"' ";
                els += "name='"+this.id+"_"+numRow+"' ";
                els += "class='myinputtextnumber' "
                els += "maxlength='"+this.length+"' value='"+this.content+"' ";
                els += "style='margin:0;width:"+(this.length*6.5)+"px;text-align:"+this.alignlong+"' ";
                els += "/>";
                break;
            default :
                break;
        }
        
        return els;
    }
}

/* Class zGrid
 * Kelas untuk manajemen zGrid
 * File yang berhubungan : /lib/zGrid.php; /style/zGrid/*theme*.css;
 */
function zGrid(num) {
    // Default Value
    typeof num=='undefined' ? this.num = 1 : this.num = num;
    
    this.totalRow = 0;
    this.column = new Array();
    this.primaryColumn = new Array();
    this.target = null;
    var gridNum = this.num;
    var statusAjax = 0; // Ajax Idle
    
    /* Function addColumn
     * Fungsi untuk menambah setting kolom
     */
    this.addColumn = function(cId,cName,cType,cCont,cAlign,cLength,cRefer) {
        var tmp = new zElement(cId,cName,cType,cCont,cAlign,cLength,cRefer);
        this.column.push(tmp);
    }
    
    /* Function addPrimColumn
     * Fungsi untuk menambah list primary key
     */
    this.addPrimColumn = function(cId,cName) {
        var tmp = new zElement(cId,cName);
        this.primaryColumn.push(tmp);
    }
    
    /* Function addRowGrid
     * Fungsi untuk menampilkan baris baru pada grid
     */
    this.addRowGrid = function() {
        var cols = this.column;
        
        // Get Available numRow
        var numRow = 0;
        while(document.getElementById('grid_tr_'+numRow)) {
            numRow++;
        }
        
        // Prep New Row
        var tmpEls = "<tr id='grid_tr_"+numRow+"'>";
        for(i in this.column) {
            tmpEls += "<td id='grid_td_"+this.column[i].id+"_"+numRow+"'>";
            tmpEls += this.column[i].renderElement(numRow);
            tmpEls += "</td>";
        }
        tmpEls += "<td id='grid_action_"+numRow+"'><img src='images/plus.png' class='zImgBtn'"+
            " onclick='theGrid["+this.num+"].addDataGrid("+numRow+")' /></td>";
        tmpEls += "</tr>";
        
        // Insert New Row
        document.getElementById('zGridBody_'+this.num).innerHTML += tmpEls;
        
        // Hide blank row
        document.getElementById('grid_tr_empty').style.display = 'none';
        this.totalRow++;
    }
    
    /* Function getRowParam
     * Fungsi untuk mengambil parameter untuk manipulasi data
     */
    this.getRowParam = function(numRow) {
        var param = "";
        // In-Grid Parameter
        for(i in this.column) {
            // Get Value
            var tmp = document.getElementById(this.column[i].id+"_"+numRow);
            if(!tmp)
                alert('DOM Definition Error : '+this.column[i].id);
            if(tmp.options) {
                var tmpVal = tmp.options[tmp.selectedIndex].value;
            } else {
                var tmpVal = tmp.value;
            }
            
            // Append to Parameter
            if(i>0)
                param += "&";
            param += this.column[i].id+"="+tmpVal;
        }
        
        // Additional Parameter
        for(i in this.primaryColumn) {
            // Get Value
            var tmp = document.getElementById(this.primaryColumn[i].id);
            if(!tmp)
                alert('DOM Definition Error : '+this.primaryColumn[i].id);
            if(tmp.options) {
                var tmpVal = tmp.options[tmp.selectedIndex].value;
            } else {
                var tmpVal = tmp.value;
            }
            
            // Append to Parameter
            if(i>0)
                param += "&";
            param += this.primaryColumn[i].name+"="+tmpVal;
        }
        
        return param;
    }
    
    /* Function addDataGrid
     * Fungsi untuk menambah data baru
     */
    this.addDataGrid = function(numRow) {
        if(this.target==null) {
            // No Target
            alert('Error : File Target Not Found');
            exit;
        } else {
            var target = this.target;
        }
        
        // Passing Parameter
        var param = this.getRowParam(numRow);
        
        // Response Function
        
        function respon() {
            statusAjax = 1;// Progress
            if (con.readyState == 4) {
                statusAjax = 0;// Idle
                if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                        alert('ERROR TRANSACTION,\n' + con.responseText);
                    } else {
                        // Success Response
                        //alert('Add Success');
                        var act = document.getElementById('grid_action_'+numRow);
        
                        // Change to Edit & Delete Action
                        act.innerHTML = "<img class='zImgBtn' src='images/001_45.png' "+
                            "onclick='theGrid["+gridNum+"].editRowGrid("+numRow+")'></img>&nbsp;";
                        act.innerHTML += "<img class='zImgBtn' src='images/delete1.png' "+
                            "onclick='theGrid["+gridNum+"].delRowGrid("+numRow+")'></img>";
                        
                        // Freeze Primary
                        for(i in this.primaryColumn) {
                            document.getElementById(this.primaryColumn[i].id+"_"+numRow).setAttribute('disabled','disabled');
                        }
                    }
                } else {
                    busy_off();
                    error_catch(con.status);
                }
            }
        }
        
        // AJAX Post
        if(statusAjax==1)
            alert('Please Wait');
        else
            post_response_text(target+'.php?proses=addGrid'+this.num, param, respon);
    }
    
    /* Function editRowGrid
     * Fungsi untuk mengubah data pada grid
     */
    this.editRowGrid = function(numRow) {
        if(this.target==null) {
            // No Target
            alert('Error : File Target Not Found');
            exit;
        } else {
            var target = this.target;
        }
        
        // Passing Parameter
        var param = this.getRowParam(numRow);
        
        // Response Function
        function respon() {
            statusAjax = 1;// Progress
            if (con.readyState == 4) {
                statusAjax = 0;// Idle
                if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                        alert('ERROR TRANSACTION,\n' + con.responseText);
                    } else {
                        // Success Response
                        alert('Edit Success');
                    }
                } else {
                    busy_off();
                    error_catch(con.status);
                }
            }
        }
        
        // AJAX Post
        if(statusAjax==1)
            alert('Please Wait');
        else
            post_response_text(target+'.php?proses=editGrid'+this.num, param, respon);
    }
    
    /* Function delRowGrid
     * Fungsi untuk menghapus data pada grid
     */
    this.delRowGrid = function(numRow) {
        if(this.target==null) {
            // No Target
            alert('Error : File Target Not Found');
            exit;
        } else {
            var target = this.target;
        }
        
        // Passing Parameter
        var param = this.getRowParam(numRow);
        
        // Response Function
        function respon() {
            statusAjax = 1;// Progress
            if (con.readyState == 4) {
                statusAjax = 0;// Idle
                if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                        alert('ERROR TRANSACTION,\n' + con.responseText);
                    } else {
                        // Success Response
                        alert('Delete Success');
                        var theTr = document.getElementById('grid_tr_'+numRow);
                        theTr.parentNode.removeChild(theTr);
                    }
                } else {
                    busy_off();
                    error_catch(con.status);
                }
            }
        }
        
        // AJAX Post
        if(statusAjax==1)
            alert('Please Wait');
        else
            post_response_text(target+'.php?proses=delGrid'+this.num, param, respon);
    }
}

var theGrid = new Array();
theGrid[1] = new zGrid(1);