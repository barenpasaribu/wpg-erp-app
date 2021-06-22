/**
 * @author antoniuslouis
 */

function getPOSupplier()
{

                penerima=document.getElementById('penerimaId').options[document.getElementById('penerimaId').selectedIndex].value
                mengetahui=document.getElementById('mengetahuiId').options[document.getElementById('mengetahuiId').selectedIndex].value
                nopo = trim(document.getElementById('nopo').value);
                statInp=document.getElementById('statInput').value;
                if(penerima=='')
                {
                    alert('Enter recipient');
                    return;
                }
                if(mengetahui=='')
                {
                    alert('Enter verificator(person)');
                    return;
                }
                if (nopo == '') 
                        alert('Po. No is obligatory');
                else {
                        tujuan = 'log_slave_terima_barang.php';
                        param = 'nopo=' + nopo + '&statInput='+statInp+'&proses=getPo'; 
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
                                            if(statInp==0)
                                            {
                                                disableHeader();
                                                isidata=con.responseText.split("###");
                                                document.getElementById('nodok').value=isidata[0];
                                                document.getElementById('container').innerHTML=isidata[1];
                                                document.getElementById('supplier').value=isidata[2];
                                                document.getElementById('idsupplier').value=isidata[2];
                                            }
                                            else
                                            {
                                                isidata=con.responseText.split("###");
                                                document.getElementById('container').innerHTML=isidata[0];
                                                l=document.getElementById('penerimaId');
                                                for(a=0;a<l.length;a++)
                                                {
                                                    if(l.options[a].value==isidata[1])
                                                        {
                                                            l.options[a].selected=true;
                                                        }
                                                }
                                                asl=document.getElementById('mengetahuiId');
                                                for(ard=0;ard<asl.length;ard++)
                                                {
                                                    if(asl.options[ard].value==isidata[2])
                                                        {
                                                            asl.options[ard].selected=true;
                                                        }
                                                }
                                                disableHeader();
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
function goPickPo(nopo)
{
        document.getElementById('nopo').value=nopo;
        penerima=document.getElementById('penerimaId').options[document.getElementById('penerimaId').selectedIndex].value
        mengetahui=document.getElementById('mengetahuiId').options[document.getElementById('mengetahuiId').selectedIndex].value
        if(penerima=='')
        {
        alert('Enter recipient');
        return;
        }
        if(mengetahui=='')
        {
        alert('Enter verificator');
        return;
        }
        statInp=document.getElementById('statInput').value;
        if (getPOSupplier()) {
                param = 'nopo=' + nopo + '&proses=getPo'+'&statInput='+statInp;
                getPOContent(param,0);
        }
}

function getPOContent(param,statInp)
{
                tujuan = 'log_slave_terima_barang.php';
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
                                                //document.getElementById('container').innerHTML=con.responseText;
                                            if(statInp==0)
                                            {
                                                disableHeader();
                                                isidata=con.responseText.split("###");
                                                document.getElementById('nodok').value=isidata[0];
                                                document.getElementById('container').innerHTML=isidata[1];
                                                document.getElementById('supplier').value=isidata[2];
                                                document.getElementById('idsupplier').value=isidata[2];
                                            }
                                            else
                                            {

                                                isidata=con.responseText.split("###");
                                                document.getElementById('container').innerHTML=isidata[0];
                                                l=document.getElementById('penerimaId');
                                                for(a=0;a<l.length;a++)
                                                {
                                                    if(l.options[a].value==isidata[1])
                                                        {
                                                            l.options[a].selected=true;
                                                        }
                                                }
                                                asl=document.getElementById('mengetahuiId');
                                                for(ard=0;ard<asl.length;ard++)
                                                {
                                                    if(asl.options[ard].value==isidata[2])
                                                        {
                                                            asl.options[ard].selected=true;
                                                        }
                                                }
                                                disableHeader();
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

function disableHeader()
{
        document.getElementById('tanggal').disabled=true;
        document.getElementById('penerimaId').disabled=true;
        document.getElementById('mengetahuiId').disabled=true;
        document.getElementById('nopo').disabled=true;
        document.getElementById('btnheader').disabled=true;
}
function enableHeader()
{
        document.getElementById('tanggal').disabled=false;
        document.getElementById('penerimaId').disabled=false;
        document.getElementById('mengetahuiId').disabled=false;
        document.getElementById('nopo').disabled=false;
        document.getElementById('btnheader').disabled=false;	
}

function kosongkan()
{
                        document.getElementById('nopo').value='';
                        document.getElementById('penerimaId').value='';
                        document.getElementById('mengetahuiId').value='';
                        document.getElementById('idsupplier').value='';
                        document.getElementById('supplier').value='';
                        document.getElementById('nodok').value='';
                        document.getElementById('container').innerHTML='';
                        document.getElementById('containerlist').innerHTML='';
                        enableHeader();	
}

function cekButton(txbox,idbtn)
{
        x=trim(txbox.value);
        if(parseFloat(x)<0 || x=='' || parseFloat(x)=='NaN')
        {
                document.getElementById(idbtn).disabled=true;
        }
        else
        {
                document.getElementById(idbtn).disabled=false;
        }
}
function getBapbList()
{
                param='proses=listData';
                tujuan = 'log_slave_terima_barang.php';
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

function saveItemPo(kodebarang){
        //get all data
        penerima=document.getElementById('penerimaId').options[document.getElementById('penerimaId').selectedIndex].value
        mengetahui=document.getElementById('mengetahuiId').options[document.getElementById('mengetahuiId').selectedIndex].value
        nodok = trim(document.getElementById('nodok').value);
        idsupplier = document.getElementById('idsupplier').value;
        tanggal = document.getElementById('tanggal').value;
        nopo = document.getElementById('nopo').value;
        qty = document.getElementById('qty' + kodebarang).value;
        satuan=trim(document.getElementById('sat' + kodebarang).innerHTML);

        hidenlalu = document.getElementById('jumlal' + kodebarang).value;
        hidensekarang= document.getElementById('jumsek' + kodebarang).value;

        
       
        param = 'nodok=' + nodok + '&idsupplier=' + idsupplier + '&tanggal=' + tanggal;
        param += '&nopo=' + nopo;
        param += '&qty=' + qty+'&kodebarang='+kodebarang;
        param += '&jumlal=' + hidenlalu+'&kodebarang='+kodebarang;
        param += '&jumsek=' + hidensekarang+'&kodebarang='+kodebarang;
        param +='&satuan='+satuan+'&proses=saveData';
        param +='&penerimaId='+penerima+'&mengetahuiId='+mengetahui;
        

diketik=parseFloat(hidenlalu)+parseFloat(qty);
//alert(diketik);
 if(parseFloat(diketik)>parseFloat(hidensekarang)){
    alert('Total Melebihi Kuantitas PO');
        }
        else{
                tujuan = 'log_slave_terima_barang.php';
                if (nodok == '' || parseFloat(qty) < 0 || parseFloat(qty) == 'NaN') {
                  alert('Volume and document number is obligatory');
                }
                else {
                        document.getElementById('qty'+kodebarang).style.backgroundColor='orange';
                        post_response_text(tujuan, param, respog);
                }
        }
        
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                                document.getElementById('qty'+kodebarang).style.backgroundColor='red';
                                        }
                                        else {
                                            document.getElementById('qty'+kodebarang).style.backgroundColor='#E8F4F4';

                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }
        
}

function selesaiBapb()
{
        //gudang=document.getElementById('sloc').options[document.getElementById('sloc').selectedIndex].value;
        getBapbList();
        kosongkan();
        //setSloc('simpan');	
}

function previewBapb(notransaksi,ev)
{
        param='notransaksi='+notransaksi;
        tujuan = 'log_slave_print_bapb_supplier_pdf.php?'+param;	
 //display window
   title=notransaksi;
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);

}
function postData(notransaksi)
{

        param='notransaksi='+notransaksi+'&proses=postingData';
        tujuan = 'log_slave_terima_barang.php';
        if(confirm("Confirm,  :"+notransaksi+"are you sure ?"))
        post_response_text(tujuan, param, respog);	
        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                         getBapbList();
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}

function editBapb(notransaksi,nopo,tanggal,supp)
{
        document.getElementById('nodok').value=notransaksi;
        document.getElementById('idsupplier').value=supp;
        document.getElementById('supplier').value=supp;
        document.getElementById('nopo').value=nopo;
        document.getElementById('tanggal').value=tanggal;	
        param = 'nopo=' + nopo+'&statInputan=1&notransaksi='+notransaksi+'&proses=getPo';
        getPOContent(param,1);
        tabAction(document.getElementById('tabFRM0'),0,'FRM',1);//jangan tanya darimana

}

function delBapb(notransaksi)
{
        param='notransaksi='+notransaksi+'&proses=deleteData';
                tujuan='log_slave_terima_barang.php';
                if(confirm('Deleting document '+notransaksi+', are you sure..?'))
                  post_response_text(tujuan, param, respog);	
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                 getBapbList();
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}

function cariBapb(num)
{
        tex=trim(document.getElementById('txtbabp').value);

        param='proses=listData';
        param+='&page='+num;
        if(tex!='')
        {param+='&tex='+tex;}
        tujuan = 'log_slave_terima_barang.php';
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
function cariPO(title,ev)
{
                  kosongkan();
                  //setSloc('simpan');
                  content= "<div>";
                  content+="<fieldset>PO:<input type=text id=textpo class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=25><button class=mybutton onclick=goCariPo()>Go</button> </fieldset>";
                  content+="<div id=containercari style=\"height:250px;width:470px;overflow:scroll;\"></div></div>";
             //display window
                 title=title+' PO:';
                   width='500';
                   height='300';
                   showDialog1(title,content,width,height,ev);	
}

function goCariPo()
{

                nopo=trim(document.getElementById('textpo').value);
                if(nopo.length<4)
                   alert('Text too short');
                else
                {   
                param='nopo='+nopo;
                tujuan = 'log_slave_cariPo.php';
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