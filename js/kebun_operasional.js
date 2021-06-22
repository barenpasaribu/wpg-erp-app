//$proses2=$_POST['proses'];

function hitungPremi()
{
    var hasilkerja="5000";
    document.getElementById('totalpremi').value=hasilkerja;
}

function cekKonversi () 

{

	kodekegiatan=document.getElementById('kodekegiatan').options[document.getElementById('kodekegiatan').selectedIndex].value;

	param='proses=cekKonversi'+'&kodekegiatan='+kodekegiatan;

	//alert(param);

	tujuan='kebun_slave_oprasional_hardaya.php';

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

				

				a=con.responseText;

				//if(a==1)

                if (a == 0)

				{

					

					document.getElementById('jjg').disabled=false;

					document.getElementById('ftAbsensi_jjg').firstChild.disabled=false;

					//document.getElementById('x').disabled=false;

					

				}

				else

				{

					document.getElementById('jjg').disabled=true;

					document.getElementById('jjg').value = 0;

					document.getElementById('ftAbsensi_jjg').firstChild.disabled=true;

					//document.getElementById('x').disabled=true;

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















//indra



function getUMR1 () 

{

	nik=document.getElementById('nik').value;

	jhk=document.getElementById('jhk').value;

	tanggal=document.getElementById('tanggal').value;

	notransaksi=document.getElementById('notransaksi').value;

	param='proses=getUMR1'+'&jhk='+jhk+'&nik='+nik+'&tanggal='+tanggal+'&notransaksi='+notransaksi;

	//alert(param);

	tujuan='kebun_slave_oprasional_hardaya.php';

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

				

				document.getElementById('ftAbsensi_umr').firstChild.value=con.responseText;

				//ftAbsensi_umr

				//document.getElementById('umr').value=con.responseText;

			}

		}

		else {

			busy_off();

			error_catch(con.status);

			

		}

      }	

     } 

}











function getKg()

{

	notransaksi=document.getElementById('notransaksi').value;

	tanggal=document.getElementById('tanggal').value;

	kodeorg=document.getElementById('kodeorg').value;

	

	kdAfd=document.getElementById('ftPrestasi_kodeorg').firstChild.value

	

	jjg=document.getElementById('jjg').value;

	param='proses=getKg'+'&jjg='+jjg+'&notransaksi='+notransaksi+'&tanggal='+tanggal+'&kdAfd='+kdAfd;

	

	//alert(param);

	tujuan='kebun_slave_oprasional_hardaya.php';

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

						//satu.value=remove_comma_var(satu.value);

						

						document.getElementById('hasilkerja').value=con.responseText; 

						

						

						

						

						/*var someNumber = 123.456;

						someNumber = someNumber.toFixed(2);*/	

                    }

                }

                else {

                    busy_off();

                    error_catch(con.status);

                    

                }

      }	

     } 

}





function getHasilKerja(row) {

	if(typeof row != 'undefined' && row==true) {

		jjg=document.getElementById('ftPrestasi_jjg_0').firstChild.value;

		kodeorg=document.getElementById('ftPrestasi_kodeorg_0').getAttribute('value');

	} else {

		jjg=document.getElementById('ftPrestasi_jjg').firstChild.value;

		kodeorgEL = document.getElementById('ftPrestasi_kodeorg').firstChild;

		kodeorg = kodeorgEL.options[kodeorgEL.selectedIndex].value;

	}

	notransaksi=document.getElementById('notransaksi').value;

	tanggal=document.getElementById('tanggal').value;

	param='proses=getHasilKerja'+'&jjg='+jjg+'&notransaksi='+notransaksi+'&tanggal='+tanggal+'&kodeorg='+kodeorg;

	//alert(param);

	tujuan='kebun_slave_oprasional_hardaya.php';

    post_response_text(tujuan, param, respog);    

    function respog()

    {

      if(con.readyState==4)

      {

        if (con.status == 200) {

			busy_off();

			if (!isSaveResponse(con.responseText)) {

				alert('ERROR TRANSACTION,\n' + con.responseText);

			} else {

				if(typeof row != 'undefined' && row==true) {

					document.getElementById('ftPrestasi_hasilkerja_0').firstChild.value=remove_comma_var(con.responseText);

				} else {

					document.getElementById('ftPrestasi_hasilkerja').firstChild.value=remove_comma_var(con.responseText);

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







function getUMR() 

{

	//alert('MASUK');

	//jjg=document.getElementById('jjg').value;

	hasilkerja=document.getElementById('ftAbsensi_hasilkerja').firstChild.value;

	hasilkerja=remove_comma_var(hasilkerja);

	

	nik=document.getElementById('nik').value;

	tanggal=document.getElementById('tanggal').value;

	

	notransaksi=document.getElementById('notransaksi').value;

	param='proses=getUMR'+'&hasilkerja='+hasilkerja+'&notransaksi='+notransaksi+'&nik='+nik+'&tanggal='+tanggal;

	//alert(param);

	tujuan='kebun_slave_oprasional_hardaya.php';

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

						//satu.value=remove_comma_var(satu.value);

						ar=con.responseText.split("###");

						

						

						document.getElementById('ftAbsensi_umr').firstChild.value=remove_comma_var(ar[0]);

						document.getElementById('ftAbsensi_jhk').firstChild.value=remove_comma_var(ar[1]);

						document.getElementById('ftAbsensi_insentif').firstChild.value=remove_comma_var(ar[2]);	

						

						/*var someNumber = 123.456;

						someNumber = someNumber.toFixed(2);*/	

                    }

                }

                else {

                    busy_off();

                    error_catch(con.status);

                    

                }

      }	

     } 

}





function getPremi() 

{

	//alert('MASUK');

	notransaksi=document.getElementById('notransaksi').value;

	param='proses=getPremi'+'&notransaksi='+notransaksi;

	//alert(param);

	tujuan='kebun_slave_oprasional_hardaya.php';

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

						//satu.value=remove_comma_var(satu.value);

						

						document.getElementById('ftAbsensi_insentif').firstChild.value=remove_comma_var(con.responseText);		

                    }

                }

                else {

                    busy_off();

                    error_catch(con.status);

                    

                }

      }	

     } 

}







function getAbsen() 

{

	//alert('MASUK');

	tanggal=document.getElementById('tanggal').value;

	param='proses=getAbsen'+'&tanggal='+tanggal;

	//alert(param);

	tujuan='kebun_slave_oprasional_hardaya.php';

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

						//satu.value=remove_comma_var(satu.value);

						

						document.getElementById('ftAbsensi_absensi').firstChild.innerHTML=con.responseText;		

                    }

                }

                else {

                    busy_off();

                    error_catch(con.status);

                    

                }

      }	

     } 

}































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

function searchTrans(tipe,tipeVal) {

    var notrans = document.getElementById('sNoTrans');

	jurnal = getValue('jurnal');

	barang = getValue('barang');

    if(notrans.value=='') {

       

		var where = '[["notransaksi","'+notrans.value+'"],["jurnal","'+jurnal+'"],["barang","'+barang+'"]]';

    } else {

        var where = '[["notransaksi","'+notrans.value+'"],["jurnal","'+jurnal+'"],["barang","'+barang+'"],["'+tipe+'","'+tipeVal+'"]]';

    }

	

	//alert(where);

    goToPages(1,showPerPage,where);

	

	

   // var where = '[["notransaksi","'+notrans.value+'"],["tanggal","'+tanggalR+'"],["jumlah","'+remove_comma_var(rupiah.value)+'"],["noakun","'+noakun+'"],["supp","'+supp+'"]]';

	

}



/* Paging

 * Paging Data

 */

function defaultList(tipe) {

    goToPages(1,showPerPage,'[["tipetransaksi","'+tipe+'"]]');

}



/*function goToPages(page,shows,where) {

    if(typeof where != 'undefined') {

        var newWhere = where.replace(/'/g,'"');

    }

    var workField = document.getElementById('workField');

    var param = "page="+page;

    param += "&shows="+shows;

    if(typeof where != 'undefined') {

        param+="&where="+newWhere;

    }

    

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

    

    post_response_text('kebun_slave_operasional.php?proses=showHeadList', param, respon);

}*/



function choosePage(obj,shows,where) {

    var pageVal = obj.options[obj.selectedIndex].value;

    goToPages(pageVal,shows,where);

}



/* Halaman Manipulasi Data

 * Halaman add, edit, delete

 */

function showAdd(tipe) {

    var workField = document.getElementById('workField');

    var param = "tipe="+tipe;

    

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

    

    post_response_text('kebun_slave_operasional.php?proses=showAdd', param, respon);

}



function showEditFromAdd(tipe) {

    var workField = document.getElementById('workField');

    var trans = document.getElementById('notransaksi');

    var param = "notransaksi="+trans.value;

    param+="&tipe="+tipe;

    

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

    

    post_response_text('kebun_slave_operasional.php?proses=showEdit', param, respon);

}



function showEdit(num,tipe) {

    var workField = document.getElementById('workField');

    var trans = document.getElementById('notransaksi_'+num);

    var param = "numRow="+num+"&notransaksi="+trans.getAttribute('value');

    param+="&tipe="+tipe;

    

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

    

    post_response_text('kebun_slave_operasional.php?proses=showEdit', param, respon);

}



/* Manipulasi Data

 * add, edit, delete

 */

function addDataTable(tipe) {

    var param = "notransaksi="+getValue('notransaksi')+"&kodeorg="+getValue('kodeorg');

    param += "&tanggal="+getValue('tanggal')+"&nikmandor="+getValue('nikmandor');

    param += "&nikmandor1="+getValue('nikmandor1')+"&nikasisten="+getValue('nikasisten');

    param += "&keranimuat="+getValue('keranimuat')+"&asistenpanen="+getValue('asistenpanen');

    function respon() {

        if (con.readyState == 4) {

            if (con.status == 200) {

                busy_off();

                if (!isSaveResponse(con.responseText)) {

                    alert('ERROR TRANSACTION,\n' + con.responseText);

                } else {

                    //=== Success Response

                    document.getElementById('notransaksi').value = con.responseText;

                   // alert('Added Data Header');

                    showEditFromAdd(tipe);

                }

            } else {

                busy_off();

                error_catch(con.status);

            }

        }

    }

    

    post_response_text('kebun_slave_operasional.php?proses=add&tipe='+tipe, param, respon);

}



function editDataTable(tipe) {

    var param = "notransaksi="+getValue('notransaksi')+"&kodeorg="+getValue('kodeorg');

    param += "&tanggal="+getValue('tanggal')+"&nikmandor="+getValue('nikmandor');

    param += "&nikmandor1="+getValue('nikmandor1')+"&nikasisten="+getValue('nikasisten');

    param += "&keranimuat="+getValue('keranimuat')+"&asistenpanen="+getValue('asistenpanen');

    

    function respon() {

        if (con.readyState == 4) {

            if (con.status == 200) {

                busy_off();

                if (!isSaveResponse(con.responseText)) {

                    alert('ERROR TRANSACTION,\n' + con.responseText);

                } else {

                    //=== Success Response

                    defaultList(tipe);

                }

            } else {

                busy_off();

                error_catch(con.status);

            }

        }

    }

    

    post_response_text('kebun_slave_operasional.php?proses=edit', param, respon);

}



/*

 * Detail

 */



function showDetail() {

    var detailField = document.getElementById('detailField');

    var notrans = document.getElementById('notransaksi').value;

    var afdeling = getValue('kodeorg');

    var param = "notransaksi="+notrans+"&afdeling="+afdeling;

	

	//alert(param);

    

    function respon() {

        if (con.readyState == 4) {

            if (con.status == 200) {

                busy_off();

                if (!isSaveResponse(con.responseText)) {

                    alert('ERROR TRANSACTION,\n' + con.responseText);

                } else {

                    //=== Success Response

                    var editFT = document.getElementsByTagName('img');

					detailField.innerHTML = con.responseText;

					zNew.setNum();

                    totalVal();

					for(var i=0;i<editFT.length;i++) {

						if(editFT[i].getAttribute('class')=='zImgBtn') {

							var tmp = editFT[i].getAttribute('onclick');

							editFT[i].setAttribute('onclick',tmp+';totalVal()');

							theFT.afterCrud='totalVal';

						}

					}

					var cancelAbs = document.getElementById('clearFTBtn_ftAbsensi');

					var tmp = cancelAbs.getAttribute('onclick');

					cancelAbs.setAttribute('onclick',tmp+';totalVal()');

                }

            } else {

                busy_off();

                error_catch(con.status);

            }

        }

    }

    

    post_response_text('kebun_slave_operasional_detail.php?proses=showDetail', param, respon);

}



function deleteData(num) {

    var notrans = document.getElementById('notransaksi_'+num).getAttribute('value');

    var param = "notransaksi="+notrans;

    

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





    if(confirm('Hapus transaksi '+notrans+'. Anda yakin?')) {

        post_response_text('kebun_slave_operasional.php?proses=delete', param, respon);

    }

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



/* Init Total HK, UMR dan Insentif

 */

function initUmrIns() {

    var numRow = 0;

    var totalPresHk = document.getElementById('totalPresHk');

    var totalAbsHk = document.getElementById('totalAbsHk');

    var totalPresUmr = document.getElementById('totalPresUmr');

    var totalAbsUmr = document.getElementById('totalAbsUmr');

    var totalPresIns = document.getElementById('totalPresIns');

    var totalAbsIns = document.getElementById('totalAbsIns');

    var idPres = 'ftPrestasi';var idAbs = 'ftAbsensi';

    

    // Init Total Value

    totalPresHk.setAttribute('realValue',0);

    totalPresHk.value = 0;

    totalAbsHk.setAttribute('realValue',0);

    totalAbsHk.value = 0;

    totalPresUmr.setAttribute('realValue',0);

    totalPresUmr.value = 0;

    totalAbsUmr.setAttribute('realValue',0);

    totalAbsUmr.value = 0;

    totalPresIns.setAttribute('realValue',0);

    totalPresIns.value = 0;

    totalAbsIns.setAttribute('realValue',0);

    totalAbsIns.value = 0;

    

    // Get Prestasi Value

    while(document.getElementById("tr_"+idPres+"_"+numRow)) {

        tmpHk = document.getElementById(idPres+"_jumlahhk_"+numRow).getAttribute('value');

        tmpUmr = document.getElementById(idPres+"_umr_"+numRow).getAttribute('value');

        tmpIns = document.getElementById(idPres+"_upahpremi_"+numRow).getAttribute('value');

        

        totalPresHk.setAttribute('realValue',(parseFloat(totalPresHk.getAttribute('realValue')) + parseFloat(tmpHk)));

        totalPresHk.value = totalPresHk.getAttribute('realValue');

        totalPresUmr.setAttribute('realValue',(parseFloat(totalPresUmr.getAttribute('realValue')) + parseFloat(tmpUmr)));

        totalPresUmr.value = totalPresUmr.getAttribute('realValue');

        totalPresIns.setAttribute('realValue',(parseFloat(totalPresIns.getAttribute('realValue')) + parseFloat(tmpIns)));

        totalPresIns.value = totalPresIns.getAttribute('realValue');

        change_number(totalPresHk);

        change_number(totalPresUmr);

        change_number(totalPresIns);

        numRow++;

    }

    

    numRow = 0;

    // Get Absensi Value

    while(document.getElementById("tr_"+idAbs+"_"+numRow)) {

        tmpHk = document.getElementById(idAbs+"_jhk_"+numRow).getAttribute('value');

        tmpUmr = document.getElementById(idAbs+"_umr_"+numRow).getAttribute('value');

        tmpIns = document.getElementById(idAbs+"_insentif_"+numRow).getAttribute('value');

        

        totalAbsHk.setAttribute('realValue',(parseFloat(totalAbsHk.getAttribute('realValue')) + parseFloat(tmpHk)));

        totalAbsHk.value = totalAbsHk.getAttribute('realValue');

        totalAbsUmr.setAttribute('realValue',(parseFloat(totalAbsUmr.getAttribute('realValue')) + parseFloat(tmpUmr)));

        totalAbsUmr.value = totalAbsUmr.getAttribute('realValue');

        totalAbsIns.setAttribute('realValue',(parseFloat(totalAbsIns.getAttribute('realValue')) + parseFloat(tmpIns)));

        totalAbsIns.value = totalAbsIns.getAttribute('realValue');

        change_number(totalAbsHk);

        change_number(totalAbsUmr);

        change_number(totalAbsIns);

        numRow++;

    }

}



/* Total HK, UMR atau Insentif

 */

function totalVal() {

    var totalPresHk = document.getElementById('totalPresHk'),

		totalAbsHk = document.getElementById('totalAbsHk'),

		totalPresUmr = document.getElementById('totalPresUmr'),

		totalAbsUmr = document.getElementById('totalAbsUmr'),

		totalPresIns = document.getElementById('totalPresIns'),

		totalAbsIns = document.getElementById('totalAbsIns'),

		formPrestasi = document.getElementById('form_ftPrestasi'),

		modeAbsensi = document.getElementById('form_ftAbsensi_mode').innerHTML,

		numRowAbsensi = document.getElementById('ftAbsensi_numRow').value,

		PresHk = document.getElementById('ftPrestasi_jhk'),

		AbsHk = document.getElementById('ftAbsensi_jhk').firstChild,

		AbsUmr = document.getElementById('ftAbsensi_umr').firstChild,

		AbsIns = document.getElementById('ftAbsensi_insentif').firstChild,

		tbodyAbs = document.getElementById('tbody_ftAbsensi'),

		valPresHk = 0,valPresUmr = 0,valPresIns = 0,

		valAbsHk = 0,valAbsUmr = 0,valAbsIns = 0;

	

	if(PresHk) {

		PresHk = PresHk.firstChild;

	}

    

	// if NaN

    if(isNaN(parseFloat(AbsHk.value))) {AbsHk.value = 0;}

    if(isNaN(parseFloat(AbsUmr.value))) {AbsUmr.value = 0;}

    if(isNaN(parseFloat(AbsIns.value))) {AbsIns.value = 0;}

    

    // Prestasi

	if(formPrestasi) {

		if(formPrestasi.style.display=='none') {

			var tmp = document.getElementById('ftPrestasi_jumlahhk_0').getAttribute('value');

			if(tmp==='') tmp=0;

			valPresHk += parseFloat(remove_comma_var(tmp));

		} else {

			var tmp = PresHk.value;

			if(tmp==='') tmp=0;

			valPresHk += parseFloat(remove_comma_var(tmp));

		}

	} else {

		var tmp = document.getElementById('ftPrestasi_jumlahhk_0').firstChild.value;

		if(tmp==='') tmp=0;

		valPresHk += parseFloat(remove_comma_var(tmp));

	}

	

	// Absensi

	for(var i=0; i<tbodyAbs.childNodes.length;i++) {

		if(document.getElementById('ftAbsensi_jhk_'+i)) {

			var tmp = document.getElementById('ftAbsensi_jhk_'+i).getAttribute('value'),

				tmp2 = document.getElementById('ftAbsensi_umr_'+i).getAttribute('value'),

				tmp3 = document.getElementById('ftAbsensi_insentif_'+i).getAttribute('value');

			if(tmp==='') tmp=0;

			if(tmp2==='') tmp2=0;

			if(tmp3==='') tmp3=0;

			valAbsHk += parseFloat(remove_comma_var(tmp));

			valAbsUmr += parseFloat(remove_comma_var(tmp2));

			valAbsIns += parseFloat(remove_comma_var(tmp3));

		}

	}

	

	var tmp = AbsHk.value,

		tmp2 = AbsUmr.value,

		tmp3 = AbsIns.value;

	if(tmp==='') tmp=0;

	if(tmp2==='') tmp2=0;

	if(tmp3==='') tmp3=0;

	valAbsHk += parseFloat(remove_comma_var(tmp));

	valAbsUmr += parseFloat(remove_comma_var(tmp2));

	valAbsIns += parseFloat(remove_comma_var(tmp3));

	

	if(modeAbsensi=='Mode Ubah') {

		if(document.getElementById('ftAbsensi_jhk_'+numRowAbsensi)) {

			var tmp = document.getElementById('ftAbsensi_jhk_'+numRowAbsensi).getAttribute('value'),

				tmp2 = document.getElementById('ftAbsensi_umr_'+numRowAbsensi).getAttribute('value'),

				tmp3 = document.getElementById('ftAbsensi_insentif_'+numRowAbsensi).getAttribute('value');

			if(tmp==='') tmp=0;

			if(tmp2==='') tmp2=0;

			if(tmp3==='') tmp3=0;

			valAbsHk -= parseFloat(remove_comma_var(tmp));

			valAbsUmr -= parseFloat(remove_comma_var(tmp2));

			valAbsIns -= parseFloat(remove_comma_var(tmp3));

		}

	}

	

    totalPresHk.setAttribute('realValue',valPresHk);totalPresHk.value = valPresHk;

    totalPresUmr.setAttribute('realValue',valPresUmr);totalPresUmr.value = valPresUmr;

    totalPresIns.setAttribute('realValue',valPresIns);totalPresIns.value = valPresIns;

    

    change_number(totalPresHk);

    change_number(totalPresUmr);

    change_number(totalPresIns);

    

    totalAbsHk.setAttribute('realValue',valAbsHk);totalAbsHk.value = valAbsHk;

    totalAbsUmr.setAttribute('realValue',valAbsUmr);totalAbsUmr.value = valAbsUmr;

    totalAbsIns.setAttribute('realValue',valAbsIns);totalAbsIns.value = valAbsIns;

    

    change_number(totalAbsHk);

    change_number(totalAbsUmr);

    change_number(totalAbsIns);

}



/* Cek HK, UMR atau Insentif

 */

function cekVal(obj,mode,cekVar) {

    var totalPres = document.getElementById('totalPres'+cekVar);

    var totalAbs = document.getElementById('totalAbs'+cekVar);

    var tmpVar = document.getElementById('tmpVal'+cekVar).value;

    if(mode=='Abs' && cekVar=='Hk') {

        if(obj.value>1) {

            alert('Nilai Maksimum HK adalah 1');

            obj.value = 1;

        }

    }

    totalVal();

    

    var selisih = parseFloat(totalAbs.getAttribute('realValue')) - parseFloat(totalPres.getAttribute('realValue'));

    if(parseFloat(totalPres.getAttribute('realValue')) < parseFloat(totalAbs.getAttribute('realValue'))) {

        alert('Nilai Prestasi harus lebih besar dari nilai Absensi');

        if(mode=='Abs' && cekVar=='Hk' && (selisih>1)) {

            obj.value = 1;

        } else {

            obj.value = tmpVar;

        }

    }

    totalVal();

}



/* Posting Data

 */

function postingData(numRow) {

    var notrans = document.getElementById('notransaksi_'+numRow).getAttribute('value');

    var param = "notransaksi="+notrans;

    

    function respon() {

        if (con.readyState == 4) {

            if (con.status == 200) {

                busy_off();

                if (!isSaveResponse(con.responseText)) {

                    alert('ERROR TRANSACTION,\n' + con.responseText);

                } else {

                    //=== Success Response

                   // alert('Posting Berhasil');

                   // javascript:location.reload(true);

                    x=document.getElementById('tr_'+numRow);

                    x.cells[8].innerHTML='';

                    x.cells[9].innerHTML='';

                    x.cells[10].innerHTML="<img class=\"zImgOffBtn\" title=\"Posting\" src=\"images/skyblue/posted.png\">";

                }

            } else {

                busy_off();

                error_catch(con.status);

            }

        }

    }

    

    if(confirm('Are you sure confirm Transaction:'+notrans+

        '?\nOnce confirmed, the data can not be edited.')) {

        post_response_text('kebun_slave_operasional_posting.php', param, respon);

    }

}



function printPDF(ev,tipe) {

    // Prep Param

    param = "proses=pdf&tipe="+tipe;

    

    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+

        " src='kebun_slave_operasional_print.php?"+param+"'></iframe>",'800','400',ev);

    var dialog = document.getElementById('dynamic1');

    dialog.style.top = '50px';

    dialog.style.left = '15%';

}



function detailPDF(numRow,ev,tipe) {

    // Prep Param

    var notransaksi = document.getElementById('notransaksi_'+numRow).getAttribute('value');

    param = "proses=pdf&tipe="+tipe+"&notransaksi="+notransaksi;

    

    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+

        " src='kebun_slave_operasional_print_detail.php?"+param+"'></iframe>",'800','400',ev);

    var dialog = document.getElementById('dynamic1');

    dialog.style.top = '50px';

    dialog.style.left = '15%';

}



function detailData(numRow,ev,tipe)

{

    var notransaksi = document.getElementById('notransaksi_'+numRow).getAttribute('value');

    param = "proses=html&tipe="+tipe+"&notransaksi="+notransaksi;

        title="Data Detail";

        showDialog1(title,"<iframe frameborder=0 style='width:795px;height:400px'"+

        " src='kebun_slave_operasional_print_detail.php?"+param+"'></iframe>",'800','400',ev);	

        var dialog = document.getElementById('dynamic1');

        dialog.style.top = '50px';

        dialog.style.left = '15%';

}



function changeOrg() {

    var prestasi = document.getElementById('ftPrestasi_kodeorg').firstChild;

    var material = document.getElementById('ftMaterial_kodeorg').firstChild;

    material.selectedIndex = prestasi.selectedIndex;

}



function updateUMR(obj) {

    var nik = obj.options[obj.selectedIndex].value;

    var umr = document.getElementById('ftAbsensi_umr').firstChild;

    var tanggal=document.getElementById('tanggal').value;

    var jhk=document.getElementById('jhk').value;

//    var tahun=tanggal.substr(6,4);

    var tahun=tanggal.getFullYear();

    var param = "nik="+nik+'&tahun='+tahun+'&jhk='+jhk;

    

    function respon() {

        if (con.readyState == 4) {

            if (con.status == 200) {

                busy_off();

                if (!isSaveResponse(con.responseText)) {

                    alert('ERROR TRANSACTION,\n' + con.responseText);

                } else {

                    //=== Success Response

                    umr.value = con.responseText

					

                }

            } else {

                busy_off();

                error_catch(con.status);

            }

        }

    }

    

    post_response_text('kebun_slave_operasional_detail.php?proses=updateUMR', param, respon);

}



function updateUMR2() {

    var nik=document.getElementById('nik');

    nik = nik.options[nik.selectedIndex].value;

    var umr = document.getElementById('ftAbsensi_umr').firstChild;

    var tanggal=document.getElementById('tanggal').value;

    var jhk=document.getElementById('jhk').value;

    var tahun=tanggal.substr(6, 4);

    var param = "nik="+nik+'&tahun='+tahun+'&jhk='+jhk;

    

    function respon() {

        if (con.readyState == 4) {

            if (con.status == 200) {

                busy_off();

                if (!isSaveResponse(con.responseText)) {

                    alert('ERROR TRANSACTION,\n' + con.responseText);

                } else {

                    //=== Success Response

                    umr.value = con.responseText

					getPremi();

                }

            } else {

                busy_off();

                error_catch(con.status);

            }

        }

    }

    

    post_response_text('kebun_slave_operasional_detail.php?proses=updateUMR', param, respon);

}





function filterKaryawan(idkomponen,cek)

{

	var allNik = document.getElementById('allptnik');

	

   try{  

     kodeorg=document.getElementById('ftPrestasi_kodeorg_0').getAttribute('value');

		if(cek.checked==true) {

			param='kodeorg='+kodeorg+'&tipe=afdeling';

			allNik.checked = false;

			allNik.disabled = true;

		} else {

			allNik.disabled = false;

			param='kodeorg='+kodeorg+'&tipe=unit';

		}

       post_response_text('kebun_slave_operasional_detail.php?proses=gatKarywanAFD', param, respon);     

   }

   catch(err){

       alert('Simpan prestasi terlebih dahulu');

   }



     function respon() {

        if (con.readyState == 4) {

            if (con.status == 200) {

                busy_off();

                if (!isSaveResponse(con.responseText)) {

                    alert('ERROR TRANSACTION,\n' + con.responseText);

                } else {

                    //=== Success Response

                    document.getElementById(idkomponen).innerHTML=con.responseText;

                }

            } else {

                busy_off();

                error_catch(con.status);

            }

        }

    }   

}



/**

 * allPtKaryawan

 * Show semua karyawan di dalam 1 PT

 * @param	string	idElement	ID dari element yang akan direplace optionnya

 * @param	object	check		Object Checkbox

 */

function allPtKaryawan(idElement, check) {

	var filterNik = document.getElementById('filternik');

	

	try {  

		kodeorg=document.getElementById('ftPrestasi_kodeorg_0').getAttribute('value');

		if(check.checked==true) {

			param='kodeorg='+kodeorg+'&tipe=all';

			filterNik.checked = false;

			filterNik.disabled = true;

		} else {

			filterNik.disabled = false;

			param='kodeorg='+kodeorg+'&tipe=default';

		}

		post_response_text('kebun_slave_operasional_karyawan.php?proses=getAllPt', param, respon);     

	} catch(err) {

		alert('Simpan prestasi terlebih dahulu');

		check.checked = false;

	}

	

	function respon() {

        if (con.readyState == 4) {

            if (con.status == 200) {

                busy_off();

                if (!isSaveResponse(con.responseText)) {

                    alert('ERROR TRANSACTION,\n' + con.responseText);

                } else {

                    //=== Success Response

                    document.getElementById(idElement).innerHTML=con.responseText;

                }

            } else {

                busy_off();

                error_catch(con.status);

            }

        }

    }

}



/**

 * cekAbsensiAll

 * Cek semua ketentuan di tab absensi

 */

function cekAbsensiAll(isHasil) {

	var notransaksi = document.getElementById('notransaksi'),

		tanggal = document.getElementById('tanggal'),

		kegiatan = document.getElementById('ftPrestasi_kodekegiatan_0'),

		nik = getById('ftAbsensi_nik').firstChild,

		jjg = getById('ftAbsensi_jjg').firstChild,

		hasilkerja = getById('ftAbsensi_hasilkerja').firstChild,

		jhk = getById('ftAbsensi_jhk').firstChild,

		umr = getById('ftAbsensi_umr').firstChild,

		insentif = getById('ftAbsensi_insentif').firstChild;



	if(typeof isHasil=='undefined') {

		isHasil = false;

	}

	

	if(kegiatan == null) {

		alert("Prestasi harus diisi terlebih dahulu");return;

	}

	kegiatan = kegiatan.getAttribute('value');

	

	param = "proses=cekAll&kegiatan="+kegiatan+"&nik="+getValue('nik')+"&jjg="+jjg.value+

		"&hasilkerja="+remove_comma_var(hasilkerja.value)+"&jhk="+remove_comma_var(jhk.value)+"&umr="+remove_comma_var(umr.value)+

		"&insentif="+remove_comma_var(insentif.value)+'&notransaksi='+notransaksi.value+'&tanggal='+tanggal.value+

		'&jjgDisable='+jjg.disabled;

//	alert(param);		

	post_response_text('kebun_slave_oprasional_hardaya.php', param, respon);

	function respon() {

        if (con.readyState == 4) {

            if (con.status == 200) {

                busy_off();

                if (!isSaveResponse(con.responseText)) {

                    alert('ERROR TRANSACTION,\n' + con.responseText);

                } else {

                    //=== Success Response

                    var res=JSON.parse(con.responseText);

					if(isHasil==false) {

						hasilkerja.value = res.hasilkerja;

					}

					jhk.value = res.jhk;

					insentif.value = res.insentif;

                    umr.value = res.umr;

                    totalpremi.value = res.totalpremi;

					totalVal();

                }

            } else {

                busy_off();

                error_catch(con.status);

            }

        }

    }

}



/**

 * savePrestasi

 * Update Detail Prestasi

 */

function savePrestasi() {

	var notransaksi = document.getElementById('notransaksi').value,

		kegiatan = document.getElementById('ftPrestasi_kodekegiatan_0').getAttribute('value'),

		kodeorg = document.getElementById('ftPrestasi_kodeorg_0').getAttribute('value'),

		jjg = document.getElementById('ftPrestasi_jjg_0').firstChild,

		hasilkerja = document.getElementById('ftPrestasi_hasilkerja_0').firstChild,

		jumlahhk = document.getElementById('ftPrestasi_jumlahhk_0').firstChild,

		param = "notransaksi="+notransaksi+"&cond_kodekegiatan="+kegiatan+

			"&cond_kodeorg="+kodeorg+'&jjg='+jjg.value+'&hasilkerja='+hasilkerja.value+

			'&jumlahhk='+jumlahhk.value;

	

	post_response_text('kebun_slave_operasional_prestasi.php?proses=edit', param, respon);

	function respon() {

        if (con.readyState == 4) {

            if (con.status == 200) {

                busy_off();

                if (!isSaveResponse(con.responseText)) {

                    alert('ERROR TRANSACTION,\n' + con.responseText);

                } else {

                    //=== Success Response

                    getById('tr_ftPrestasi_0').style.background='#E8F4F4';

					getById('totalPresHk').value = _formatted(jumlahhk);

                }

            } else {

                busy_off();

                error_catch(con.status);

            }

        }

    }

}