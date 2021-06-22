// JavaScript Document
function getForm()
{
        kdBrg=document.getElementById('kdBrg').value;
        param='kdBrg='+kdBrg+'&method=GetForm';
        tujuan='pabrik_slave_timbangan.php';
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
                                                        document.getElementById('content').innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 	
}
function fillField(notrans,kdbrg,nmcust,factryId,wktMsk,wktKeluar,statBuah,thnTnm1,thnTnm2,thnTnm3,kdKbn,tmbngn)
{
        noTrans=notrans;
        kdBrg=kdbrg;
        idCust=nmcust;
        kdPbrk=factryId;
        jmMasuk=wktMsk;
        jmKeluar=wktKeluar;
        BuahStat=statBuah;
        thntnm1=thnTnm1;
        thntnm2=thnTnm2;
        thntnm3=thnTnm3;
        kdkbn=kdKbn;
        statTmbngn=tmbngn;
        tabAction(document.getElementById('tabFRM0'),0,'FRM',1);
        document.getElementById('kdBrg').value=kdBrg;
        document.getElementById('kdBrg').disabled=true;

        param='noTrans='+noTrans+'&kdBrg='+kdBrg+'&idCust='+idCust+'&kdPbrk='+kdPbrk;
        param+='&jmMasuk='+jmMasuk+'&jmKeluar='+jmKeluar+'&BuahStat='+BuahStat+'&thntnm1='+thntnm1;
        param+='&thntnm2='+thntnm2+'&thntnm3='+thntnm2+'&method=GetForm'+'&kdKbn='+kdkbn+'&statTmbngn='+statTmbngn;
        //alert(param);
        tujuan='pabrik_slave_timbangan.php';
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
                                                        document.getElementById('content').innerHTML=con.responseText;
                                                        document.getElementById('method').value='update';
                                                        getKbn(kdkbn,BuahStat);

                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 	
}
function saveCpk()
{
        notrans=document.getElementById('noTrans').value;
        noKontrk=document.getElementById('nokontrk').value;
        idCust=document.getElementById('nmCust').value;
        kdpabrik=document.getElementById('kdPbrik').value;
        nDo=document.getElementById('nodo').value;
        nosipb=document.getElementById('nosipb').value;
        npol=document.getElementById('nopol').value;
        nmSpir=document.getElementById('spir').value;
        brtKsng=document.getElementById('brtKosong').value;
        brtBrsih=document.getElementById('brtBersih').value;
        brtKlr=document.getElementById('brtKeluar').value;
        jmMasuk=document.getElementById('jmMasuk').value;
        mntMasuk=document.getElementById('mntMasuk').value;
        jmKeluar=document.getElementById('jmKeluar').value;
        mntKeluar=document.getElementById('mntKeluar').value;
        kdBrg=document.getElementById('kdBrg').value;
        tgl=document.getElementById('tglTrans').value;
        statTimbangan=document.getElementById('statTmbngn').options[document.getElementById('statTmbngn').selectedIndex].value;
        jamMasuk=jmMasuk+':'+mntMasuk;
        jamKeluar=jmKeluar+':'+mntKeluar;
        meth=document.getElementById('method').value;
        param='noTrans='+notrans+'&noKontrak='+noKontrk+'&idCust='+idCust+'&noDo='+nDo+'&nosipb='+nosipb;
        param+='&nopol='+npol+'&nmSpir='+nmSpir+'&brtKsng='+brtKsng+'&brtBrsih='+brtBrsih+'&brtKlr='+brtKlr+'&jamMasuk='+jamMasuk;
        param+='&jamKeluar='+jamKeluar+'&method='+meth+'&kdBrg='+kdBrg+'&kdpabrik='+kdpabrik+'&tglTrans='+tgl+'&statTmbngn='+statTimbangan;
        //alert(param);
        tujuan='pabrik_slave_timbangan_insert.php';
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
                                                        alert("Save Data Success !!!");
                                                        clearDt();
                                                        loadNewData();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 	

}
function saveJk()
{
        notrans=document.getElementById('noTrans').value;
        idCust=document.getElementById('nmCust').value;
        kdpabrik=document.getElementById('kdPbrik').value;
        npol=document.getElementById('nopol').value;
        nmSpir=document.getElementById('spir').value;
        brtKsng=document.getElementById('brtKosong').value;
        brtBrsih=document.getElementById('brtBersih').value;
        brtKlr=document.getElementById('brtKeluar').value;
        jmMasuk=document.getElementById('jmMasuk').value;
        mntMasuk=document.getElementById('mntMasuk').value;
        jmKeluar=document.getElementById('jmKeluar').value;
        mntKeluar=document.getElementById('mntKeluar').value;
        kdBrg=document.getElementById('kdBrg').value;
        tgl=document.getElementById('tglTrans').value;
        jamMasuk=jmMasuk+':'+mntMasuk;
        jamKeluar=jmKeluar+':'+mntKeluar;
        meth=document.getElementById('method').value;
        param='noTrans='+notrans+'&idCust='+idCust;
        param+='&nopol='+npol+'&nmSpir='+nmSpir+'&brtKsng='+brtKsng+'&brtBrsih='+brtBrsih+'&brtKlr='+brtKlr+'&jamMasuk='+jamMasuk;
        param+='&jamKeluar='+jamKeluar+'&method='+meth+'&kdBrg='+kdBrg+'&kdpabrik='+kdpabrik+'&tglTrans='+tgl;
        //alert(param);
        tujuan='pabrik_slave_timbangan_insert.php';
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
                                                        alert("Save Data Success !!!");
                                                        clearDt();
                                                        loadNewData();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 	
}
function saveTbs()
{
        notrans=document.getElementById('noTrans').value;
        idCust=document.getElementById('suppId').value;
        kdpabrik=document.getElementById('kdPbrik').value;
        npol=document.getElementById('nopol').value;
        nmSpir=document.getElementById('spir').value;
        brtKsng=document.getElementById('brtKosong').value;
        brtBrsih=document.getElementById('brtBersih').value;
        brtKlr=document.getElementById('brtKeluar').value;
        jmMasuk=document.getElementById('jmMasuk').value;
        mntMasuk=document.getElementById('mntMasuk').value;
        jmKeluar=document.getElementById('jmKeluar').value;
        mntKeluar=document.getElementById('mntKeluar').value;
        kdBrg=document.getElementById('kdBrg').value;
        tgl=document.getElementById('tglTrans').value;
        statBuah=document.getElementById('statBuah').value;
        kdorg=document.getElementById('kdOrg').value;
        nospb=document.getElementById('noSpb').value;
        statSortaso=document.getElementById('statSortasi').value;
        ptgsSortasi=document.getElementById('tgsSortasi').value;
        thnTnm1=document.getElementById('thnTnm1').value;
        thnTnm2=document.getElementById('thnTnm2').value;
        thnTnm3=document.getElementById('thnTnm3').value;

        jmlTndn1=document.getElementById('jmlhTndn1').value;
        jmlTndn2=document.getElementById('jmlhTndn2').value;
        jmlTndn3=document.getElementById('jmlhTndn3').value;
        jamMasuk=jmMasuk+':'+mntMasuk;
        jamKeluar=jmKeluar+':'+mntKeluar;
        meth=document.getElementById('method').value;
        param='noTrans='+notrans+'&noSpb='+nospb+'&kdOrg='+kdorg+'&statSortasi='+statSortaso+'&tgsSortasi='+ptgsSortasi;
        param+='&nopol='+npol+'&nmSpir='+nmSpir+'&brtKsng='+brtKsng+'&brtBrsih='+brtBrsih+'&brtKlr='+brtKlr+'&jamMasuk='+jamMasuk;
        param+='&jamKeluar='+jamKeluar+'&method='+meth+'&kdBrg='+kdBrg+'&kdpabrik='+kdpabrik+'&tglTrans='+tgl;
        param+='&thnTnm1='+thnTnm1+'&thnTnm2='+thnTnm2+'&thnTnm3='+thnTnm3;
        param+='&jmlhTndn1='+jmlTndn1+'&jmlhTndn2='+jmlTndn2+'&jmlhTndn3='+jmlTndn3+'&statBuah='+statBuah+'&idCust='+idCust;
        //alert(param);
        tujuan='pabrik_slave_timbangan_insert.php';
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
                                                        alert("Save Data Success !!!");
                                                        clearDt();
                                                        loadNewData();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 	

}
function getKbn(kdkbn,bhStat,kdpabrk)
{
        if((kdkbn=='0')||(bhStat=='0')||(kdpabrk=='0'))
        {
                buahStat=document.getElementById('statBuah').options[document.getElementById('statBuah').selectedIndex].value;
                kdpabrik=document.getElementById('kdPbrik').options[document.getElementById('kdPbrik').selectedIndex].value;
                if(kdpabrik=='')
                    {
                        alert("Error: Field Tidak Boleh Kosong");
                        return;
                    }
                param='method=getkbn'+'&BuahStat='+buahStat+'&kdpabrik='+kdpabrik;	
        }
        else
        {
                kdKbn=kdkbn;
                buahStat=bhStat;
                kdpabrik=kdpabrk;
                param='method=getkbn'+'&BuahStat='+buahStat+'&kdKbn='+kdKbn;	
        }
        //alert(param);
        tujuan='pabrik_slave_timbangan.php';
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
                                                        //alert("Save Data Success !!!");
                                                        ar=con.responseText.split("###")
                                                        if(ar[1]==0)
                                                        {
                                                                document.getElementById('suppId').innerHTML=ar[0];
                                                                document.getElementById('kdOrg').disabled=true;
                                                                document.getElementById('suppId').disabled=false;
                                                        }
                                                        else if(ar[1]!=0)
                                                        {
                                                                document.getElementById('kdOrg').innerHTML=ar[0];
                                                                document.getElementById('suppId').disabled=true;
                                                                document.getElementById('kdOrg').disabled=false;

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
function clearDt()
{
        document.getElementById('content').innerHTML='';
        document.getElementById('kdBrg').disabled=false;
        document.getElementById('kdBrg').value='';
}
function displayList()
{
    document.getElementById('txtnotransaksi').value='';
    document.getElementById('kdBrgCari').value='';
    document.getElementById('tglCari').value='';
    loadNewData();
}
function loadNewData()
{
        param='method=loadData';
        tujuan='pabrik_slave_timbangan.php';
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

                                                        document.getElementById('containerlist').innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 	

}
function delData(noTrans)
{
        notrans=noTrans;
        param='method=delData'+'&noTrans='+notrans
        tujuan='pabrik_slave_timbangan_insert.php';

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
                                                        alert("Delete Data Success !!!");
                                                        loadNewData();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 
         if(confirm("Yakin akan hapus data ?"))
         {post_response_text(tujuan, param, respog);}

}
function cariBast(num)
{
                param='method=loadData';
                param+='&page='+num;
                tujuan = 'pabrik_slave_timbangan.php';
                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('containerlist').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}
function cariNotansaksi()
{
        txtSearch=document.getElementById('txtnotransaksi').value;
//        kdBrg=document.getElementById('kdBrgCari').options[document.getElementById('kdBrgCari').selectedIndex].value;
//        tglCari=document.getElementById('tglCari').value;
//	param='txtSearch='+txtSearch+'&method=cariNotransaksi'+'&kdBrg='+kdBrg+'&tglCari='+tglCari;
        param='txtSearch='+txtSearch+'&method=cariNotransaksi';
//	alert(param);
        tujuan='pabrik_slave_timbangan.php';
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
                                                        document.getElementById('containerlist').innerHTML=con.responseText;

                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 
}
function cariTrk(num)
{
                txtSearch=document.getElementById('txtnotransaksi').value;
                kdBrg=document.getElementById('kdBrgCari').options[document.getElementById('kdBrgCari').selectedIndex].value;
                tglCari=document.getElementById('tglCari').value;
                param='txtSearch='+txtSearch+'&method=cariNotransaksi'+'&kdBrg='+kdBrg+'&tglCari='+tglCari;
                param+='&page='+num;
                tujuan = 'pabrik_slave_timbangan.php';
                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('containerlist').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}