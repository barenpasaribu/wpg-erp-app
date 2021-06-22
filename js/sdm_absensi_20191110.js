// JavaScript Document
function add_new_data()
{

            //alert(con.responseText);
                    document.getElementById('headher').style.display="block";
                    document.getElementById('listData').style.display="none";
                    document.getElementById('detailEntry').style.display="none";
                    document.getElementById('tmbLheader').innerHTML='<button class=mybutton id=dtlAbn onclick=detailAbsn()>'+nmTmblSave+'</button><button class=mybutton id=cancelAbn onclick=cancelAbsn()>'+nmTmblCancel+'</button>';
                    document.getElementById('tombol').innerHTML='';
                    document.getElementById('contentDetail').innerHTML='';
                    statFrm=0;
                    status_inputan=0;
                    unlockForm();	



}
function cancelAbsn()
{
        displayList();
}
function displayList()
{
        document.getElementById('listData').style.display='block';
        document.getElementById('headher').style.display='none';
        document.getElementById('detailEntry').style.display='none';
        document.getElementById('kdOrgCari').value='';
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
                tujuan='sdm_slave_absensi.php';
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
                tujuan='sdm_slave_absensi.php';
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
        period=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
        tgl=document.getElementById('tglAbsen').value;
        if((kdorg=='')&&(tgl==''))
        {
                alert("Please complete the Form");
                return;
        }

        id=kdorg+"###"+tgl;
        //alert(hsl);
        //return;
        //alert(notran);
        tujuan='sdm_slave_absensi.php';
        param='absnId='+id+'&proses=cekHeader'+'&period='+period;
       // alert(param);
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
        //alert(hsl);
        //return;
        //alert(notran);
        param='absnId='+id;
        param+="&proses=createTable";
        //alert(param);
        tujuan='sdm_slave_absen_detail.php';
        function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                                        //alert(con.responseText);
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
        document.getElementById('periode').disabled=true;
}
function unlockForm()
{
        document.getElementById('kdOrg').disabled=false;
        document.getElementById('tglAbsen').disabled=false;
        document.getElementById('periode').disabled=false;
        document.getElementById('kdOrg').value='';
        document.getElementById('tglAbsen').value='';
        document.getElementById('periode').value='';
}
status_inputan=0;
function addDetail() {

        crt=document.getElementById('proses');
//	alert(crt.value);
        kdorg=document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;
        tgl=document.getElementById('tglAbsen').value;

        var detKode = kdorg+"###"+tgl;
        var period=document.getElementById('periode').value;
    var rkrywn = document.getElementById('krywnId');
    var rshft = document.getElementById('shiftId');
    var rasbnsi = document.getElementById('absniId');
        var rjm = document.getElementById('jmId');
        var rmnt = document.getElementById('mntId');
        var jam=rjm.value+":"+rmnt.value;
        var rket = document.getElementById('ktrng');
        //var catu = document.getElementById('catu').options[document.getElementById('catu').selectedIndex].value;
                
              var dendakehadiran = document.getElementById('dendakehadiran').value;
              if(dendakehadiran=='')
                  dendakehadiran=0;
        //addSession();
        //var id_user = trim(document.getElementById('user_id').value);
        if(status_inputan==0)
        {
                if(confirm('Add detail, are you sure..?'))
                {
                        cek_data();	
                }
        }
        else
        {
        //alert('test');
                cek_data();
        }


}
function editDetail(krywnId, shft, absn, jm,jm2, ket,penalty,premi,instif)
{
        document.getElementById('krywnId').disabled=true;
        ct=document.getElementById('krywnId');
        for(x=0;x<ct.length;x++)
        {
                if(ct.options[x].value==krywnId)
                {
                        ct.options[x].selected=true;
                }
        }
        //document.getElementById('krywnId').value=krywnId;
        document.getElementById('shiftId').value=shft;
        document.getElementById('absniId').value=absn;
        document.getElementById('dendakehadiran').value=penalty;
        document.getElementById('premiInsentif').value=premi;
        //document.getElementById('premi').value=premi;
        document.getElementById('insentif').value=instif;

//        ct3=document.getElementById('premiPil');
//        for(x55=0;x55<ct3.length;x55++)
//        {
//                if(ct3.options[x55].value==prm)
//                {
//                        ct3.options[x55].selected=true;
//                }
//        }
        jam=jm.split(':');
        jam2=jm2.split(':');
        /*alert(jam[0]);
        return;*/
        document.getElementById('jmId').value=jam[0];
        document.getElementById('mntId').value=jam[1];
        document.getElementById('jmId2').value=jam2[0];
        document.getElementById('mntId2').value=jam2[1];
        document.getElementById('ktrng').value=ket;
        document.getElementById('proses').value='updateData';
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
                tujuan='sdm_slave_absen_detail.php';
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
                newRow.innerHTML += "<td><select id='krywnId_"+numRow+"' type='text' style='width:150px' />"+optIsi+"</select></td><td>"+"<input id='shiftId_"+numRow+"' type='text' class='myinputtext' value='' onkeypress='return tanpa_kutip(event)' style='width:120px' /></td><td><select id='absniId_"+numRow+"' type='text' style='width:100px' />"+optAbsn+"</select></td>"+"<td><select id='jmId_"+numRow+"' type='text' />"+optJm+"</select>:<select id='mntId_"+numRow+"' type='text' />"+optMnt+"</select>"+"<td>"+"<input id='ktrng_"+numRow+"' type='text' class='myinputtext' style='width:150px' value='' onkeypress='return tanpa_kutip(event)' /></td>"+"<td><img id='detail_add_"+numRow+"' title='Tambah' class=zImgBtn onclick=\"addDetail('"+numRow+"')\" src='images/save.png'/>"+"&nbsp;<img id='detail_delete_"+numRow+"' />"+"&nbsp;<img id='detail_pass_"+numRow+"' />"+"</td>";
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
function showTmbl()
{
        pros=document.getElementById('proses').value;
        if(pros!='updateData')
        {
document.getElementById('tombol').innerHTML="<button class=mybutton onclick=frm_aju()>"+nmTmblDone+"</button><button class=mybutton onclick=reset_data()>"+nmTmblCancel+"</button>";
        }
        else
        {
        document.getElementById('tombol').innerHTML="<button class=mybutton onclick=frm_aju()>"+nmTmblDone+"</button>";
        }
}
function cek_data()
{
        //var detKode = document.getElementById('detail_kode');
        kdorg=document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;
        tgl=document.getElementById('tglAbsen').value;

        var detKode = kdorg+"###"+tgl;
        var period=document.getElementById('periode').value;
        var rkrywn = document.getElementById('krywnId').options[document.getElementById('krywnId').selectedIndex].value;
        var rshft = document.getElementById('shiftId');
        var rasbnsi = document.getElementById('absniId');
        var rjm = document.getElementById('jmId').options[document.getElementById('jmId').selectedIndex].value;
        var rmnt = document.getElementById('mntId').options[document.getElementById('mntId').selectedIndex].value;
        var jam=rjm+":"+rmnt;
        var rjm2 = document.getElementById('jmId2').options[document.getElementById('jmId2').selectedIndex].value;
        var rmnt2 = document.getElementById('mntId2').options[document.getElementById('mntId2').selectedIndex].value;
        var jam2=rjm2+":"+rmnt2;
        var rket = document.getElementById('ktrng');
                //var catu = document.getElementById('catu').options[document.getElementById('catu').selectedIndex].value;       
        premidt = document.getElementById('premiInsentif').value;
        var ins = document.getElementById('insentif').value;
              //var prm = document.getElementById('premi').value;
              var period=document.getElementById('periode').value;
              var dendakehadiran=document.getElementById('dendakehadiran').value;
              if(dendakehadiran=='')
                  dendakehadiran=0;
              
        pros=document.getElementById('proses').value;
        if(pros!='updateData')
        {
                param = "proses=cekData";
        }
        else
        {
                param = "proses="+pros;
        }
        //alert(param);
    //param = "proses=cekData";
        param += "&absnId="+detKode;
        param += "&krywnId="+rkrywn;
        param += "&shifTid="+rshft.value;
        param += "&asbensiId="+rasbnsi.value;
        param += "&Jam="+jam;
        param += "&Jam2="+jam2
        param += "&period="+period;
        param += "&ket="+rket.value;
        //param +="&catu="+catu;"&premi="+prm+"&insentif="+ins+
        param+='&premidt='+premidt+"&insentif="+ins;
        param+="&dendakehadiran="+dendakehadiran;

        tujuan='sdm_slave_absensi.php';
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
                                                                bersihFormDetail();
                                                                showTmbl();
                                                                loadDetail();
                                                                status_inputan=1;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 	
}
function bersihFormDetail()
{
        document.getElementById('krywnId').value='';
        document.getElementById('krywnId').disabled=false;
        document.getElementById('shiftId').value='';
        document.getElementById('absniId').value='';
        document.getElementById('ktrng').value='';
        document.getElementById('proses').value='insert';
//        document.getElementById('jmId').value='00';
//        document.getElementById('mntId').value='00';
//        document.getElementById('jmId2').value='00';
//        document.getElementById('mntId2').value='00';
        document.getElementById('premiInsentif').value='';
        document.getElementById('insentif').value='';
        document.getElementById('dendakehadiran').value='0';
}
 
function loadDetail()
{
        kdOrg=document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;
        tglAbsn=document.getElementById('tglAbsen').value;
        tujuan='sdm_slave_absen_detail.php';
        param='kdOrg='+kdOrg+'&tgAbsn='+tglAbsn+'&proses=loadDetail';
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
function loadData()
{
        param='proses=loadNewData';
        tujuan='sdm_slave_absensi.php';
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
                                                        //alert(con.responseText);
                                                        //return;
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
function cariBast(num)
{
                param='proses=loadNewData';
                param+='&page='+num;
                tujuan = 'sdm_slave_absensi.php';
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
function fillField(kdorg,tgl,period)
{
        tmp=kdorg+"###"+tgl;
        document.getElementById('kdOrg').value=kdorg;
        document.getElementById('tglAbsen').value=tgl;
        document.getElementById('periode').value=period;
        param='absnId='+tmp;
        param+="&proses=createTable";
        tujuan='sdm_slave_absen_detail.php';
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

                                //alert(con.responseText);
                                document.getElementById('listData').style.display='none';
                                document.getElementById('headher').style.display='block';
                                document.getElementById('detailEntry').style.display='block';
                                var detailDiv = document.getElementById('detailIsi');
                                detailDiv.innerHTML = con.responseText;
                                status_inputan=1;
                                statFrm=1;
                                showTmbl();
                                loadDetail();
                                document.getElementById('tmbLheader').innerHTML='';
                                        }
                                } else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }


}

function delDataIn(kdorg,tgl)
{
        kdtmp=kdorg;
        tgltmp=tgl;
        absnId=kdtmp+"###"+tgltmp;
        param='absnId='+absnId+'&proses=delData';
        tujuan='sdm_slave_absensi.php';
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
function delDetail(kdorg,tgl,krynid)
{
        kdtmp=kdorg;
        tgltmp=tgl;
        absnId=kdtmp+"###"+tgltmp;
        krywnId=krynid;
        param='absnId='+absnId+'&proses=delDetail'+'&krywnId='+krywnId;
        tujuan='sdm_slave_absensi.php';

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
function delData(kdorg,tgl)
{
        kdtmp=kdorg;
        tgltmp=tgl;
        absnId=kdtmp+"###"+tgltmp;
        param='absnId='+absnId+'&proses=delData';
        tujuan='sdm_slave_absensi.php';

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
        if(confirm("Deleting, are you sure..?"))
        post_response_text(tujuan, param, respog);	
}
statFrm=0;
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
                        delDataIn(kdorg,tgl);
                }
        }
        else if(statFrm==1)
        {
                displayList();
        }

}
function cariAsbn()
{
        kdorg=document.getElementById('kdOrgCari').value;
        tgl=document.getElementById('tgl_cari').value;

        id=kdorg+"###"+tgl;
        param='absnId='+id+'&proses=cariAbsn';
        tujuan='sdm_slave_absensi.php';
        post_response_text(tujuan, param, respog);	
        //alert(param);
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
function getPremiTetap(){
        
        prd=document.getElementById('tglAbsen').value;
        karyId=document.getElementById('krywnId').options[document.getElementById('krywnId').selectedIndex].value;
        var rjm = document.getElementById('jmId').options[document.getElementById('jmId').selectedIndex].value;
        var rmnt = document.getElementById('mntId').options[document.getElementById('mntId').selectedIndex].value;
        var jam=rjm+":"+rmnt;
        var rjm2 = document.getElementById('jmId2').options[document.getElementById('jmId2').selectedIndex].value;
        var rmnt2 = document.getElementById('mntId2').options[document.getElementById('mntId2').selectedIndex].value;
        var jam2=rjm2+":"+rmnt2;
        kdsb=document.getElementById('absniId');
        kdsb=kdsb.options[kdsb.selectedIndex].value;
        param='absnId='+kdsb+'&proses=getPremi'+'&jmMulai='+jam;
        param+='&jamPlg='+jam2+'&tglDt='+prd+'&karyId='+karyId;
        tujuan='sdm_slave_absensi.php';
        
        post_response_text(tujuan, param, respog);
        //alert(param);
        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                    document.getElementById('insentif').value="";
                                    document.getElementById('insentif').value=con.responseText;
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }	
 
}

function saveHariLibur()
{
    jnlibur=document.getElementById('jlibur').options[document.getElementById('jlibur').selectedIndex].value;
    tgllibur=document.getElementById('tgllibur').value;
    param='jnlibur='+jnlibur+'&tgllibur='+tgllibur;
    tujuan='sdm_slave_absenLibur.php';
    if(confirm('Are you sure..?')){
         post_response_text(tujuan, param, respog);
        //alert(param);       
    }
       function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                         alert('Done');
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
      //getPremiTetap();
      closeDialog();
}