// JavaScript Document
function save_pil()
{

        comId=document.getElementById('company_id').options[document.getElementById('company_id').selectedIndex].value;
        jnsVhc=document.getElementById('jnsVhc').options[document.getElementById('jnsVhc').selectedIndex].value;
        kdVhc=document.getElementById('kdVhc').options[document.getElementById('kdVhc').selectedIndex].value;
        alokasi=document.getElementById('alokasi').options[document.getElementById('alokasi').selectedIndex].value;
        tglAwl=document.getElementById('tglAwal').value;
        tglAkhr=document.getElementById('tglAkhir').value;
        //periodT=document.getElementById('period').options[document.getElementById('period').selectedIndex].value;
        param='comId='+comId+'&kdVhc='+kdVhc+'&proses=get_result'+'&jnsVhc='+jnsVhc+'&tglAkhir='+tglAkhr+'&alokasi='+alokasi;
        param+='&tglAwal='+tglAwl;
        //alert(param);
        tujuan='vhc_slave_laporanKerjaKendaraan.php';
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
                                                document.getElementById('alokasi').disabled=true;
                                                //document.getElementById('period').disabled=true;
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
        document.getElementById('company_id').disabled=false;
        document.getElementById('kdVhc').disabled=false;
        document.getElementById('jnsVhc').disabled=false;
        document.getElementById('alokasi').disabled=false;
        document.getElementById('company_id').value='';
        document.getElementById('kdVhc').value='';
        document.getElementById('alokasi').value='';
        document.getElementById('jnsVhc').value='';
        document.getElementById('tglAwal').value='';
        document.getElementById('tglAkhir').value='';
        //document.getElementById('period').disabled=false;
        document.getElementById('contain').innerHTML='';
        //document.getElementById('nmKry').value='';
        //document.getElementById('nm_goods').value='';
}
function get_jnsVhc()
{
        comId=document.getElementById('company_id').options[document.getElementById('company_id').selectedIndex].value;
        param='comId='+comId+'&proses=getJnsVhc';
        //alert(param);
        tujuan='vhc_slave_laporanKerjaKendaraan.php';
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
                                                document.getElementById('jnsVhc').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }
}
function getKdVhc()
{
        //jnsVhc=document.getElementById('jnsVhc').value;
        jnsVhc=document.getElementById('jnsVhc').options[document.getElementById('jnsVhc').selectedIndex].value;
        param='jnsVhc='+jnsVhc+'&proses=getKdvhc';
        //alert(param);
        tujuan='vhc_slave_laporanKerjaKendaraan.php';
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
function getKryResult()
{
        if(document.getElementById('idKry').value=='')
        {
                document.getElementById('idKry').disabled=false;
                save_pil();
        }
        else if(document.getElementById('idKry').value!='')
        {	
                kryId=document.getElementById('idKry').value;
                param='kryId='+kryId+'&proses=getResultKry';
                tujuan='vhc_slave_laporanKerjaKendaraan.php';

                function respog(){
                                if (con.readyState == 4) {
                                        if (con.status == 200) {
                                                busy_off();
                                                if (!isSaveResponse(con.responseText)) {
                                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                                }
                                                else {
                                                        //alert(con.responseText);
                                                        document.getElementById('idKry').disabled=true;
                                                        document.getElementById('contain').innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                                }
                        }
                        post_response_text(tujuan, param, respog);
        }
}
function gantiKry()
{
        document.getElementById('idKry').disabled=false;
        document.getElementById('idKry').value='';
}
function dataKeExcel(ev,tujuan)
{
        comId=document.getElementById('company_id').options[document.getElementById('company_id').selectedIndex].value;
        jnsVhc=document.getElementById('jnsVhc').options[document.getElementById('jnsVhc').selectedIndex].value;
        kdVhc=document.getElementById('kdVhc').options[document.getElementById('kdVhc').selectedIndex].value;
        alokasi=document.getElementById('alokasi').options[document.getElementById('alokasi').selectedIndex].value;
        tglAwl=document.getElementById('tglAwal').value;
        tglAkhr=document.getElementById('tglAkhir').value;

        judul='Report Ms.Excel';	
        param='comId='+comId+'&kdVhc='+kdVhc+'&proses=excel'+'&jnsVhc='+jnsVhc+'&tglAkhir='+tglAkhr+'&alokasi='+alokasi;
        param+='&tglAwal='+tglAwl;

        printFile(param,tujuan,judul,ev)	
}
function dataKePDF(ev)
{
        comId=document.getElementById('company_id').options[document.getElementById('company_id').selectedIndex].value;
        jnsVhc=document.getElementById('jnsVhc').options[document.getElementById('jnsVhc').selectedIndex].value;
        kdVhc=document.getElementById('kdVhc').options[document.getElementById('kdVhc').selectedIndex].value;
        tglAwl=document.getElementById('tglAwal').value;
        tglAkhr=document.getElementById('tglAkhir').value;


        tujuan='vhc_slave_laporanKerjaKendaraan.php';
        judul='Report PDF';			
        param='comId='+comId+'&kdVhc='+kdVhc+'&proses=pdf'+'&jnsVhc='+jnsVhc+'&tglAkhir='+tglAkhr;
        param+='&tglAwal='+tglAwl;
        //param='pt='+pt+'&kdVhc='+kdvhc+'&periode='+periode+'&jnsVhc='+jnsVhc+'&proses=pdf';
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

function cariOpt(title,content,ev)
{
        if(document.getElementById('company_id').disabled==true)
        {
                width='500';
                height='400';
                showDialog1(title,content,width,height,ev);
        }
        else
        {
                alert('Please Choose Company');
        }
}
function findOpt()
{
                //kode_gudang=document.getElementById('gudang_id').value;
                txt_cari=document.getElementById('nmKry').value;
                param='txtcari='+txt_cari;
                param+='&proses=cariOpt'
                tujuan='vhc_slave_laporanKerjaKendaraan.php';
                //alert(param);
                //tujuan='log_slave_2keluarmasukbrg.php';
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