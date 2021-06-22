/**
 * @author antoniuslouis
 */

function getTransaksiGudang()
{
unit =document.getElementById('unit');
        unit	=unit.options[unit.selectedIndex].value;
tahun =document.getElementById('tahun');
        tahun	=tahun.options[tahun.selectedIndex].value;
kelompok =document.getElementById('kelompok');
        kelompok	=kelompok.options[kelompok.selectedIndex].value;
kodebarang =document.getElementById('kodebarang').value;
pilih =document.getElementById('pilih');
        pilih	=pilih.options[pilih.selectedIndex].value;
param='unit='+unit+'&tahun='+tahun+'&kelompok='+kelompok+'&pilih='+pilih+'&kodebarang='+kodebarang;
tujuan='log_slave_2kalkulasi_stock.php';
post_response_text(tujuan, param, respog);

    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    showById('printPanel');
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
 
function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='400';
   height='200';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}
 
function rekalkulasiStockKeExcel(ev,tujuan)
{
unit =document.getElementById('unit');
        unit	=unit.options[unit.selectedIndex].value;
tahun =document.getElementById('tahun');
        tahun	=tahun.options[tahun.selectedIndex].value;
kelompok =document.getElementById('kelompok');
        kelompok	=kelompok.options[kelompok.selectedIndex].value;
pilih =document.getElementById('pilih');
        pilih	=pilih.options[pilih.selectedIndex].value;
param='unit='+unit+'&tahun='+tahun+'&kelompok='+kelompok+'&pilih='+pilih+'&excel=excel';
   judul='Report Ms.Excel';	
    printFile(param,tujuan,judul,ev)	
}

function getDetailGudangKeExcel(ev,tujuan,pilih,barang,periode)
{
param='pilih='+pilih+'&barang='+barang+'&periode='+periode+'&unit='+unit+'&excel=excel';
   judul='Report Ms.Excel';	
    printFile(param,tujuan,judul,ev)	    
}

function getDetailGudang(pilih,barang,periode,ev)
{
    unit = document.getElementById('unit');
    unit = unit.options[unit.selectedIndex].value;
    param='pilih='+pilih+'&barang='+barang+'&periode='+periode+'&unit='+unit;
    tujuan='log_slave_2kalkulasi_stock_detail.php'+"?"+param;  
    width='800';
    height='400';

    content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
    showDialog1('Detail Transaksi '+pilih+' '+barang+' '+periode,content,width,height,ev); 
	
}

function pilihmayor()
{
    mayor = document.getElementById('mayor');
    mayor = mayor.options[mayor.selectedIndex].value;
    if(mayor=='mayor'){
        document.getElementById('pilih').value='nilai';
        document.getElementById('pilih').disabled=true;        
    }else{
        document.getElementById('pilih').disabled=false;
    } 
        
}

function searchBrg(title,content,ev)
{
    width='500';
    height='400';
    showDialog1(title,content,width,height,ev);
}

function findBrg()
{
    txt=trim(document.getElementById('no_brg').value);
    if(txt=='')
    {
        alert('Text is obligatory');
    }
    else if(txt.length<3)
    {
        alert('Too Short Words');
    }
    else
    {
        param='txtcari='+txt+'&proses=cari_barang';
        tujuan='sdm_slave_preventivemaintenance.php';
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

function clearkobar()
{
    document.getElementById('kodebarang').value='';
}

function throwThisRow(no_brg,namabrg,satuan)
{
    document.getElementById('kodebarang').value=no_brg;
    document.getElementById('kelompok').value=no_brg.substr(0,3);
    closeDialog();
}
