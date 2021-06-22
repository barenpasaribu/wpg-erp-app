// JavaScript Document
function add_new_data()
{
	
							//alert(con.responseText);
								document.getElementById('headher').style.display="block";
								document.getElementById('listData').style.display="none";
								document.getElementById('detailEntry').style.display="none";
								document.getElementById('tmbLheader').innerHTML='<button class=mybutton id=dtlAbn onclick=detailAbsn()>'+nmTmblSave+'</button><button class=mybutton id=cancelAbn onclick=cancelAbsn()>'+nmTmblCancel+'</button>';
								unlockForm();	
							
			
	
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
		alert('Too Short Words');
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
		alert('Too Short Words');
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
	kdorg=document.getElementById('kdOrg').value;
	tgl=document.getElementById('tglAbsen').value;
	if((kdorg=='')||(tgl==''))
	{
		alert("Please Complete The Form");
		return;
	}
	
	id=kdorg+"###"+tgl;
	//alert(hsl);
	//return;
	//alert(notran);
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
	//alert(hsl);
	//return;
	//alert(notran);
	param='absnId='+id;
	param+="&proses=createTable";
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
}
function unlockForm()
{
	document.getElementById('kdOrg').disabled=false;
	document.getElementById('tglAbsen').disabled=false;
	document.getElementById('kdOrg').value='';
	document.getElementById('tglAbsen').value='';
}
status_inputan=0;
function addDetail(id) {
	
	crt=document.getElementById('proses');
//	alert(crt.value);
	kdorg=document.getElementById('kdOrg').value;
	tgl=document.getElementById('tglAbsen').value;
	
	var detKode = kdorg+"###"+tgl;
	var rkrywn = document.getElementById('krywnId_'+id);
	var rtpLmbr = document.getElementById('tpLmbr_'+id);
	var rungMkn = document.getElementById('uang_mkn_'+id);
	rungMkn.value=remove_comma(rungMkn);
	var rjm = document.getElementById('jmId_'+id);
	var rmnt = document.getElementById('mntId_'+id);
	var jam=rjm.value+":"+rmnt.value;
	var rungTrans = document.getElementById('uang_trnsprt_'+id);
	rungTrans.value=remove_comma(rungTrans);
	var rungLbhjm = document.getElementById('uang_lbhjm_'+id);
	rungLbhjm.value=remove_comma(rungLbhjm);
	//addSession();
	//var id_user = trim(document.getElementById('user_id').value);
	if(status_inputan==0)
	{
		if(confirm('Are You Sure add this detail'))
		{
			cek_data(id);	
		}
	}
	else if(status_inputan!=0)
	{
	//alert('test');
 			param = "proses=detail_add";
			param += "&absnId="+detKode;
			param += "&tpLmbr="+rtpLmbr.value;
			param += "&krywnId="+rkrywn.value;
			param += "&ungTrans="+rungTrans.value;
			param += "&ungLbhjm="+rungLbhjm.value;
			param += "&ungMkn="+rungMkn.value;
			param += "&Jam="+jam;
		
			//param += "&kd_jenis="+rkd_jenis;
			//param += "&user_id="+id_user;
			tujuan='sdm_slave_detail_lembur.php';
		
		function respon(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					} else {
						// Success Response
					   //alert(con.responseText);
					//   ar=document.getElementById('kd_brg_'+id).value;
					  // document.getElementById('skd_brg_'+id).value=ar;
					   switchEditAdd(id,'detail');
					   addNewRow('detailBody',true);
					   chngeFormat(id);
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
   	kdorg=document.getElementById('kdOrg').value;
	tgl=document.getElementById('tglAbsen').value;
	
	var detKode = kdorg+"###"+tgl;
	
 	var rkrywn = document.getElementById('krywnId_'+id);
	var rtpLmbr = document.getElementById('tpLmbr_'+id);
	var rungMkn = document.getElementById('uang_mkn_'+id);
	rungMkn.value=remove_comma(rungMkn);
	var rjm = document.getElementById('jmId_'+id);
	var rmnt = document.getElementById('mntId_'+id);
	var jam=rjm.value+":"+rmnt.value;
	var rungTrans = document.getElementById('uang_trnsprt_'+id);
	rungTrans.value=remove_comma(rungTrans);
	var rungLbhjm = document.getElementById('uang_lbhjm_'+id);
	rungLbhjm.value=remove_comma(rungLbhjm);
	
	
    param = "proses=detail_edit";
	param += "&absnId="+detKode;
	param += "&tpLmbr="+rtpLmbr.value;
	param += "&krywnId="+rkrywn.value;
	param += "&ungTrans="+rungTrans.value;
	param += "&ungLbhjm="+rungLbhjm.value;
	param += "&ungMkn="+rungMkn.value;
	param += "&Jam="+jam;
   //	alert(param);
   tujuan='sdm_slave_detail_lembur.php';
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    chngeFormat(id);
					alert('Edit Succeed');
					
		}
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text(tujuan, param, respon);
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
			if(confirm('Are You Sure Delete This Data!!!'))
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
		newRow.innerHTML += "<td><select id='krywnId_"+numRow+"' type='text' style='width:150px' />"+optIsi+"</select></td><td>"+"<select id='tpLmbr_"+numRow+"' />"+optLmbr+"</select></td>"+"<td><select id='jmId_"+numRow+"' type='text' />"+optJm+"</select>:<select id='mntId_"+numRow+"' type='text' />"+optMnt+"</select></td>"+"<td><input type='text' onfocus=\"normal_number_1('"+numRow+"')\" onblur=\"chngeFormat('"+numRow+"')\" maxlength='5' onkeypress='return angka_doang(event)' style='width: 100px;' value='0' class='myinputtextnumber' name=uang_mkn_"+numRow+" id=uang_mkn_"+numRow+"></td>"+"<td><input type='text' onfocus=\"normal_number_1('"+numRow+"')\" onblur=\"chngeFormat('"+numRow+"')\" maxlength='5' onkeypress='return angka_doang(event)' style='width: 100px;' value='0' class='myinputtextnumber' name=uang_trnsprt_"+numRow+" id=uang_trnsprt_"+numRow+"></td>"+"<td><input type='text' onfocus=\"normal_number_1('"+numRow+"')\" onblur=\"chngeFormat('"+numRow+"')\" maxlength='5' onkeypress='return angka_doang(event)' style='width: 100px;' value='0' class='myinputtextnumber' name=uang_lbhjm_"+numRow+" id=uang_lbhjm_"+numRow+"></td>"+"<td><img id='detail_add_"+numRow+"' title='Tambah' class=zImgBtn onclick=\"addDetail('"+numRow+"')\" src='images/save.png'/>"+"&nbsp;<img id='detail_delete_"+numRow+"' />"+"&nbsp;<img id='detail_pass_"+numRow+"' />"+"</td>";
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
	document.getElementById('tombol').innerHTML="<button class=mybutton onclick=frm_aju()>"+nmTmblDone+"</button><button class=mybutton onclick=reset_data()>"+nmTmblCancel+"</button>";
}
function cek_data(id)
{
	//var detKode = document.getElementById('detail_kode');
	kdorg=document.getElementById('kdOrg').value;
	tgl=document.getElementById('tglAbsen').value;
	
	var detKode = kdorg+"###"+tgl;
	var rkrywn = document.getElementById('krywnId_'+id);
	var rtpLmbr = document.getElementById('tpLmbr_'+id);
	var rungMkn = document.getElementById('uang_mkn_'+id);
	rungMkn.value=remove_comma(rungMkn);
	var rjm = document.getElementById('jmId_'+id);
	var rmnt = document.getElementById('mntId_'+id);
	var jam=rjm.value+":"+rmnt.value;
	var rungTrans = document.getElementById('uang_trnsprt_'+id);
	rungTrans.value=remove_comma(rungTrans);
	var rungLbhjm = document.getElementById('uang_lbhjm_'+id);
	rungLbhjm.value=remove_comma(rungLbhjm);
	
    param = "proses=cekData";
	param += "&absnId="+detKode;
	param += "&tpLmbr="+rtpLmbr.value;
	param += "&krywnId="+rkrywn.value;
	param += "&ungTrans="+rungTrans.value;
	param += "&ungLbhjm="+rungLbhjm.value;
	param += "&ungMkn="+rungMkn.value;
	param += "&Jam="+jam;
	//var id_user = trim(document.getElementById('user_id').value);
	
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
							//alert(con.responseText);
							//return;
							var id=con.responseText;
							id=id-1;
							switchEditAdd(id,'detail');
						   	addNewRow('detailBody',true);
							status_inputan=1;
							showTmbl();
							chngeFormat(id);
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
	tmp=kdorg+"###"+tgl;
	document.getElementById('kdOrg').value=kdorg;
	document.getElementById('tglAbsen').value=tgl;
	param='absnId='+tmp;
	param+="&proses=createTable";
	tujuan='sdm_slave_detail_lembur.php';
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
				document.getElementById('proses').value='update';
				//alert(con.responseText);
				document.getElementById('listData').style.display='none';
				document.getElementById('headher').style.display='block';
				document.getElementById('detailEntry').style.display='block';
				//document.getElementById('dtlSpb').disabled=true;
//				document.getElementById('cancelSpb').disabled=true;
				var detailDiv = document.getElementById('detailIsi');
				detailDiv.innerHTML = con.responseText;
				status_inputan=1;
				statFrm=1;
				document.getElementById('tombol').innerHTML="<button class=mybutton onclick=frm_aju()>"+nmTmblDone+"</button>";
				document.getElementById('tmbLheader').innerHTML='';
					}
				} else {
					busy_off();
					error_catch(con.status);
				}
			}
		}
	
	
}
/*function editData(id)
{
 	//alert("masuk"+id);
	var rkrywn = document.getElementById('krywnId_'+id);
    var rshft = document.getElementById('shiftId_'+id);
    var rasbnsi = document.getElementById('absniId_'+id);
	var rjm = document.getElementById('jmId_'+id);
	var rmnt = document.getElementById('mntId_'+id);
	var rket = document.getElementById('ktrng_'+id);
	rkrywn.disabled=false;
	rshft.disabled=false;
	rasbnsi.disabled=false;
	rjm.disabled=false;
	rmnt.disabled=false;
	rket.disabled=false;		
}
*/
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
	if(confirm("Are You Sure Want Delete All Data!!!"))
	post_response_text(tujuan, param, respog);	
}
statFrm=0;
function frm_aju()
{
	
	if(statFrm==0)
	{
		if(confirm("Are You Sure Already Done Entry Data !!"))
		{
			displayList();
		}
	}
	else if(statFrm==1)
	{		
		if(confirm("Are You Sure Already Done Edit Data !!"))
		{
			displayList();
		}
	}
}
function reset_data()
{
	if(statFrm==0)
	{
		if(confirm("Are You Sure Cancel Entry Data !!"))
		{
			kdorg=document.getElementById('kdOrg').value;
			tgl=document.getElementById('tglAbsen').value;
			delData(kdorg,tgl);
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
function normal_number_1(id)
{

	satu=document.getElementById('uang_mkn_'+id);
	satu.value=remove_comma(satu);
}
function normal_number_2(id)
{
	dua=document.getElementById('uang_trnsprt_'+id);
	dua.value=remove_comma(dua);
}
function normal_number_3(id)
{
	tiga=document.getElementById('uang_lbhjm_'+id);
	tiga.value=remove_comma(tiga);
}
function chngeFormat(id)
{
	if(document.getElementById('uang_mkn_'+id).value!=0)
	{ 
		sat=document.getElementById('uang_mkn_'+id); 
		change_number(sat);   
	}
	if(document.getElementById('uang_trnsprt_'+id).value!=0)
	{ 
		dua=document.getElementById('uang_trnsprt_'+id);
		change_number(dua);   
	}
	if(document.getElementById('uang_lbhjm_'+id).value!=0)
	{
		tiga=document.getElementById('uang_lbhjm_'+id);
		change_number(tiga);  
	}
}