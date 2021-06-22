/**
 * @author antoniuslouis
 */
// dhyaz sep 14, 2011

function proses0()
{
    tahunbudget0 =document.getElementById('tahunbudget0');
    tahunbudget0V	=tahunbudget0.options[tahunbudget0.selectedIndex].value;
    regional0 =document.getElementById('regional0');
    regional0V	=regional0.options[regional0.selectedIndex].value;
    kelompokbarang0 =document.getElementById('kelompokbarang0');
    kelompokbarang0V	=kelompokbarang0.options[kelompokbarang0.selectedIndex].value;
    if(tahunbudget0V==''){
        alert('Tahun Budget is empty.');
        return;
    }
    if(regional0V==''){
        alert('Regional is empty.');
        return;
    }
    
    param='cekapa=tab0&tahunbudget0='+tahunbudget0V+'&regional0='+regional0V+'&kelompokbarang0='+kelompokbarang0V;
    tujuan='bgt_slave_laporan_harga_barang.php'; 
    post_response_text(tujuan, param, respog);
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //                    showById('printPanel');
                    if(con.responseText==''){
                        alert('Data tidak tersedia.');
                    }else
                    {
                        document.getElementById('container0').innerHTML=con.responseText;
                    }
                //                    alert(con.responseText);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
}

function hargabarangKeExcel(ev,tujuan)
{
    tahunbudget0 =document.getElementById('tahunbudget0');
    tahunbudget0V	=tahunbudget0.options[tahunbudget0.selectedIndex].value;
    regional0 =document.getElementById('regional0');
    regional0V	=regional0.options[regional0.selectedIndex].value;
    kelompokbarang0 =document.getElementById('kelompokbarang0');
    kelompokbarang0V	=kelompokbarang0.options[kelompokbarang0.selectedIndex].value;
                                
	judul='Report Ms.Excel';	
	param='tab=1&tahunbudget0='+tahunbudget0V+'&regional0='+regional0V+'&kelompokbarang0='+kelompokbarang0V;
	printFile(param,tujuan,judul,ev)	
}

function hargabarangKePDF(ev,tujuan)
{
    tahunbudget0 =document.getElementById('tahunbudget0');
    tahunbudget0V	=tahunbudget0.options[tahunbudget0.selectedIndex].value;
    regional0 =document.getElementById('regional0');
    regional0V	=regional0.options[regional0.selectedIndex].value;
    kelompokbarang0 =document.getElementById('kelompokbarang0');
    kelompokbarang0V	=kelompokbarang0.options[kelompokbarang0.selectedIndex].value;
                                
	judul='Report PDF';	
	param='tab=1&tahunbudget0='+tahunbudget0V+'&regional0='+regional0V+'&kelompokbarang0='+kelompokbarang0V;
	printFile(param,tujuan,judul,ev)	
}

function hargabarangKeExcel2(ev,tujuan)
{
    tahunbudget1 =document.getElementById('tahunbudget1');
    tahunbudget1V	=tahunbudget1.options[tahunbudget1.selectedIndex].value;
    regional1 =document.getElementById('regional1');
    regional1V	=regional1.options[regional1.selectedIndex].value;
    namabarang1 =document.getElementById('namabarang1');
    namabarang1V	=namabarang1.value;
                                
	judul='Report Ms.Excel';	
	param='tab=2&tahunbudget0='+tahunbudget1V+'&regional0='+regional1V+'&kelompokbarang0='+namabarang1V;
	printFile(param,tujuan,judul,ev)	
}

function hargabarangKePDF2(ev,tujuan)
{
    tahunbudget0 =document.getElementById('tahunbudget0');
    tahunbudget0V	=tahunbudget0.options[tahunbudget0.selectedIndex].value;
    regional0 =document.getElementById('regional0');
    regional0V	=regional0.options[regional0.selectedIndex].value;
    namabarang1 =document.getElementById('namabarang1');
    namabarang1V	=namabarang1.value;
                                
	judul='Report PDF';	
	param='tab=2&tahunbudget0='+tahunbudget1V+'&regional0='+regional1V+'&kelompokbarang0='+namabarang1V;
	printFile(param,tujuan,judul,ev)	
}

function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='900';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 
}

function proses1()
{
    tahunbudget1 =document.getElementById('tahunbudget1');
    tahunbudget1V	=tahunbudget1.options[tahunbudget1.selectedIndex].value;
    regional1 =document.getElementById('regional1');
    regional1V	=regional1.options[regional1.selectedIndex].value;
    namabarang1 =document.getElementById('namabarang1');
    namabarang1V	=namabarang1.value;
    if(tahunbudget1V==''){
        alert('Tahun Budget is empty.');
        return;
    }
    if(regional1V==''){
        alert('Regional is empty.');
        return;
    }
    
    param='cekapa=tab1&tahunbudget1='+tahunbudget1V+'&regional1='+regional1V+'&namabarang1='+namabarang1V;
    tujuan='bgt_slave_laporan_harga_barang.php'; 
    post_response_text(tujuan, param, respog);
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //                    showById('printPanel');
                    if(con.responseText==''){
                        alert('Data tidak tersedia.');
                    }else
                    {
                        document.getElementById('container1').innerHTML=con.responseText;
                    }
                //                    alert(con.responseText);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
}






















