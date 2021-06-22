// JavaScript Document

// awan 07 04 2020

function getTahun() {
    karyawanid = document.getElementById('karyawanId').value;
    param = "proses=getTahun";
    param += "&karyawanid="+karyawanid;
    tujuan='sdm_slave_ijin_meninggalkan_kantor.php';
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
                        dataTahun = JSON.parse(con.responseText);

                        // menghilangkan semua pilihan
                        var select = document.getElementById("periodec");
                        var length = select.options.length;
                        for (i = length-1; i >= 0; i--) {
                            select.options[i] = null;
                        }
                        
                        var i = 0
                        select.options[select.options.length] = new Option("Pilih Data");
                        for(index in dataTahun) {
                            select.options[select.options.length] = new Option(dataTahun[i].periodecuti, dataTahun[i].periodecuti);
                            i++;
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

function cancelForm()
{
        document.getElementById('tglIzin').disabled=false;
        document.getElementById('tglIzin').value='';
        document.getElementById('jam1').value=00;
         q=document.getElementById('jam1');
        for(a=0;a<q.length;a++)
        {
        if(q.options[a].value==00)
            {
                q.options[a].selected=true;
            }
        }
        q2=document.getElementById('mnt1');
        for(a2=0;a2<q2.length;a2++)
        {
        if(q2.options[a2].value==00)
            {
                q2.options[a2].selected=true;
            }
        }
         qjm2=document.getElementById('jam2');
        for(aqjm2=0;aqjm2<qjm2.length;aqjm2++)
        {
        if(qjm2.options[aqjm2].value==00)
            {
                qjm2.options[aqjm2].selected=true;
            }
        }
        qmnt2=document.getElementById('mnt2');
        for(aqmnt2=0;aqmnt2<qmnt2.length;aqmnt2++)
        {
        if(qmnt2.options[aqmnt2].value==00)
            {
                qmnt2.options[aqmnt2].selected=true;
            }
        }
        document.getElementById('jnsIjin').value='';
        document.getElementById('tglAwal').value='';
        document.getElementById('tglEnd').value='';
        document.getElementById('keperluan').value='';
        document.getElementById('ket').value='';
        document.getElementById('atsSblm').value='';
        document.getElementById('atasan').value='';
		document.getElementById('atasan2').value='';
		document.getElementById('hrd').value='';
}

function saveForm()
{
        tglijin=document.getElementById('tglIzin').value;
        tglAwal=document.getElementById('tglAwal').value;
        tglEnd=document.getElementById('tglEnd').value;
        karyawanId=document.getElementById('karyawanId').value;
        jnsIjin=document.getElementById('jnsIjin').options[document.getElementById('jnsIjin').selectedIndex].value;
        jam1=document.getElementById('jam1').options[document.getElementById('jam1').selectedIndex].value;
        mnt1=document.getElementById('mnt1').options[document.getElementById('mnt1').selectedIndex].value;
        jam2=document.getElementById('jam2').options[document.getElementById('jam2').selectedIndex].value;
        mnt2=document.getElementById('mnt2').options[document.getElementById('mnt2').selectedIndex].value;
        keperluan=document.getElementById('keperluan').value;
        ket=document.getElementById('ket').value;
        atasan=document.getElementById('atasan').options[document.getElementById('atasan').selectedIndex].value;
		atasan2=document.getElementById('atasan2').options[document.getElementById('atasan2').selectedIndex].value;
        jamDr=jam1+":"+mnt1;
        jamSmp=jam2+":"+mnt2;
        pros=document.getElementById('proses').value;
        hk=document.getElementById('jumlahhk').value;
        hrd=document.getElementById('hrd').options[document.getElementById('hrd').selectedIndex].value;
        periodec=document.getElementById('periodec').options[document.getElementById('periodec').selectedIndex].value;
		
		ganti=document.getElementById('ganti').options[document.getElementById('ganti').selectedIndex].value;
		
		
		
        param = "proses="+pros;
        if(pros=='update')
        {
            atsSblm=document.getElementById('atsSblm').value;
            param+="&atsSblm="+atsSblm;
        }
        param += "&tglijin="+tglijin;
        param += "&jnsIjin="+jnsIjin;
        param += "&jamDr="+jamDr;
        param += "&jamSmp="+jamSmp;
        param += "&keperluan="+keperluan;
        param += "&ket="+ket;
        param += "&atasan="+atasan;
		param += "&atasan2="+atasan2;
        param += "&tglAwal="+tglAwal;
        param += "&tglEnd="+tglEnd;
        param += "&jumlahhk="+hk;
        param += "&hrd="+hrd;
		param += "&ganti="+ganti;
        param += "&periodec="+periodec;
        param += "&karyawanid="+karyawanId;
		
		//alert(param);
        if((jnsIjin=='CUTI' || jnsIjin=='MELAHIRKAN' || jnsIjin=='KAWIN/SUNATAN/WISUDA') && (hk=='0' || hk=='')){
            alert('Number of day(s) required');
        }else{
            tujuan='sdm_slave_ijin_meninggalkan_kantor.php';
            //alert(param);
    //	return;
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
                            //document.getElementById('contain').innerHTML=con.responseText;
                           
                            loadNData();
                    }
                }
                else {
                        busy_off();
                        error_catch(con.status);
                }
            }	
         } 	

}


function loadNData()
{
        param='proses=loadData';
        tujuan='sdm_slave_ijin_meninggalkan_kantor.php';
        //alert(tujuan);
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
                tujuan = 'sdm_slave_ijin_meninggalkan_kantor.php';
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
function fillField(keprlan,tanggal,jnsijin,perstjan,statPrstjn,drjam,smpjam,hrd,hk,periodec,atasan2,ganti) 
{ 

                param='proses=getKet'+'&tglijin='+tanggal;
                tujuan = 'sdm_slave_ijin_meninggalkan_kantor.php';
                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                jm1=drjam.split(" ");
                                                tlgAwal=jm1[0].split("-");
                                                tglAwal1=tlgAwal[2]+"-"+tlgAwal[1]+"-"+tlgAwal[0];
                                                jmDari=jm1[1].split(":");


                                                jm2=smpjam.split(" ");
                                                tlgAkhir=jm2[0].split("-");
                                                tlgAkhir1=tlgAkhir[2]+"-"+tlgAkhir[1]+"-"+tlgAkhir[0];
                                                jamSmp=jm2[1].split(":");
                                                document.getElementById('tglIzin').disabled=true;
                                                document.getElementById('tglIzin').value=tanggal;
                                                document.getElementById('keperluan').value=keprlan;
                                                document.getElementById('proses').value='update';
                                                document.getElementById('tglAwal').value=tglAwal1;
                                                document.getElementById('tglEnd').value=tlgAkhir1;
                                                document.getElementById('jumlahhk').value=hk;
                                                 q=document.getElementById('jam1');
                                                  for(a=0;a<q.length;a++)
                                                  {
                                                        if(q.options[a].value==jmDari[0])
                                                            {
                                                                q.options[a].selected=true;
                                                            }
                                                   }
                                                   q2=document.getElementById('mnt1');
                                                  for(a2=0;a2<q2.length;a2++)
                                                  {
                                                        if(q2.options[a2].value==jmDari[1])
                                                            {
                                                                q2.options[a2].selected=true;
                                                            }
                                                   }

                                                   q3=document.getElementById('jam2');
                                                  for(a3=0;a3<q3.length;a3++)
                                                  {
                                                        if(q.options[a3].value==jamSmp[0])
                                                            {
                                                                q3.options[a3].selected=true;
                                                            }
                                                   }
                                                   qakr=document.getElementById('mnt2');
                                                  for(a5=0;a5<qakr.length;a5++)
                                                  {
                                                        if(qakr.options[a5].value==jamSmp[1])
                                                            {
                                                                qakr.options[a5].selected=true;
                                                            }
                                                   }
                                                   jns=document.getElementById('jnsIjin');
                                                  for(ajns=0;ajns<jns.length;ajns++)
                                                  {
                                                        if(jns.options[ajns].value==jnsijin)
                                                            {
                                                                jns.options[ajns].selected=true;
                                                            }
                                                   }
                                                   atsn=document.getElementById('atasan');
                                                  for(aatsn=0;aatsn<atsn.length;aatsn++)
                                                  {
                                                        if(atsn.options[aatsn].value==perstjan)
                                                            {
                                                                atsn.options[aatsn].selected=true;
                                                            }
                                                   }
												   
												   x=document.getElementById('atasan2');
                                                  for(j=0;j<x.length;j++)
                                                  {
                                                        if(x.options[j].value==atasan2)
                                                            {
                                                                x.options[j].selected=true;
                                                            }
                                                   }  
											
												   
												   
                                                  x=document.getElementById('hrd');
                                                  for(j=0;j<x.length;j++)
                                                  {
                                                        if(x.options[j].value==hrd)
                                                            {
                                                                x.options[j].selected=true;
                                                            }
                                                   }  
												   
												  
												    
                                                  x=document.getElementById('periodec');
                                                  for(j=0;j<x.length;j++)
                                                  {
                                                        if(x.options[j].value==periodec)
                                                            {
                                                                x.options[j].selected=true;
                                                            }
                                                   }
												   
												   
												    x=document.getElementById('ganti');
                                                  for(j=0;j<x.length;j++)
                                                  {
                                                        if(x.options[j].value==ganti)
                                                            {
                                                                x.options[j].selected=true;
                                                            }
                                                   }
												   
												                                                        
                                                   document.getElementById('atsSblm').value='';
                                                   document.getElementById('atsSblm').value=perstjan;
                                                   document.getElementById('ket').value=con.responseText;
												  
                                        }
                                }
                                else {
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
        tujuan='sdm_slave_ijin_meninggalkan_kantor.php';
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
function delData(karyawanid,tanggal)
{
        var str = tanggal;
        var tgl = str.replace("-", "_");
        param='&proses=deleteData'+'&karyawanid='+karyawanid+'&tanggaldel='+tgl;
        tujuan='sdm_slave_ijin_meninggalkan_kantor.php';
        if(confirm("Deleting, are you sure !!"))
        post_response_text(tujuan, param, respog);

        function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                        loadNData();
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	



}

// function loadSisaCuti(periode,karyawanid)
// {
//     karyawanid2 = document.getElementById('karyawanId').value;
//     param='periode='+periode+'&karyawanid='+karyawanid2;
//     tujuan='sdm_slave_ijin_getSisaCuti.php';
//     post_response_text(tujuan, param, respog);
//     function respog(){
//             if (con.readyState == 4) {
//                 if (con.status == 200) {
//                         busy_off();
//                         if (!isSaveResponse(con.responseText)) {
//                                 alert('ERROR TRANSACTION,\n' + con.responseText);
//                         }
//                         else {
//                             document.getElementById('sis').innerHTML=con.responseText;
//                         }
//                 }
//                 else {
//                         busy_off();
//                         error_catch(con.status);
//                 }
//             }
//        }    
// }

function previewPdf(tgl,karywn,ev)
{
    tglijin=tgl;
    krywnId=karywn;
    param='proses=prevPdf'+'&tglijin='+tglijin+'&krywnId='+krywnId;
    tujuan = 'sdm_slave_laporan_ijin_meninggalkan_kantor.php?'+param;	
    //display window
    title='Print PDF';
    width='700';
    height='400';
    content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
    showDialog1(title,content,width,height,ev);
}



function loadSisaCuti()
{
    periode     = document.getElementById('periodec').value;
    karyawanid  = document.getElementById('karyawanId').value;
    tipe_ijin   = document.getElementById('jnsIjin').value;
    jumlahhk   = document.getElementById('jumlahhk').value;
    tglAwal   = document.getElementById('tglAwal').value;


    param='periode='+periode+'&karyawanid='+karyawanid+'&tipe_ijin='+tipe_ijin+'&tglAwal='+tglAwal;
    tujuan='sdm_slave_ijin_getSisaCuti.php';
    
    post_response_text(tujuan, param, respog);
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                            alert('ERROR TRANSACTION,\n' + con.responseText);
                    }
                    else {
                        dataJson = JSON.parse(con.responseText);
                        
                        document.getElementById('sis').innerHTML=dataJson.sisaCuti;

                        if (dataJson.pengurang == 1) {
                            document.getElementById('jumlahhk').disabled = false;
                            if ( parseInt(jumlahhk) > parseInt(dataJson.sisaCuti)) {
                                document.getElementById('dtlForm').disabled = true;
                                document.getElementById('messageHK').innerHTML = "<b><i>*HK tidak mencukupi, tidak bisa simpan.</i></b>";
                            } else {
                                document.getElementById('dtlForm').disabled = false;
                                document.getElementById('messageHK').innerHTML = "";
                            }
                        }else{
                            document.getElementById('jumlahhk').disabled = true;
                            document.getElementById('jumlahhk').value = dataJson.jumlah_hk;
                            document.getElementById('dtlForm').disabled = false;
                            document.getElementById('messageHK').innerHTML = "";
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