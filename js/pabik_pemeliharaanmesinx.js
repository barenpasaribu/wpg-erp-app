// JavaScript Document
function add_new_data()
{
        bersih_form();
        status_inputan=0;
        //document.getElementById('proses').value='insert';
        param='proses=generateNo';
        tujuan='pabrikPemeliharaanmesin_slave.php';
          function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                                                document.getElementById('list_ganti').style.display='none';
                                                document.getElementById('headher').style.display='block';
                                                document.getElementById('detail_ganti').style.display='none';
                                                document.getElementById('tmblHeader').innerHTML="<button class=mybutton id=dtlForm onclick=detailForm()>"+nmSaveHeader+"</button><button class=mybutton id=cancelForm onclick=cancelForm()>"+nmCancelHeader+"</button>";
                                                //document.getElementById('trans_no').value = con.responseText;


                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
        post_response_text(tujuan, param, respon);
        //document.getElementById('detail_ganti').style.display='';
}
function bersih_form()
{
        document.getElementById('pbrkId').value='';
        document.getElementById('shitId').innerHTML="<option value=''></options>";
        document.getElementById('statId').innerHTML="<option value=''></options>";
        document.getElementById('msnId').innerHTML="<option value=''></options>";
        document.getElementById('tmblDetail').innerHTML='';
        document.getElementById('tglCek').value='';
        document.getElementById('jmAwal').value='';
        document.getElementById('jmAkhir').value='';
        document.getElementById('kegtn').value='';
        document.getElementById('ketr').value='';
        document.getElementById('jamMulai').value='00';
        document.getElementById('mntMulai').value='00';
        document.getElementById('jamSlsi').value='00';
        document.getElementById('mntSlsi').value='00';
        document.getElementById('pbrkId').disabled=false;
        document.getElementById('statId').disabled=false;
        document.getElementById('msnId').disabled=false;
        document.getElementById('tglCek').disabled=false;
        document.getElementById('jmAwal').disabled=false;
        document.getElementById('jmAkhir').disabled=false;
        document.getElementById('shitId').disabled=false;
        document.getElementById('kegtn').disabled=false;
        document.getElementById('ketr').disabled=false;
}
function getStation(shft,stat,msnId)
{
        if((shft!=0)||(stat!=0)||(msnId!=0))
        {
                kdOrg=document.getElementById('pbrkId').options[document.getElementById('pbrkId').selectedIndex].value;
                sht=shft;
                statid=stat;
                param='kdOrg='+kdOrg+'&proses=GetStat'+'&shft='+sht+'&statid='+statid;
        }
        else
        {
                kdOrg=document.getElementById('pbrkId').value;
                param='kdOrg='+kdOrg+'&proses=GetStat';
        }
        //alert(param);
        tujuan='pabrikPemeliharaanmesin_slave.php';
        post_response_text(tujuan, param, respon);
        function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Respons
                                                //document.getElementById('trans_no').value = con.responseText;
                                                as=con.responseText.split("###");
                                                document.getElementById('statId').innerHTML=as[0];
                                                document.getElementById('shitId').innerHTML=as[1];
                                                getMesin(stat,msnId);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

}
function getMesin(stat,msnId)
{
        if(stat!=0||msnId!=0)
        {
                statId=stat;
                mesinId=msnId;
                param='statId='+statId+'&proses=GetMsn'+'&mesinId='+mesinId;
        }
        else
        {
                statId=document.getElementById('statId').value;
                param='statId='+statId+'&proses=GetMsn';
        }

        //alert(param);
        tujuan='pabrikPemeliharaanmesin_slave.php';
        post_response_text(tujuan, param, respon);
        function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Respons
                                                //document.getElementById('trans_no').value = con.responseText;
                                                document.getElementById('msnId').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

}
function getNoTrans(jwbn)
{
        kdOrg=document.getElementById('pbrkId').value;
        statId=document.getElementById('statId').value;
        kegtn=document.getElementById('kegtn').value;
        tgl=document.getElementById('tglCek').value;
        jmawal=document.getElementById('jmAwal').value;
        jmakhir=document.getElementById('jmAkhir').value;
        jamMulai=document.getElementById('jamMulai').options[document.getElementById('jamMulai').selectedIndex].value;
        mntMulai=document.getElementById('mntMulai').options[document.getElementById('mntMulai').selectedIndex].value;
        jamSlsi=document.getElementById('jamSlsi').options[document.getElementById('jamSlsi').selectedIndex].value;
        mntSlsi=document.getElementById('mntSlsi').options[document.getElementById('mntSlsi').selectedIndex].value;
        ketr=document.getElementById('ketr').value;

        if((kdOrg=='')||(statId=='')||(kegtn=='')||(tgl=='')||(jmawal=='')||(jmakhir==''))
        {
                alert("All field are requied");
                return;
        }
        param='statId='+statId+'&proses=CreateNo'+'&jmAwal='+jmawal+'&jmAkhir='+jmakhir;
        param+='&jamMulai='+jamMulai+'&mntMulai='+mntMulai+'&jamSlsi='+jamSlsi+'&mntSlsi='+mntSlsi;
        //alert(param);
        tujuan='pabrikPemeliharaanmesin_slave.php';
        post_response_text(tujuan, param, respon);
        function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Respons
                                        //alert(con.responseText);
                                                //document.getElementById('trans_no').value = con.responseText;
                                                if(jwbn==0)
                                                {
                                                        document.getElementById('trans_no').value=con.responseText;
                                                        add_detail();
                                                        document.getElementById('tmblHeader').innerHTML='';
                                                        lockForm();	
                                                }
                                                else
                                                {
                                                        document.getElementById('trans_no').value=con.responseText;
                                                        saveHeader();
                                                }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function lockForm()
{
        document.getElementById('pbrkId').disabled=true;
        document.getElementById('statId').disabled=true;
        document.getElementById('msnId').disabled=true;
        document.getElementById('tglCek').disabled=true;
        document.getElementById('jmAwal').disabled=true;
        document.getElementById('jmAkhir').disabled=true;
        document.getElementById('shitId').disabled=true;
        document.getElementById('kegtn').disabled=true;
        document.getElementById('ketr').disabled=true;
}
function add_detail()
{
        notran=document.getElementById('trans_no').value;
        //alert(notran);
        param='noTrans='+notran;
        param+="&proses=createTable";
        //alert(param);
        tujuan='pabrikPemeliharaanMesin_detail.php';
        function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                                        //alert(con.responseText);
                                        document.getElementById('list_ganti').style.display='none';
                                        document.getElementById('headher').style.display='block';
                                        document.getElementById('detail_ganti').style.display='block';
                                        document.getElementById('detail_isi').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
        post_response_text(tujuan, param, respon);
}
function detailForm()
{

        if(confirm("Additional material usage? \nPress Cancel if no material usage \nPress Ok to add material usage"))
        {	
                //alert("masuk save header");
                getNoTrans(0);
        }
        else
        {
                //alert("masuk save header");
                getNoTrans(1);
        }

}
function saveHeader()
{
        notrans=document.getElementById('trans_no').value;
        pbrkId=document.getElementById('pbrkId').value;
        shft=document.getElementById('shitId').value;
        statid=document.getElementById('statId').value;
        mesinId=document.getElementById('msnId').value;
        tgl=document.getElementById('tglCek').value;
        jmAwal=document.getElementById('jmAkhir').value;
        jmAkhir=document.getElementById('jmAkhir').value;
        jmMulai=document.getElementById('jamMulai').options[document.getElementById('jamMulai').selectedIndex].value;
        mtMulai=document.getElementById('mntMulai').options[document.getElementById('mntMulai').selectedIndex].value;
        jmSlsi=document.getElementById('jamSlsi').options[document.getElementById('jamSlsi').selectedIndex].value;
        mtSlsi=document.getElementById('mntSlsi').options[document.getElementById('mntSlsi').selectedIndex].value;
        kegtn=document.getElementById('kegtn').value;
        ketr=document.getElementById('ketr').value;
        //pros=document.getElementById('proses').value;

        param = "proses=saveHeader";
        param += "&noTrans="+notrans;
        param += "&pbrkId="+pbrkId;
        param += "&shft="+shft;
        param += "&statid="+statid;
        param += "&mesinId="+mesinId;
        param += "&tgl="+tgl;
        param += "&jmAwal="+jmAwal;
        param += "&jmAkhir="+jmAkhir;
        param += "&kgtn="+kegtn;
        param += "&jamMulai="+jmMulai;
        param += "&mntMulai="+mtMulai;
        param += "&jamSlsi="+jmSlsi;
        param += "&mntSlsi="+mtSlsi;
        param += "&ketr="+ketr;
        tujuan='pabrikPemeliharaanmesin_slave.php';
        //alert(param);
//	return;
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
                                                        alert(con.responseText);
                                                        //return;				
                                                        //document.getElementById('contain').innerHTML=con.responseText;
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
status_inputan=0;
function cekData(id)
{
        //var detKode = document.getElementById('detail_kode');
    var kdbrg = document.getElementById('kd_brg_'+id);
    var satuan = document.getElementById('sat_'+id);
    var jmlhMinta = document.getElementById('jmlh_'+id);
        var ketrngn = document.getElementById('ket_'+id);

        //detail Data
        notrans=document.getElementById('trans_no').value;
        pbrkId=document.getElementById('pbrkId').value;
        shft=document.getElementById('shitId').value;
        statid=document.getElementById('statId').value;
        mesinId=document.getElementById('msnId').value;
        tgl=document.getElementById('tglCek').value;
        jmAwal=document.getElementById('jmAkhir').value;
        jmAkhir=document.getElementById('jmAkhir').value;
        kegtn=document.getElementById('kegtn').value;
        ketr=document.getElementById('ketr').value;
        //var id_user = trim(document.getElementById('user_id').value);
        param = "proses=cekData";
        param += "&noTrans="+notrans;
        param += "&pbrkId="+pbrkId;
        param += "&shft="+shft;
        param += "&statid="+statid;
        param += "&mesinId="+mesinId;
        param += "&tgl="+tgl;
        param += "&jmAwal="+jmAwal;
        param += "&jmAkhir="+jmAkhir;
        param += "&kdbrg="+kdbrg.value;
        param += "&satuan="+satuan.value;
        param += "&jmlhMinta="+jmlhMinta.value;
        param += "&ketrngn="+ketrngn.value;
        param += "&kegtn="+kegtn.value;
        param += "&ketr="+ketr.value;
        tujuan='pabrikPemeliharaanmesin_slave.php';
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
                                                        //alert(con.responseText);
                                                        //return;
                                                        var id=con.responseText;
                                                        id=id-1;
                                                        switchEditAdd(id,'detail');
                                                        addNewRow('detailBody',true);
                                                        status_inputan=1;
                                                        showTmbl();
                                                        //document.getElementById('contain').innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 	
}
function showTmbl()
{
        document.getElementById('tmblDetail').innerHTML="<button class=mybutton onclick=frm_aju()>"+nmDetialDone+"</button><button class=mybutton onclick=reset_data()>"+nmDetailCancel+"</button>";
}
function addDetail(id) {

        crt=document.getElementById('proses');
//	alert(crt.value);
        var detKode = document.getElementById('detail_kode');
    var rkd_brg = document.getElementById('kd_brg_'+id);
    var rsatuan = document.getElementById('sat_'+id);
    var rjmlhDiminta = document.getElementById('jmlh_'+id);
        var rket = document.getElementById('ket_'+id);

        //var id_user = trim(document.getElementById('user_id').value);
        if(status_inputan==0)
        {
                if(confirm('Are you sure ?'))
                {
                        cekData(id);
                }
        }
        else
        {
        //alert('test');
                notrans=document.getElementById('trans_no').value;
                pbrkId=document.getElementById('pbrkId').value;
                shft=document.getElementById('shitId').value;
                statid=document.getElementById('statId').value;
                mesinId=document.getElementById('msnId').value;
                tgl=document.getElementById('tglCek').value;
                jmAwal=document.getElementById('jmAkhir').value;
                jmAkhir=document.getElementById('jmAkhir').value;
                        param = "proses=detail_add";
                        param += "&kd_brg="+rkd_brg.value;
                        param += "&satuan="+rsatuan.value;
                        param += "&jmlh="+rjmlhDiminta.value;
                        param += "&ket="+rket.value;
                        param += "&noTrans="+detKode.value;
                        param += "&pbrkId="+pbrkId;
                        param += "&shft="+shft;
                        param += "&statid="+statid;
                        param += "&mesinId="+mesinId;
                        param += "&tgl="+tgl;
                        param += "&jmAwal="+jmAwal;
                        param += "&jmAkhir="+jmAkhir;
                        //param += "&user_id="+id_user;
                        tujuan='pabrikPemeliharaanMesin_detail.php';

                function respon(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        } else {
                                                // Success Response
                                           //alert(con.responseText);
                                           ar=document.getElementById('kd_brg_'+id).value;
                                           document.getElementById('skd_brg_'+id).value=ar;
                                           switchEditAdd(id,'detail');
                                           addNewRow('detailBody',true);
                                        }
                                } else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }

                post_response_text(tujuan, param, respon);
        }

}
/* Function editDetail(id,primField,primVal)
 * Fungsi untuk mengubah data Detail
 * I : id row (urutan row pada table Detail)
 * P : Mengubah data pada tabel Detail
 * O : Notifikasi data telah berubah
 */
function editDetail(id) {
//	alert('test');
    var detKode = document.getElementById('detail_kode');
    var rkd_brg = document.getElementById('kd_brg_'+id);
        var skd_brg = document.getElementById('skd_brg_'+id);
    var rsatuan = document.getElementById('sat_'+id);
    var rjmlhDiminta = document.getElementById('jmlh_'+id);
    var rket = document.getElementById('ket_'+id);

    param = "proses=detail_edit";
    param += "&noTrans="+detKode.value;
    param += "&kd_brg="+rkd_brg.value;
        param += "&dkd_brg="+skd_brg.value;
    param += "&satuan="+rsatuan.value;
    param += "&jmlh="+rjmlhDiminta.value;
    param += "&ket="+rket.value;
   //	alert(param);
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    alert('Done');
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

    post_response_text('pabrikPemeliharaanMesin_detail.php', param, respon);
}

/* Function deleteDelete(id)
 * Fungsi untuk menghapus data Detail
 * I : id row (urutan row pada table Detail)
 * P : Menghapus data pada tabel Detail
 * O : Menghapus baris pada tabel Detail
 */
function deleteDetail(id) {
    var detKode = document.getElementById('detail_kode');
        var rkd_brg = document.getElementById('kd_brg_'+id);


                param = "proses=detail_delete";
                param += "&noTrans="+detKode.value;
                param += "&kd_brg="+rkd_brg.value;
                //alert(param);

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
                        if(confirm('Are you sure ?'))
                        {
                                post_response_text('pabrikPemeliharaanMesin_detail.php', param, respon);	
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
                newRow.innerHTML += "<td><input id='kd_brg_"+numRow+"' type='text' class='myinputtext' style='width:120px' disabled='disabled' value='' /><input type=hidden id=skd_brg_"+numRow+" name=skd_brg_"+numRow+" /></td><td>"+
            "<input id='nm_brg_"+numRow+"' type='text' class='myinputtext' style='width:120px' disabled='disabled' value='' /></td><td><input id='sat_"+numRow+
        "' type='text' class='myinputtext' style='width:70px'disabled='disabled' value='' /><img src=images/search.png class=dellicon title='"+jdl_ats_0+"' onclick=\"searchBrg('"+jdl_ats_1+"','"+content_0+"<input id=nomor type=hidden value="+numRow+" />',event)\";></td>"+"<td><input id='jmlh_"+numRow+"' type='text' class='myinputtextnumber' onkeypress='return angka_doang(event)' style='width:70px' value='' />"+"<td><input id='ket_"+numRow+"' type='text' class='myinputtext' style='width:130px' value='' onkeypress='return tanpa_kutip(event)' />"+"<td><img id='detail_add_"+numRow+
        "' title='Tambah' class=zImgBtn onclick=\"addDetail('"+numRow+"')\" src='images/save.png'/>"+
        "&nbsp;<img id='detail_delete_"+numRow+"' />"+
        "&nbsp;<img id='detail_pass_"+numRow+"' />"+
        "</td>";

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
        }
        delImg.setAttribute('src','images/delete_32.png');

    } else {
        alert('DOM Definition Error');
    }
}
function searchBrg(title,content,ev)
{
        width='500';
        height='400';
        showDialog1(title,content,width,height,ev);
}

function findBrg()
{
        txt=trim(document.getElementById('no_brg').value);
        if(txt=='')
        {
                alert('Text is obligatory');
        }
        else if(txt.length<3)
        {
                alert('Min 3 char');
        }
        else
        {
                param='txtcari='+txt+'&proses=cari_barang';
                tujuan='pabrikPemeliharaanmesin_slave.php';
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

function throwThisRow(no_brg,namabrg,satuan,nomor)
{
         nomor=document.getElementById('nomor').value;
     document.getElementById('kd_brg_'+nomor).value=no_brg;
         document.getElementById('nm_brg_'+nomor).value=namabrg;
         document.getElementById('sat_'+nomor).value=satuan;
         closeDialog();
}
function displayList()
{
        document.getElementById('list_ganti').style.display='block';
        document.getElementById('headher').style.display='none';
        document.getElementById('detail_ganti').style.display='none';
        document.getElementById('txtsearch').value='';
        document.getElementById('tgl_cari').value='';
        loadNData();
}
function loadNData()
{
        param='proses=loadData';
        tujuan='pabrikPemeliharaanmesin_slave.php';
        function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                                        //alert(con.responseText);
                                        document.getElementById('contain').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
        post_response_text(tujuan, param, respon);
}
function cariBast(num)
{
                param='proses=loadData';
                param+='&page='+num;
                tujuan = 'pabrikPemeliharaanmesin_slave.php';
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
function fillField(notrans,tanggal,pbrik,shft,station,stengine,kegtn,tglaw,tglslsi,jmmlai,mntmlai,jamslsi,mntslsi,ketr) 
{

                document.getElementById('pbrkId').disabled=true;
                document.getElementById('statId').disabled=true;
                document.getElementById('msnId').disabled=true;
                document.getElementById('shitId').disabled=true;

                document.getElementById('trans_no').value=notrans;
                document.getElementById('tglCek').value=tanggal;
                document.getElementById('pbrkId').value=pbrik;
                document.getElementById('kegtn').value=kgtn;
                //document.getElementById('shitId').value=shft;

                document.getElementById('jmAwal').value=tglaw;
                document.getElementById('jmAkhir').value=tglslsi;
                document.getElementById('jamMulai').value=jmmlai;
                document.getElementById('mntMulai').value=mntmlai;
                document.getElementById('jamSlsi').value=jamslsi;
                document.getElementById('mntSlsi').value=mntslsi;
                document.getElementById('list_ganti').style.display='none';
                document.getElementById('headher').style.display='block';
                document.getElementById('detail_ganti').style.display='block';
                document.getElementById('ketr').value=ketr;
                showTmbl();
                status_inputan=1;
                stat_reset=1;
                var notrans = notrans;
                param = "noTrans="+notrans;
                param += "&proses=createTable";
                post_response_text('pabrikPemeliharaanMesin_detail.php', param, respon);
                function respon(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        } else {
                                                // Success Response
                                document.getElementById('tmblHeader').innerHTML='';
                                document.getElementById('proses').value='update';
                                var detailDiv = document.getElementById('detail_isi');
                                detailDiv.innerHTML = con.responseText;
                                getStation(shft,station,stengine);
                                        }
                                } else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }
}
function cariTransaksi()
{
        txtSearch=document.getElementById('txtsearch').value;
        txtTgl=document.getElementById('tgl_cari').value;

        param='txtSearch='+txtSearch+'&txtTgl='+txtTgl+'&proses=cariTransaksi';
        //alert(param);
        tujuan='pabrikPemeliharaanmesin_slave.php';
        post_response_text(tujuan, param, respog);			
        function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('list_ganti').style.display='block';
                                                document.getElementById('headher').style.display='none';
                                                document.getElementById('detail_ganti').style.display='none';
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
function dataKePDF(notrans,ev)
{
        noTrans	= notrans;
        tujuan='vhc_DetailPenggantianKomponen_pdf.php';
        judul= noTrans;		
        param='noTrans='+noTrans;
        //alert(param);
        printFile(param,tujuan,judul,ev)		
}
function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}
function delData(notrans)
{
        noTrans=notrans;
        param='noTrans='+noTrans+'&proses=deletData';
        tujuan='pabrikPemeliharaanmesin_slave.php';

        function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                        displayList()
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	

        if(confirm("Delete, Are you sure?"))
        {
                post_response_text(tujuan, param, respog);
        }

}
function upDate()
{
        //alert("masuk");
        noTrans=document.getElementById('trans_no').value;
        tglSp=document.getElementById('tglCek').value;
        jmAwal=document.getElementById('jmAwal').value;
        jmAkhir=document.getElementById('jmAkhir').value;
        kegtn=document.getElementById('kegtn').value;
        ketr=document.getElementById('ketr').value;
        jmMulai=document.getElementById('jamMulai').options[document.getElementById('jamMulai').selectedIndex].value;
        mtMulai=document.getElementById('mntMulai').options[document.getElementById('mntMulai').selectedIndex].value;
        jmSlsi=document.getElementById('jamSlsi').options[document.getElementById('jamSlsi').selectedIndex].value;
        mtSlsi=document.getElementById('mntSlsi').options[document.getElementById('mntSlsi').selectedIndex].value;
        param='noTrans='+noTrans+'&proses=upDate'+'&tgl='+tglSp+'&jmAwal='+jmAwal+'&jmAkhir='+jmAkhir+'&kegtn='+kegtn;
        param += "&jamMulai="+jmMulai;
        param += "&mntMulai="+mtMulai;
        param += "&jamSlsi="+jmSlsi;
        param += "&mntSlsi="+mtSlsi;
        param += "&ketr="+ketr;
        //alert(param);

        tujuan = 'pabrikPemeliharaanmesin_slave.php';
        post_response_text(tujuan, param, respog);			
        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                        //document.getElementById('contain').innerHTML=con.responseText;
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
        if(confirm("Are you sure?"))
        {
                if(status_inputan==1)
                {
                        tglSp=document.getElementById('tglCek').value;
                        jmAwal=document.getElementById('jmAwal').value;
                        jmAkhir=document.getElementById('jmAkhir').value;
                        if((tglSp!='')||(jmAwal!='')||(jmAwal!=''))
                        {
                                upDate();
                        }
                }
                else
                {
                        displayList();
                }
        }
}
function cancelForm()
{
        if(confirm("Cancel, Are you yure?"))
        {displayList();}
}
stat_reset=0;
function reset_data()
{
        if(confirm("Cancel, Are you sure"))
        {
                if(stat_reset==1)
                {
                        displayList();
                }
                else if(stat_reset==0)
                {
                        notrans=document.getElementById('detail_kode').value;
                        del(notrans);
                }
        }
}
function del(notrans)
{
        noTrans=notrans;
        param='noTrans='+noTrans+'&proses=deletData';
        tujuan='pabrikPemeliharaanmesin_slave.php';
        post_response_text(tujuan, param, respog);		
        function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                        displayList()
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}