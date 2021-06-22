var showPerPage = 10;

function getValue(id) {
    var tmp = document.getElementById(id);
    
    if(tmp) {
        if(tmp.options) {
            return tmp.options[tmp.selectedIndex].value;
        } else if(tmp.nodeType=='checkbox') {
            if(tmp.checked==true) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return tmp.value;
        }
    } else {
        return false;
    }
}

/* Search
 * Filtering Data
 */
function searchTrans() {
    var notrans = document.getElementById('sNoTrans'),
		rupiah = document.getElementById('sRupiah'),
		tanggal = getValue('sTanggal'),
		noakun = getValue('sAkun');
		supp = getValue('sSup');
    if(tanggal!='') {
        var tmpTanggal = tanggal.split('-');
        var tanggalR = tmpTanggal[2]+"-"+tmpTanggal[1]+"-"+tmpTanggal[0];
    } else {
        var tanggalR = '';
    }
    var where = '[["notransaksi","'+notrans.value+'"],["tanggal","'+tanggalR+'"],["jumlah","'+remove_comma_var(rupiah.value)+'"],["noakun","'+noakun+'"],["supp","'+supp+'"]]';
    
    goToPages(1,showPerPage,where);
}

/* Paging
 * Paging Data
 */
function defaultList() {
    document.getElementById('sNoTrans').value='';
    document.getElementById('sAkun').value='';
    document.getElementById('sTanggal').value='';
    document.getElementById('sRupiah').value='';
    document.getElementById('sSup').value='';
    goToPages(1,showPerPage);
}

function goToPages(page,shows,where) {
    if(typeof where != 'undefined') {
        var newWhere = where.replace(/'/g,'"');
    }
    var workField = document.getElementById('workField');
    var param = "page="+page;
    param += "&shows="+shows+"&tipe=KB";
    if(typeof where != 'undefined') {
        param+="&where="+newWhere;
    }
    
	//alert(param);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    workField.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_kasbank.php?proses=showHeadList', param, respon);
}

function choosePage(obj,shows,where) {
    var pageVal = obj.options[obj.selectedIndex].value;
    goToPages(pageVal,shows,where);
}

/* Halaman Manipulasi Data
 * Halaman add, edit, delete
 */
function showAdd() {
    var workField = document.getElementById('workField');
    var param = "";
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    workField.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_kasbank.php?proses=showAdd', param, respon);
}

function showEditFromAdd() {
    var workField = document.getElementById('workField');
    var trans = document.getElementById('notransaksi');
    var param = "notransaksi="+trans.value+"&kodeorg="+getValue('kodeorg')+
        "&noakun="+getValue('noakun2a')+"&tipetransaksi="+getValue('tipetransaksi');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    workField.innerHTML = con.responseText;
                    showDetail();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_kasbank.php?proses=showEdit', param, respon);
}

function showEdit(num) {
    var workField = document.getElementById('workField');
    var trans = document.getElementById('notransaksi_'+num).getAttribute('value');
    var kodeorg = document.getElementById('kodeorg_'+num).getAttribute('value');
    var noakun = document.getElementById('noakun_'+num).getAttribute('value');
    var tipetransaksi = document.getElementById('tipetransaksi_'+num).getAttribute('value');
    var param = "numRow="+num+"&notransaksi="+trans+"&kodeorg="+
        kodeorg+"&noakun="+noakun+"&tipetransaksi="+tipetransaksi;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    workField.innerHTML = con.responseText;
                    showDetail();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_kasbank.php?proses=showEdit', param, respon);
}

/* Manipulasi Data
 * add, edit, delete
 */
function addDataTable() {
    var hutangunit='';
    var pemilikhutang=getValue('pemilikhutang');
    var noakunhutang=getValue('noakunhutang');
    if(document.getElementById("hutangunit").checked==true){
        hutangunit='1';
    }else{
        pemilikhutang='';
        noakunhutang='';
    }
    var param = "notransaksi="+getValue('notransaksi')+"&noakun="+getValue('noakun2a');
    param += "&tanggal="+getValue('tanggal')+"&matauang="+getValue('matauang');
    param += "&kurs="+getValue('kurs')+"&tipetransaksi="+getValue('tipetransaksi');
    param += "&jumlah="+getValue('jumlah')+"&cgttu="+getValue('cgttu');
    param += "&keterangan="+getValue('keterangan')+"&yn="+getValue('yn')+"&kodeorg="+getValue('kodeorg')+"&nogiro="+getValue('nogiro');
    param+= "&hutangunit="+hutangunit;
    param+= "&pemilikhutang="+pemilikhutang;
    param+= "&noakunhutang="+noakunhutang;
    param+= "&diperiksa="+getValue('diperiksa');
    param+= "&disetujui="+getValue('disetujui');
    param+= "&diterima="+getValue('diterima');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    //alert('Added Data Header');
					document.getElementById('notransaksi').value = con.responseText;
                    showEditFromAdd();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_kasbank.php?proses=add', param, respon);
}

function editDataTable() {
    var hutangunit='';
    var pemilikhutang=getValue('pemilikhutang');
    var noakunhutang=getValue('noakunhutang');
    if(document.getElementById("hutangunit").checked==true){
        hutangunit='1';
    }else{
        pemilikhutang='';
        noakunhutang='';
    }
    var param = "notransaksi="+getValue('notransaksi')+"&noakun="+getValue('noakun2a');
    param += "&tanggal="+getValue('tanggal')+"&matauang="+getValue('matauang');
    param += "&kurs="+getValue('kurs')+"&tipetransaksi="+getValue('tipetransaksi');
    param += "&jumlah="+getValue('jumlah')+"&cgttu="+getValue('cgttu');
    param += "&keterangan="+getValue('keterangan')+"&yn="+getValue('yn')+"&kodeorg="+getValue('kodeorg')+"&nogiro="+getValue('nogiro');
    param+= "&oldNoakun="+getValue('oldNoakun');
    param+= "&hutangunit="+hutangunit;
    param+= "&pemilikhutang="+pemilikhutang;
    param+= "&noakunhutang="+noakunhutang;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    alert(con.responseText);
                    defaultList();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_kasbank.php?proses=edit', param, respon);
}

/*
 * Detail
 */
function showDetail() {
    var detailField = document.getElementById('detailField');
    var notrans = document.getElementById('notransaksi').value;
    var param = "notransaksi="+notrans+"&kodeorg="+getValue('kodeorg')+"&tipetransaksi="+
        getValue('tipetransaksi')+"&noakun="+getValue('noakun2a')+"&jumlahHeader="+getValue('jumlah').replace(/,/g,'');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    detailField.innerHTML = con.responseText;
					// var res = JSON.parse(con.responseText);
					// detailField.innerHTML = res.page;
					theFT.afterCrud='afterCrud';
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_kasbank_detail.php?proses=showDetail', param, respon);
}

function pilihhutang(){
//    var kodeorg=getValue('kodeorg');
//    if(kodeorg.substring(2, 4)=='HO'){
//        
//    }else{
//        alert('Pilihan hanya untuk HO');
//        document.getElementById('hutangunit').checked=false;
//        document.getElementById('pemilikhutang').disabled=true;
//        document.getElementById('noakunhutang').disabled=true;
//        exit();
//    }
    var centang = document.getElementById('hutangunit');
    if(centang.checked!=true){
        document.getElementById('pemilikhutang').disabled=true;
        document.getElementById('noakunhutang').disabled=true;
    }else{
        document.getElementById('pemilikhutang').disabled=false;
        document.getElementById('noakunhutang').disabled=false;        
    }
}

//function gantiValue(obj){
//    if(obj.value==1)
//        obj.value=0; else obj.value=1;
//}

function deleteData(num) {
	
	
	
    var notrans = document.getElementById('notransaksi_'+num).getAttribute('value');
    var kodeorg = document.getElementById('kodeorg_'+num).getAttribute('value');
    var noakun = document.getElementById('noakun_'+num).getAttribute('value');
    var tipetransaksi = document.getElementById('tipetransaksi_'+num).getAttribute('value');
    var param = "notransaksi="+notrans+"&kodeorg="+kodeorg+"&noakun="+noakun+
        "&tipetransaksi="+tipetransaksi;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    var tmp = document.getElementById('tr_'+num);
                    tmp.parentNode.removeChild(tmp);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
	if(confirm('Are You Sure Delete this data ??'))
    post_response_text('keu_slave_kasbank.php?proses=delete', param, respon);
}

/* Posting Data
 */
 
 
function postingData(numRow)
{
	  var notrans = document.getElementById('notransaksi_'+numRow).getAttribute('value'),
		kodeorg = document.getElementById('kodeorg_'+numRow).getAttribute('value'),
		noakun = document.getElementById('noakun_'+numRow).getAttribute('value'),
		tipetransaksi = document.getElementById('tipetransaksi_'+numRow).getAttribute('value');
	content= "<div id=formPost  style=\"height:280px;width:800px;\"></div>";
	
	//content+="<div id=formCariBarang></div>";

	title='posting';
	width='800';
	height='275';
	showDialog1(title,content,width,height,'event');	
	getformPost(notrans,kodeorg,noakun,tipetransaksi,numRow);
}

function getformPost(notrans,kodeorg,noakun,tipetransaksi,numRow)
{
	param='method=getFormPost'+'&notrans='+notrans+'&kodeorg='+kodeorg+'&noakun='+noakun+'&tipetransaksi='+tipetransaksi+'&numRow='+numRow;
	//alert(param);
	tujuan = 'keu_slave_kasbank_formPost.php';
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
									document.getElementById('formPost').innerHTML=con.responseText;
							}
					}
					else {
							busy_off();
							error_catch(con.status);
					}
			}
	} 
		
} 
 
 
 
 
 
 
 
// postingData(numRow)
 function savePosting(notrans,kodeorg,noakun,tipetransaksi,numRow) {
    
	nobayar=document.getElementById('nobayar').value;
	tglpost=document.getElementById('tglpost').value;	
		
		
		param = "notransaksi="+notrans+"&kodeorg="+kodeorg+"&noakun="+noakun+
        "&tipetransaksi="+tipetransaksi+"&nobayar="+nobayar+"&tglpost="+tglpost;
		
		
    //alert(param);
	if(nobayar=='') {
		alert('Payment Number must be filled');
		return;
	}
	if(tglpost=='') {
		alert('Date must be filled');
		return;
	}
	if(confirm('Posting '+notrans+'\nThis transaction will released. are you sure?')) {
           post_response_text('keu_slave_kasbank_posting.php', param, respon);
        }
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    //alert('Posting Berhasil');
                    x=document.getElementById('tr_'+numRow);
					//console.log(x.cells[8].firstChild);
                    //x.cells[8].firstChild.disabled=true;
					
					x.cells[8].innerHTML=nobayar;
                    x.cells[9].innerHTML='';
					x.cells[10].innerHTML='';
                    x.cells[11].innerHTML="<img class=\"zImgOffBtn\" title=\"Posting\" src=\"images/skyblue/posted.png\">";
                    //javascript:location.reload(true);	
			closeDialog();		
					
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    
}

function printPDF(ev) {
    // Prep Param
    param = "proses=pdf";
    
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='keu_slave_kasbank_print.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '50px';
    dialog.style.left = '15%';
}

function detailPDF(numRow,ev) {
    // Prep Param
    var notransaksi = document.getElementById('notransaksi_'+numRow).getAttribute('value');
    var noakun = document.getElementById('noakun_'+numRow).getAttribute('value');
    var tipetransaksi = document.getElementById('tipetransaksi_'+numRow).getAttribute('value');
    var kodeorg = document.getElementById('kodeorg_'+numRow).getAttribute('value');
    param = "proses=pdf&notransaksi="+notransaksi+"&kodeorg="+kodeorg+
        "&tipetransaksi="+tipetransaksi+"&noakun="+noakun;
    
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='keu_slave_kasbank_print_detail.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '50px';
    dialog.style.left = '15%';
}

function tampilDetail(numRow,ev)
{
    var notransaksi = document.getElementById('notransaksi_'+numRow).getAttribute('value');
    var noakun = document.getElementById('noakun_'+numRow).getAttribute('value');
    var tipetransaksi = document.getElementById('tipetransaksi_'+numRow).getAttribute('value');
    var kodeorg = document.getElementById('kodeorg_'+numRow).getAttribute('value');
   param = "proses=html&notransaksi="+notransaksi+"&kodeorg="+kodeorg+
        "&tipetransaksi="+tipetransaksi+"&noakun="+noakun;
        title="Data Detail";
        showDialog1(title,"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='keu_slave_kasbank_print_detail.php?"+param+"'></iframe>",'800','400',ev);	
        var dialog = document.getElementById('dynamic1');
        dialog.style.top = '50px';
        dialog.style.left = '15%';
}
/* Update No Urut di halaman absensi
 */
function updNoUrut() {
    var tabBody = document.getElementById('mTabBody');
    var nourut = document.getElementById('nourut');
    var maxNum = 0;
    
    if(tabBody.childNodes.length>0) {
        for(i=0;i<tabBody.childNodes.length;i++) {
            var tmp = document.getElementById('nourut_'+i);
            if(tmp.innerHTML > maxNum) {
                maxNum = tmp.innerHTML;
            }
        }
    }
    nourut.value = parseInt(maxNum)+1;
}

/* Update Field Aktif berdasarkan akun yang dipilih
 */
function updFieldAktif() {
    var id='ftPrestasi_';
    var noakun = document.getElementById(id+'noakun').childNodes;
    var kodekegiatan = document.getElementById(id+'kodekegiatan').childNodes;
    var kodeasset = document.getElementById(id+'kodeasset').childNodes;
    var kodebarang = document.getElementById(id+'kodebarang').childNodes;
    var nik = document.getElementById(id+'nik').childNodes;
    var kodecustomer = document.getElementById(id+'kodecustomer').childNodes;
    var kodesupplier = document.getElementById(id+'kodesupplier').childNodes;
    var kodevhc = document.getElementById(id+'kodevhc').childNodes;
    var param = "noakun="+noakun[0].options[noakun[0].selectedIndex].value;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    var res = con.responseText;
                    
                    // Kegiatan
                    if(res[0]==0) {
                        kodekegiatan[0].setAttribute('disabled','disabled');
                        kodekegiatan[0].selectedIndex=0;
                    } else {
                        kodekegiatan[0].removeAttribute('disabled');
                    }
                    
                    // Asset
                    if(res[1]==0) {
                        kodeasset[0].setAttribute('disabled','disabled');
                        kodeasset[0].selectedIndex=0;
                    } else {
                        kodeasset[0].removeAttribute('disabled');
                    }
                    
                    // Barang
                    if(res[2]==0) {
                        kodebarang[0].setAttribute('disabled','disabled');
                        kodebarang[2].setAttribute('disabled','disabled');
                        kodebarang[3].setAttribute('disabled','disabled');
                        kodebarang[0].value='';
                        kodebarang[2].value='';
                    } else {
                        kodebarang[0].removeAttribute('disabled');
                        kodebarang[2].removeAttribute('disabled');
                        kodebarang[3].removeAttribute('disabled');
                    }
                    
                    // Karyawan
                    if(res[3]==0) {
                        nik[0].setAttribute('disabled','disabled');
                        nik[0].selectedIndex=0;
                    } else {
                        nik[0].removeAttribute('disabled');
                    }
                    
                    // Customer
                    if(res[4]==0) {
                        kodecustomer[0].setAttribute('disabled','disabled');
                        kodecustomer[0].selectedIndex=0;
                    } else {
                        kodecustomer[0].removeAttribute('disabled');
                    }
                    
                    // Supplier
                    if(res[5]==0) {
                        kodesupplier[0].setAttribute('disabled','disabled');
                        kodesupplier[0].selectedIndex=0;
                    } else {
                        kodesupplier[0].removeAttribute('disabled');
                    }
                    
                    // Kendaraan
                    if(res[6]==0) {
                        kodevhc[0].setAttribute('disabled','disabled');
                        kodevhc[0].selectedIndex=0;
                    } else {
                        kodevhc[0].removeAttribute('disabled');
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_kasbank_detail.php?proses=updField', param, respon);
}

//jamhari
function searchNopo(title,content,ev)
{
        //isi=document.getElementById('tipeinvoice').options[document.getElementById('tipeinvoice').selectedIndex].value;
        //content=content+"<input type='hidden' id='jnsInvoice' value="+isi+">";
	width='850';
	height='620';
	showDialog1(title,content,width,height,ev);
        getForminvoice();
	//alert('asdasd');
}
function getForminvoice()
{
        param='';
        tujuan='keu_slave_kasbank_detail.php';
        post_response_text(tujuan+'?'+'proses=getForminvoice', param, respog);
	
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
							document.getElementById('formPencariandata').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }
	 }
} 
function findNoinvoice()
{
	txt=trim(document.getElementById('no_brg').value);
        idSupplier=document.getElementById('supplierIdcr').value;
		nopocr=document.getElementById('nopocr').value;
	param='txtfind='+txt;
	
        if(idSupplier!='')
        {
            param+='&idSupplier='+idSupplier
        }
		if(nopocr!='')
		{
			param+='&nopocr='+nopocr
		}
        //alert(param);
        tujuan='keu_slave_kasbank_detail.php';
        if((txt=='')&&(idSupplier=='')&&(nopocr==''))
        {
            alert("Field can't obligatory");
        } else post_response_text(tujuan+'?'+'proses=getInvoice', param, respog);
        
	
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
							document.getElementById('container2').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }
	 }
}
function setPo(np,nilai,akn,ket,supp,nopo)
{
    document.getElementById('keterangan1').value=np;
//    document.getElementById('jumlah').value='';
    ds=document.getElementById('ftPrestasi_jumlah');
    ds.childNodes[0].value=nilai;
   // document.getElementById('noakun').value=akn;
    document.getElementById('keterangan2').value=nopo;
    l=document.getElementById('noakun');
    document.getElementById('nodok').value=nopo;
    
    for(a=0;a<l.length;a++)
        {
            if(l.options[a].value==akn)
                {
                    l.options[a].selected=true;
                }
        }
  l2=document.getElementById('kodesupplier');  
    for(a2=0;a2<l2.length;a2++)
        {
            if(l2.options[a2].value==supp)
                {
                    l2.options[a2].selected=true;
                }
        }
    closeDialog();
}

/**
 * afterCrud
 * Function execute after CRUD (create, read, update, delete)
 */
function afterCrud()
{
	var jumlah = getValue('jumlah').replace(/,/g,''),
		tBody = document.getElementById('tbody_ftPrestasi'),
		tBodyLen = tBody.childNodes.length,
		jmlHeadEl = document.getElementById('ftPrestasi_jumlah').firstChild,
		tbodyLen = document.getElementById('tbody_ftPrestasi').childNodes.length;
		jmlDetail = 0;
	
	// Count Jumlah Detail
	for(var i=0;i<tBodyLen;i++) {
		var tmp = document.getElementById('ftPrestasi_jumlah_'+i);
		if(tmp) {
			jmlDetail += parseFloat(tmp.getAttribute('value').replace(/,/g,''));
		}
	}
	
	// Remove comma from No Invoice
	for(var i=0;i<tBodyLen;i++) {
		var tmp = getById('ftPrestasi_keterangan1_'+i),
			tmpVal = tmp.innerHTML;
		tmpVal = tmpVal.replace(/,/g,'');
		tmp.innerHTML = tmpVal.replace('.00','');
	}
	
	// Update Result
	jmlHeadEl.value = parseFloat(jumlah) - parseFloat(jmlDetail);
}