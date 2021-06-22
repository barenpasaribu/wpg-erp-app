/**
 * @author antoniuslouis
 */

function showWindowBarang(title,ev)
{

          content= "<div style='width:100%;'>";
          content+="<fieldset>"+title+"<input type=text id=txtnamabarang class=myinputtext size=25 onkeypress=\"return enterEuy(event);\" maxlength=35><button class=mybutton onclick=goCariBarang()>Go</button> </fieldset>";
          content+="<div id=containercari style='overflow:scroll;height:300px;width:520px'></div></div>";
     //display window
           width='550';
           height='350';
           showDialog1(title,content,width,height,ev);		
}

function enterEuy(evt)
{
        key=getKey(evt);
        if(key==13)
        {
                goCariBarang();
        }
        else
        {
                return tanpa_kutip(evt);
        }

}

function goCariBarang()
{

                txtcari = trim(document.getElementById('txtnamabarang').value);
                                if (txtcari.length < 3) {
                                        alert('material name min. 3 char');
                                }
                                else {
                                        param = 'txtcari=' + txtcari;
                                        tujuan = 'log_slave_2transaksigudangcari.php';
                                        post_response_text(tujuan, param, respog);
                                }
        function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('containercari').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }		
}

function loadField(kode)
{
        document.getElementById('kodebarang').value=kode;
        closeDialog();		
}

function setAll()
{
        document.getElementById('kodebarang').value='';
}

function ambilPeriode(gudang)
{
        param='gudang='+gudang;
        tujuan='log_slave_getPeriode.php';
        post_response_text(tujuan, param, respog);

                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('periode').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	

}

function getTransaksiGudang()
{ 
        unit =document.getElementById('unit');
        periode =document.getElementById('periode');
        jenis =document.getElementById('jenis');
        kodebarang =document.getElementById('kodebarang').value;
                unit	=unit.options[unit.selectedIndex].value;
                periode	=periode.options[periode.selectedIndex].value;
                jenis	=jenis.options[jenis.selectedIndex].value;
        param='unit='+unit+'&periode='+periode+'&jenis='+jenis+'&kodebarang='+kodebarang;
        tujuan='log_slave_2transaksigudang.php';

        if(jenis=='9'){
            if(kodebarang==''){
                alert('For seachring of all type of transaction, inventory code is required');
            }else{
                post_response_text(tujuan, param, respog);
            }
        }else{
            post_response_text(tujuan, param, respog);
        }


                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                showById('printPanel');
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

function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}

function viewDetail(ev,kodevhc,tanggalmulai,tanggalsampai,unit,periode)
{
   param='kodevhc='+kodevhc+'&tanggalmulai='+tanggalmulai+'&tanggalsampai='+tanggalsampai+'&unit='+unit+'&periode='+periode;
   tujuan='vhc_slave_2biayatotalperkendaraandetail.php'+"?"+param;  
   width='500';
   height='400';

   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1('Detail Biaya per Kendaraan '+kodevhc,content,width,height,ev); 

}

function detailExcel(ev,tujuan)
{
    width='300';
   height='100';

   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1('Detail Biaya per Kendaraan',content,width,height,ev); 
}

function transaksiGudangKeExcel(ev,tujuan)
{
        unit =document.getElementById('unit');
        periode =document.getElementById('periode');
        jenis =document.getElementById('jenis');
        kodebarang =document.getElementById('kodebarang').value;
                unit	=unit.options[unit.selectedIndex].value;
                periode	=periode.options[periode.selectedIndex].value;
                jenis	=jenis.options[jenis.selectedIndex].value;
        judul='Report Ms.Excel';	
        param='unit='+unit+'&periode='+periode+'&jenis='+jenis+'&kodebarang='+kodebarang;
        printFile(param,tujuan,judul,ev)	
}

function ambilPeriode2(unit)
{
        param='unit='+unit;
        tujuan='sdm_slave_getPeriode.php';
        post_response_text(tujuan, param, respog);

                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('periode').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	

}
