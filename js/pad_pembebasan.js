/**
 * @author antoniuslouis
 */
 
function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='900';
   height='500';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}

function ptintPDF(idlahan,pemilik,ev)
{
    method='pdf';
    param='idlahan='+idlahan+'&pemilik='+pemilik+'&method='+method;
    tujuan='pad_slave_save_pembebasan.php';
    judul='Report PDF';	
    printFile(param,tujuan,judul,ev)	
}

function simpanJabatan()
{
    mid=document.getElementById('mid').value;
    unit=document.getElementById('unit'); 
    unit=unit.options[unit.selectedIndex].value;
    pemilik=document.getElementById('pemilik'); 
    pemilik=pemilik.options[pemilik.selectedIndex].value;    
    lokasi=document.getElementById('lokasi').value;
    luas=document.getElementById('luas').value;
    bisaditanam=document.getElementById('bisaditanam').value;
    blok=document.getElementById('blok'); 
    blok=blok.options[blok.selectedIndex].value;    
    batastimur=document.getElementById('batastimur').value;
    batasbarat=document.getElementById('batasbarat').value;
    batasutara=document.getElementById('batasutara').value;
    batasselatan=document.getElementById('batasselatan').value;
    rptanaman=remove_comma_var(document.getElementById('rptanaman').value);
    rptanah=remove_comma_var(document.getElementById('rptanah').value);
    biayakades=remove_comma_var(document.getElementById('biayakades').value);
    biayacamat=remove_comma_var(document.getElementById('biayacamat').value);
    biayamatrai=remove_comma_var(document.getElementById('biayamatrai').value);
    statuspermintaandana=document.getElementById('statuspermintaandana'); 
    statuspermintaandana=statuspermintaandana.options[statuspermintaandana.selectedIndex].value;    
    statuspermbayaran=document.getElementById('statuspermbayaran'); 
    statuspermbayaran=statuspermbayaran.options[statuspermbayaran.selectedIndex].value;     
    statuskades=document.getElementById('statuskades'); 
    statuskades=statuskades.options[statuskades.selectedIndex].value; 
    statuscamat=document.getElementById('statuscamat'); 
    statuscamat=statuscamat.options[statuscamat.selectedIndex].value; 
    nosurat=document.getElementById('nosurat').value;
    keterangan=document.getElementById('keterangan').value;
    tanggalpermintaan=document.getElementById('tanggalpermintaan').value;
    tanggalbayar=document.getElementById('tanggalbayar').value;
    tanggalkades=document.getElementById('tanggalkades').value;
    tanggalcamat=document.getElementById('tanggalcamat').value;
    met=document.getElementById('method').value;
   if(luas<bisaditanam)
       {
           alert('Luas harus lebih besar atau sama dengan luas dapat ditanam');
       }
   else if((tanggalpermintaan!='' && statuspermintaandana=='0') || (tanggalbayar!='' && statuspermbayaran=='0') || (tanggalkades!='' && statuskades=='0') || (tanggalcamat!='' && statuscamat=='0'))
       {
           alert('Status dan tanggal status ada yang tidak sesuai');
       }
    else if(statuspermintaandana!='0' &&(rptanaman=='0' && rptanah=='0' && biayakades=='0' && biayacamat=='0') )
        {
            alert('Biaya belum diisi');
        }
  else if(lokasi=='')
      {
          alert('Mohon diisi keterangan lokasi');
      }      
  else
      {
        param='mid='+mid+'&unit='+unit+'&pemilik='+pemilik;
        param+='&lokasi='+lokasi+'&luas='+luas+'&bisaditanam='+bisaditanam;
        param+='&blok='+blok+'&batastimur='+batastimur+'&batasbarat='+batasbarat;
        param+='&batasutara='+batasutara+'&batasselatan='+batasselatan+'&rptanaman='+rptanaman;
        param+='&rptanah='+rptanah+'&biayakades='+biayakades+'&biayacamat='+biayacamat;
        param+='&biayamatrai='+biayamatrai+'&statuspermintaandana='+statuspermintaandana+'&statuspermbayaran='+statuspermbayaran;
        param+='&statuskades='+statuskades+'&statuscamat='+statuscamat+'&nosurat='+nosurat;
        param+='&keterangan='+keterangan+'&tanggalpermintaan='+tanggalpermintaan+'&tanggalbayar='+tanggalbayar;
        param+='&tanggalkades='+tanggalkades+'&tanggalcamat='+tanggalcamat;
        param+='&method='+met;
        tujuan='pad_slave_save_pembebasan.php';
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
                                                cancelJabatan();
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }	
 }

}
                                                                                                                                                   
function fillField(idlahan,pemilik,unit,lokasi,luas,luasdapatditanam,rptanaman,rptanah,statuspermintaandana,statuspermbayaran,kodeblok,statuskades,statuscamat,tanggalpengajuan,tanggalbayar,tanggalkades,tanggalcamat,biayakades,biayacamat,biayamatrai,keterangan,nosurat,batastimur,batasbarat,batasutara,batasselatan)
{
    if(document.getElementById('unit').options[document.getElementById('unit').selectedIndex].value!=unit)
     {
         alert('Pilih unit terlebih dahulu');
     }   
     else{
            document.getElementById('mid').value=idlahan;
            x=document.getElementById('pemilik');
            for(y=0;y<x.length;y++)
                {
                    if(x.options[y].value==pemilik)
                        x.options[y].selected=true;
                }
           document.getElementById('lokasi').value=lokasi;
           document.getElementById('luas').value=luas;
           document.getElementById('bisaditanam').value=luasdapatditanam;
            x=document.getElementById('blok');
            for(y=0;y<x.length;y++)
                {
                    if(x.options[y].value==kodeblok)
                        x.options[y].selected=true;
                }         
            document.getElementById('batastimur').value=batastimur;
            document.getElementById('batasbarat').value=batasbarat;
            document.getElementById('batasutara').value=batasutara;
            document.getElementById('batasselatan').value=batasselatan;
            document.getElementById('rptanaman').value=rptanaman;
            document.getElementById('rptanah').value=rptanah;
            document.getElementById('biayakades').value=biayakades;
            document.getElementById('biayacamat').value=biayacamat;
            document.getElementById('biayamatrai').value=biayamatrai;
            
            x=document.getElementById('statuspermintaandana'); 
            for(y=0;y<x.length;y++)
                {
                    if(x.options[y].value==statuspermintaandana)
                        x.options[y].selected=true;
                }     
            x=document.getElementById('statuspermbayaran'); 
            for(y=0;y<x.length;y++)
                {
                    if(x.options[y].value==statuspermbayaran)
                        x.options[y].selected=true;
                }     
            x=document.getElementById('statuskades'); 
            for(y=0;y<x.length;y++)
                {
                    if(x.options[y].value==statuskades)
                        x.options[y].selected=true;
                }     
            x=document.getElementById('statuscamat'); 
            for(y=0;y<x.length;y++)
                {
                    if(x.options[y].value==statuscamat)
                        x.options[y].selected=true;
                }  
            document.getElementById('nosurat').value=nosurat;
            document.getElementById('keterangan').value=keterangan;
            if(tanggalpengajuan=='00-00-0000')
                tanggalpengajuan='';
            document.getElementById('tanggalpermintaan').value=tanggalpengajuan;
            if(tanggalbayar=='00-00-0000')
                tanggalbayar='';            
            document.getElementById('tanggalbayar').value=tanggalbayar;
            if(tanggalkades=='00-00-0000')
                tanggalkades='';                  
            document.getElementById('tanggalkades').value=tanggalkades;
            if(tanggalcamat=='00-00-0000')
                tanggalcamat='';                              
            document.getElementById('tanggalcamat').value=tanggalcamat;
     }
    document.getElementById('method').value='update';
}

function cancelJabatan()
{
    document.getElementById('mid').value='';
    x=document.getElementById('pemilik');
    x.options[0].selected=true;
   document.getElementById('lokasi').value='';
   document.getElementById('luas').value=0;
   document.getElementById('bisaditanam').value=0;
    x=document.getElementById('blok');
    x.options[0].selected=true;    
    document.getElementById('batastimur').value='';
    document.getElementById('batasbarat').value='';
    document.getElementById('batasutara').value='';
    document.getElementById('batasselatan').value='';
    document.getElementById('rptanaman').value=0;
    document.getElementById('rptanah').value=0;
    document.getElementById('biayakades').value=0;
    document.getElementById('biayacamat').value=0;
    document.getElementById('biayamatrai').value=0;     
    x=document.getElementById('statuspermintaandana'); 
    x.options[0].selected=true;
    x=document.getElementById('statuspermbayaran'); 
    x.options[0].selected=true;
    x=document.getElementById('statuskades'); 
    x.options[0].selected=true;
    x=document.getElementById('statuscamat'); 
    x.options[0].selected=true;
    document.getElementById('nosurat').value='';
    document.getElementById('keterangan').value='';
    document.getElementById('tanggalpermintaan').value='';
    document.getElementById('tanggalbayar').value='';     
    document.getElementById('tanggalkades').value='';                   
    document.getElementById('tanggalcamat').value='';
    document.getElementById('method').value='insert';		
}

function updatePemilik(unit){
        param='unit='+unit+'&method=getPemilik';
        tujuan='pad_slave_save_pembebasan.php';
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
                                                        document.getElementById('pemilik').innerHTML=con.responseText;
                                                        updateBlok(unit);
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }    
}

function updateBlok(unit){
        param='unit='+unit+'&method=getBlok';
        tujuan='pad_slave_save_pembebasan.php';
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
                                                        document.getElementById('blok').innerHTML=con.responseText;
                                                        updateList(unit);
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }    
}

function changeTanggal(objid,value)
{
    if(value=='0')
        document.getElementById(objid).value='';
}
function  updateList(unit){
        param='unit='+unit+'&method=getList';
        tujuan='pad_slave_save_pembebasan.php';
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
                                                        document.getElementById('container').innerHTML=con.responseText;
                                                        cancelJabatan();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }          
}

function deleteData(idlahan,unit)
{
    param='mid='+idlahan+'&unit='+unit+'&method=delete';
        tujuan='pad_slave_save_pembebasan.php';
        
     if(confirm('Deleting id:'+idlahan+', Are you sure..?')){   
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
function postingData(idlahan,unit)
{
    param='mid='+idlahan+'&unit='+unit+'&method=posting';
        tujuan='pad_slave_save_pembebasan.php';
        
     if(confirm('Posting id:'+idlahan+' will commited for good,  Are you sure..?')){   
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
                                                        cancelJabatan();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }          
}
//====================upoad photo
xz=0;   
function uploadDocument(notransaksi,pemilik,ev)
{
   xz++;    
   param='notransaksi='+notransaksi;
   tujuan='pad_uploadPhoto.php'+"?"+param;  
   width='700';
   height='400';
  
   content="<iframe name=uploadPhoto"+xz+" frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1('Upload Photo PAD lahan id:'+notransaksi,content,width,height,ev); 
}

function simpanPhoto(){
    eval("uploadPhoto"+xz+".document.getElementById('photoqc').action='pad_slave_savePhoto.php'");	
    eval("uploadPhoto"+xz+".document.getElementById('photoqc').submit()");
}

function delPicture(notransaksi,filename)
{
    param='notransaksi='+notransaksi+'&filename='+filename+'&aksi=del';
        tujuan = 'pad_slave_savePhoto.php';
    if(confirm("Anda yakin menghapus file "+filename+" ?"))
    {
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
                                    eval("uploadPhoto"+xz+".location.reload();");
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }
}