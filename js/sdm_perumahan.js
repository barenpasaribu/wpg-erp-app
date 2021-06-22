// JavaScript Document

//save section
function save_header()
{
	kode_org=document.getElementById('kode_org').value;
	blok=document.getElementById('blok_rmh').value;
	normh=document.getElementById('no_rmh').value;
	tipe_rmh=document.getElementById('tipe_rmh');
        tipe_rmh=tipe_rmh.options[tipe_rmh.selectedIndex].value;
	thn_bangun=document.getElementById('thn_buat_rmh').value;
	kndsi_rmh=document.getElementById('kndsi_rmh').value;
	note=trim(document.getElementById('ket_rmh').value);
	almt=trim(document.getElementById('almt_rmh').value);
	kompleks=trim(document.getElementById('nm_kompleks').value);
	if((blok=='')||(normh==''))
	{
		alert('Please Complete The Form');
		return;
	}
	as=document.getElementById('kode_org');
	if(as.disabled==true)
	{
		met='update_headher';
	}
	else
	{
		met='save_header';
	}
	
	param='kd_org='+kode_org+'&blok='+blok+'&no_rmh='+normh+'&tipermh='+tipe_rmh;
	param+='&thnbgn='+thn_bangun+'&kndsi_rmh='+kndsi_rmh+'&note='+note+'&almt_rmh='+almt;
	param+='&method='+met+'&kmplk='+kompleks;
	tujuan='log_slave_sdm_perumahan.php';
	if(confirm("Are You Sure Want Save This Data"))
	{
		post_response_text(tujuan, param, respog);
	}
	else
	{
		return;
	}
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					//document.getElementById('contain').innerHTML=con.responseText;
					/*aset(kode_org,normh,blok);
					penghuni(kode_org,normh,blok);*/
					load_data();
					clear_save_form();			
					/*document.getElementById('save_kepala').disabled=true;
					document.getElementById('cancel_kepala').disabled=true;*/
				
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}	
}
function save_asset()
{
	//alert('masuk');
	as=document.getElementById('kode_org_asset');
	if(as.disabled==true)
	{
		met='update_asset';
	}
	else
	{
		met='save_asset';
	}
	blok_asset=document.getElementById('blok_rmh_asset').value;
	normh_asset=document.getElementById('no_rmh_asset').value;
	kodeorg_asset=document.getElementById('kode_org_asset').value;
	kode_asset=document.getElementById('kode_asset').value;
	param='blok='+blok_asset+'&no_rmh='+normh_asset+'&kd_org='+kodeorg_asset;
	param+='&method='+met+'&kd_asset='+kode_asset;
	tujuan='log_slave_sdm_perumahan.php';
	//alert(param);
	if(confirm("Are You Sure Want Save This Data"))
	{
	post_response_text(tujuan, param, respog);
	}
	else
	{
		return;
	}
	function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						clear_save_form_asset();
						load_data_asset();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
	
	
}
function save_penghuni()
{
	//alert('masuk');
	as=document.getElementById('kode_org_penghuni');
	if(as.disabled==true)
	{
		met='update_penghuni';
	}
	else
	{
		met='save_penghuni';
	}
	blok_penghuni=document.getElementById('blok_rmh_penghuni').value;
	normh_penghuni=document.getElementById('no_rmh_penghuni').value;
	kodeorg_penghuni=document.getElementById('kode_org_penghuni').value;
	kode_kary=document.getElementById('kode_karyawan').value;
	
	param='blok='+blok_penghuni+'&no_rmh='+normh_penghuni+'&kd_org='+kodeorg_penghuni;
	param+='&method='+met+'&kd_kary='+kode_kary;
	tujuan='log_slave_sdm_perumahan.php';
	//alert(param);
	if(confirm("Are You Sure Want Save This Data"))
	{
		post_response_text(tujuan, param, respog);
	}
	else
	{
		return;
	}
	function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						clear_save_form_penghuni();
						load_data_penghuni();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
	
	
}

//load data section
function load_data()
{
	//alert("masuk");
	kode_org=document.getElementById('kode_org').value;
	param='method=load_new_data'+'&code_org='+kode_org;
	//alert(param);
	tujuan = 'log_slave_sdm_perumahan.php';
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
function load_data_asset()
{
	//alert("masuk");
	kd_org=document.getElementById('kode_org_asset').value;
	param='method=load_new_data_asset'+'&code_org='+kd_org;
	tujuan = 'log_slave_sdm_perumahan.php';
	//alert(param);
	post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('containasset').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}

//edit section
function fillField(kodeOrg,blokHeadher,normhHeadher)
{
	code_org=kodeOrg;
	blok=blokHeadher;
	no_rmh=normhHeadher;
	param='method=getData'+'&code_org='+code_org+'&blok='+blok+'&no_rmh='+no_rmh;
	tujuan='log_slave_sdm_perumahan.php';
	post_response_text(tujuan, param, respog);			
        function respog(){
            if (con.readyState == 4) {
                if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                            alert('ERROR TRANSACTION,\n' + con.responseText);
                    }
                    else {
                        //document.getElementById('containasset').innerHTML=con.responseText;
                        ar=con.responseText.split("###");
                        document.getElementById('kode_org').value=kodeOrg;
                        document.getElementById('blok_rmh').value=blokHeadher;
                        document.getElementById('no_rmh').value=normhHeadher;						
                        document.getElementById('thn_buat_rmh').value=ar[1];
                        document.getElementById('ket_rmh').value=ar[3];
                        document.getElementById('almt_rmh').value=ar[4];

                        jk=document.getElementById('kndsi_rmh');
                            for(x=0;x<jk.length;x++)
                            {
                                    if(jk.options[x].value==ar[2])
                                    {
                                            jk.options[x].selected=true;
                                    }
                            }
                        jk=document.getElementById('nm_kompleks');
                            for(x=0;x<jk.length;x++)
                            {
                                    if(jk.options[x].value==ar[5])
                                    {
                                            jk.options[x].selected=true;
                                    }
                            }
                        jk=document.getElementById('tipe_rmh');
                            for(x=0;x<jk.length;x++)
                            {
                                    if(jk.options[x].value==ar[0])
                                    {
                                            jk.options[x].selected=true;
                                    }
                            }

                        document.getElementById('kode_org').disabled=true;
                        document.getElementById('blok_rmh').disabled=true;
                        document.getElementById('no_rmh').disabled=true;
                        document.getElementById('nm_kompleks').disabled=true;
                    }
                }
                else {
                    busy_off();
                    error_catch(con.status);
                }
            }
        }	
	
}
function fillFieldAsset(kodeOrg,blokHeadher,normhHeadher,asset)
{
	get_normh(blokHeadher,normhHeadher,kodeOrg);
	document.getElementById('kode_org_asset').disabled=true;
	document.getElementById('blok_rmh_asset').disabled=true;
	document.getElementById('no_rmh_asset').disabled=true;
	document.getElementById('kode_org_asset').value=kodeOrg;
	document.getElementById('blok_rmh_asset').value=blokHeadher;
	//document.getElementById('no_rmh_asset').value=normhHeadher;
	document.getElementById('kode_asset').value=asset;

}

function fillFieldPenghuni(kodeOrg,blokHeadher,normhHeadher,penghuni)
{
	get_normh_penghuni(blokHeadher,normhHeadher,kodeOrg);
	document.getElementById('kode_org_penghuni').value=kodeOrg;
	document.getElementById('blok_rmh_penghuni').value=blokHeadher;
	//document.getElementById('no_rmh_penghuni').value=normhHeadher;
	document.getElementById('kode_karyawan').value=penghuni;
	document.getElementById('kode_org_penghuni').disabled=true;
	document.getElementById('blok_rmh_penghuni').disabled=true;
	document.getElementById('no_rmh_penghuni').disabled=true;
}

function load_data_penghuni()
{
	//alert("masuk");
	kd_org=document.getElementById('kode_org_penghuni').value;
	param='method=load_new_data_penghuni'+'&code_org='+kd_org;
	tujuan = 'log_slave_sdm_perumahan.php';
	post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('containpenghuni').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}
//paging section 
function cariBast(num,kodeorg)
{
		param='method=load_new_data';
		param+='&page='+num+'&code_org='+kodeorg;
		tujuan = 'log_slave_sdm_perumahan.php';
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
function cariBastAsset(num,kodeorg)
{
		param='method=load_new_data_asset';
		param+='&page='+num+'&code_org='+kodeorg;
		tujuan = 'log_slave_sdm_perumahan.php';
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('containasset').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}
function cariBastPenghuni(num,kodeorg)
{
		param='method=load_new_data_penghuni';
		param+='&page='+num+'&code_org='+kodeorg;
		tujuan = 'log_slave_sdm_perumahan.php';
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('containpenghuni').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}

//get_data
function aset(kode_org,no_rmh_blok)
{
	document.getElementById('blok_rmh_asset').value=blok;
	document.getElementById('no_rmh_asset').value=normh;
	document.getElementById('kode_org_asset').value=kode_org;
	document.getElementById('blok_rmh_asset').disabled=true;
	document.getElementById('no_rmh_asset').disabled=true;
	document.getElementById('kode_org_asset').disabled=true;
}
function penghuni(kode_org,no_rmh_blok)
{
	document.getElementById('blok_rmh_penghuni').value=blok;
	document.getElementById('no_rmh_penghuni').value=normh;
	document.getElementById('kode_org_penghuni').value=kode_org;
	document.getElementById('blok_rmh_penghuni').disabled=true;
	document.getElementById('no_rmh_penghuni').disabled=true;
	document.getElementById('kode_org_penghuni').disabled=true;
}

function get_blok()
{
	/*asset=document.getElementById('kode_org_asset');
	penghuni=document.getElementById('kode_org_penghuni');
	if(penghuni.selectedIndex!='')
	{
		alert('penghuni');
		alert(asset.value);
		kd_org=penghuni.value;
		asset.selectedIndex='';
		//penghuni.selectedIndex='';
		id_contianer='blok_rmh_penghuni';
	}
	else if(asset.selectedIndex!='')
	{
		penghuni.selectedIndex='';
		kd_org=asset.value;
		id_contianer='blok_rmh_asset';
	}
	*/
	kd_org=document.getElementById('kode_org_asset').value;
	tujuan='log_get_data_header_rumah.php'
	param='method=get_blok'+'&code_org='+kd_org;
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
						//alert(con.responseText);
						document.getElementById('blok_rmh_asset').innerHTML=con.responseText;
						load_data_asset();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}
}

function get_blok_penghuni()
{
	kd_org=document.getElementById('kode_org_penghuni').value;
	tujuan='log_get_data_header_rumah.php'
	param='method=get_blok'+'&code_org='+kd_org;
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
						//alert(con.responseText);
					    document.getElementById('blok_rmh_penghuni').innerHTML = con.responseText;
					    //document.getElementById('kode_karyawan').innerHTML = con.responseText;
					    //document.getElementById('kode_org_penghuni').value = kd_org;
					    load_data_penghuni();
					    load_data_kary();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}
}
//=======================enhance
function load_data_kary() {
    //alert("masuk");
    kd_org = document.getElementById('kode_org_penghuni').value;
    param = 'method=get_kary' + '&code_org=' + kd_org;
    tujuan = 'log_get_data_header_rumah.php';
    post_response_text(tujuan, param, respog);
    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('kode_karyawan').innerHTML = con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function get_normh(blok_rmh,no_rmh,kode_org)
{		
	//alert(blok_rmh);
	if((no_rmh!=0)&&(blok_rmh!=0))
	{
		normh=no_rmh;
		tujuan='log_get_data_header_rumah.php'
		param='method=get_normh'+'&rmh_no='+normh+'&kode_blok='+blok_rmh+'&code_org='+kode_org;
	}
	else if((no_rmh==0)&&(blok_rmh==0))
	{
		kode_org=document.getElementById('kode_org_asset').value;
		kd_blok=document.getElementById('blok_rmh_asset').value;
		tujuan='log_get_data_header_rumah.php'
		param='method=get_normh'+'&kode_blok='+kd_blok+'&code_org='+kode_org;
		
	}
	//alert(param);
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
						//alert(con.responseText);
						document.getElementById('no_rmh_asset').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}
}
function get_normh_penghuni(blok_rmh,no_rmh,kode_org)
{
	if((no_rmh!=0)&&(blok_rmh!=0))
	{
		normh=no_rmh;
		tujuan='log_get_data_header_rumah.php'
		param='method=get_normh'+'&rmh_no='+normh+'&kode_blok='+blok_rmh+'&code_org='+kode_org;
	}
	else if((no_rmh==0)&&(blok_rmh==0))
	{
		kode_org=document.getElementById('kode_org_penghuni').value;
		kd_blok=document.getElementById('blok_rmh_penghuni').value;
		tujuan='log_get_data_header_rumah.php'
		param='method=get_normh'+'&kode_blok='+kd_blok+'&code_org='+kode_org;
	} 
	//tujuan='log_get_data_header_rumah.php'
//	param='method=get_normh'+'&kode_blok='+kd_blok;
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
						//alert(con.responseText);
						document.getElementById('no_rmh_penghuni').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}
}


function clear_save_form()
{
	document.getElementById('blok_rmh').value='';
	document.getElementById('no_rmh').value='';
	document.getElementById('thn_buat_rmh').value='';
	document.getElementById('ket_rmh').value='';
	document.getElementById('almt_rmh').value='';
	document.getElementById('blok_rmh').disabled=false;
	document.getElementById('no_rmh').disabled=false;
	document.getElementById('kode_org').disabled=false;
        document.getElementById('nm_kompleks').disabled=false;
}
function clear_save_form_asset()
{
	document.getElementById('blok_rmh_asset').value='';
	document.getElementById('no_rmh_asset').value='';
	document.getElementById('kode_asset').value='';
	document.getElementById('blok_rmh_asset').disabled=false;
	document.getElementById('no_rmh_asset').disabled=false;
	document.getElementById('kode_org_asset').disabled=false;
}
function clear_save_form_penghuni()
{
	document.getElementById('blok_rmh_penghuni').value='';
	document.getElementById('no_rmh_penghuni').value='';
	document.getElementById('kode_karyawan').value='';
	document.getElementById('blok_rmh_penghuni').disabled=false;
	document.getElementById('no_rmh_penghuni').disabled=false;
	document.getElementById('kode_org_penghuni').disabled=false;
}
function delHeader(kode_org,blok_rmh,no_rmh)
{
	kdorg=kode_org;
	hblok=blok_rmh;
	rmh_no=no_rmh;
	param='kd_org='+kdorg+'&blok='+hblok+'&no_rmh='+rmh_no+'&method=delHeader';
	tujuan='log_slave_sdm_perumahaan.php';
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
						load_data();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}
}
function delHeader(kode_org,blok_rmh,no_rmh)
{
	kdorg=kode_org;
	hblok=blok_rmh;
	rmh_no=no_rmh;
	param='kd_org='+kdorg+'&blok='+hblok+'&no_rmh='+rmh_no+'&method=delHeader';
	tujuan='log_slave_sdm_perumahan.php';
	if(confirm("Are You Sure Want Delete This Data?"))
	{
		post_response_text(tujuan, param, respog);		
	}
	else
	{
		clear_save_form();
	}
	//post_response_text(tujuan, param, respog);		
	function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						//alert(con.responseText);
						load_data();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}
}
function delAsset(kode_org,blok_rmh,no_rmh,asset_kode)
{
	kdorg=kode_org;
	hblok=blok_rmh;
	rmh_no=no_rmh;
	param='kd_org='+kdorg+'&blok='+hblok+'&no_rmh='+rmh_no+'&kd_asset='+asset_kode+'&method=delAsset';
	//alert(param);
	tujuan='log_slave_sdm_perumahan.php';
	if(confirm("Are You Sure Want Delete This Data?"))
	{
		post_response_text(tujuan, param, respog);		
	}
	else
	{
		clear_save_form_asset();
	}
	function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						//alert(con.responseText);
						load_data_asset();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}
}
function delPenghuni(kode_org,blok_rmh,no_rmh,penghuni_kode)
{
	kdorg=kode_org;
	hblok=blok_rmh;
	rmh_no=no_rmh;
	param='kd_org='+kdorg+'&blok='+hblok+'&no_rmh='+rmh_no+'&kd_kary='+penghuni_kode+'&method=delPenghuni';
	tujuan='log_slave_sdm_perumahan.php';
	if(confirm("Are You Sure Want Delete This Data?"))
	{
		post_response_text(tujuan, param, respog);		
	}
	else
	{
		clear_save_form_penghuni();
	}		
	function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						//alert(con.responseText);
						load_data_penghuni();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}
}