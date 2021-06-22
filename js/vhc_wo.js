// JavaScript Document

function simpan()
{
    tanggal=trim(document.getElementById('tanggal').value);
    jam=document.getElementById('jam').options[document.getElementById('jam').selectedIndex].value;
    mnt=document.getElementById('mnt').options[document.getElementById('mnt').selectedIndex].value;
    kodetraksi=document.getElementById('kodetraksi').options[document.getElementById('kodetraksi').selectedIndex].value;
    kodealat=document.getElementById('kodealat').options[document.getElementById('kodealat').selectedIndex].value;
    operator=document.getElementById('operator').options[document.getElementById('operator').selectedIndex].value;
    posisihm=document.getElementById('posisihm').value;
    namapelapor=trim(document.getElementById('namapelapor').value);
    indikasikerusakan=trim(document.getElementById('indikasikerusakan').value);
    penyebabrusak=document.getElementById('penyebabrusak').options[document.getElementById('penyebabrusak').selectedIndex].value;
    noberitaacara=document.getElementById('noberitaacara').options[document.getElementById('noberitaacara').selectedIndex].value;
    hedept=document.getElementById('hedept').options[document.getElementById('hedept').selectedIndex].value;
    divmanager=document.getElementById('divmanager').options[document.getElementById('divmanager').selectedIndex].value;
    workshop=document.getElementById('workshop').options[document.getElementById('workshop').selectedIndex].value;
    method=document.getElementById('method').value;	
    notransaksi=document.getElementById('notransaksi').value;	
    
    if(tanggal=='')            { alert('Please fill TANGGAL'); exit(); }
    
    param='tanggal='+tanggal+'&jam='+jam+'&mnt='+mnt;
    param+='&kodetraksi='+kodetraksi+'&kodealat='+kodealat+'&operator='+operator+'&posisihm='+posisihm+'&namapelapor='+namapelapor;
    param+='&indikasikerusakan='+indikasikerusakan+'&penyebabrusak='+penyebabrusak+'&noberitaacara='+noberitaacara+'&hedept='+hedept;
    param+='&divmanager='+divmanager+'&workshop='+workshop+'&notransaksi='+notransaksi;
    param+='&method='+method;
    tujuan = 'vhc_slave_wo.php';
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
                    alert('Done.');
                    loadData();
                    batal();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }	
}

function batal()
{
    var d = new Date();
    var curr_date = d.getDate();
    var curr_month = d.getMonth() + 1; //Months are zero based
    var curr_year = d.getFullYear();
    d1=curr_date + "-" + curr_month + "-" + curr_year;

    document.getElementById('tanggal').value=d1;
    document.getElementById('jam').value='';
    document.getElementById('mnt').value='';
    document.getElementById('kodetraksi').value='';
    document.getElementById('kodealat').value='';
    document.getElementById('operator').value='';
    document.getElementById('posisihm').value='0';
    document.getElementById('namapelapor').value='';
    document.getElementById('indikasikerusakan').value='';
    document.getElementById('penyebabrusak').value='UMUM';
    document.getElementById('noberitaacara').value='';
    document.getElementById('hedept').value='';
    document.getElementById('divmanager').value='';
    document.getElementById('workshop').value='';
    document.getElementById('method').value="update";
}

function fillField(notransaksi,kodetraksi,tanggal,jam,mnt,kodealat,operator,posisihm,namapelapor,indikasikerusakan,
    penyebabrusak,noberitaacara,hedept,divmanager,workshop)
{
    document.getElementById('notransaksi').value=notransaksi;
    document.getElementById('tanggal').value=tanggal;
    document.getElementById('jam').value=jam;
    document.getElementById('mnt').value=mnt;
    document.getElementById('kodetraksi').value=kodetraksi;
    document.getElementById('kodealat').value=kodealat;
    document.getElementById('operator').value=operator;
    document.getElementById('posisihm').value=posisihm;
    document.getElementById('namapelapor').value=namapelapor;
    document.getElementById('indikasikerusakan').value=indikasikerusakan;
    document.getElementById('penyebabrusak').value=penyebabrusak;
    document.getElementById('noberitaacara').value=noberitaacara;
    document.getElementById('hedept').value=hedept;
    document.getElementById('divmanager').value=divmanager;
    document.getElementById('workshop').value=workshop;
    document.getElementById('method').value="update";
	getAlat(kodealat,operator);
	cekBA(noberitaacara);
}

function del(notransaksi)
{
    document.getElementById('method').value='hapus';
    param='notransaksi='+notransaksi+'&method=delete';
    if(confirm('Delete/Hapus '+notransaksi+'?'))
    {
        tujuan='vhc_slave_wo.php';
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
                    alert('Done.');
                    loadData();
                    batal();
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
    perSch=document.getElementById('perSch').value;
    param='method=loadData'+'&perSch='+perSch;
    tujuan='vhc_slave_wo.php';
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

function printPdf(notransaksi,event) {
    param = "method=printPdf&notransaksi="+notransaksi;
    
    showDialog1('Print PDF',"<iframe frameborder=0 width='100%' height='100%'"+
        " src='vhc_slave_wo.php?"+param+"'></iframe>",'800','400',event);
}
function getAlat(value,operator)
{
    kodetraksi=document.getElementById('kodetraksi').options[document.getElementById('kodetraksi').selectedIndex].value;
    param='kodetraksi='+kodetraksi+'&method=getAlat';
    tujuan='vhc_slave_wo.php';
    post_response_text(tujuan, param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    document.getElementById('kodealat').innerHTML = con.responseText;
					if(typeof value!='undefined') {
						var kodealat = document.getElementById('kodealat'),
							index=-1;
						for(i in kodealat.options) {
							if(kodealat.options[i].value==value) {
								index = i;
							}
						}
						kodealat.selectedIndex = index;
						getOperator(operator);
					}
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function getOperator(value)
{
    kodealat=document.getElementById('kodealat').options[document.getElementById('kodealat').selectedIndex].value;
    param='kodealat='+kodealat+'&method=getOperator';
    tujuan='vhc_slave_wo.php';
    post_response_text(tujuan, param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    document.getElementById('operator').innerHTML = con.responseText;
					if(typeof value!='undefined') {
						var operator = document.getElementById('operator'),
							index=-1;
						for(i in operator.options) {
							if(operator.options[i].value==value) {
								index = i;
							}
						}
						operator.selectedIndex = index;
					}
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function cekBA(value) {
	var sebab = document.getElementById('penyebabrusak');
	if(sebab.options[sebab.selectedIndex].value=='KECELAKAAN') {
		if(typeof value!='undefined') {
			getBA(value);
		} else {
			getBA();
		}
	} else {
		document.getElementById('noberitaacara').innerHTML = '';
	}
}

function getBA(value)
{
	if(document.getElementById('kodealat').selectedIndex==-1) {
		alert("Warning: Kode Alat harus dipilih terlebih dahulu");
		document.getElementById('noberitaacara').selectedIndex = 0;
		return;
	}
    kodealat=document.getElementById('kodealat').options[document.getElementById('kodealat').selectedIndex].value;
    param='kodealat='+kodealat+'&method=getBA';
    tujuan='vhc_slave_wo.php';
    post_response_text(tujuan, param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    document.getElementById('noberitaacara').innerHTML = con.responseText;
					if(typeof value!='undefined') {
						var ba = document.getElementById('noberitaacara'),
							index=-1;
						for(i in ba.options) {
							if(ba.options[i].value==value) {
								index = i;
							}
						}
						ba.selectedIndex = index;
					}
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}