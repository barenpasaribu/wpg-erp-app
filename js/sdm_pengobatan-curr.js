/**
 * @author antoniuslouis
 */
function getTrxNumber(thn)
{
        param='tahun='+thn;
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

function getFamily(karid){

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
                                                        belahd=con.responseText.split("####");
                                                        document.getElementById('ygberobat').innerHTML=belahd[0];
                                                        document.getElementById('mediIdCard').value=belahd[1];
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }

}
function getMedId(nmrurut){
        dert=document.getElementById('tipeDt');
        if(dert.value=='MASYRAKAT'){
            return;
        }
        param='nomor='+nmrurut+'&method=getMedId';
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
                                                document.getElementById('mediIdCard').value=con.responseText;
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
        byrtrs	=remove_comma(document.getElementById('byTransport'));
        ttl=parseFloat(bylab)+parseFloat(byadmin)+parseFloat(byobat)+parseFloat(bydr)+parseFloat(byrs)+parseFloat(byrtrs);
        document.getElementById('total').value=ttl;
        document.getElementById('bebanperusahaan').value=ttl;
        change_number(document.getElementById('total'));
        change_number(document.getElementById('bebanperusahaan'));
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
        rs		=document.getElementById('rs');
       rs		=rs.options[rs.selectedIndex].value;	
        diagnosa	=document.getElementById('diagnosa');
        diagnosa	=diagnosa.options[diagnosa.selectedIndex].value;	
        klaim		=document.getElementById('klaim');
        klaim	=klaim.options[klaim.selectedIndex].value;
        tipe		=document.getElementById('tipeDt');
        tipe	=tipe.options[tipe.selectedIndex].value;

        method	=document.getElementById('method').value;
        notransaksi	=document.getElementById('notransaksi').value;
        hariistirahat	=document.getElementById('hariistirahat').value;
        if(hariistirahat=='')
          hariistirahat=0;
        tanggal		=document.getElementById('tanggal').value;
        medid           =document.getElementById('mediIdCard').value;
        keterangan		=document.getElementById('keterangan').value;
        byrs			=remove_comma(document.getElementById('byrs'));
        byadmin		=remove_comma(document.getElementById('byadmin'));
        bylab			=remove_comma(document.getElementById('bylab'));
        byobat		=remove_comma(document.getElementById('byobat'));
        bydr			=remove_comma(document.getElementById('bydr'));
        bylab			=remove_comma(document.getElementById('bylab'));
        byTransport		=remove_comma(document.getElementById('byTransport'));
        total			=remove_comma(document.getElementById('total'));
        bebanperusahaan		=remove_comma(document.getElementById('bebanperusahaan'));
        bebankaryawan		=remove_comma(document.getElementById('bebankaryawan'));        
        bebanjamsostek		=remove_comma(document.getElementById('bebanjamsostek'));
        
        if(tipe!='MASYRAKAT'){
            if(karyawanid==''){//idmedical
                    alert('Please choose employee');
                    document.getElementById('karyawanid').focus();		
            }
        }
        
        
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
        else if(tanggal=='')
        {
                alert('Date is obligatory');
                document.getElementById('tanggal').focus();			
        }
        else
        {
                if(confirm('Saving, are you sure..?'))
                {
                   param='tahunplafon='+thnplafon+'&periode='+periode+'&jenisbiaya='+jenisbiaya;
                   param+='&karyawanid='+karyawanid+'&method='+method+'&ygberobat='+ygberobat;
                   param+='&rs='+rs+'&diagnosa='+diagnosa+'&klaim='+klaim+'&notransaksi='+notransaksi;
                   param+='&hariistirahat='+hariistirahat+'&tanggal='+tanggal+'&keterangan='+keterangan;		   
                   param+='&byrs='+byrs+'&byadmin='+byadmin+'&bydr='+bydr+'&byTransport='+byTransport;
                   param+='&byobat='+byobat+'&total='+total+'&bylab='+bylab+'&tipeDt='+tipe+'&mediIdCard='+medid;
                   param+='&bebanperusahaan='+bebanperusahaan+'&bebankaryawan='+bebankaryawan+'&bebanjamsostek='+bebanjamsostek;
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
        document.getElementById('bylab').value='0';
        document.getElementById('total').value='0';
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

        param='notransaksi='+notransaksi+'&method=del';
        tujuan='sdm_slave_savePengobatan.php';
        if(confirm('You are deleting '+notransaksi+', are you sure?'))
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

function loadPengobatanPrint()
{
    per=document.getElementById('optplafon').options[document.getElementById('optplafon').selectedIndex].value;
    org=document.getElementById('optkodeorg').options[document.getElementById('optkodeorg').selectedIndex].value;
    rs=document.getElementById('optrs').options[document.getElementById('optrs').selectedIndex].value;

    param='periode='+per+'&kodeorg='+org+'&rs='+rs+'&method=1'; //alert(param);
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
    rs=document.getElementById('optrs').options[document.getElementById('optrs').selectedIndex].value;
    document.getElementById('frmku').src='sdm_2laporanKlaimToExcel.php?periode='+per+'&kodeorg='+org+'&rs='+rs;	
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

function savePClaim(no,notransaksi)
{
    bayar=remove_comma(document.getElementById('bayar'+no));
    tglbayar=remove_comma(document.getElementById('tglbayar'+no));

    if(notransaksi=='' || bayar=='' || tglbayar.length!=10)
    {
            alert('Data incomplete');
    }
    else if(bayar=='')
    {
            alert('Payment can not be empty');
    }
    else
    {
            param='notransaksi='+notransaksi+'&bayar='+bayar+'&tglbayar='+tglbayar;
            if(confirm('Saving payment '+notransaksi+', Are you sure..?'))
            tujuan='sdm_simpanPembayaranKlaim.php';
            post_response_text(tujuan, param, respog);
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

function loadOptkar(lokasitugas,idkary){
    param='kodeorganisasi='+lokasitugas;
    if(idkary!=0){
        param+='&karyawanid='+idkary;
    }
    param+='&method=getKary';
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
                                    if(idkary!=0){
                                        getFamily(idkary);
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
    karyawanid=document.getElementById('karyawanid').options[document.getElementById('karyawanid').selectedIndex].value;

    param='periode='+per+'&karyawanid='+karyawanid+'&method=6'; //alert(param);
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
    karyawanid=document.getElementById('karyawanid').options[document.getElementById('karyawanid').selectedIndex].value;
    nama=document.getElementById('karyawanid').options[document.getElementById('karyawanid').selectedIndex].text;
    document.getElementById('frmku5').src='sdm_2laporanKlaimToExcel5.php?periode='+per+'&karyawanid='+karyawanid+'&nama='+nama;   
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
         c=parseFloat(remove_comma(document.getElementById('bebanjamsostek')));
         pangurang=b+c;
         document.getElementById('bebanperusahaan').value=a-pangurang;
        change_number(document.getElementById('bebanperusahaan'));
        change_number(document.getElementById('bebankaryawan'));
        change_number(document.getElementById('bebanjamsostek')); 
}
function printRekapKlaim(){
   per=document.getElementById('optplafon').options[document.getElementById('optplafon').selectedIndex].value;
  document.getElementById('frmku').src='sdm_2laporanKlaimRekapExcel.php?periode='+per;
}
function formDt(){
    dert=document.getElementById('tipeDt').value;
    document.getElementById('karyawanid').value="";
    document.getElementById('lokasitugas').value="";
    document.getElementById('mediIdCard').value="";
    document.getElementById('ygberobat').innerHTML="<option value=''></option>";
    if(dert=='MASYRAKAT'){
        document.getElementById('lokasitugas').disabled=true;
        document.getElementById('karyawanid').disabled=true;
        document.getElementById('mediIdCard').disabled=false;
        
    }else{
        document.getElementById('lokasitugas').disabled=false;
        document.getElementById('karyawanid').disabled=false;
        document.getElementById('mediIdCard').disabled=true;
    }
    
}
function showDetail(ev)
{
        title=med+" "+dcari;
        content="<fieldset><legend>"+dcari+"</legend>\n\
                 <div id=contDetail style='overflow:auto;width:450px'></div></fieldset>\n\
                 ";
        width='450px';
        height='450px';
        showDialog1(title,content,width,height,ev);	
}
function getForm(){
        dert=document.getElementById('tipeDt');
        if(dert.value=='MASYRAKAT'){
            return;
        }
        kar=document.getElementById('karyawanid');
        kar=kar.options[kar.selectedIndex].value;
        ygbrobat=document.getElementById('ygberobat');
        ygbrobat=ygbrobat.options[ygbrobat.selectedIndex].value;
        param='karyawanid='+kar+'&method=getForm';
        param+='&ygberobat='+ygbrobat;
        showDetail('event');
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
                                                        document.getElementById('contDetail').innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  
      
}
function cariData(){
    kar=document.getElementById('karyId').value;
    ygobat=document.getElementById('ygBerobat').value;
    txtCari=document.getElementById('txtCr').value;
    param='method=cari'+'&karyId='+kar+'&txtCr='+txtCari+'&ygBerobat='+ygobat;
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
                                                        document.getElementById('hslCari').innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  
}
function setDt(med,kary,lksitgs){
    loadOptkar(lksitgs,kary);
    document.getElementById('mediIdCard').value=trim(med);//karyawanid
    hk=document.getElementById('lokasitugas');
    for(x=0;x<hk.length;x++){
            if(hk.options[x].value==lksitgs)
            {
                    hk.options[x].selected=true;
            }
    }
    document.getElementById('tipeDt').value="KARYAWAN";
    closeDialog();
}