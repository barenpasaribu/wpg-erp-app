// JavaScript Document
function save_pil()
{

        comId=document.getElementById('company_id').options[document.getElementById('company_id').selectedIndex].value;
        kdVhc=document.getElementById('kdVhc').options[document.getElementById('kdVhc').selectedIndex].value;
         jnsVhc=document.getElementById('jnsVhc').options[document.getElementById('jnsVhc').selectedIndex].value;
        //periodT=document.getElementById('period').options[document.getElementById('period').selectedIndex].value;
        tglAwl=document.getElementById('tglAwal').value;
        tglAkhr=document.getElementById('tglAkhir').value;
        param='comId='+comId+'&kdVhc='+kdVhc+'&proses=get_result'+'&tglAkhir='+tglAkhr+'&tglAwal='+tglAwl+'&jenisVhc='+jnsVhc;
      //alert(param);
        tujuan='vhc_slave_laporanPenggunaanKomponen.php';
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
                                                document.getElementById('company_id').disabled=true;
                                                document.getElementById('kdVhc').disabled=true;
                                                document.getElementById('jnsVhc').disabled=true;
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
function ganti_pil()
{
        document.getElementById('company_id').value='';
        document.getElementById('jnsVhc').value='';
        document.getElementById('kdVhc').value='';
        document.getElementById('company_id').disabled=false;
        document.getElementById('kdVhc').disabled=false;
        document.getElementById('jnsVhc').disabled=false;
        document.getElementById('tglAwal').value='';
        document.getElementById('tglAkhir').value='';
        //document.getElementById('period').disabled=false;
        document.getElementById('contain').innerHTML=''; 
        //document.getElementById('nm_goods').value='';
}
function get_sortVhc()
{
        comId=document.getElementById('company_id').options[document.getElementById('company_id').selectedIndex].value;
        jnsVhc=document.getElementById('jnsVhc').options[document.getElementById('jnsVhc').selectedIndex].value;
        param='comId='+comId+'&proses=getKdvhc'+'&jenisVhc='+jnsVhc;
        //alert(param);
        tujuan='vhc_slave_laporanPenggunaanKomponen.php';
       // document.getElementById('kdVhc').innerHTML='';
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
                                                 document.getElementById('jnsVhc').disabled=true;
                                                document.getElementById('kdVhc').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }
}
function get_kdVhc()
{
        comId=document.getElementById('company_id').options[document.getElementById('company_id').selectedIndex].value;
        //comId=document.getElementById('company_id').value;
        param='comId='+comId+'&proses=getKdvhc';
        //alert(param);
        tujuan='vhc_slave_laporanPenggunaanKomponen.php';
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
                                                document.getElementById('company_id').disabled=true;
                                                document.getElementById('kdVhc').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }
}
function cari_brng(title,content,ev)
{
        if(document.getElementById('company_id').disabled==true)
        {
                width='500';
                height='400';
                showDialog1(title,content,width,height,ev);
        }
        else
        {
                alert('Please choose Company');
        }
}
function findBrg()
{
                //kode_gudang=document.getElementById('gudang_id').value;
                txt_cari=document.getElementById('no_brg').value;
                param='txtcari='+txt_cari;
                tujuan='log_slave_cariBarangUmum.php';
                post_response_text(tujuan, param, respog);
                function respog(){
                        if (con.readyState == 4) {
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
function throwThisRow(kd_brg,nm_brg,satuan)
{
        document.getElementById('nm_goods').value=nm_brg;
        document.getElementById('nm_goods').disabld=true;
        comId=document.getElementById('company_id').value;
        kdVhc=document.getElementById('kdVhc').value;
        periodT=document.getElementById('period').value;
        param='comId='+comId+'&kdVhc='+kdVhc+'&period='+periodT+'&proses=get_result_cari';
        param+='&kdBrg='+kd_brg;
        //alert(param);
        tujuan='vhc_slave_laporanPenggunaanKomponen.php';
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
                                        closeDialog();				
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }	
}
function dataKeExcel(ev,tujuan)
{
        comId=document.getElementById('company_id').options[document.getElementById('company_id').selectedIndex].value;
        kdVhc=document.getElementById('kdVhc').options[document.getElementById('kdVhc').selectedIndex].value;
         jnsVhc=document.getElementById('jnsVhc').options[document.getElementById('jnsVhc').selectedIndex].value;
        tglAwl=document.getElementById('tglAwal').value;
        tglAkhr=document.getElementById('tglAkhir').value;

        judul='Report Ms.Excel';	
        param='comId='+comId+'&kdVhc='+kdVhc+'&proses=getExcel'+'&tglAkhir='+tglAkhr+'&tglAwal='+tglAwl+'&jenisVhc='+jnsVhc;
        printFile(param,tujuan,judul,ev)	
}
function dataKePDF(ev)
{
        comId=document.getElementById('company_id').options[document.getElementById('company_id').selectedIndex].value;
        kdVhc=document.getElementById('kdVhc').options[document.getElementById('kdVhc').selectedIndex].value;
         jnsVhc=document.getElementById('jnsVhc').options[document.getElementById('jnsVhc').selectedIndex].value;
        //periodT=document.getElementById('period').options[document.getElementById('period').selectedIndex].value;
        tglAwl=document.getElementById('tglAwal').value;
        tglAkhr=document.getElementById('tglAkhir').value;
        tujuan='vhc_slave_laporanPenggunaanKomponen.php';
        judul='Report PDF';		
        param='comId='+comId+'&kdVhc='+kdVhc+'&proses=pdf'+'&tglAkhir='+tglAkhr+'&tglAwal='+tglAwl+'&jenisVhc='+jnsVhc;
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