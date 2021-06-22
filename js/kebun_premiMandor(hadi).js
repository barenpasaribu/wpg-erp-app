function ambilMandor(tanggal){
    
    param='tanggal='+tanggal+'&aksi=ambilMandor';
    tujuan='kebun_slave_premiKemandoran.php';
    if(tanggal=='')
        {
            alert('Date required');
        }
    else{    
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
                            document.getElementById('idkaryawan').innerHTML=con.responseText;
                            tampilkanList(tanggal,'MANDORPANEN');
                      }
                    }
            else {
                    busy_off();
                    error_catch(con.status);
            }
      }	
    }  
}

function loadPremi(nikmandor)
{
    tanggal=document.getElementById('tanggal').value;
    param='nikmandor='+nikmandor+'&tanggal='+tanggal+'&aksi=ambilPremiPanen';
    tujuan='kebun_slave_premiKemandoran.php';
    if(tanggal=='')
        {
            alert('Date required');
        }
    else{    
        document.getElementById('komputer').innerHTML='0';
        document.getElementById('premi').value='0';
        document.getElementById('premipanen').innerHTML='0';
        document.getElementById('anggota').innerHTML='0';
        document.getElementById('save1').style.disabled=true;
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
                            arr=con.responseText.split("#");
                            document.getElementById('premipanen').innerHTML=arr[1];
                            document.getElementById('anggota').innerHTML=arr[0];
                            //standard====================
                            jlhpemanen=parseInt(arr[0]);
                            standarpembagi=parseInt(arr[2]);
                            if(jlhpemanen>=standarpembagi)
                            //if (jlhpemanen <= standarpembagi)
                                {
                                    standarpembagi=jlhpemanen;
                                }
                                    document.getElementById('pembagi').innerHTML=standarpembagi;
                                 
                          // hitung premi mandor panen==================
                         //pp=parseInt(remove_comma_var(arr[1])); 
                         // if(standarpembagi==0)
                         //     premimandor=0;
                         //else
                               //premimandor=pp/standarpembagi;
                              //premimandor = (pp / standarpembagi)*1.5;
                          
                          //document.getElementById('komputer').innerHTML=premimandor.toFixed(2);
                          //document.getElementById('premi').value=premimandor.toFixed(2);
                          //document.getElementById('save1').style.disabled=false;
                          document.getElementById('pembagi').disabled = false;
                      }
                    }
            else {
                    busy_off();
                    error_catch(con.status);
            }
      }	
    }    
}
function hitungpremimandor()
{
    prempanen = document.getElementById('premipanen').innerHTML;
    premipanen = parseInt(remove_comma_var(prempanen));
    anggota = document.getElementById('anggota').innerHTML;
    pembagi = document.getElementById('pembagi').value;
    if (pembagi == '')
        hasilhitungpremi = 0;
    else if (pembagi == 0)
        alert('Kolom Devider(Pembagi) masih 0');
    else
        //hasilhitungpremi = (premipanen / pembagi) * 1.5;
        hasilhitungpremi = (premipanen / pembagi) * 1.25;

    //alert(hasilhitungpremi);
    document.getElementById('komputer').innerHTML = hasilhitungpremi.toFixed(2);
    document.getElementById('premi').value = hasilhitungpremi.toFixed(2);
    document.getElementById('save1').style.disabled = false;
    
}
function tampilkanList(tanggal,tipe)
{
    param='tanggal='+tanggal+'&aksi=ambilList&tipe='+tipe;
    tujuan='kebun_slave_premiKemandoran.php';
    if(tanggal=='')
        {
            alert('Date required');
        }
    else{    
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
                            document.getElementById('container'+tipe).innerHTML=con.responseText;
                      }
                    }
            else {
                    busy_off();
                    error_catch(con.status);
            }
      }	
    }   
}

function savePremiMandor()
{
        komputer=document.getElementById('komputer').innerHTML;
        premimandor=document.getElementById('premi').value;  
        sumber=remove_comma_var(document.getElementById('premipanen').innerHTML);
        //pembagi=document.getElementById('pembagi').innerHTML;  
        pembagi = document.getElementById('pembagi').value;
        tanggal=document.getElementById('tanggal').value;
        karyawanid=document.getElementById('idkaryawan');
        karyawanid = karyawanid.options[karyawanid.selectedIndex].value;
        if (pembagi == '')
            pembagi = 0;
        
        param='tanggal='+tanggal+'&karyawanid='+karyawanid+'&pembagi='+pembagi+'&sumber='+sumber;
        param+='&komputer='+komputer+'&premi='+premimandor+'&aksi=simpan&jabatan=MANDORPANEN';
        tujuan='kebun_slave_premiKemandoran.php';        
        if(karyawanid!='' && pembagi!='0' && premimandor!='0.00'){  
            post_response_text(tujuan, param, respog);
        }
        else
            {
                alert('Incomplete data');
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
                                document.getElementById('pembagi').value = 0;
                                document.getElementById('pembagi').disabled = true;
                                     tampilkanList(tanggal,'MANDORPANEN');
                            }
                            }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }	
            }   
            
           
}

function ambilMandorKepala(tanggalmk){
    
    param='tanggalmk='+tanggalmk+'&aksi=ambilMandorMK';
    tujuan='kebun_slave_premiKemandoran.php';
    if(tanggalmk=='')
        {
            alert('Date required');
        }
    else{    
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
                            document.getElementById('idkaryawanmk').innerHTML=con.responseText;
                            tampilkanList(tanggalmk,'MANDOR1');
                      }
                    }
            else {
                    busy_off();
                    error_catch(con.status);
            }
      }	
    }  
}


function loadPremiMK(nikmandor1)
{
    tanggal=document.getElementById('tanggalmk').value;
    param='nikmandor1='+nikmandor1+'&tanggal='+tanggal+'&aksi=ambilPremiMandor';
    tujuan='kebun_slave_premiKemandoran.php';
    if(tanggal=='')
        {
            alert('Date required');
        }
    else{    
        document.getElementById('komputermk').innerHTML='0';
        document.getElementById('premimk').value='0';
        document.getElementById('premimandor').innerHTML='0';
        document.getElementById('anggotamk').innerHTML='0';
        document.getElementById('save2').style.disabled=true;
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
                            arr=con.responseText.split("#");
                            document.getElementById('premimandor').innerHTML=arr[1];
                            document.getElementById('anggotamk').innerHTML=arr[0];
                            //standard====================
                            standarpembagi=parseInt(arr[0]);
                           document.getElementById('pembagimk').innerHTML=standarpembagi;
                                 
                          // hitung premi mandor panen==================
                          //pp=parseInt(remove_comma_var(arr[1]));
                          //if(standarpembagi==0)
                          //    premimandor=0;
                          //else
                          //    premimandor=pp/standarpembagi;
                      
                          //document.getElementById('komputermk').innerHTML=premimandor.toFixed(2);
                          //document.getElementById('premimk').value=premimandor.toFixed(2);
                          document.getElementById('save2').style.disabled=false;
                          document.getElementById('pembagimk').disabled = false;
                      }
                    }
            else {
                    busy_off();
                    error_catch(con.status);
            }
      }	
    }    
}
function hitungpremimk() {
    premmandor = document.getElementById('premimandor').innerHTML;
    premimandor = parseInt(remove_comma_var(premmandor));
    anggotamk = document.getElementById('anggotamk').innerHTML;
    pembagimk = document.getElementById('pembagimk').value;
    if (pembagimk == '')
        hasilhitungpremimk = 0;
    else if (pembagimk == 0)
        alert('Kolom Devider(Pembagi) masih 0');
    else
        hasilhitungpremimk = (premimandor / pembagimk) * 1.5;

    //alert(hasilhitungpremi);
    document.getElementById('komputermk').innerHTML = hasilhitungpremimk.toFixed(2);
    document.getElementById('premimandor').value = hasilhitungpremimk.toFixed(2);
    document.getElementById('save2').style.disabled = false;

}
function savePremiMK()
{
        komputer=document.getElementById('komputermk').innerHTML;
        premimandor=document.getElementById('premimk').value;  
        sumber=remove_comma_var(document.getElementById('premimandor').innerHTML);
        pembagi=document.getElementById('pembagimk').value;     
        tanggal=document.getElementById('tanggalmk').value;
        karyawanid=document.getElementById('idkaryawanmk');
        karyawanid=karyawanid.options[karyawanid.selectedIndex].value;
        param='tanggal='+tanggal+'&karyawanid='+karyawanid+'&pembagi='+pembagi+'&sumber='+sumber;
        param+='&komputer='+komputer+'&premi='+premimandor+'&aksi=simpan&jabatan=MANDOR1';
        tujuan='kebun_slave_premiKemandoran.php';        
        if(karyawanid!='' && pembagi!='0' && premimandor!='0.00'){  
            post_response_text(tujuan, param, respog);
        }
        else
            {
                alert('Incomplete data');
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
                                     tampilkanList(tanggal,'MANDOR1');
                            }
                            }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }	
            }   
            
           
}

function ambilKerani(tanggal){
    
    param='tanggal='+tanggal+'&aksi=ambilKerani';
    tujuan='kebun_slave_premiKemandoran.php';
    if(tanggal=='')
        {
            alert('Date required');
        }
    else{    
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
                            document.getElementById('idkaryawanKerani').innerHTML=con.responseText;
                            tampilkanList(tanggal,'Kerani');
                      }
                    }
            else {
                    busy_off();
                    error_catch(con.status);
            }
      }	
    }  
}

function loadPremiKerani(nikkerani){
    //gunakan kolom nikasisten
    tanggal=document.getElementById('tanggalKerani').value;
    param='nikkerani='+nikkerani+'&tanggal='+tanggal+'&aksi=ambilPremiKerani';
    tujuan='kebun_slave_premiKemandoran.php';
    if(tanggal=='')
        {
            alert('Date required');
        }
    else{    
        document.getElementById('komputerKerani').innerHTML='0';
        document.getElementById('premiKerani').value='0';
        document.getElementById('premiPanenKerani').innerHTML='0';
        document.getElementById('anggotaKerani').innerHTML='0';
        document.getElementById('save2').style.disabled=true;
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
                            arr=con.responseText.split("#");
                            document.getElementById('premiPanenKerani').innerHTML=arr[1];
                            document.getElementById('anggotaKerani').innerHTML=arr[0];
                            //standard====================
                            standarpembagi=parseInt(arr[0]);
                            document.getElementById('pembagiKerani').innerHTML=standarpembagi;
                                 
                          // hitung premi mandor panen==================
                          pp=parseInt(remove_comma_var(arr[1]));
                          if(standarpembagi==0)
                              premimandor=0;
                          else
                              premimandor=(pp/standarpembagi)*(80/100);//80% saja
                          
                          document.getElementById('komputerKerani').innerHTML=premimandor.toFixed(2);
                          document.getElementById('premiKerani').value=premimandor.toFixed(2);
                          document.getElementById('save2').style.disabled=false;
                          
                      }
                    }
            else {
                    busy_off();
                    error_catch(con.status);
            }
      }	
    }  
    }
    
function savePremiKerani()
{
        komputer=document.getElementById('komputerKerani').innerHTML;
        premimandor=document.getElementById('premiKerani').value;  
        sumber=remove_comma_var(document.getElementById('premiPanenKerani').innerHTML);
        pembagi=document.getElementById('pembagiKerani').innerHTML;     
        tanggal=document.getElementById('tanggalKerani').value;
        karyawanid=document.getElementById('idkaryawanKerani');
        karyawanid=karyawanid.options[karyawanid.selectedIndex].value;
        param='tanggal='+tanggal+'&karyawanid='+karyawanid+'&pembagi='+pembagi+'&sumber='+sumber;
        param+='&komputer='+komputer+'&premi='+premimandor+'&aksi=simpan&jabatan=Kerani';
        tujuan='kebun_slave_premiKemandoran.php';        
        if(karyawanid!='' && pembagi!='0' && premimandor!='0.00'){  
            post_response_text(tujuan, param, respog);
        }
        else
            {
                alert('Incomplete data');
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
                                     tampilkanList(tanggal,'Kerani');
                            }
                            }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }	
            }   
}

function ambilKeraniPanen(tanggal){
    
    param='tanggal='+tanggal+'&aksi=ambilKeraniPanen';
    tujuan='kebun_slave_premiKemandoran.php';
    if(tanggal=='')
        {
            alert('Date required');
        }
    else{    
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
                            document.getElementById('idkaryawanKeraniPanen').innerHTML=con.responseText;
                            tampilkanList(tanggal,'KERANIPANEN');
                      }
                    }
            else {
                    busy_off();
                    error_catch(con.status);
            }
      }	
    }  
}

function loadPremiKeraniPanen(nikkeraniPanen){
    //gunakan kolom nikasisten
    tanggal=document.getElementById('tanggalKeraniPanen').value;
    param='nikkeraniPanen='+nikkeraniPanen+'&tanggal='+tanggal+'&aksi=ambilPremiKeraniPanen';
    tujuan='kebun_slave_premiKemandoran.php';
    if(tanggal=='')
        {
            alert('Date required');
        }
    else{    
        document.getElementById('komputerKeraniPanen').innerHTML='0';
        document.getElementById('premiKeraniPanen').value='0';
        document.getElementById('premiPanenKeraniPanen').innerHTML='0';
        document.getElementById('anggotaKeraniPanen').innerHTML='0';
        document.getElementById('save3').style.disabled=true;
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
                            arr=con.responseText.split("#");
                            document.getElementById('premiPanenKeraniPanen').innerHTML=arr[1];
                            document.getElementById('anggotaKeraniPanen').innerHTML=arr[2];
                            //standard====================
                            standarpembagi=parseInt(arr[0]);
                            document.getElementById('pembagiKeraniPanen').innerHTML=standarpembagi;
                                 
                          // hitung premi mandor panen==================
                          //pp=parseInt(remove_comma_var(arr[1]));
                          //if(standarpembagi==0)
                          //    premimandor=0;
                          //else
                          //    //premimandor=pp/standarpembagi;//disini 100 %
                          //    premimandor = (pp/standarpembagi)*1.25;
                          
                          //document.getElementById('komputerKeraniPanen').innerHTML=premimandor.toFixed(2);
                          //document.getElementById('premiKeraniPanen').value=premimandor.toFixed(2);
                          document.getElementById('save3').style.disabled=false;
                          document.getElementById('pembagiKeraniPanen').disabled = false;
                      }
                    }
            else {
                    busy_off();
                    error_catch(con.status);
            }
      }	
    }  
    }
function hitungpremikeranipanen() {
    premiPanenKeraniPanenqw = document.getElementById('premiPanenKeraniPanen').innerHTML;
    premiPanenKeraniPanen = parseInt(remove_comma_var(premiPanenKeraniPanenqw));
    anggotaKeraniPanen = document.getElementById('anggotaKeraniPanen').innerHTML;
    pembagiKeraniPanen = document.getElementById('pembagiKeraniPanen').value;
    if (pembagiKeraniPanen == '')
        hasilhitungpremikeranipanen= 0;
    else if (pembagiKeraniPanen == 0)
        alert('Kolom Devider(Pembagi) masih 0');
    else
        hasilhitungpremikeranipanen = (premiPanenKeraniPanen / pembagiKeraniPanen) * 1.25;

    //alert(hasilhitungpremikeranipanen);
    document.getElementById('komputerKeraniPanen').innerHTML = hasilhitungpremikeranipanen.toFixed(2);
    document.getElementById('premiKeraniPanen').value = hasilhitungpremikeranipanen.toFixed(2);
    document.getElementById('save3').style.disabled = false;

}   
 function savePremiKeraniPanen()
{
        komputer=document.getElementById('komputerKeraniPanen').innerHTML;
        premimandor=document.getElementById('premiKeraniPanen').value;  
        sumber=remove_comma_var(document.getElementById('premiPanenKeraniPanen').innerHTML);
        pembagi=document.getElementById('pembagiKeraniPanen').value;     
        tanggal=document.getElementById('tanggalKeraniPanen').value;
        karyawanid=document.getElementById('idkaryawanKeraniPanen');
        karyawanid=karyawanid.options[karyawanid.selectedIndex].value;
        param='tanggal='+tanggal+'&karyawanid='+karyawanid+'&pembagi='+pembagi+'&sumber='+sumber;
        param+='&komputer='+komputer+'&premi='+premimandor+'&aksi=simpan&jabatan=KERANIPANEN';
        tujuan='kebun_slave_premiKemandoran.php';        
        if(karyawanid!='' && pembagi!='0' && premimandor!='0.00'){  
            post_response_text(tujuan, param, respog);
        }
        else
            {
                alert('Incomplete data');
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
                                     tampilkanList(tanggal,'KERANIPANEN');
                            }
                            }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }	
            }   
}

function deletePremi(karyawanid,kodeorg,tanggal,tipe)
{
    //format tangga==YYYY-mm-dd
        param='karyawanid='+karyawanid+'&kodeorg='+kodeorg+'&aksi=delete&jabatan='+tipe+'&tanggal='+tanggal;
      tujuan='kebun_slave_premiKemandoran.php';   

        if(confirm('Delete, Are you sure..?')){  
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
                                     tanggal=tanggal.substr(8,2)+"-"+tanggal.substr(5,2)+"-"+tanggal.substr(0,4);
                                     tampilkanList(tanggal,tipe);
                            }
                            }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }	
            }    
}

function listAllPremi()
{
    periode=document.getElementById('periode');
    periode=periode.options[periode.selectedIndex].value;
     tampilkanList(periode,'ALLLIST');
}

function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='900';
   height='200';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
//   document.getElementById('container').innerHTML = "<iframe frameborder=0 style='width:100%;height:99%' src='"+fileTarget+".php?"+param+"'></iframe>";
   showDialog1(title,content,width,height,ev); 	
}

function getexcel(ev,tujuan){
    periode=document.getElementById('periode');
    periode=periode.options[periode.selectedIndex].value;
    judul='Report Ms.Excel';	
    param='tanggal='+periode+'&aksi=ambilList&proses=excel';
    printFile(param,tujuan,judul,ev)    
} 

function postingPremi(karyawanid,kodeorg,tanggal,tipe)
{
     param='karyawanid='+karyawanid+'&kodeorg='+kodeorg+'&aksi=posting&jabatan='+tipe+'&tanggal='+tanggal;
      tujuan='kebun_slave_premiKemandoran.php';   

        if(confirm('Posting, Are you sure..?')){  
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
                                    listAllPremi();
                            }
                            }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }	
            }   
}
