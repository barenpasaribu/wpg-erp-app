// JavaScript Document
var currentPage=1;
var limitPage=10;
var fields = {
    id:0,
    userlogin:0,
    karyawanid:0,
    cuti:false,
    perjalanandinas:false
}

function bindDataToElements(data, isreset = false) {
    var isreset__ = isreset;
    var keys = Object.keys(data);
    for (var i = 0; i <= keys.length - 1; i++) {
        var el = document.getElementById(keys[i]);
        var isarray = Array.isArray(data[keys[i]]);
        var value = (isarray ? '' : data[keys[i]] == null ? '' : data[keys[i]]);
        if (el != null) {
            if (keys[i]=='cuti' || keys[i]=='perjalanandinas' ) {
                el.checked=(value=='1');
            } else
            el.value = value;//(isreset__ ? '' : isNaN(value)? value : Intl.NumberFormat().format(value) ); 

        } 
    }
}

function cancelForm()
{
    bindDataToElements(fields,true);
}


function saveForm()
{
    karyawanid=document.getElementById('karyawanid').value;
    if (karyawanid==''){
        alert('Anda belum memilih nama karyawan');
        document.getElementById('karyawanid').focus();
        return;
    }
    cuti=document.getElementById('cuti').checked;
    perjalanandinas=document.getElementById('perjalanandinas').checked;
    param = "karyawanid="+karyawanid;
    param += "&cuti="+(cuti?1:0);
    param += "&perjalanandinas="+(perjalanandinas?1:0);
    tujuan="setup_pengaturanAdmin.php?proses=transaction";
    post_response_text(tujuan, param, respog);
        function respog()
        {
            if(con.readyState==4)
            {
            if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                            alert('ERROR TRANSACTION,\n' + con.responseText);
                    }
                    else {
                         reloadData(currentPage,limitPage);
                    }
                }
                else {
                        busy_off();
                        error_catch(con.status);
                }
            }	
         } 	

}

function reloadData(page,limit) {
    window.location = "setup_pengaturanAdmin.php?proses=init&page="+page+"&limit="+limit; 
}

function gotoPage(page,totpage,tipe){
    document.getElementById("prevButton").disabled = false;
    document.getElementById("nextButton").disabled = false;
    if (page==1){
        document.getElementById("prevButton").disabled = true;
    } 
    if (page==totpage){
        document.getElementById("nextButton").disabled = true;
    }
    if (tipe=='prev'){
        if (page>1) page=page-1;
    }
    if (tipe=='next'){
        if (page<totpage) page=page+1;
    }
    currentPage=page;
    reloadData(page,limitPage);
}

function editData(data){
    bindDataToElements(data);
}

function delData(id){
    if (confirm("Anda yakin ingin hapus data ini ?")){
        tujuan="setup_pengaturanAdmin.php?proses=delete";
        param = "id="+id;
        post_response_text(tujuan, param, respog);
        function respog()
        {
            if(con.readyState==4)
            {
            if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                            alert('ERROR TRANSACTION,\n' + con.responseText);
                    }
                    else {
                        reloadData(currentPage,limitPage);
                    }
                }
                else {
                        busy_off();
                        error_catch(con.status);
                }
            }	
         } 	
    }
}
 