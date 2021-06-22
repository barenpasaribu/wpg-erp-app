// JavaScript Document
function add_new_data()
{
                document.getElementById('headher').style.display="block";
                document.getElementById('listData').style.display="none";
                document.getElementById('detailEntry').style.display="none";
                document.getElementById('tmbLheader').innerHTML='<button class=mybutton id=dtlAbn onclick=detailAbsn()>'+nmTmblSave+'</button><button class=mybutton id=cancelAbn onclick=cancelAbsn()>'+nmTmblCancel+'</button>';
                unlockForm();	
                document.getElementById('contentDetail').innerHTML='';
                statFrm=0;
}
function displayList()
{
        document.getElementById('listData').style.display='block';
        document.getElementById('headher').style.display='none';
        document.getElementById('detailEntry').style.display='none';
        document.getElementById('kdOrgCr').value='';
        document.getElementById('tgl_cari').value='';
        loadData();
}
function cariOrg(title,content,ev)
{
        width='500';
        height='400';
        showDialog1(title,content,width,height,ev);
        //alert('asdasd');
}
function findOrg()
{
        txt=trim(document.getElementById('fnOrg').value);
        if(txt=='')
        {
                alert('Text is obligatory');
        }
        else if(txt.length<3)
        {
                alert('Text too short');
        }
        else
        {
                param='txtfind='+txt+'&proses=cariOrg';
                tujuan='sdm_slave_lembur.php';
                post_response_text(tujuan, param, respog);
        }
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
                                                        //alert(con.responseText);
                                                        document.getElementById('container').innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  	
}
function setOrg(kdOrg,nmOrg)
{
        document.getElementById('kdOrg').value=kdOrg;
        document.getElementById('nmOrg').value=nmOrg;
        closeDialog();
}
function findOrg2()
{
        txt=trim(document.getElementById('crOrg').value);
        if(txt=='')
        {
                alert('Text is obligatory');
        }
        else if(txt.length<3)
        {
                alert('Text too short');
        }
        else
        {
                param='txtfind='+txt+'&proses=cariOrg2';
                tujuan='sdm_slave_lembur.php';
                post_response_text(tujuan, param, respog);
        }
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
                                                        //alert(con.responseText);
                                                        document.getElementById('container').innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  	
}
function setOrg2(kdOrg,nmOrg)
{
        document.getElementById('kdOrg').value=kdOrg;
        document.getElementById('txtsearch').value=nmOrg;
        closeDialog();
}
function detailAbsn()
{
        kdorg=document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;
        tgl=document.getElementById('tglAbsen').value;
        if((kdorg=='')||(tgl==''))
        {
                alert("Date and organization code are obligatory");
                return;
        }

        id=kdorg+"###"+tgl;
        tujuan='sdm_slave_lembur.php';
        param='absnId='+id+'&proses=cekHeader';
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
                                                                add_detail();
                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  

}
function add_detail()
{
        kdorg=document.getElementById('kdOrg').value;
        tgl=document.getElementById('tglAbsen').value;
        id=kdorg+"###"+tgl;
        param='absnId='+id;
        param+="&proses=createTable";
        tujuan='sdm_slave_lembur.php';
        function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                            document.getElementById('detailEntry').style.display='block';
                            document.getElementById('detailIsi').innerHTML=con.responseText;
                            document.getElementById('tmbLheader').innerHTML='';
                            lockForm();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
        post_response_text(tujuan, param, respon);
}
function lockForm()
{
        document.getElementById('kdOrg').disabled=true;
        document.getElementById('tglAbsen').disabled=true;
}
function unlockForm()
{
        document.getElementById('kdOrg').disabled=false;
        document.getElementById('tglAbsen').disabled=false;
        document.getElementById('kdOrg').value='';
        document.getElementById('tglAbsen').value='';
}
status_inputan=0;
function addDetail() {
        if(status_inputan==0)
        {
                if(confirm('Are you sure..?'))
                {
                        cek_data();	
                }
        }
        else if(status_inputan!=0)
        {
                cek_data();	
        }

}

function editDetail(krywn,tplmbr,jmaktl,ungmkn,ungtrans,unglbhjm) {
        //alert('test');

        //document.getElementById('krywnId').value=krywn; 
        ct=document.getElementById('krywnId');
        for(x=0;x<ct.length;x++)
        {
                if(ct.options[x].value==krywn)
                {
                        ct.options[x].selected=true;
                }
        }
        ct2=document.getElementById('tpLmbr');
        for(x=0;x<ct2.length;x++)
        {
                if(ct2.options[x].value==tplmbr)
                {
                        ct2.options[x].selected=true;
                }
        }
        document.getElementById('krywnId').disabled=true;
        //document.getElementById('tpLmbr').value=tplmbr;
        document.getElementById('uang_mkn').value=ungmkn;
        document.getElementById('uang_trnsprt').value=ungtrans;
        document.getElementById('uang_lbhjm').value=unglbhjm;
        document.getElementById('proses').value="updateDetail";
        getLembur(tplmbr,jmaktl);
        //chngeFormat();   
}

/* Function deleteDelete(id)
 * Fungsi untuk menghapus data Detail
 * I : id row (urutan row pada table Detail)
 * P : Menghapus data pada tabel Detail
 * O : Menghapus baris pada tabel Detail
 */
function deleteDetail(id) {
        kdorg=document.getElementById('kdOrg').value;
        tgl=document.getElementById('tglAbsen').value;

        var detKode = kdorg+"###"+tgl;
        var rkrywn = document.getElementById('krywnId_'+id);
                param = "proses=detail_delete";
                param += "&absnId="+detKode;
                param += "&krywnId="+rkrywn.value;
                //alert(param);
                tujuan='sdm_slave_detail_lembur.php';
                function respon(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        } else {
                                                // Success Response
                                row = document.getElementById("detail_tr_"+id);
                                if(row) {
                                row.style.display="none";
                                } else {
                                alert("Row undetected");
                                }
                                        }
                                } else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }
                        if(confirm('Deleting, are you sure..?'))
                        {
                                post_response_text(tujuan, param, respon);	
                        }
                        else
                        {
                                return;
                        }
}
 /* Function addNewRow
 * Fungsi untuk menambah row baru ke dalam table
 * I : id dari tbody tabel
 * P : Persiapan row dalam bentuk HTML
 * O : Tambahan row pada akhir tabel (append)
 */
function addNewRow(body,onDetail) {
        //alert(body);
    var tabBody = document.getElementById(body);
    if(onDetail) {
        var detail = onDetail;

    } else {
        var detail = false;
    }

    // Search Available numRow
    var numRow = 0;
    if(!detail) {
        while(document.getElementById('tr_'+numRow)) {
            numRow++;
        }
    } else {
        while(document.getElementById('detail_tr_'+numRow)) {
            numRow++;
        }
    }

    // Add New Row
    var newRow = document.createElement("tr");
    tabBody.appendChild(newRow);
    if(!detail) {
        newRow.setAttribute("id","tr_"+numRow);
    } else {
        newRow.setAttribute("id","detail_tr_"+numRow);
    }
    newRow.setAttribute("class","rowcontent");

    if(!detail) {
        newRow.innerHTML += "<td><input id='kode_"+numRow+
        "' type='text' class='myinputtext' style='width:70px' onkeypress='return tanpa_kutip(event)' value='' /></td><td><input id='matauang_"+numRow+
        "' type='text' class='myinputtext' style='width:70px' onkeypress='return tanpa_kutip(event)' value='' /></td><td><input id='simbol_"+numRow+
        "' type='text' class='myinputtext' style='width:70px' onkeypress='return tanpa_kutip(event)' value='' /></td><td><input id='kodeiso_"+numRow+
        "' type='text' class='myinputtext' style='width:70px' onkeypress='return tanpa_kutip(event)' value='' /></td><td><img id='add_"+numRow+
        "' title='Tambah' class=zImgBtn onclick=\"addMain('"+numRow+"')\" src='images/plus.png'/>"+
        "&nbsp;<img id='delete_"+numRow+"' />"+
        "&nbsp;<img id='pass_"+numRow+"' />"+
        "</td>";
    } else
        {
                // Create Row
                newRow.innerHTML += "<td><select id='krywnId_"+numRow+"' type='text' style='width:150px' />"+optIsi+"</select></td><td>"+"<select id='tpLmbr_"+numRow+"' />"+optLmbr+"</select></td>"+"<td><select id='jmId_"+numRow+"' type='text' />"+optJm+"</select>:<select id='mntId_"+numRow+"' type='text' />"+optMnt+"</select></td>"+"<td><input type='text' onfocus=\"normal_number_1('"+numRow+"')\" onblur=\"chngeFormat('"+numRow+"')\" maxlength='10' onkeypress='return angka_doang(event)' style='width: 100px;' value='0' class='myinputtextnumber' name=uang_mkn_"+numRow+" id=uang_mkn_"+numRow+"></td>"+"<td><input type='text' onfocus=\"normal_number_1('"+numRow+"')\" onblur=\"chngeFormat('"+numRow+"')\" maxlength='10' onkeypress='return angka_doang(event)' style='width: 100px;' value='0' class='myinputtextnumber' name=uang_trnsprt_"+numRow+" id=uang_trnsprt_"+numRow+"></td>"+"<td><input type='text' onfocus=\"normal_number_1('"+numRow+"')\" onblur=\"chngeFormat('"+numRow+"')\" maxlength='10' onkeypress='return angka_doang(event)' style='width: 100px;' value='0' class='myinputtextnumber' name=uang_lbhjm_"+numRow+" id=uang_lbhjm_"+numRow+"></td>"+"<td><img id='detail_add_"+numRow+"' title='Tambah' class=zImgBtn onclick=\"addDetail('"+numRow+"')\" src='images/save.png'/>"+"&nbsp;<img id='detail_delete_"+numRow+"' />"+"&nbsp;<img id='detail_pass_"+numRow+"' />"+"</td>";
          }
  }
/* Function switchEditAdd
 * Fungsi untuk mengganti image add menjadi edit dan keroconya
 * I : id nomor row
 * P : Image Add menjadi Edit
 * O : Image Edit
 */
function switchEditAdd(id,main) {

 if(main=='main') {
        var idField = document.getElementById('add_'+id);
        var delImg = document.getElementById('delete_'+id);
        var passImg = document.getElementById('pass_'+id);
        var kode = document.getElementById('kode_'+id);
    } else {
        //alert(id);
        var idField = document.getElementById('detail_add_'+id);
        var delImg = document.getElementById('detail_delete_'+id);
    }
    if(idField) {
        idField.removeAttribute('id');
        idField.removeAttribute('name');
        idField.removeAttribute('onclick');
        idField.removeAttribute('src');
        idField.removeAttribute('title');

        // Set Edit Image Attr
        idField.setAttribute('title','Edit');
        if(main=='main') {
            idField.setAttribute('id','edit_'+id);
            idField.setAttribute('name','edit_'+id);
            idField.setAttribute('onclick','editMain(\''+id+'\',\'kode\',\''+kode.value+'\')');
        } else {
                        //alert(id);
                idField.setAttribute('id','detail_edit_'+id);
                        idField.setAttribute('name','detail_edit_'+id);
            idField.setAttribute('onclick','editDetail(\''+id+'\')');
        }
        idField.setAttribute('src','images/001_45.png');

        // Set Delete Image Attr
                delImg.setAttribute('class','zImgBtn');
        delImg.setAttribute('title','Hapus');
        if(main=='main') {
                        delImg.setAttribute('name','delete_'+id);
            delImg.setAttribute('onclick','deleteMain(\''+id+'\',\'kode\',\''+kode.value+'\')');
        } else {
                        //alert(id);
                        delImg.setAttribute('name','detail_delete_'+id);
            delImg.setAttribute('onclick','deleteDetail(\''+id+'\')');
                        document.getElementById('krywnId_'+id).disabled=true;
        }
        delImg.setAttribute('src','images/delete_32.png');

    } else {
        alert('DOM Definition Error');
    }
}
statFrm=0;
function showTmbl()
{
        if(statFrm==0)
        {
                document.getElementById('tombol').innerHTML="<button class=mybutton onclick=frm_aju()>"+nmTmblDone+"</button><button class=mybutton onclick=reset_data()>"+nmTmblCancel+"</button>";
        }
        else if(statFrm==1)
        {
                document.getElementById('tombol').innerHTML="<button class=mybutton onclick=frm_aju()>"+nmTmblDone+"</button>";
        }
}
function cek_data()
{
        //var detKode = document.getElementById('detail_kode');
        kdorg=document.getElementById('kdOrg').value;
        tgl=document.getElementById('tglAbsen').value;

        var detKode = kdorg+"###"+tgl;
        var rkrywn = document.getElementById('krywnId').options[document.getElementById('krywnId').selectedIndex].value;
        var rtpLmbr = document.getElementById('tpLmbr').options[document.getElementById('tpLmbr').selectedIndex].value;
        var rungMkn = document.getElementById('uang_mkn').value;
        var jam=document.getElementById('jam').options[document.getElementById('jam').selectedIndex].value;
        var rungTrans = document.getElementById('uang_trnsprt').value;
        var rungLbhjm = document.getElementById('uang_lbhjm').value;
		pros=document.getElementById('proses').value;
		if(rungLbhjm<=0) {
			alert("Warning: Gaji Pokok belum ada");return;
		}
		
        if(pros!="updateDetail")
        {
        param = "proses=cekData";
        }
        else
        {
                param = "proses=updateDetail";
        }
        param += "&absnId="+detKode;
        param += "&tpLmbr="+rtpLmbr;
        param += "&krywnId="+rkrywn;
        param += "&ungTrans="+rungTrans;
        param += "&ungLbhjm="+rungLbhjm;
        param += "&ungMkn="+rungMkn;
        param += "&Jam="+jam;

        tujuan='sdm_slave_lembur.php';
        //alert(param);
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
                                                        status_inputan=1;
                                                        showTmbl();
                                                        bersihFormDet();
                                                        loadDetail();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 	
}
function bersihFormDet()
{
                document.getElementById('krywnId').value='';
                document.getElementById('krywnId').disabled=false;
                document.getElementById('tpLmbr').value='';
                document.getElementById('uang_mkn').value='0';
                document.getElementById('uang_trnsprt').value='0';
                document.getElementById('uang_lbhjm').value='0';
                document.getElementById('jam').value='';
                document.getElementById('proses').value="";
}
function delDetail(kdorg,tgl,krywn)
{
        kdtmp=kdorg;
        tgltmp=tgl;
        krywnId=krywn;
        absnId=kdtmp+"###"+tgltmp;
        param='absnId='+absnId+'&proses=delDetail'+'&krywnId='+krywnId;
        tujuan='sdm_slave_lembur.php';
        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                        loadDetail();
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }	
        if(confirm("Deleting, are you sure..?"))
        post_response_text(tujuan, param, respog);	
}

function loadData()
{
        param='proses=loadNewData';
        tujuan='sdm_slave_lembur.php';
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
                                                        document.getElementById('contain').innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 	
}
function loadDetail()
{
        tgl=document.getElementById('tglAbsen').value;
        kdrg=document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;
        param='tgl='+tgl+'&kdOrg='+kdrg+'&proses=loadDetail';
        //alert(param);
        tujuan='sdm_slave_detail_lembur.php';
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
                                                        document.getElementById('contentDetail').innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 	

}
function cariBast(num)
{
                param='proses=loadNewData';
                param+='&page='+num;
                tujuan = 'sdm_slave_lembur.php';
                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('contain').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}
function fillField(kdorg,tgl)
{
        document.getElementById('kdOrg').value=kdorg;
        document.getElementById('tglAbsen').value=tgl;
        tmp=kdorg+"###"+tgl;
        param='absnId='+tmp;
        param+="&proses=createTable";
        tujuan='sdm_slave_lembur.php';
        post_response_text(tujuan, param, respon);
                function respon(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        } else {
                                                // Success Response
                                lockForm();
                                document.getElementById('listData').style.display='none';
                                document.getElementById('headher').style.display='block';
                                document.getElementById('detailEntry').style.display='block';
                                var detailDiv = document.getElementById('detailIsi');
                                detailDiv.innerHTML = con.responseText;
                                status_inputan=1;
                                statFrm=1;
                                showTmbl();
                                document.getElementById('tmbLheader').innerHTML='';
                                loadDetail();
                                        }
                                } else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }


}

function delData(kdorg,tgl)
{
        kdtmp=kdorg;
        tgltmp=tgl;
        absnId=kdtmp+"###"+tgltmp;
        param='absnId='+absnId+'&proses=delData';
        tujuan='sdm_slave_lembur.php';

        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                        displayList();
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }	
        if(confirm("Deleteing, are you sure..?"))
        post_response_text(tujuan, param, respog);	
}
function delDataAll(kdorg,tgl)
{
        kdtmp=kdorg;
        tgltmp=tgl;
        absnId=kdtmp+"###"+tgltmp;
        param='absnId='+absnId+'&proses=delData';
        tujuan='sdm_slave_lembur.php';
        post_response_text(tujuan, param, respog);			
        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                        displayList();
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }	


}
function frm_aju()
{

        if(statFrm==0)
        {
                if(confirm("Done, are you sure..?"))
                {
                        displayList();
                }
        }
        else if(statFrm==1)
        {		
                if(confirm("Done, are you sure..?"))
                {
                        displayList();
                }
        }
}
function reset_data()
{
        if(statFrm==0)
        {
                if(confirm("Canceling, are you sure..?"))
                {
                        kdorg=document.getElementById('kdOrg').value;
                        tgl=document.getElementById('tglAbsen').value;
                        delDataAll(kdorg,tgl);
                }
        }

}
function cariData(num)
{

                kdorg=document.getElementById('kdOrgCr').value;
                tgl=document.getElementById('tgl_cari').value;
                id=kdorg+"###"+tgl;
                param='absnId='+id+'&proses=cariAbsn';
                param+='&page='+num;
                tujuan = 'sdm_slave_lembur.php';
                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('listData').style.display='block';
                                                document.getElementById('headher').style.display='none';
                                                document.getElementById('detailEntry').style.display='none';
                                                document.getElementById('contain').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}
function cariAsbn()
{
        kdorg=document.getElementById('kdOrgCr').value;
        tgl=document.getElementById('tgl_cari').value;
        id=kdorg+"###"+tgl;
        param='absnId='+id+'&proses=cariAbsn';
        //alert(param);
        tujuan='sdm_slave_lembur.php';
        post_response_text(tujuan, param, respog);	

        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                        document.getElementById('listData').style.display='block';
                                        document.getElementById('headher').style.display='none';
                                        document.getElementById('detailEntry').style.display='none';
                                        document.getElementById('contain').innerHTML=con.responseText;
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }	
}
function normal_number_1()
{

        satu=document.getElementById('uang_mkn');
        satu.value=remove_comma(satu);
}
function normal_number_2()
{
        dua=document.getElementById('uang_trnsprt');
        dua.value=remove_comma(dua);
}
function normal_number_3()
{
        tiga=document.getElementById('uang_lbhjm');
        tiga.value=remove_comma(tiga);
}
function chngeFormat()
{
        if(document.getElementById('uang_mkn').value!=0)
        { 
                sat=document.getElementById('uang_mkn'); 
                change_number(sat);   
        }
        if(document.getElementById('uang_trnsprt').value!=0)
        { 
                dua=document.getElementById('uang_trnsprt');
                change_number(dua);   
        }
        if(document.getElementById('uang_lbhjm').value!=0)
        {
                tiga=document.getElementById('uang_lbhjm');
                change_number(tiga);  
        }
}
function getLembur(tplmbr,basisjam)
{
        if((tplmbr=='')&&(basisjam==''))
        {
                tipeLembur=document.getElementById('tpLmbr').options[document.getElementById('tpLmbr').selectedIndex].value;
                param='tpLembur='+tipeLembur+'&proses=getBasis';
        }
        else
        {
                tipeLembur=tplmbr;
                bsisJam=basisjam;
                param='tpLembur='+tipeLembur+'&proses=getBasis'+'&basisJam='+bsisJam;
        }
        tujuan='sdm_slave_lembur.php';
        post_response_text(tujuan, param, respog);	
        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                        document.getElementById('jam').innerHTML=con.responseText;
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }	
}
function getUangLem()
{
        basis=document.getElementById('jam').options[document.getElementById('jam').selectedIndex].value;
        idKry=document.getElementById('krywnId').options[document.getElementById('krywnId').selectedIndex].value;
        kodeOrg=document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;
        tpeLmbr=document.getElementById('tpLmbr').options[document.getElementById('tpLmbr').selectedIndex].value;
        tanggal=document.getElementById('tglAbsen').value;
        tahun=tanggal.substr(6, 4);
        param='basisJam='+basis+'&proses=getUang'+'&krywnId='+idKry+'&kodeOrg='+kodeOrg+'&tpLmbr='+tpeLmbr+'&tahun='+tahun;
        tujuan='sdm_slave_lembur.php';
        post_response_text(tujuan, param, respog);	
        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                    document.getElementById('uang_lbhjm').value=con.responseText;
									if(con.responseText==0) {
										alert("Warning: Gaji Pokok belum ada");
									}
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }	
}
function getKary(title,pil,ev){
         
        kdOrge=document.getElementById('kdOrg');
        kdOrge=kdOrge.options[kdOrge.selectedIndex].value;
        tgl=document.getElementById('tglAbsen').value;
        content= "<div style='width:100%;'>";
        content+="<fieldset>"+title+"<input type=hidden id=kdOrg value="+kdOrge+" /><input type=hidden id=tgldr value="+tgl+" /><input type=text id=txtnamabarang class=myinputtext size=25 maxlength=35><button class=mybutton onclick=goCariKary("+pil+")>Go</button> </fieldset>";
        content+="<div id=containercari style='overflow:scroll;height:300px;width:520px'></div></div>";    
       //display window
       width='550';
       height='350';
       showDialog1(title,content,width,height,ev);		
}
function goCariKary(pil){
        kdOrg2=document.getElementById('kdOrg').value;
        nmdr=document.getElementById('txtnamabarang').value;
        tgl=document.getElementById('tgldr').value;
        param='unit='+kdOrg2+'&pil='+pil+'&nmkary='+nmdr+'&tanggalcr='+tgl;
        if(pil==1){
            param+='&proses=getKary';
        }
    tujuan = 'sdm_slave_absen_detail.php';
    post_response_text(tujuan, param, respog);				
    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                                    document.getElementById('containercari').innerHTML=con.responseText;
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }	
}
function setKary(karyid){
      kar=document.getElementById('krywnId');
      for(x=0;x<kar.length;x++){
        if(kar.options[x].value==karyid){
                kar.options[x].selected=true;
        }
      }
      closeDialog();
}