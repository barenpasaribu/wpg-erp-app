// JavaScript Document
function cancel() {
    document.location.reload();
}

function del(tanggal,kodeblok)
{
    param='method=delete'+'&tanggal='+tanggal+'&kodeblok='+kodeblok;
    tujuan='kebun_slave_qc_pemupukan.php';
    post_response_text(tujuan, param, respog);	
    function respog() {
        if(con.readyState==4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                        alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                        cancel();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }
}

function cariBast(num) {
    kdKebunSch=document.getElementById('kdKebunSch').value;
    perSch=document.getElementById('perSch').value;

    param='method=loadData'+'&kdKebunSch='+kdKebunSch+'&perSch='+perSch+'&page='+num;
    tujuan = 'kebun_slave_qc_pemupukan.php';
    post_response_text(tujuan, param, respog);			
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //displayList();
                    document.getElementById('container').innerHTML=con.responseText;
                    //loadData();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	
}

function loadData ()  {
    kdKebunSch=document.getElementById('kdKebunSch').value;
    perSch=document.getElementById('perSch').value;
    param='method=loadData'+'&kdKebunSch='+kdKebunSch+'&perSch='+perSch;
    //alert(param);	
    tujuan='kebun_slave_qc_pemupukan.php';
    post_response_text(tujuan, param, respog);
    function respog() {
        if(con.readyState==4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                        alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                   // alert(con.responseText);
                    document.getElementById('container').innerHTML=con.responseText;
                }
            } else {
                    busy_off();
                    error_catch(con.status);
            }
        }	
    }  
}

function loadDataPrev ()  {
    kdKebunSch=document.getElementById('kdKebunSch').value;
    perSch=document.getElementById('perSch').value;
    if(kdKebunSch=='' || perSch=='') {
        alert('Kebun dan periode harus dipilih');
    } else {
        loadData();
    }
}

function lockHeader() {
	document.getElementById('barang').disabled=true;
    document.getElementById('saveHeader').disabled=true;
    document.getElementById('cancelHeader').disabled=true;
    document.getElementById('tanggal').disabled=true;
    document.getElementById('jamMulai').disabled=true;
    document.getElementById('mntMulai').disabled=true;
    document.getElementById('jamSelesai').disabled=true;
    document.getElementById('mntSelesai').disabled=true;
    document.getElementById('kodedivisi').disabled=true;
    document.getElementById('jumlahpekerja').disabled=true;
    document.getElementById('kodeafdeling').disabled=true;
    document.getElementById('dosis').disabled=true;
    document.getElementById('kodeblok').disabled=true;
    document.getElementById('teraplikasi').disabled=true;
    document.getElementById('namapengawas').disabled=true;
    document.getElementById('kondisilahan').disabled=true;
    document.getElementById('comment').disabled=true;
    document.getElementById('pengawas').disabled=true;
    document.getElementById('asisten').disabled=true;
    document.getElementById('mengetahui').disabled=true;
	document.getElementById('barang').disabled=true;
}

function fillFieldDetail(nojalur,pkkdipupuk,pkktdkdipupuk,apltdkstandar,keterangan) {	
    document.getElementById('nojalur').value=nojalur;
    document.getElementById('nojalur').disabled=true;
    document.getElementById('pkkdipupuk').value=pkkdipupuk;
    document.getElementById('pkktdkdipupuk').value=pkktdkdipupuk;
    document.getElementById('apltdkstandar').value=apltdkstandar;
    document.getElementById('keterangan').value=keterangan;
}

function clearDetail() {	
    document.getElementById('nojalur').value='';
    document.getElementById('nojalur').disabled=false;
    document.getElementById('pkkdipupuk').value='';
    document.getElementById('pkktdkdipupuk').value='';
    document.getElementById('apltdkstandar').value='';
    document.getElementById('keterangan').value='';
}

function saveHeader() {
	barang=document.getElementById('barang').value;
    tanggal=document.getElementById('tanggal').value;
    kodeblok=document.getElementById('kodeblok').value;
    kodedivisi=document.getElementById('kodedivisi').value;
    jamMulai=document.getElementById('jamMulai').value;
    mntMulai=document.getElementById('mntMulai').value;
    jamSelesai=document.getElementById('jamSelesai').value;
    mntSelesai=document.getElementById('mntSelesai').value;

    jumlahpekerja=document.getElementById('jumlahpekerja').value;
    kodeafdeling=document.getElementById('kodeafdeling').value;
    dosis=document.getElementById('dosis').value;
    teraplikasi=document.getElementById('teraplikasi').value;
    namapengawas=document.getElementById('namapengawas').value;
    kondisilahan=document.getElementById('kondisilahan').value;
    comment=document.getElementById('comment').value;
    pengawas=document.getElementById('pengawas').value;
    asisten=document.getElementById('asisten').value;
    mengetahui=document.getElementById('mengetahui').value;

    if(tanggal=='' || kodedivisi=='' || kodeafdeling=='' || kodeblok=='' || pengawas=='' || asisten=='' || mengetahui=='') {
        alert('Date, Divisi, Afdeling, Block, Supervisor, Assistant, Verify was empty');return;
    }

    param='method=saveHeader'+'&tanggal='+tanggal+'&kodeblok='+kodeblok+'&kodedivisi='+kodedivisi+'&kodeafdeling='+kodeafdeling;
    param+='&jamMulai='+jamMulai+'&mntMulai='+mntMulai+'&jamSelesai='+jamSelesai+'&mntSelesai='+mntSelesai;		
    param+='&jumlahpekerja='+jumlahpekerja+'&dosis='+dosis+'&teraplikasi='+teraplikasi+'&namapengawas='+namapengawas;		
    param+='&kondisilahan='+kondisilahan+'&comment='+comment+'&pengawas='+pengawas+'&asisten='+asisten+'&mengetahui='+mengetahui+'&barang='+barang;	

    tujuan='kebun_slave_qc_pemupukan.php';
    post_response_text(tujuan, param, respog);	
    function respog() {
        if(con.readyState==4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else  {
                    lockHeader();
                    document.getElementById('detailForm').style.display='block';
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }
}

function saveDetail() {
    tanggal=document.getElementById('tanggal').value;
    kodeblok=document.getElementById('kodeblok').value;

    nojalur=document.getElementById('nojalur').value;
    pkkdipupuk=document.getElementById('pkkdipupuk').value;
    pkktdkdipupuk=document.getElementById('pkktdkdipupuk').value;
    apltdkstandar=document.getElementById('apltdkstandar').value;
    keterangan=document.getElementById('keterangan').value;

    if(nojalur=='' || nojalur=='0') {
        alert('Incorect number of Path');return; 
    }

    if (document.getElementById('nojalur').disabled==false){
        param='method=insertDetail';
    } else {
        param='method=updateDetail';
    }
    param+='&tanggal='+tanggal+'&kodeblok='+kodeblok+'&nojalur='+nojalur;
    param+='&pkkdipupuk='+pkkdipupuk+'&pkktdkdipupuk='+pkktdkdipupuk+'&apltdkstandar='+apltdkstandar+'&keterangan='+keterangan;

    //alert(param);
    tujuan='kebun_slave_qc_pemupukan.php';
    post_response_text(tujuan, param, respog);	
    function respog() {
        if(con.readyState==4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                        alert('ERROR TRANSACTION,\n' + con.responseText);
                } else  {
                    //bersihDetail();
                    clearDetail();
                    loadDataDetail();

                    //lockHeader();
                    //document.getElementById('detailForm').style.display='block';
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }
}

function loadDataDetail() {
    //alert('masuk');
    tanggal=document.getElementById('tanggal').value;
    kodeblok=document.getElementById('kodeblok').value;
    param='method=loadDetail'+'&tanggal='+tanggal+'&kodeblok='+kodeblok;
    //alert(param);
    tujuan='kebun_slave_qc_pemupukan.php';
    post_response_text(tujuan, param, respog);
    function respog() {
        if(con.readyState==4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //alert(con.responseText);
                    //return;
                    //document.getElementById('contentDetail').innerHTML=con.responseText;
                    document.getElementById('containList').style.display='block';
                    document.getElementById('containList').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }	
    } 	
}

function DelDetail(tanggal,kodeblok,nojalur) {
    param='method=deleteDetail'+'&tanggal='+tanggal+'&kodeblok='+kodeblok+'&nojalur='+nojalur;
    //alert(param);
    tujuan='kebun_slave_qc_pemupukan.php';
    post_response_text(tujuan, param, respog);	
    function respog() {
        if(con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    clearDetail();
                    loadDataDetail();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }
    //alert("Data telah terhapus !!!");	
}

function getAfdeling() {
    kodedivisi=document.getElementById('kodedivisi').options[document.getElementById('kodedivisi').selectedIndex].value;
    param='kodedivisi='+kodedivisi+'&method=getAfdeling';
    tujuan='kebun_slave_qc_pemupukan.php';
    post_response_text(tujuan, param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    document.getElementById('kodeafdeling').innerHTML = con.responseText;
                    getKar();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function getBlok() {
    kodeafdeling=document.getElementById('kodeafdeling').options[document.getElementById('kodeafdeling').selectedIndex].value;
    param='kodeafdeling='+kodeafdeling+'&method=getBlok';
    tujuan='kebun_slave_qc_pemupukan.php';
    post_response_text(tujuan, param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    document.getElementById('kodeblok').innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function getKar() {
    kodedivisi=document.getElementById('kodedivisi').value;
    param='method=getKar'+'&kodedivisi='+kodedivisi;
    tujuan='kebun_slave_qc_pemupukan.php';
    post_response_text(tujuan, param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    ar=con.responseText.split("###");
                   // document.getElementById('namapengawas').innerHTML = ar[0];
                    document.getElementById('pengawas').innerHTML = ar[0];
                    document.getElementById('asisten').innerHTML = ar[1];
                    document.getElementById('mengetahui').innerHTML = ar[2];
					document.getElementById('namapengawas').innerHTML = ar[1];
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
    width='300';
    height='100';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog2(title,content,width,height,ev); 	
}

function datakeExcel(ev,tanggal,kodeblok)
{
        param='method=printExcel'+'&tanggal='+tanggal+'&kodeblok='+kodeblok;
        //alert(param);
        tujuan='kebun_slave_qc_pemupukan.php';
        judul=' Print Excel';		
        printFile(param,tujuan,judul,ev)	
}

function previewQCPemupukanPDF(tanggal,kodeblok,ev)
{
    param='tanggal='+tanggal+'&kodeblok='+kodeblok;
    tujuan = 'kebun_slave_qc_pemupukan_pdf.php?'+param;	
    //display window
    title='Print PDF';
    width='900';
    height='400';
    content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
    showDialog1(title,content,width,height,ev);	
}