function detailPembelian()
{
//alert('ok');
	kd_bag=trim(document.getElementById('kd_bag').value);
	tgl_ppr=trim(document.getElementById('tgl_pp').value);
	if(kd_bag=='')
	{
		alert('Data inconsistent');	
	}
	else
	{
		document.getElementById('detailTable').style.display = 'block';
        document.getElementById('tmbl_all').style.display = 'block';
		document.getElementById('nopp').disabled=true;
		document.getElementById('tgl_pp').disabled=true;
		document.getElementById('kd_bag').disabled=true;
		document.getElementById('dtl_pem').disabled=true;
        pass2detail();
	}
}

/* Function pass2detail
 * Fungsi untuk menampilkan tabel detail dari tabel Main yang dimaksud
 * I : numRow dari tabel Main
 * P : Ajax untuk extract data dan persiapan tabel dalam bentuk HTML
 * O : Tampilan tabel detail
 */
function pass2detail() {
    var kode = document.getElementById('nopp');
    param = "id="+kode.value;
    param += "&proses=createTable";
    
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
		    var detailDiv = document.getElementById('detailTable');
		    detailDiv.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text('log_slave_pp_detail.php', param, respon);
}

function get_isi(kdorg,nm_org)
{
	param='kdorg='+kdorg;
	tujuan='log_slave_get_no_pp.php';
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
							document.getElementById('nopp').value=trim(con.responseText);
						    //document.getElementById('captionkelompok').innerHTML=namakelompok;
						    //getSupplierList(kdkelompok);
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  	
}
//bagian cari data barang dan kode anggaran, dari log_5masterbarang, keu_anggaran
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
	if(txt=='')
	{
		alert('Text is obligatory');
	}
	else
	{
		param='txtfind='+txt;
		tujuan='log_slave_get_brg.php';
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
function setBrg(no_brg,namabrg,satuan,nomor)
{
	 nomor=document.getElementById('nomor').value;
     document.getElementById('kd_brg_'+nomor).value=no_brg;
	 document.getElementById('nm_brg_'+nomor).value=namabrg;
	 document.getElementById('sat_'+nomor).value=satuan;
	 closeDialog();
}

//search anggaran

function searchAngrn(title,content,ev)
{
	width='500';
	height='400';
	showDialog1(title,content,width,height,ev);
	//alert('asdasd');
}
function findAngrn()
{
	txt2=trim(document.getElementById('no_angrn').value);
	if(txt2=='')
	{
		alert('Text is obligatory');
	}
	else
	{
		param='txtfind2='+txt2;
		tujuan='log_slave_get_brg.php';
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
function setAngrn(no_angrn)
{
	 var nomor=document.getElementById('nomor').value;
        //alert(nomor);
	 document.getElementById('kd_angrn_'+nomor).value=no_angrn;
	 //document.getElementById('nm_angrn_'+nomor).value=no_angrn;
	 closeDialog();
}

function clear_data(id)
 {
	document.getElementById("nopp").value='';
	document.getElementById("tgl_pp").value='';
	document.getElementById('detail_pp').style.display = 'none';
	document.getElementById('nopp').disabled=false;
	document.getElementById('tgl_pp').disabled=false;
	document.getElementById('kd_bag').disabled=false;
	document.getElementById('dtl_pem').disabled=false;
	stat_inputb=0;
	stat_input=0;
 }
 //Simpan data header 
function simpanPerpem()
  {

	rnopp = trim(document.getElementById('nopp').value);
	rtgl_pp = trim(document.getElementById('tgl_pp').value);
	rkd_bag = trim(document.getElementById('kd_bag').value);
	id_user = trim(document.getElementById('user_id').value);
	method=document.getElementById('method').value;
	param='rnopp='+rnopp+'&rtgl_pp='+rtgl_pp+'&rkd_bag='+rkd_bag+'&usr_id='+id_user; //+'&rkd_org='+rkd_org;
	param+='&method='+method;
	//param+=strUrl;
	tujuan='log_slave_save_log_pp.php';
	//alert(param);
	
	//alert(param);
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
								//document.getElementById('contain').innerHTML=con.responseText;
								//alert('Saved succeed !!');
								//clear_all_data();
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
stat_input=0;
stat_inputc=0;
function edit_header()
  {
	//alert(strUrl);

	stats=document.getElementById('method');
	if(stat_input==1)
	{
		
	//	alert('edit');
		rnopp = trim(document.getElementById('nopp').value);
		rtgl_pp = trim(document.getElementById('tgl_pp').value);
		rkd_bag = trim(document.getElementById('kd_bag').value);
		id_user = trim(document.getElementById('user_id').value);
		//rkd_org = trim(document.getElementById('kode_org').value);
		method=document.getElementById('method').value;
		param='rnopp='+rnopp+'&rtgl_pp='+rtgl_pp+'&rkd_bag='+rkd_bag+'&usr_id='+id_user; //+'&rkd_org='+rkd_org;
		param+='&method='+method;
		//param+=strUrl;
		tujuan='log_slave_save_log_pp.php';
	
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
									document.getElementById('contain').innerHTML=con.responseText;
									//alert('Saved succeed !!');
									clear_all_data();
							}
						}
						else {
							busy_off();
							error_catch(con.status);
						}
				  }	
		 } 	
		 //post_response_text(tujuan, param, respog);
			var answer =confirm('Are you sure, Edit Header?');
			if (answer){
			post_response_text(tujuan, param, respog);
			}
			else{
			clear_all_data();
			}
	}
	else if(stat_input==0)
	{
		//alert('insert');
		if(stat_inputc==0)
		{
			cek_data();
		}
		else
		{
			displayList();
		}
	}
}

function cek_data()
{
	nopp=document.getElementById('detail_kode').value;
	rtgl_pp = trim(document.getElementById('tgl_pp').value);
	rkd_bag = trim(document.getElementById('kd_bag').value);
	id_user = trim(document.getElementById('user_id').value);
	met=document.getElementById('method').value='cek_data_header';
	var tbl = document.getElementById("ppDetailTable");
	var row = tbl.rows.length;
	strUrl = '';
	for(i=0;i<row;i++)
	{
			try{
				if(strUrl != '')
				{
					strUrl += '&kdbrg[]='+encodeURIComponent(trim(document.getElementById('kd_brg_'+i).value))
							  +'&ketrng[]='+encodeURIComponent(trim(document.getElementById('ket_'+i).value))
							  +'&rkd_angrn[]='+encodeURIComponent(trim(document.getElementById('kd_angrn_'+i).value))
							  +'&rjmlhDiminta[]='+encodeURIComponent(trim(document.getElementById('jmlhDiminta_'+i).value))
							  +'&rtgl_sdt[]='+encodeURIComponent(trim(document.getElementById('tgl_sdt_'+i).value));
				}
				else
				{
					strUrl += '&kdbrg[]='+encodeURIComponent(trim(document.getElementById('kd_brg_'+i).value))
							  +'&ketrng[]='+encodeURIComponent(trim(document.getElementById('ket_'+i).value))
							  +'&rkd_angrn[]='+encodeURIComponent(trim(document.getElementById('kd_angrn_'+i).value))
							  +'&rjmlhDiminta[]='+encodeURIComponent(trim(document.getElementById('jmlhDiminta_'+i).value))
							  +'&rtgl_sdt[]='+encodeURIComponent(trim(document.getElementById('tgl_sdt_'+i).value));
				}
			}
			catch(e){}
	}
	param='cknopp='+nopp+'&tgl_sdt='+rtgl_pp+'&kd_org='+rkd_bag+'&user_id='+id_user;
	param+=strUrl;
	tujuan='log_slave_get_user_id_log_pp.php';
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
							stat_inputc=1;
							//document.getElementById('contain').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 } 	
	/*alert(param);
	return;*/
	
}
	
function addDetail(id) {
	
	crt=document.getElementById('method');
	var detKode = document.getElementById('detail_kode');
    var rkd_brg = document.getElementById('kd_brg_'+id);
    var rkd_angrn = document.getElementById('kd_angrn_'+id);
    var rjmlhDiminta = document.getElementById('jmlhDiminta_'+id);
    var rtgl_sdt = document.getElementById('tgl_sdt_'+id);
    var rket = document.getElementById('ket_'+id);
	var tgl_header=document.getElementById('tgl_pp').value;
	var kd_org=document.getElementById('kd_bag').value;
	var id_user = trim(document.getElementById('user_id').value);
	nopp=document.getElementById('detail_kode').value;
	rtgl_pp = trim(document.getElementById('tgl_pp').value);
	rkd_bag = trim(document.getElementById('kd_bag').value);
	if(crt.value=='insert')
	{
		
		var a=confirm('Are You Sure add this detail');
		if(a)
		{
			cek_data();
		}
	}
	else
	{
	//alert('test');
 			param = "proses=detail_add";
			param += "&kode="+detKode.value;
			param += "&kd_brg="+rkd_brg.value;
			param += "&kd_angrn="+rkd_angrn.value;
			param += "&jmlhDiminta="+rjmlhDiminta.value;
			param += "&tgl_sdt="+rtgl_sdt.value;
			param += "&ket="+rket.value;
			param += "&rkd_bag="+rkd_bag;
			param += "&rtgl_pp="+rtgl_pp;
			tujuan='log_slave_pp_detail.php';
		
		function respon(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					} else {
						// Success Response
					   //alert(con.responseText);
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
    var rkd_angrn = document.getElementById('kd_angrn_'+id);
    var rjmlhDiminta = document.getElementById('jmlhDiminta_'+id);
    var rtgl_sdt = document.getElementById('tgl_sdt_'+id);
    var rket = document.getElementById('ket_'+id);
	
    param = "proses=detail_edit";
    param += "&nopp="+detKode.value;
    param += "&kd_brg="+rkd_brg.value;
    param += "&kd_angrn="+rkd_angrn.value;
    param += "&jmlhDiminta="+rjmlhDiminta.value;
    param += "&tgl_sdt="+rtgl_sdt.value;
    param += "&ket="+rket.value;
   // alert(param);
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    alert('Edit Succeed');
		}
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('log_slave_pp_detail.php', param, respon);
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
    var rkd_angrn = document.getElementById('kd_angrn_'+id);
    var rjmlhDiminta = document.getElementById('jmlhDiminta_'+id);
    var rtgl_sdt = document.getElementById('tgl_sdt_'+id);
    var rket = document.getElementById('ket_'+id);
	
   
		param = "proses=detail_delete";
		param += "&nopp="+detKode.value;
		param += "&kd_brg="+rkd_brg.value;
		param += "&kd_angrn="+rkd_angrn.value;
		param += "&jmlhDiminta="+rjmlhDiminta.value;
		param += "&tgl_sdt="+rtgl_sdt.value;
		param += "&ket="+rket.value;
		
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
		 a=confirm('Are You Sure Delete This Data!!!');
			if(a)
			{
				//alert(param);
			//	return;
				post_response_text('log_slave_pp_detail.php', param, respon);
				
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
	newRow.innerHTML += "<td><input id='kd_brg_"+numRow+"' type='text' class='myinputtext' style='width:120px' disabled='disabled' value='' /></td><td>"+
            "<input id='nm_brg_"+numRow+"' type='text' class='myinputtext' style='width:120px' disabled='disabled' value='' /></td><td><input id='sat_"+numRow+
	"' type='text' class='myinputtext' style='width:70px'disabled='disabled' value='' /><img src=images/search.png class=dellicon title='"+jdl_ats_0+"' onclick=\"searchBrg('"+jdl_ats_1+"','"+content_0+"<input id=nomor type=hidden value="+numRow+" />',event)\";></td>"+"<td><input id='kd_angrn_"+numRow+"' type='text' class='myinputtext' style='width:70px' disabled='disabled' value='' />"+
        "<img src=images/search.png class=dellicon title='"+jdl_bwh_0+"' onclick=\"searchAngrn('"+jdl_bwh_1+"','"+content_1+"<input id=nomor type=hidden value="+numRow+" />',event)\";></td><td><input id='jmlhDiminta_"+numRow+"' type='text' class='myinputtext' style='width:70px' value='' onkeypress='return angka_doang(event)' /></td>"+"<td><input type='text' style='width:70px' id='tgl_sdt_"+numRow+"' class='myinputtext' name='tgl_sdt_"+numRow+"' maxlength=\"10\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\" ></td><td><input id='ket_"+numRow+"' type='text' class='myinputtext' style='width:130px' onkeypress='return tanpa_kutip(event)' value='' /></td>"+"<td><img id='detail_add_"+numRow+
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

function fillField(nopp,tgl,kd_org,stat) {
	
	if(stat>0)
	{
		alert('You Already Confirm This No.PP'+nopp+'. Now wait Approval');
		return;
	}
	else
	{
		document.getElementById('detailTable').style.display = 'block';
		document.getElementById('form_pp').style.display='block';
		document.getElementById('list_pp').style.display='none';
		document.getElementById('persetujuan').style.display='none';
		document.getElementById('dtl_pem').style.display='none';
		document.getElementById('method').value='update';
		document.getElementById('nopp').value=nopp;
		document.getElementById('nopp').disabled=true;
		document.getElementById('kd_bag').disabled=true;
		document.getElementById('tgl_pp').value=tgl;
		document.getElementById('kd_bag').value=kd_org;
		//document.getElementById('dtl_pem').disabled=false;
		document.getElementById('tmbl_all').style.display = 'block';
		stat_input=1;
                stat_inputb=0;
		var kode = document.getElementById('nopp');
		param = "id="+kode.value;
		param += "&proses=createTable";
		
		function respon(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					} else {
						// Success Response
				var detailDiv = document.getElementById('detailTable');
				detailDiv.innerHTML = con.responseText;
					}
				} else {
					busy_off();
					error_catch(con.status);
				}
			}
		}
		post_response_text('log_slave_pp_detail.php', param, respon);
	}
}
function delPp(rnopp,stat)
{
	
	if(stat>0)
	{
		alert('You Already Confirm This No.PP'+rnopp+'. Now wait Approval');
		return;//clear_all_data();
	}
	else
	{
			a=confirm("Are You Sure Want Delete All Data");
			
			if(a)
			{
				param='rnopp='+rnopp;
				param+='&method=delete';
				tujuan='log_slave_save_log_pp.php';
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
										document.getElementById('contain').innerHTML=con.responseText;
										alert('Delete Data Succeed');
										clear_all_data();
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
			else
			{
				displayList();
			}
		
	}
}
function clear_all_data()
 {
	var cell = document.getElementById("detailTable");
	
	if ( cell.hasChildNodes() )
	{
		while ( cell.childNodes.length >= 1 )
		{
		cell.removeChild(cell.firstChild);       
		} 
	}
	document.getElementById("nopp").disabled=true;
	document.getElementById("nopp").value='';
	//document.getElementById("tgl_pp").value='';
	document.getElementById('detailTable').style.display = 'none';
    document.getElementById('tmbl_all').style.display = 'none';
	document.getElementById('kd_bag').value='';
	document.getElementById('tgl_pp').disabled=false;
	document.getElementById('kd_bag').disabled=false;
	document.getElementById('dtl_pem').disabled=false;
	document.getElementById('method').value='insert';
   	document.getElementById('form_pp').style.display = 'none';
	document.getElementById('persetujuan').style.display = 'none';
	document.getElementById('list_pp').style.display = 'block';
	stat_inputb=0;
	stat_input=0;
	stat_inputc=0;
	document.getElementById('method').value='insert';
	//addDetail('detialPem');
 }
 function frm_aju(nopp,stat)
 {
	
	if(stat>0)
	{
		alert('Waiting Approval Can`t Edit or Delete');
	}
	else
	{
		var tbl = document.getElementById("ppDetailTable");
 		var row = tbl.rows.length;
		row=row-2;
		/*alert(row);
		return;*/
		for(i=0;i<=row;i++)
		{
			kd_barang=document.getElementById('kd_brg_'+i).value;
			kd_anggaran=document.getElementById('kd_angrn_'+i).value;
			jmdl_dimint=document.getElementById('jmlhDiminta_'+i).value;
			if(kd_barang==''||kd_anggaran==''||jmdl_dimint=='')
			{
				alert('Please Complete and Save The Data')
				break;
			}
			else
			{
				a=confirm('Apakah Anda Ingin Mengajukan PP Ini');
				if(a)
				{
				     	nopp=document.getElementById('detail_kode').value;
						stat=0;
						document.getElementById('list_pp').style.display='none';
						document.getElementById('form_pp').style.display='none';
						document.getElementById('persetujuan').style.display='block';
						document.getElementById('fnopp').value=nopp;
						document.getElementById('cls_stat').value=stat;				
				}
				else
				{
					clear_all_data();
					displayList();
				}
			}
		}
	}
	
 }
 function frm_ajun(nopp,stat)
 {
	if(stat>0)
	{
		alert('Waiting Approval Can`t Edit or Delete');
		return;
	}
	else
	{
		document.getElementById('list_pp').style.display='none';
		document.getElementById('form_pp').style.display='none';
		document.getElementById('persetujuan').style.display='block';
		document.getElementById('fnopp').value=nopp;
		document.getElementById('cls_stat').value=stat;
	}
 }
 stat_inputb=0;
function reset_data()
{
	op = document.getElementById('method');
	if(stat_inputb==0)
	{
		clear_all_data();
	}
	else if(stat_inputb==1)
	{
		rnopp = document.getElementById('detail_kode');
		rnopp = rnopp.value;
		param='rnopp='+rnopp;
		param+='&method=delete';
		tujuan='log_slave_save_log_pp.php';
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
								document.getElementById('contain').innerHTML=con.responseText;
								//alert('Delete Data Succeed');
								clear_all_data();
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
function displayFormInput()
{
	clear_all_data();
	document.getElementById('method').value='insert';
	document.getElementById('form_pp').style.display='block';
	document.getElementById('dtl_pem').style.display='block';
	document.getElementById('list_pp').style.display='none';
	document.getElementById('tmbl_all').style.display='none';
        stat_inputb=1;
}
function displayList()
{
	document.getElementById('form_pp').style.display='none';
	document.getElementById('list_pp').style.display='block';	
	document.getElementById('persetujuan').style.display='none';
	stat_input=0;
	loadEmployeeList();
}
function reset_data_setuju()
{
	document.getElementById('persetujuan').style.display = 'none';
	document.getElementById('form_pp').style.display = 'none';
	document.getElementById('fnopp').value='';
	document.getElementById('karywn_id').value='';
	document.getElementById('list_pp').style.display='block';
}
function save_persetujuan()
{
	nopp=trim(document.getElementById('fnopp').value);
	stat=trim(document.getElementById('cls_stat').value);
	kary=trim(document.getElementById('karywn_id').value);
	if(kary=='')
	{
		alert('Please Check Again Your Selection');
	}
	else
	{
		method='insert_persetujuan';
		param='rnopp='+nopp+'&usr_id='+kary+'&method='+method+'&stat='+stat;
		tujuan='log_slave_save_log_pp.php';
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
									document.getElementById('contain').innerHTML=con.responseText;
									displayList();
									
								}
							}
							else {
								busy_off();
								error_catch(con.status);
							}
					  }	
			 }
			 //post_response_text(tujuan, param, respog);
			var answer =confirm('Are you sure?');
			if (answer){
			post_response_text(tujuan, param, respog);
			}
			else{
			reset_data_setuju();
			}
	}
		
}
function cariNopp()
{
	txtSearch=trim(document.getElementById('txtsearch').value);
	tglCari=trim(document.getElementById('tgl_cari').value);
	met=document.getElementById('method');
	met=met.value='cari_pp';
	met=trim(met);
	param='txtSearch='+txtSearch+'&tglCari='+tglCari+'&method='+met;
	tujuan='log_slave_get_user_id_log_pp.php';
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
								document.getElementById('contain').innerHTML=con.responseText;
								
								
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
function loadEmployeeList()
{
   	met=document.getElementById('method');
	met=met.value='cari_pp';
	param='method='+met;
	tujuan = 'log_slave_get_user_id_log_pp.php';
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