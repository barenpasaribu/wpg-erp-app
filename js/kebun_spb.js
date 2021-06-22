// JavaScript Document

function add_new_data(nmTmblSave,nmTmblCancel)
{
	bersihForm();
	status_inputan=0;
	//document.getElementById('proses').value='insert';
	param='proses=generateNo';
	tujuan='kebun_slave_save_spb.php';
	  function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
						document.getElementById('listSpb').style.display='none';
						document.getElementById('headher').style.display='block';
						document.getElementById('detailSpb').style.display='none';
						//document.getElementById('noSpb').value = con.responseText;
						//document.getElementById('noSpb').disabled=true;
						document.getElementById('tmbLheader').innerHTML='<button class=mybutton id=dtlSpb onclick=detailSpb()>'+nmTmblSave+'</button><button class=mybutton id=cancelSpb onclick=cancelSpb()>'+nmTmblCancel+'</button>';
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	post_response_text(tujuan, param, respon);
}
function bersihForm()
{
	document.getElementById('nourut').value='';
	document.getElementById('tgl_ganti').value='';
	document.getElementById('kodeOrg').value='';
	document.getElementById('kodeOrg').disabled=false;
	document.getElementById('kodeDiv').disabled=false;
	document.getElementById('period').disabled=false;
	document.getElementById('nourut').disabled=false;
	document.getElementById('kodeDiv').innerHTML='';
        document.getElementById('mnculSma').disabled=false;
        document.getElementById('mnculSma').checked=false;
	document.getElementById('contentDetail').innerHTML='';
}
function bersihDetailForm()
{
	document.getElementById('blok').value='';
    document.getElementById('bjr').value='0';
    document.getElementById('jjng').value='0';
	document.getElementById('brondln').value='0';
	document.getElementById('mtng').value='0';
	document.getElementById('mnth').value='0';
	document.getElementById('bsk').value='0';
	document.getElementById('lwtmtng').value='0';
}
function add_detail()
{
	kdDiv=document.getElementById('kodeDiv').options[document.getElementById('kodeDiv').selectedIndex].value;
	periode=document.getElementById('period').options[document.getElementById('period').selectedIndex].value
	noUrut=document.getElementById('nourut').value;
	tgl=document.getElementById('tgl_ganti').value;
	a=periode.split('-');
	var hsl=noUrut+'/'+kdDiv+'/'+a[1]+'/'+a[0];
	//alert(hsl);
	//return;
	document.getElementById('noSpb').value=hsl;
	//nospb=document.getElementById('noSpb').value;
	document.getElementById('detail_kode').value=hsl;
	//alert(notran);
	param='noSpb='+hsl+'&kdDiv='+kdDiv+'&periode='+periode+'&tgl='+tgl;
	param+="&proses=createTable";
	//alert(param);
	tujuan='kebun_spb_slave_detail.php';
	function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
					//alert(con.responseText);
					document.getElementById('kodeOrg').disabled=true;
					document.getElementById('kodeDiv').disabled=true;
					document.getElementById('period').disabled=true;
					document.getElementById('nourut').disabled=true;
					document.getElementById('tmbLheader').innerHTML='';
					document.getElementById('detailSpb').style.display='block';
					document.getElementById('ppDetailTable').innerHTML=con.responseText;
					showTmbl();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	post_response_text(tujuan, param, respon);
}
function detailSpb()
{
	tgl=document.getElementById('tgl_ganti').value;
	kdOrg=document.getElementById('kodeOrg').options[document.getElementById('kodeOrg').selectedIndex].value;
	NoUrut=document.getElementById('nourut').value;
	if((tgl=='')||(kdOrg=='')||(NoUrut=='')||(NoUrut==0000000))
	{
		alert("Please Complete The Form and Don't Use Zero as No Urut");
		return;
	}
	add_detail();
	//tmbhDetail();
}
function getBjr()
{
    blk = document.getElementById('blok').options[document.getElementById('blok').selectedIndex].value;
    tgl_ganti = document.getElementById('tgl_ganti').value;
	//document.getElementById('oldBlok').value=blk;tgl_ganti
        periode=document.getElementById('period');
        periode=periode.options[periode.selectedIndex].value;
        param = 'blok=' + blk + '&proses=amblBjr&periode=' + periode + '&tgl_ganti=' + tgl_ganti;
	//alert(param);
	tujuan='kebun_slave_save_spb.php';
	  function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
						document.getElementById('bjr').value = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	post_response_text(tujuan, param, respon);
}
status_inputan=0;
function addDetail() {
	
	crt=document.getElementById('proses');
//	alert(crt.value);
	var detKode = document.getElementById('detail_kode');
    var rblok = document.getElementById('blok').options[document.getElementById('blok').selectedIndex].value;
    var rbjr = document.getElementById('bjr');
    var rjjng = document.getElementById('jjng');
	var rbrondln = document.getElementById('brondln');
	var rmatang = document.getElementById('mtng');
	var rmentah = document.getElementById('mnth');
	var rbusuk = document.getElementById('bsk');
        
	var rlwtmatang = document.getElementById('lwtmtng');
	//addSession();
	//var id_user = trim(document.getElementById('user_id').value);
	rtgl = trim(document.getElementById('tgl_ganti').value);
	rnospb = trim(document.getElementById('noSpb').value);
	
	
	if(rnospb=='')
	{
		alert('no spb empty, please reload frame');
	}
	
	
	//document.getElementById('detail_kode').value=rnospb;
	if(status_inputan==0)
	{
		if(confirm('Are You Sure add this detail'))
		{
			cek_data();	
		}
	}
	else
	{
		cek_data();	
	}
	
}
function loadDetail()
{
	noSpb=document.getElementById('detail_kode').value;
	kdDiv=document.getElementById('kodeOrg').options[document.getElementById('kodeOrg').selectedIndex].value;
	tujuan='kebun_spb_slave_detail.php';
	param='noSpb='+noSpb+'&kdDiv='+kdDiv+'&proses=loadDetail';
	post_response_text(tujuan, param, respon);
	function respon(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					} else {
						// Success Response
					   //alert(con.responseText);
					   document.getElementById('contentDetail').innerHTML=con.responseText;
					}
				} else {
					busy_off();
					error_catch(con.status);
				}
			}
		}
}

function editDetail(nospb,blok, jjg, bjr, brondolan, mentah, busuk, matang, lewatmatang) 
{//	alert('test');
	document.getElementById('blok').value=blok;
	document.getElementById('bjr').value=bjr;
	document.getElementById('jjng').value=jjg;
	document.getElementById('brondln').value=brondolan;
	document.getElementById('mtng').value=matang;
	document.getElementById('mnth').value=mentah;
	document.getElementById('bsk').value=busuk;
	document.getElementById('lwtmtng').value=lewatmatang;	
	document.getElementById('oldBlok').value=blok;
	document.getElementById('proses').value='updateData';
}

/* Function deleteDelete(id)
 * Fungsi untuk menghapus data Detail
 * I : id row (urutan row pada table Detail)
 * P : Menghapus data pada tabel Detail
 * O : Menghapus baris pada tabel Detail
 */
function deleteDetail(id) {
    var detKode = document.getElementById('detail_kode');
    var rblok = document.getElementById('blok_'+id);
		param = "proses=detail_delete";
		param += "&noSpb="+detKode.value;
		param += "&blok="+rblok.value;
		//alert(param);
		tujuan='kebun_spb_slave_detail.php';
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
		newRow.innerHTML += "<td><select id='blok_"+numRow+"' type='text' style='width:150px' onchange='getBjr("+numRow+")' />"+optIsi+"</select><input type=hidden id=oldBlok_"+numRow+"  /></td><td>"+
            "<input id='bjr_"+numRow+"' type='text' class='myinputtextnumber' style='width:120px' disabled='disabled' value=''  /></td><td><input id='jjng_"+numRow+
	"' type='text' class='myinputtextnumber' style='width:100px' value='' onkeypress='return angka_doang(event)' maxlength='12' /></td>"+"<td><input id='brondln_"+numRow+"' type='text' class='myinputtextnumber' onkeypress='return angka_doang(event)' style='width:100px' value='0' maxlength='12' />"+""+"<td><input id='mnth_"+numRow+"' type='text' class='myinputtextnumber' onkeypress='return angka_doang(event)' style='width:100px' value='0' maxlength='12' />"+""+"<td><input id='bsk_"+numRow+"' type='text' class='myinputtextnumber' onkeypress='return angka_doang(event)' style='width:100px' value='0' maxlength='12' />"+""+"<td><input id='mtng_"+numRow+"' type='text' class='myinputtextnumber' onkeypress='return angka_doang(event)' style='width:100px' value='0' maxlength='12' />"+""+"<td><input id='lwtmtng_"+numRow+"' type='text' class='myinputtextnumber' onkeypress='return angka_doang(event)' style='width:100px' value='0' maxlength='12' />"+"<td><img id='detail_add_"+numRow+"' title='Tambah' class=zImgBtn onclick=\"addDetail('"+numRow+"')\" src='images/save.png'/>"+"&nbsp;<img id='detail_delete_"+numRow+"' />"+"&nbsp;<img id='detail_pass_"+numRow+"' />"+"</td>";
	/*newRow.innerHTML += "<td><select id='kd_brg_"+numRow+"' style='width:180px' onchange='set_brg("+numRow+")'>"+isi_barang+"</select><input type=hidden id=skd_brg_"+numRow+" name=skd_brg_"+numRow+" /></td><td>"+
            "<select id='sat_"+numRow+"'  style='width:70px'></select></td>"+"<td><input id='jmlh_"+numRow+"' type='text' class='myinputtextnumber' onkeypress='return amgka_doang(event)' style='width:70px' value='' />"+"<td><input id='ket_"+numRow+"' type='text' class='myinputtext' style='width:130px' value='' onkeypress='return tanpa_kutip(event)' />"+"<td><img id='detail_add_"+numRow+
	"' title='Tambah' class=zImgBtn onclick=\"addDetail('"+numRow+"')\" src='images/save.png'/>"+
	"&nbsp;<img id='detail_delete_"+numRow+"' />"+
	"&nbsp;<img id='detail_pass_"+numRow+"' />"+
	"</td>";*/
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
function showTmbl()
{
	document.getElementById('tombol').innerHTML="<button class=mybutton onclick=frm_aju()>"+nmTmblDone+"</button><button class=mybutton onclick=reset_data()>"+nmTmblCancel+"</button>";
}
function cek_data()
{
	//var detKode = document.getElementById('detail_kode');
    var rblok = document.getElementById('blok');
    var rbjr = document.getElementById('bjr');
    var rjjng = document.getElementById('jjng');
	var rbrondln = document.getElementById('brondln');
	var rmatang = document.getElementById('mtng');
	var rmentah = document.getElementById('mnth');
	var rbusuk = document.getElementById('bsk');
	var rlwtmatang = document.getElementById('lwtmtng');
        var kagewebe = document.getElementById('kgwb');
	var rkodeOrg= document.getElementById('kodeOrg').options[document.getElementById('kodeOrg').selectedIndex].value;

	//var id_user = trim(document.getElementById('user_id').value);
	
	
	
	rtgl = trim(document.getElementById('tgl_ganti').value);
	rnospb = trim(document.getElementById('noSpb').value);
	pros=document.getElementById('proses').value;
	if(pros!='updateData')
	{
		param = "proses=cekData";
	}
	else
	{
		oldBlok=document.getElementById('oldBlok').value;
		param = "proses="+pros+'&oldBlok='+oldBlok;
	}
	param += "&noSpb="+rnospb;
	param += "&blok="+rblok.value;
	param += "&bjr="+rbjr.value;
	param += "&jjng="+rjjng.value;
	param += "&brondolan="+rbrondln.value;
	param += "&tgl="+rtgl;
	param += "&matang="+rmatang.value;
	param += "&mentah="+rmentah.value;
	param += "&busuk="+rbusuk.value;
	param += "&lwtmatang="+rlwtmatang.value;
	param += "&kdOrg="+rkodeOrg;
        param += "&kgwb="+kagewebe;
	tujuan='kebun_slave_save_spb.php';
	//alert(param);
        if(rbjr.value=='' || parseInt(rbjr.value)==0)
         {
            alert('BJR is NULL, Mohon diisi BJR terlebih dahulu') 
         }   
         else{
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
							//return;
							//var id=con.responseText;
							//id=id-1;
							//switchEditAdd(id,'detail');
						   //	addNewRow('detailBody',true);
							status_inputan=1;
                                                        document.getElementById('proses').value="cekData";
							bersihDetailForm();
							loadDetail();
							//showTmbl();
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
function displayList()
{
	document.getElementById('listSpb').style.display='block';
	document.getElementById('headher').style.display='none';
	document.getElementById('detailSpb').style.display='none';
	document.getElementById('txtsearch').value='';
	document.getElementById('tgl_cari').value='';
        document.getElementById('mnculSma').disabled=false;
	loadData();
}
function loadData()
{
	param='proses=loadNewData';
	tujuan='kebun_slave_save_spb.php';
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
function fillField(nospb,tanggal,stat,period,statDt) 
{
	ar=document.getElementById('noSpb').value=nospb;
	ars=ar.split("/");
	document.getElementById('nourut').value=ars[0];
	
	kdorg=ars[1].substring(0,4);
	document.getElementById('kodeOrg').value=kdorg;
	periode=ars[3]+'-'+ars[2];
	document.getElementById('period').value=periode;
	document.getElementById('tgl_ganti').value=tanggal;
	nospb=document.getElementById('noSpb').value;
	kdDiv=ars[1];
	tgl=tanggal;
	periode=period;
	Stat=stat;
	param='noSpb='+nospb+'&stat='+Stat+'&kdDiv='+kdDiv+'&tgl='+tgl+'&periode='+periode;
	param+="&proses=createTable"+'&statusCek='+statDt;
//	alert(param);
	tujuan='kebun_spb_slave_detail.php';
	post_response_text(tujuan, param, respon);
		function respon(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					} else {
						// Success Response
						document.getElementById('detail_kode').value=nospb;
				lockForm();
				document.getElementById('proses').value='cekData';
				//alert(con.responseText);

				document.getElementById('listSpb').style.display='none';
				document.getElementById('headher').style.display='block';
				document.getElementById('detailSpb').style.display='block';
				//document.getElementById('dtlSpb').disabled=true;
//				document.getElementById('cancelSpb').disabled=true;
				var detailDiv = document.getElementById('ppDetailTable');
				detailDiv.innerHTML = con.responseText;
				status_inputan=1;
				statForm=1;
				//showTmbl();
                                document.getElementById('mnculSma').checked=false;
                                if(statDt==1){
                                    document.getElementById('mnculSma').checked=true;
                                    document.getElementById('mnculSma').disabled=true;
                                }

				document.getElementById('tmbLheader').innerHTML='';
				document.getElementById('tombol').innerHTML="<button class=mybutton onclick=frm_aju()>"+nmTmblDone+"</button>";
				//alert(ars[1]);
				getDiv(ars[1]);
				
					}
				} else {
					busy_off();
					error_catch(con.status);
				}
			}
		}
	
}
function delData(nosbp)
{
	noSpb=nosbp;
	param='noSpb='+noSpb+'&proses=delData';
	tujuan='kebun_slave_save_spb.php';
	function respog()
	{
		  if(con.readyState==4)
		  {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else 
					{
						loadData();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	 } 	
	 if(confirm("Are You Sure Want Delete All Data!!!"))
	 {
		 post_response_text(tujuan, param, respog);
	 }
	 else
	 {
		 return;
	 }
}
function delDetail(nosbp,blk)
{
	noSpb=nosbp;
	blok=blk;
	
	param='noSpb='+noSpb+'&proses=delDetail'+'&blok='+blok;
	tujuan='kebun_slave_save_spb.php';
	function respog()
	{
		  if(con.readyState==4)
		  {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else 
					{
						loadDetail();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	 } 	
	 if(confirm("Anda yakin ingin menghapus data ini!!!"))
	 {
		 post_response_text(tujuan, param, respog);
	 }
	 else
	 {
		 return;
	 }
	
}
function cariSpb()
{
	txtSearch=document.getElementById('txtsearch').value;
	txtTgl=document.getElementById('tgl_cari').value;
	
	param='txtSearch='+txtSearch+'&txtTgl='+txtTgl+'&proses=cariNospb';
	//alert(param);
	tujuan = 'kebun_slave_save_spb.php';
	post_response_text(tujuan, param, respog);			
	function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('listSpb').style.display='block';
						document.getElementById('headher').style.display='none';
						document.getElementById('detailSpb').style.display='none';
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
function cariData(num)
{
		txtSearch=document.getElementById('txtsearch').value;
		txtTgl=document.getElementById('tgl_cari').value;
		param='txtSearch='+txtSearch+'&txtTgl='+txtTgl+'&proses=cariNospb';
		param+='&page='+num;
		tujuan = 'kebun_slave_save_spb.php';
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
function cariBast(num)
{
		param='proses=loadNewData';
		param+='&page='+num;
		tujuan = 'kebun_slave_save_spb.php';
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
function cancelSpb()
{
	if(confirm("Are You Sure Want Cancel!!"))
	{
		document.getElementById('listSpb').style.display='block';
		document.getElementById('headher').style.display='none';
		document.getElementById('detailSpb').style.display='none';
		document.getElementById('tgl_ganti').value='';
	}
	else
	{
		return;
	}
}
statForm=0;
function frm_aju()
{
	
		if(confirm("Are You Sure Already Done !!"))
		{
			displayList();
		}
}
function upDate()
{
	nospb=document.getElementById('noSpb').value;
	tglSpb=document.getElementById('tgl_ganti').value;
	param='noSpb='+nospb+'&proses=update'+'&tgl='+tglSpb;
	tujuan = 'kebun_slave_save_spb.php';
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
function reset_data()
{
	if(statForm==0)
	{
		nsbp=document.getElementById('noSpb').value;
		param='noSpb='+nsbp+'&proses=delData';
		tujuan='kebun_slave_save_spb.php';
		function respog()
		{
			  if(con.readyState==4)
			  {
					if (con.status == 200) {
						busy_off();
						if (!isSaveResponse(con.responseText)) {
							alert('ERROR TRANSACTION,\n' + con.responseText);
						}
						else 
						{
							displayList();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
			  }	
		 } 	
		 if(confirm("Are You Sure Want Cancel This Entry !!!"))
		 {
			 post_response_text(tujuan, param, respog);
		 }
		 else
		 {
			 return;
		 }
	}
	else if(statForm==1)
	{
		if(confirm("Are You Sure Want Cancel This Action !!!"))
		{displayList();} 
		document.getElementById('tgl_ganti').value='';
	}
}
function dataKePDF(ev)
{
	pt		=document.getElementById('unitOrg');
	periode =document.getElementById('periode');
	pt		=pt.options[pt.selectedIndex].value;
	//gudang	=gudang.options[gudang.selectedIndex].value;
	periode	=periode.options[periode.selectedIndex].value;
	tujuan='kebun_spb_pdf.php';
	judul='List Data SPB PDF';		
	param='pt='+pt+'&periode='+periode;
	//alert(param);
	printFile(param,tujuan,judul,ev)		
}
function dataKePDFDat(nospb,ev)
{
	noSpb=nospb;
	param='noSpb='+noSpb+'&proses=pdf';
	//alert(param);
	tujuan='kebun_spbPdf.php';
	judul='List Data SPB PDF';		
	
	//alert(param);
	printFile(param,tujuan,judul,ev)		
}
function dataKeExcel(ev,tujuan)
{
	pt		=document.getElementById('unitOrg');
	periode =document.getElementById('periode');
	pt		=pt.options[pt.selectedIndex].value;
	//gudang	=gudang.options[gudang.selectedIndex].value;
	periode	=periode.options[periode.selectedIndex].value;
	//tujuan='kebun_spb_pdf.php';
	judul='Download Daftar SPB';		
	param='pt='+pt+'&periode='+periode;
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
function getDiv(idn)
{
	
	if(idn==0)
	{
		kdOrg=document.getElementById('kodeOrg').value;
		param='kdOrg='+kdOrg+'&proses=getDivData';
		tujuan='kebun_slave_save_spb.php';
	}
	else
	{
		kdrrg=idn.substring(0,4);
		idDiv=idn;
		param='kdOrg='+kdrrg+'&idDiv='+idDiv+'&proses=getDivData';
		tujuan='kebun_slave_save_spb.php';
	}
//	alert(param);

	post_response_text(tujuan, param, respog);
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					//alert(con.responseText);
					if(idn==0)
					{
						//alert("masuk");
						document.getElementById('kodeDiv').innerHTML=con.responseText;
						
					}
					else
					{
						document.getElementById('kodeDiv').innerHTML=con.responseText;
						loadDetail();
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
function fillZero()
{
	//alert("test");
	str=document.getElementById('nourut').value;
	while(str.length<7)
	{
		str=0+str;
	}
	document.getElementById('nourut').value=str;
}
function lockForm()
{
	document.getElementById('kodeOrg').disabled=true;
	document.getElementById('kodeDiv').disabled=true;
	document.getElementById('period').disabled=true;
	document.getElementById('nourut').disabled=true;
}
function getBlokSma(){
    pil=document.getElementById('kodeDiv');
    pil=pil.options[pil.selectedIndex].value;
    der=document.getElementById('mnculSma');
    if(der.checked==true){
        param='proses=getBlokSma';
    }else{
        param='proses=getBlokNor';
    }
    param+='&kdAfd='+pil;
    tujuan='kebun_spb_slave_detail.php';
    post_response_text(tujuan, param, respog);
    function respog(){
    if (con.readyState == 4) {
            if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                            alert('ERROR TRANSACTION,\n' + con.responseText);
                    }
                    else {
                            //alert(con.responseText);
                             document.getElementById('blok').innerHTML=con.responseText;
                    }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
    }
    }	
}
function searchBrg(title,content,ev)
{
        width='500';
        height='400';
        showDialog1(title,content,width,height,ev);
        //alert('asdasd');
}
function findBrg()
{
        txt=trim(document.getElementById('no_brg').value);
        dt=trim(document.getElementById('kdafd').value);
        
        if(txt=='')
        {
                alert('Text is obligatory');
        }
        else if(txt.length<3)
        {
                alert('Too short words');
        }
        else
        {
                der=document.getElementById('mnculSma');
                if(der.checked==true){
                    param='idCer=1';
                }else{
                    param='idCer=0';
                }
                param+='&txtfind='+txt+'&proses=cariBlok';
                param+='&kdAfd='+dt;
                tujuan='kebun_spb_slave_detail.php';
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
function setBlok(blk){
     l=document.getElementById('blok');

    for(a=0;a<l.length;a++)
        {
            if(l.options[a].value==blk)
                {
                    l.options[a].selected=true;
                }
        }
       closeDialog();
       getBjr();
}
/*function addSession()
{
	if(document.getElementById('detail_kode').value!='')
	{
		nosbp=document.getElementById('detail_kode').value;
		param='noSpb='+nosbp+'proses=addSession';
		tujuan='kebun_slave_save_spb.php';
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						//document.getElementById('kodeDiv').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
		post_response_text(tujuan, param, respog);
	}
}
*//*function cekData()
{
	nospb=document.getElementById('noSpb').value;
	param='noSpb='+nospb;
	param+='&proses=CekData';
	tujuan = 'kebun_slave_save_spb.php';
	post_response_text(tujuan, param, respog);			
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					status=con.responseText;
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}	
}*/