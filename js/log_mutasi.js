/**
 * @author antoniuslouis
 */
function setSloc(x){
        gudang = document.getElementById('sloc').options[document.getElementById('sloc').selectedIndex].value;
        tglstart=document.getElementById(gudang+'_start').value;
        tglend=document.getElementById(gudang+'_end').value;
        tglstart=tglstart.substr(6,2)+"-"+tglstart.substr(4,2)+"-"+tglstart.substr(0,4);
        tglend=tglend.substr(6,2)+"-"+tglend.substr(4,2)+"-"+tglend.substr(0,4);
        document.getElementById('displayperiod').innerHTML=tglstart+" - "+tglend;

        if (gudang != '') {
                if (x == 'simpan') {
                        document.getElementById('sloc').disabled = true;
                        document.getElementById('btnsloc').disabled = true;
                        document.getElementById('pemilikbarang').disabled = true;
                        tujuan = 'log_slave_getBastNumber.php';
                        param = 'gudang=' + gudang;
                        post_response_text(tujuan, param, respog);
                }
                else {
                        document.location.reload();//updateby jamhari
//                        document.getElementById('nodok').value='';
//                        document.getElementById('sloc').disabled = false;
//                        document.getElementById('pemilikbarang').disabled = false;
//                        document.getElementById('pemilikbarang').innerHTML = "<option valu=''></option>";
//                        document.getElementById('sloc').options[0].selected=true;
//                        document.getElementById('btnsloc').disabled = false;
//                        kosongkan();
                }	

        }	
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                //alert(con.responseText);
                                                document.getElementById('nodok').value = trim(con.responseText);
                                            getMutasiList(gudang);
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }
}


function getPT(gudang)
{
   param='gudang='+gudang;
        tujuan = 'log_slave_gudangGetPTOption.php';
        post_response_text(tujuan, param, respog);
        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                        document.getElementById('pemilikbarang').innerHTML=con.responseText;
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
        document.getElementById('kegudang').disabled=true;
        document.getElementById('catatan').disabled=true;	
}
function enableHeader()
{
        document.getElementById('tanggal').disabled=false;
        document.getElementById('kegudang').disabled=false;
        document.getElementById('catatan').disabled=false;
}

function showWindowBarang(title,ev){
          content= "<div style='width:100%;'>";
          content+="<fieldset>"+title+"<input type=text id=txtnamabarang class=myinputtext size=25 onkeypress=\"return enterEuy(event);\" maxlength=35><button class=mybutton onclick=goCariBarang()>Go</button> </fieldset>";
          content+="<div id=containercari style='overflow:scroll;height:300px;width:520px'></div></div>";
     //display window
           width='550';
           height='350';
           showDialog1(title,content,width,height,ev);		
}
function showWindowKonosemen(title,ev){
        gudang = document.getElementById('sloc').options[document.getElementById('sloc').selectedIndex].value;
        gudang2 = document.getElementById('kegudang').options[document.getElementById('kegudang').selectedIndex].value;
        tanggal=document.getElementById('tanggal').value;
                x=tanggal;
                _start=document.getElementById(gudang+'_start').value;
                _end=document.getElementById(gudang+'_end').value;
                while (x.lastIndexOf("-") > -1) {
                        x = x.replace("-", "");
                }
                while (x.lastIndexOf("-") > -1) {
                    x=x.replace("/","");
                }

                curdateY=x.substr(4,4).toString();
                curdateM=x.substr(2,2).toString();
                curdateD=x.substr(0,2).toString();
                curdate=curdateY+curdateM+curdateD;	
                curdate=parseInt(curdate);
        if (curdate < parseInt(_start) || curdate > parseInt(_end)) {
                alert('Date out of range');
                return;
        }
        if(gudang2==''){
            alert("Tujuan/Destination can't empty");
            return;
        }
          content= "<div style='width:100%;'>";
          content+="<fieldset>"+title+"<input type=text id=txtnokonosemen class=myinputtext size=25 onkeypress=\"return enterEuy2(event);\" maxlength=35><button class=mybutton onclick=goCariKonosemen()>Go</button> </fieldset>";
          content+="<div id=containercari style='overflow:scroll;height:300px;width:520px'></div></div>";
     //display window
           width='550';
           height='350';
           showDialog1(title,content,width,height,ev);		
}
function showWindowGrn(title,ev){
        gudang = document.getElementById('sloc').options[document.getElementById('sloc').selectedIndex].value;
        gudang2 = document.getElementById('kegudang').options[document.getElementById('kegudang').selectedIndex].value;
        tanggal=document.getElementById('tanggal').value;
                x=tanggal;
                _start=document.getElementById(gudang+'_start').value;
                _end=document.getElementById(gudang+'_end').value;
                while (x.lastIndexOf("-") > -1) {
                        x = x.replace("-", "");
                }
                while (x.lastIndexOf("-") > -1) {
                    x=x.replace("/","");
                }

                curdateY=x.substr(4,4).toString();
                curdateM=x.substr(2,2).toString();
                curdateD=x.substr(0,2).toString();
                curdate=curdateY+curdateM+curdateD; 
                curdate=parseInt(curdate);
        if (curdate < parseInt(_start) || curdate > parseInt(_end)) {
                alert('Date out of range');
                return;
        }
        if(gudang2==''){
            alert("Tujuan/Destination can't empty");
            return;
        }
          content= "<div style='width:100%;'>";
          content+="<fieldset>"+title+"<input type=text id=txtnogrn class=myinputtext size=25 onkeypress=\"return enterEuy2(event);\" maxlength=35><button class=mybutton onclick=goCariGrn()>Go</button> </fieldset>";
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
function enterEuy2(evt)
{
        key=getKey(evt);
        if(key==13)
        {
                goCariKonosemen();
        }
        else
        {
                return tanpa_kutip(evt);
        }

}

function loadField(kode,nama,satuan)
{
        document.getElementById('kodebarang').value=kode;
        document.getElementById('namabarang').value=nama;
        document.getElementById('satuan').value=satuan;
        closeDialog();		
}

function kosongkan()
{
                        document.getElementById('kodebarang').value='';
                        document.getElementById('catatan').value='';
                        document.getElementById('satuan').value='';
                        document.getElementById('qty').value=0;
                        enableHeader();	
}

function nextItem()
{
        document.getElementById('kodebarang').disabled=false;
        document.getElementById('satuan').disabled=false;
        document.getElementById('namabarang').disabled=false;	
        document.getElementById('kodebarang').value='';
        document.getElementById('namabarang').value='';
        document.getElementById('satuan').value='';
        document.getElementById('qty').value=0;		
}

function bastBaru()
{
  statusInput=0;
  nextItem();
  kosongkan();	
  setSloc('simpan');
  document.getElementById('bastcontainer').innerHTML='';
}
function inputBaru()
{
 window.location.reload();
}


function goCariBarang()
{
        gudang = document.getElementById('sloc').options[document.getElementById('sloc').selectedIndex].value;

        nodok=document.getElementById('nodok').value;
        if (nodok == '') {
                alert('Document Number is Obligatory');
        }
        else {
                txtcari = trim(document.getElementById('txtnamabarang').value);
                pemilikbarang = document.getElementById('pemilikbarang');
                pemilikbarang = pemilikbarang.options[pemilikbarang.selectedIndex].value;

                if (document.getElementById('nodok') == '') {
                        alert('Document number is obligatory');
                }
                else 
                        if (pemilikbarang.length < 3) {
                                alert('Googs Owner(PT) is obligatory');
                        }
                        else {
                                if (txtcari.length < 3) {
                                        alert('material name min. 3 char');
                                }
                                else {
                                        param = 'txtcari=' + txtcari + '&pemilikbarang=' + pemilikbarang;
                                        param +='&gudang='+gudang;
                                        tujuan = 'log_slave_cariBarang.php';
                                        post_response_text(tujuan, param, respog);
                                }
                        }
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
function goCariKonosemen(){
        
        gudang = document.getElementById('sloc').options[document.getElementById('sloc').selectedIndex].value;
        tgl=document.getElementById('tanggal').value;
        nodok=document.getElementById('nodok').value;
        if (nodok == '') {
                alert('Document Number is Obligatory');
        }
        else {
                txtcari = trim(document.getElementById('txtnokonosemen').value);
                pemilikbarang = document.getElementById('pemilikbarang');
                pemilikbarang = pemilikbarang.options[pemilikbarang.selectedIndex].value;
                kgdng = document.getElementById('kegudang');
                kgdng = kgdng.options[kgdng.selectedIndex].value;
                if(kgdng==''){
                    alert("Tujuan/Destination can't empty");
                    return;
                }
                if (document.getElementById('nodok') == '') {
                        alert('Document number is obligatory');
                }
                else 
                        if (pemilikbarang.length < 3) {
                                alert('Goods Owner(PT) is obligatory');
                        }
                        else {
                                if (txtcari.length < 3) {
                                        alert('material name min. 3 char');
                                }
                                else {
                                        param = 'txtcari=' + txtcari + '&pemilikbarang=' + pemilikbarang+'&tngl='+tgl+'&nodok='+nodok;
                                        param +='&gudang='+gudang+'&proses=getKonosemen'+'&gdngTujuan='+kgdng;
                                        tujuan = 'log_slave_mutasiKonosemen.php';
                                        post_response_text(tujuan, param, respog);
                                }
                        }
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
function goCariGrn(){
        
        gudang = document.getElementById('sloc').options[document.getElementById('sloc').selectedIndex].value;
        tgl=document.getElementById('tanggal').value;
        nodok=document.getElementById('nodok').value;
        if (nodok == '') {
                alert('Document Number is Obligatory');
        }
        else {
                txtcari = trim(document.getElementById('txtnogrn').value);
                pemilikbarang = document.getElementById('pemilikbarang');
                pemilikbarang = pemilikbarang.options[pemilikbarang.selectedIndex].value;
                kgdng = document.getElementById('kegudang');
                kgdng = kgdng.options[kgdng.selectedIndex].value;
                if(kgdng==''){
                    alert("Tujuan/Destination can't empty");
                    return;
                }
                if (document.getElementById('nodok') == '') {
                        alert('Document number is obligatory');
                }
                else 
                        if (pemilikbarang.length < 3) {
                                alert('Goods Owner(PT) is obligatory');
                        }
                        else {
                                if (txtcari.length < 3) {
                                        alert('material name min. 3 char');
                                }
                                else {
                                        param = 'txtcari=' + txtcari + '&pemilikbarang=' + pemilikbarang+'&tngl='+tgl+'&nodok='+nodok;
                                        param +='&gudang='+gudang+'&proses=getGRN'+'&gdngTujuan='+kgdng;
                                        tujuan = 'log_slave_mutasiGRN.php';
                                        post_response_text(tujuan, param, respog);
                                }
                        }
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
function GetGRNdetail(notransaksi,gdngPengirim,gdngTjan,pmlikBrg,tglTrans,notrans){
  
                               
      
       param ='notransaksi='+notransaksi+'&tngl='+tglTrans +'&proses=getGRNdetail';
       tujuan = 'log_slave_mutasiGRN.php';
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
                                                document.getElementById('grn').value=notransaksi;
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
statusInput=0;//tambahan jamhari
function saveItemBast(){
        gudang = document.getElementById('sloc').options[document.getElementById('sloc').selectedIndex].value;
        tanggal=document.getElementById('tanggal').value;
                x=tanggal;
                _start=document.getElementById(gudang+'_start').value;
                _end=document.getElementById(gudang+'_end').value;
                while (x.lastIndexOf("-") > -1) {
                        x = x.replace("-", "");
                }
                while (x.lastIndexOf("-") > -1) {
                    x=x.replace("/","");
                }

                curdateY=x.substr(4,4).toString();
                curdateM=x.substr(2,2).toString();
                curdateD=x.substr(0,2).toString();
                curdate=curdateY+curdateM+curdateD;	
                curdate=parseInt(curdate);
        if (curdate < parseInt(_start) || curdate > parseInt(_end)) {
                alert('Date out of range')
        }
        else {
                        nodok		=trim(document.getElementById('nodok').value);
                        tanggal		=trim(document.getElementById('tanggal').value);
                        kodebarang	=trim(document.getElementById('kodebarang').value);
                        catatan		=trim(document.getElementById('catatan').value);			
                        satuan		=trim(document.getElementById('satuan').value);
                        qty			=trim(document.getElementById('qty').value);

                        kegudang		=document.getElementById('kegudang');
                                kegudang	=trim(kegudang.options[kegudang.selectedIndex].value);
                gudang 		=trim(document.getElementById('sloc').options[document.getElementById('sloc').selectedIndex].value);
                pemilikbarang =trim(document.getElementById('pemilikbarang').options[document.getElementById('pemilikbarang').selectedIndex].value);
                if(nodok=='')
                {
                        alert('Document Number is obligatory');
                }
                else if(kegudang=='')
                {
                        alert('Destination is obligatory');
                } 
                else if(kodebarang=='' || satuan=='' || parseFloat(qty)<0.001)
                {
                        alert('Material, UOM and volume is obligatory');
                }
                else
                {
                        if(confirm('Are you sure?'))
                        {
                                param='nodok='+nodok+'&tanggal='+tanggal+'&kodebarang='+kodebarang;
                                param+='&kegudang='+kegudang+'&satuan='+satuan+'&qty='+qty;
                                param+='&gudang='+gudang+'&catatan='+catatan;
                                param+='&pemilikbarang='+pemilikbarang+'&statusInput='+statusInput;
                                tujuan='log_slave_saveMutasi.php';
                                post_response_text(tujuan, param, respog);
                                disableHeader();
                                document.getElementById('qty').style.backgroundColor='red';
                        }
                }
        }
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('qty').style.backgroundColor='#ffffff';
                                                nextItem();
                                                //document.getElementById('bastcontainer').innerHTML=con.responseText;
                                                balikdr=con.responseText.split("####");
						document.getElementById('bastcontainer').innerHTML=balikdr[0];
                                                document.getElementById('nodok').value=balikdr[1];
                                                statusInput=1;
                                                getMutasiList(gudang)
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}

function cekGudang(elemkegudang)
{
        kegudang=elemkegudang.options[elemkegudang.selectedIndex].value;
        src=document.getElementById('sloc');
        gudang 		=trim(src.options[src.selectedIndex].value);
        if(src.disabled)
        {
                if(gudang==kegudang)
                {
                        alert('Storage Location is the same');
                        elemkegudang.options[0].selected=true;
                }
        }
        else
        {
                elemkegudang.options[0].selected=true;
                alert('Document Number is obligatory');
        }
}
function getMutasiList(gudang)
{
                param='gudang='+gudang;
                tujuan = 'log_slave_getMutasiList.php';
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

function delMutasi(notransaksi,kodebarang)
{
        kegudang	=document.getElementById('kegudang');
                     kegudang=trim(kegudang.options[kegudang.selectedIndex].value);
                pemilikbarang = document.getElementById('pemilikbarang');
                     pemilikbarang=trim(pemilikbarang.options[pemilikbarang.selectedIndex].value);
                param='nodok='+notransaksi+'&kodebarang='+kodebarang;
                param+='&delete=true&pemilikbarang='+pemilikbarang;
                param+='&kegudang='+kegudang;
                tujuan='log_slave_saveMutasi.php';
                if(confirm('Deleting Document '+notransaksi+', are you sure..?'))
                  post_response_text(tujuan, param, respog);	
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('bastcontainer').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}

function delXMutasi(nodok)
{
        if(confirm('Deleting Doc: '+nodok+', Are sure..?'))
        {
                param='notransaksi='+nodok;
                tujuan='log_slave_deleteBapb.php';//file ini berfungsi untuk penerimaan dan pengeluaran
           if(confirm('All data in this document will be removed. Continue ?'))
           {
                 post_response_text(tujuan, param, respog);
           }   
        }
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                gudang = document.getElementById('sloc').options[document.getElementById('sloc').selectedIndex].value;
                                                setSloc('simpan');
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }		
}



function cariBast(num)
{
        tex=trim(document.getElementById('txtbabp').value);
        gudang = document.getElementById('sloc').options[document.getElementById('sloc').selectedIndex].value;
    if(gudang =='')
        {
                alert('Storage Location  is obligatory')
        }
        else
        {
                param='gudang='+gudang;
                param+='&page='+num;
                if(tex!='')
                        param+='&tex='+tex;
                tujuan = 'log_slave_getMutasiList.php';
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


function previewMutasi(notransaksi,ev)
{
        param='notransaksi='+notransaksi;
        tujuan = 'log_slave_print_mutasi_pdf.php?'+param;	
 //display window
   title=notransaksi;
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);

}
function saveKono(nokonosemen,gdngPengirim,gdngTjan,pmlikBrg,tglTrans,notrans){
       
        if(confirm("Are you sure procces this transaction? It will be take a while")){
            
            param='proses=saveKonosemen'+'&nokonsemen='+nokonosemen+'&gdngPengirim='+gdngPengirim;
            param+='&gdngTujuan='+gdngTjan+'&pemilikBrg='+pmlikBrg+'&tanggal='+tglTrans+'&notransaksiGdng='+notrans;
            tujuan = 'log_slave_mutasiKonosemen.php';
            post_response_text(tujuan, param, respog);
        }
            param='proses=saveKonosemen'+'&nokonsemen='+nokonosemen+'&gdngPengirim='+gdngPengirim;
            param+='&gdngTujuan='+gdngTjan+'&pemilikBrg='+pmlikBrg+'&tanggal='+tglTrans+'&notransaksiGdng='+notrans;
            tujuan = 'log_slave_mutasiKonosemen.php';
            post_response_text(tujuan, param, respog);
            function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                        document.getElementById('bastcontainer').innerHTML=con.responseText;
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
            }	
            
}
function saveGRN(notransaksi,gdngPengirim,gdngTjan,pmlikBrg,tglTrans,notrans){
       
        if(confirm("Are you sure procces this transaction? It will be take a while")){
            
            param='proses=saveGRN'+'&notransaksi='+notransaksi+'&gdngPengirim='+gdngPengirim;
            param+='&gdngTujuan='+gdngTjan+'&pemilikBrg='+pmlikBrg+'&tanggal='+tglTrans+'&notransaksiGdng='+notrans;
            tujuan = 'log_slave_mutasiGRN.php';
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
                                        document.getElementById('bastcontainer').innerHTML=con.responseText;
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
            }   
            
}
function SimpanHeader(){
       
        tgl=document.getElementById('tanggal').value;
        pemilikbarang = document.getElementById('pemilikbarang').value;
        gdngTjan = document.getElementById('kegudang').value;
        gdngPengirim = document.getElementById('sloc').value;
        nodok = document.getElementById('nodok').value;
        notrans = document.getElementById('grn').value;
        if(notrans  ==''){
                saveItemBast();
        }else{
           param='proses=SimpanHeader'+'&nodok='+nodok+'&gdngPengirim='+gdngPengirim;
            param+='&gdngTujuan='+gdngTjan+'&pemilikBrg='+pemilikbarang+'&tanggal='+tgl+'&notransaksiGdng='+notrans;
            tujuan = 'log_slave_mutasiGRN.php';
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
                                     SimpanLoopingGrn();
                                     tutupProses('simpan');
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
            }   
            
}
function SimpanLoopingGrn()
{
//
	maxRow = kodebarang=document.getElementById('totaldata').innerHTML;
	if(confirm('apakah anda yakin ?'))
	{
		   loopClosingFisik(1,maxRow);
		  
	}
	
}




function loopClosingFisik(currRow,maxRow)
{
 console.log('loopawal');  
	nodok=document.getElementById('nodok').value;
    chk=document.getElementById('chk'+currRow);
	kodebarang=document.getElementById('kodebarang'+currRow).value;
	satuan=document.getElementById('satuan'+currRow).value;

	
	notransaksi=document.getElementById('grn').value;
    jumlah=document.getElementById('jumlah'+currRow).value;
    if(notransaksi ==""){
        console.log('s=j')
        saldo=document.getElementById('jumlah'+currRow).value;
    }else{
        console.log(nodok)
        saldo=document.getElementById('saldo'+currRow).value;
    }
    if(chk.checked == true){
        console.log('loop');
	param ='&proses=SimpanDetail';
	param += '&nodok=' + nodok + '&kodebarang=' + kodebarang+ '&satuan=' + satuan+ '&jumlah=' + jumlah+ '&saldo=' + saldo+ '&notransaksi=' + notransaksi;
    //alert(param);
    tujuan = 'log_slave_mutasiGRN.php';
    //alert(param);
	post_response_text(tujuan, param, respog);
    }else{
        console.log('loopnggal');

    param ='&proses=SimpanDetail';
    param += '&nodok=';
    //alert(param);
    tujuan = 'log_slave_mutasiGRN.php';
    post_response_text(tujuan, param, respog);
    }
	document.getElementById('row_'+currRow).style.backgroundColor='orange';
	lockScreen('wait');
	function respog(){
		if (con.readyState == 4) {
			
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
					document.getElementById('row_'+currRow).style.backgroundColor='red';
				   unlockScreen();
				}
				else {
					//alert(con.responseText);
					//return;
					if(con.responseText==1)
					{
						document.getElementById('row_'+currRow).style.backgroundColor='green';
						currRow+=1;
					}
					else if(con.responseText==0)
					{
						document.getElementById('row_'+currRow).style.backgroundColor='red';
						currRow+=1;
					}
					else
					{
						alert("Error");
						tutupProses();
						//unlockScreen();
						
					}
					if(currRow>maxRow)
					{
						
						tutupProses('simpan');
                        inputBaru();
					}  
					else
					{
						loopClosingFisik(currRow,maxRow);
					}
				}
			}
			else {
				busy_off();
				error_catch(con.status);
				unlockScreen();
			}
		}
	}		
	
}
function tutupProses(x)
{
	
		if (x == 'simpan') {
			unlockScreen();
			alert("Data Telah Telah Tersimpan !!!");
			
		}
		else
		{
			unlockScreen();
		}
	
		
}