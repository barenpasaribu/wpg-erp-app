/**
 * @author Developer
 */
function getTrxNumber()
{
		tahun = document.getElementById('thnplafon');
		thn=tahun.options[tahun.selectedIndex].value;		
		lokasitugas = document.getElementById('lokasitugas');
		lkstgs=lokasitugas.options[lokasitugas.selectedIndex].value;
		
        param='tahun='+thn+'&lokasitugas='+lkstgs;
                tujuan='sdm_slave_getPengobatanNumber.php';
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
                                                        document.getElementById('notransaksi').value=trim(con.responseText);
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }

}

function getFamily(karid)
{

        param='karyawanid='+karid;
        tujuan='sdm_slave_getKeluargaOpt.php';
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
                                                        document.getElementById('ygberobat').innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }

}

function calculateTotal()
{
        bylab	=remove_comma(document.getElementById('bylab'));
        byadmin	=remove_comma(document.getElementById('byadmin'));
        byobat	=remove_comma(document.getElementById('byobat'));
        bydr	=remove_comma(document.getElementById('bydr'));
        byrs	=remove_comma(document.getElementById('byrs'));
        // byfl	=remove_comma(document.getElementById('byfl'));
        ttl=parseFloat(bylab)+parseFloat(byadmin)+parseFloat(byobat)+parseFloat(bydr)+parseFloat(byrs);
        document.getElementById('total').value=ttl;
        //document.getElementById('bebanperusahaan').value=ttl;
		change_number(document.getElementById('total'));
        //change_number(document.getElementById('bebanperusahaan'));
}

function savePengobatan()
{
        thnplafon	=document.getElementById('thnplafon');
        thnplafon	=thnplafon.options[thnplafon.selectedIndex].value;	
        periode	=document.getElementById('periode');
        periode	=periode.options[periode.selectedIndex].value;	
        jenisbiaya	=document.getElementById('jenisbiaya');
        jenisbiaya	=jenisbiaya.options[jenisbiaya.selectedIndex].value;	
        karyawanid	=document.getElementById('karyawanid');
        karyawanid	=karyawanid.options[karyawanid.selectedIndex].value;		
        ygberobat	=document.getElementById('ygberobat');
        ygberobat	=ygberobat.options[ygberobat.selectedIndex].value;	
        //rs		=document.getElementById('rs');
       //rs		=rs.options[rs.selectedIndex].value;
		rs		=document.getElementById('rs').value;	   
        diagnosa	=document.getElementById('diagnosa');
        diagnosa	=diagnosa.options[diagnosa.selectedIndex].value;	
        klaim		=document.getElementById('klaim');
        klaim	=klaim.options[klaim.selectedIndex].value;

        method	=document.getElementById('method').value;
        notransaksi	=document.getElementById('notransaksi').value;
        hariistirahat	=document.getElementById('hariistirahat').value;
        if(hariistirahat=='')
          hariistirahat=0;
        tanggal		=document.getElementById('tanggal').value;
        tanggalselesai		=document.getElementById('tanggalselesai').value;
        keterangan		=document.getElementById('keterangan').value;
        byrs			=remove_comma(document.getElementById('byrs'));
        byadmin		=remove_comma(document.getElementById('byadmin'));
        //bylab			=remove_comma(document.getElementById('bylab'));
        byobat		=remove_comma(document.getElementById('byobat'));
        bydr			=remove_comma(document.getElementById('bydr'));
        bylab			=remove_comma(document.getElementById('bylab'));
        // byfl			=remove_comma(document.getElementById('byfl'));
        total			=remove_comma(document.getElementById('total'));
        //bebanperusahaan		=remove_comma(document.getElementById('bebanperusahaan'));
        //bebankaryawan		=remove_comma(document.getElementById('bebankaryawan'));        
        //bebanjamsostek		=remove_comma(document.getElementById('bebanjamsostek'));
        // alertsure = document.getElementById('alertsure').value;
        // alertsaved = document.getElementById('alertsaved').value;
        if(notransaksi=='')
        {
                alert('Transaction number is obligatory');
                document.getElementById('thnplafon').focus();
        }
        else if(total<0.1)
        {
                alert('Claim value is obligatory');
                document.getElementById('byrs').focus();		
        }
        else if(karyawanid=='')
        {
                alert('Please choose employee');
                document.getElementById('karyawanid').focus();		
        }
        else if(hariistirahat>0 && tanggal=='')
        {
                alert('Date is obligatory');
                document.getElementById('tanggal').focus();			
        }
        else
        {
                if(confirm('Are you sure?'))
                {
                   param='tahunplafon='+thnplafon+'&periode='+periode+'&jenisbiaya='+jenisbiaya;
                   param+='&karyawanid='+karyawanid+'&method='+method+'&ygberobat='+ygberobat;
                   param+='&rs='+rs+'&diagnosa='+diagnosa+'&klaim='+klaim+'&notransaksi='+notransaksi;
                   param+='&hariistirahat='+hariistirahat+'&tanggal='+tanggal+'&tanggalselesai='+tanggalselesai+'&keterangan='+keterangan;		   
                   param+='&byrs='+byrs+'&byadmin='+byadmin+'&bydr='+bydr;
                   param+='&byobat='+byobat+'&total='+total+'&bylab='+bylab;
                   //param+='&bebanperusahaan='+bebanperusahaan+'&bebankaryawan='+bebankaryawan+'&bebanjamsostek='+bebanjamsostek;
                   //param+='&bebanperusahaan='+bebanperusahaan+'&bebankaryawan='+bebankaryawan;
                   tujuan='sdm_slave_savePengobatan.php';
                   post_response_text(tujuan, param, respog);
                }
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
                            document.getElementById('container').innerHTML=con.responseText;
                            document.getElementById('mainsavebtn').disabled=true;
                            alert('Done');
                            tabAction(document.getElementById('tabFRM1'),1,'FRM',0);
                        }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
              }	
         }	
}

function clearForm()
{
        document.getElementById('notransaksi').value='';
        document.getElementById('hariistirahat').value='1';
        document.getElementById('keterangan').value='';
        document.getElementById('byrs').value='0';
        document.getElementById('byadmin').value='0';
        document.getElementById('bylab').value='0';
        document.getElementById('byobat').value='0';
        document.getElementById('bydr').value='0';
        //document.getElementById('bylab').value='0';
        // document.getElementById('byfl').value='0';
        document.getElementById('total').value='0';
        //document.getElementById('bebanperusahaan').value='0';
        //document.getElementById('bebankaryawan').value='0';
        document.getElementById('tanggal').value='';
        document.getElementById('tanggalselesai').value='';
        thnplafon		=document.getElementById('thnplafon');
                thnplafon	=thnplafon.options[0].selected=true;	
        periode			=document.getElementById('periode');
                periode		=periode.options[0].selected=true;
        jenisbiaya		=document.getElementById('jenisbiaya');
                jenisbiaya	=jenisbiaya.options[0].selected=true;	
        karyawanid		=document.getElementById('karyawanid');
                karyawanid	=karyawanid.options[0].selected=true;	
        ygberobat		=document.getElementById('ygberobat');
                ygberobat	=ygberobat.options[0].selected=true;	
        rs				=document.getElementById('rs');
                rs			=rs.options[0].selected=true;
        diagnosa		=document.getElementById('diagnosa');
                diagnosa	=diagnosa.options[0].selected=true;
        klaim			=document.getElementById('klaim');
                klaim		=klaim.options[0].selected=true;
   document.getElementById('mainsavebtn').disabled=false;
}

function saveObat()
{
        nodok=document.getElementById('notransaksi').value;
        namaobat=document.getElementById('namaobat').value;
                    jenisobat=document.getElementById('jenisobat');
                    jenisobat=jenisobat.options[jenisobat.selectedIndex].value;

        param='notransaksi='+nodok+'&namaobat='+namaobat+'&jenisobat='+jenisobat;
        tujuan='sdm_slave_saveObat.php';	
        if(nodok=='' || namaobat=='')
         alert('Document Not Valid');
        else
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
                                                   document.getElementById('container1').innerHTML=con.responseText;
                                                   alert('Done');
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }		
}

function deleteObat(id,notransaksi)
{
    param='id='+id+'&del=true&notransaksi='+notransaksi;
        tujuan='sdm_slave_saveObat.php';
        if(confirm('Deleting are you sure..?'))	
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
                                                   document.getElementById('container1').innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 
}

function deletePengobatan(notransaksi)
{
		cnfdel=document.getElementById('cnfdel').value;
        param='notransaksi='+notransaksi+'&method=del';
        tujuan='sdm_slave_savePengobatan.php';
        //if(confirm('You are deleting '+notransaksi+', are you sure?'))
        if(confirm(cnfdel))
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

function loadPengobatan(thn)
{
        param='tahunplafon='+thn+'&method=none';
        tujuan='sdm_slave_savePengobatan.php';
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

function loadKaryawan(){
	org=document.getElementById('optkodeorg').options[document.getElementById('optkodeorg').selectedIndex].value;
	param='kodeorg='+org+'&method=8'; //alert(param);
    tujuan='sdm_slave_getPengobatanList.php';
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
                    document.getElementById('optkryids').innerHTML=con.responseText;
					loadPengobatanPrint();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }
}

function loadPengobatanPrint()
{
    per=document.getElementById('optplafon').options[document.getElementById('optplafon').selectedIndex].value;
    org=document.getElementById('optkodeorg').options[document.getElementById('optkodeorg').selectedIndex].value;
    kryid=document.getElementById('optkryids').options[document.getElementById('optkryids').selectedIndex].value;
    //rs=document.getElementById('optrs').options[document.getElementById('optrs').selectedIndex].value;
    //param='periode='+per+'&kodeorg='+org+'&rs='+rs+'&method=1'; //alert(param);
    param='periode='+per+'&kodeorg='+org+'&karyawanid='+kryid+'&method=1'; //alert(param);
    tujuan='sdm_slave_getPengobatanList.php';
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

function loadPengobatanPrint1()
{
    per=document.getElementById('optplafon1').options[document.getElementById('optplafon1').selectedIndex].value;
    org=document.getElementById('optkodeorg1').options[document.getElementById('optkodeorg1').selectedIndex].value;

    param='periode='+per+'&kodeorg='+org+'&method=2'; //alert(param);
    tujuan='sdm_slave_getPengobatanList.php';
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
                    document.getElementById('container1').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }			
}

function loadPengobatanPrint2()
{
    per=document.getElementById('optplafon2').options[document.getElementById('optplafon2').selectedIndex].value;
    org=document.getElementById('optkodeorg2').options[document.getElementById('optkodeorg2').selectedIndex].value;

    param='periode='+per+'&kodeorg='+org+'&method=3'; //alert(param);
    tujuan='sdm_slave_getPengobatanList.php';
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

function loadPengobatanPrint3()
{
    per=document.getElementById('optplafon3').options[document.getElementById('optplafon3').selectedIndex].value;
    org=document.getElementById('optkodeorg3').options[document.getElementById('optkodeorg3').selectedIndex].value;

    param='periode='+per+'&kodeorg='+org+'&method=4'; //alert(param);
    tujuan='sdm_slave_getPengobatanList.php';
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
                    document.getElementById('container3').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }			
}

function loadPengobatanPrint4()
{
    per=document.getElementById('optplafon4').options[document.getElementById('optplafon4').selectedIndex].value;
    org=document.getElementById('optkodeorg4').options[document.getElementById('optkodeorg4').selectedIndex].value;

    param='periode='+per+'&kodeorg='+org+'&method=5'; //alert(param);
    tujuan='sdm_slave_getPengobatanList.php';
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
                    document.getElementById('container4').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }			
}

function printKlaim()
{
    per=document.getElementById('optplafon').options[document.getElementById('optplafon').selectedIndex].value;
    org=document.getElementById('optkodeorg').options[document.getElementById('optkodeorg').selectedIndex].value;
    //rs=document.getElementById('optrs').options[document.getElementById('optrs').selectedIndex].value;
    //document.getElementById('frmku').src='sdm_2laporanKlaimToExcel.php?periode='+per+'&kodeorg='+org+'&rs='+rs;	
    document.getElementById('frmku').src='sdm_2laporanKlaimToExcel.php?periode='+per+'&kodeorg='+org;	
}

function printKlaim1()
{
    per=document.getElementById('optplafon1').options[document.getElementById('optplafon1').selectedIndex].value;
    org=document.getElementById('optkodeorg1').options[document.getElementById('optkodeorg1').selectedIndex].value;
    document.getElementById('frmku1').src='sdm_2laporanKlaimToExcel1.php?periode='+per+'&kodeorg='+org;	
}

function printKlaim2()
{
    per=document.getElementById('optplafon2').options[document.getElementById('optplafon2').selectedIndex].value;
    org=document.getElementById('optkodeorg2').options[document.getElementById('optkodeorg2').selectedIndex].value;
    document.getElementById('frmku2').src='sdm_2laporanKlaimToExcel2.php?periode='+per+'&kodeorg='+org;	
}

function printKlaim3()
{
    per=document.getElementById('optplafon3').options[document.getElementById('optplafon3').selectedIndex].value;
    org=document.getElementById('optkodeorg3').options[document.getElementById('optkodeorg3').selectedIndex].value;
    document.getElementById('frmku3').src='sdm_2laporanKlaimToExcel3.php?periode='+per+'&kodeorg='+org;	
//    alert(org);
}
function printKlaim4()
{
     per=document.getElementById('optplafon4').options[document.getElementById('optplafon4').selectedIndex].value;
    org=document.getElementById('optkodeorg4').options[document.getElementById('optkodeorg4').selectedIndex].value;
    document.getElementById('frmku3').src='sdm_2laporanKlaimToExcel4.php?periode='+per+'&kodeorg='+org;   
}

function previewPengobatan(notransaksi,ev)
{
    param='notransaksi='+notransaksi;
    tujuan='sdm_slave_previewPengobatan.php';
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
                                                       title=notransaksi;
                                                       width='500';
                                                       height='400';
                                                       content="<div style='height:380px;width:480px;overflow:scroll;'>"+con.responseText+"</div>";
                                                       showDialog1(title,content,width,height,ev);
                                            }
                                    }
                                    else {
                                            busy_off();
                                            error_catch(con.status);
                                    }
                  }	
     }			
}

function savePClaim(no,notransaksi,totalklaim,limit,plafon,ls,sisa)
{
	cnfsimpan=document.getElementById('cnfsimpan').value;
    bayar=remove_comma(document.getElementById('bayar'+no));
    tglbayar=remove_comma(document.getElementById('tglbayar'+no));
    datatdklkp=document.getElementById('datatdklkp').value;
    bayartdkblh=document.getElementById('bayartdkblh').value;
    limithbs=document.getElementById('limithbs').value;
    plafonksng=document.getElementById('plafonksng').value;
    sisalimit=document.getElementById('sisalimit').value;
    if(notransaksi=='' || bayar=='' || tglbayar.length!=10)
    {
            alert(datatdklkp);
    }
    else if(bayar==0.00)
    {
            alert(bayartdkblh);
    }
    else
    {		
			if ((limit==0 && plafon==0&& ls==0)){
				
				param='notransaksi='+notransaksi+'&bayar='+bayar+'&tglbayar='+tglbayar+'&totalklaim='+totalklaim+'&plafon='+sisa;
				//if(confirm('Saving payment '+notransaksi+', Are you sure..?'))
				if(confirm(cnfsimpan))
				tujuan='sdm_simpanPembayaranKlaim.php';
				post_response_text(tujuan, param, respog);
				
				
			}
			else if (limit==1 && ls==0){
				alert(limithbs);
			}
			else if (limit==1 && ls==1 ){
				if (bayar>sisa){
					alert(sisalimit+': '+sisa);
				}
				else {
					param='notransaksi='+notransaksi+'&bayar='+bayar+'&tglbayar='+tglbayar+'&totalklaim='+totalklaim+'&plafon='+sisa;
					//if(confirm('Saving payment '+notransaksi+', Are you sure..?'))
					if(confirm(cnfsimpan))
					tujuan='sdm_simpanPembayaranKlaim.php';
					post_response_text(tujuan, param, respog);
				}
				
			}
            else if (plafon==1){
				alert(plafonksng);
			}
    }
    function respog()
    {
                  if(con.readyState==4)
                  {
                            if (con.status == 200) {
                                            busy_off();
                                            if (!isSaveResponse(con.responseText)) {
                                                    document.getElementById('bayar'+no).style.backgroundColor='red';
                                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                                            }
                                            else {
                                                    document.getElementById('bayar'+no).style.backgroundColor='#C3DAF9';
                                            }
                                    }
                                    else {
                                            busy_off();
                                            error_catch(con.status);
                                    }
                  }	
     }	
}

function loadOptkar(lokasitugas){
    param='kodeorganisasi='+lokasitugas+'&method=getKary';
    tujuan='sdm_slaveGetKaryawanPengobatan.php';
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
                                    document.getElementById('karyawanid').innerHTML=con.responseText;
									getTrxNumber();
                            }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
            }	
         }    
}

function previewPerorang(karyawanid,ev)
{
        tahun=document.getElementById('optplafon2').options[document.getElementById('optplafon2').selectedIndex].value;
        param='karyawanid='+karyawanid+'&tahun='+tahun;
        tujuan='sdm_slave_previewPengobatanPerorang.php';
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
                                                   title='Medical detail:'+karyawanid+' Period:'+tahun;
                                                   width='620';
                                                   height='400';
                                                   content="<div style='height:380px;width:600px;overflow:scroll;'>"+con.responseText+"</div>";
                                                   showDialog1(title,content,width,height,ev);
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                    }	
         }			
}

function loadPengobatanPrint5()
{
    per=document.getElementById('optplafon5').options[document.getElementById('optplafon5').selectedIndex].value;
    kodeorg=document.getElementById('optkodeorg5').options[document.getElementById('optkodeorg5').selectedIndex].value;

    param='periode='+per+'&kodeorg='+kodeorg+'&method=6'; //alert(param);
    tujuan='sdm_slave_getPengobatanList.php';
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
                    document.getElementById('container5').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }			
}

function printKlaim5()
{
     per=document.getElementById('optplafon5').options[document.getElementById('optplafon5').selectedIndex].value;
    //karyawanid=document.getElementById('karyawanid').options[document.getElementById('karyawanid').selectedIndex].value;
    kodeorg=document.getElementById('optkodeorg5').options[document.getElementById('optkodeorg5').selectedIndex].value;
    //nama=document.getElementById('karyawanid').options[document.getElementById('karyawanid').selectedIndex].text;
    document.getElementById('frmku5').src='sdm_2laporanKlaimToExcel5.php?periode='+per+'&kodeorg='+kodeorg+'&periode='+per;   
}

function loadPengobatanPrint6()
{
    per=document.getElementById('optplafon6').options[document.getElementById('optplafon6').selectedIndex].value;
    org=document.getElementById('optkodeorg6').options[document.getElementById('optkodeorg6').selectedIndex].value;

    param='periode='+per+'&kodeorg='+org+'&method=7'; //alert(param);
    tujuan='sdm_slave_getPengobatanList.php';
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
                    document.getElementById('container6').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }			
}

function printKlaim6()
{
     per=document.getElementById('optplafon6').options[document.getElementById('optplafon6').selectedIndex].value;
    org=document.getElementById('optkodeorg6').options[document.getElementById('optkodeorg6').selectedIndex].value;
    document.getElementById('frmku6').src='sdm_2laporanKlaimToExcel6.php?periode='+per+'&kodeorg='+org;   
}

function getDaftar()
{
   per=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
   
   window.location='?periode='+per;
}

function kurangkanTotal(obj){
         a=parseFloat(remove_comma(document.getElementById('total')));
         b=parseFloat(remove_comma(document.getElementById('bebankaryawan')));
         //c=parseFloat(remove_comma(document.getElementById('bebanjamsostek')));
         //pangurang=b+c;
         pangurang=b;
         document.getElementById('bebanperusahaan').value=a-pangurang;
        change_number(document.getElementById('bebanperusahaan'));
        change_number(document.getElementById('bebankaryawan'));
        //change_number(document.getElementById('bebanjamsostek')); 
}
function printRekapKlaim(){
   per=document.getElementById('optplafon').options[document.getElementById('optplafon').selectedIndex].value;
  document.getElementById('frmku').src='sdm_2laporanKlaimRekapExcel.php?periode='+per;
}
/*editPengobatan(notransaksi,karyawanid,jenisbiaya,lokasitugas,thnplafon,periode,rs,byrs,bydr,bylab,byobat,byadmin,ygberobat,diagnosa,tanggal,total,hariistirahat,klaim,keterangan)*/

function editPengobatan(notransaksi,karyawanid,jenisbiaya,lokasitugas,thnplafon,periode,rs,byrs,bydr,bylab,byobat,byadmin,ygberobat,diagnosa,tanggal,tanggalsls,total,hariistirahat,klaim,keterangan)
{         
    document.getElementById('notransaksi').value=notransaksi;
    document.getElementById('karyawanid').value=karyawanid;
    document.getElementById('thnplafon').value=thnplafon;
    document.getElementById('thnplafon').disabled=true
    document.getElementById('periode').value=periode;
    document.getElementById('lokasitugas').value=lokasitugas;
    document.getElementById('jenisbiaya').value=jenisbiaya;
    document.getElementById('rs').value=rs.replace(/%20/g, " ");
    document.getElementById('byrs').value=byrs;
    document.getElementById('bydr').value=bydr;
    document.getElementById('bylab').value=bylab;
    document.getElementById('byobat').value=byobat;
    document.getElementById('byadmin').value=byadmin;
    document.getElementById('ygberobat').value=ygberobat;
    document.getElementById('diagnosa').value=diagnosa;
	if(tanggal=='00-00-0000' || tanggal=='30-11--0001'){
		document.getElementById('tanggal').value='';
	}
	else{
		document.getElementById('tanggal').value=tanggal;
	}
	
	if(tanggalsls=='00-00-0000' || tanggalsls=='30-11--0001'){
		 document.getElementById('tanggalselesai').value='';
	}
	else{
		 document.getElementById('tanggalselesai').value=tanggalsls;
	}
    
   
    document.getElementById('total').value=total;
    document.getElementById('hariistirahat').value=hariistirahat;
    //document.getElementById('bebankaryawan').value=bebankaryawan;
    //document.getElementById('bebanjamsostek').value=bebanjamsostek;
    //document.getElementById('bebanperusahaan').value=bebanperusahaan;
    document.getElementById('klaim').value=klaim;
    document.getElementById('keterangan').value=keterangan.replace(/%20/g, " ");
    document.getElementById('method').value="update";
    param='notransaksi='+notransaksi+'&karyawanid='+karyawanid+'&jenisbiaya='+jenisbiaya;
    param+='&lokasitugas='+lokasitugas+'&thnplafon='+thnplafon;
    param+='&periode='+periode+'&rs='+rs+'&byrs='+byrs+'&bydr='+bydr+'&bylab='+bylab;
    param+='&byobat='+byobat+'&byadmin='+byadmin+'&ygberobat='+ygberobat+'&diagnosa='+diagnosa;
    param+='&tanggal='+tanggal+'&total='+total+'&klaim='+klaim+'&keterangan='+keterangan;
    param+='&hariistirahat='+hariistirahat;
//    alert(param);
	tujuan='sdm_slave_savePengobatan.php';
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
                                                       tabAction(document.getElementById('tabFRM0'),0,'FRM',1);
                                                       document.getElementById('mainsavebtn').disabled=false;
                                                    }
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }	
}
