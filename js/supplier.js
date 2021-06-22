/**
 * @author antoniuslouis
 */
//=================================================sisi purchasing
function getKelompokSupplier(tipe)
{
        param='tipe='+tipe;
        tujuan='log_slave_get_klsupplier.php';
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
                                                        document.getElementById('kdkelompok').innerHTML=trim(con.responseText);
                                                    document.getElementById('captiontipe').innerHTML=tipe;		
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  	
}

function getSupplierNumber(kdkelompok,namakelompok)
{
        param='kelompok='+kdkelompok;
        tujuan='log_slave_get_klsupplier_number.php';
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
                                                        document.getElementById('idsupplier').value=trim(con.responseText);
                                                    document.getElementById('captionkelompok').innerHTML=namakelompok;
                                                    getSupplierList(kdkelompok);
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  	
}

function getSupplierList(kdkelompok)
{
        param='kelompok='+kdkelompok;
        tujuan='log_slave_save_supplier.php';
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
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 	
}
function trfWB(idsuplier,namasupplier,alamat,kota,kodekl)
{
   if(confirm('Kirim Ke Program Weight Bridge Untuk Data Suplier : '+namasupplier+', Are you sure..?'))
        {
       
       
        param='idsuplier='+idsuplier+'&namasupplier='+namasupplier+'&alamat='+alamat+'&kota='+kota+'&kodekl='+kodekl+'&method=trfWB';
        tujuan='log_slave_save_supplier.php';
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
                                                     
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      } 
         }  
}

function cancelSupplier()
{
           document.getElementById('tipe').options[0].selected=true;
           document.getElementById('telp').value='';
           document.getElementById('kdkelompok').options[0].selected=true;
           document.getElementById('fax').value='';
           document.getElementById('idsupplier').value='';
           document.getElementById('email').value='';
           document.getElementById('namasupplier').value='';
           document.getElementById('npwp').value='';
           document.getElementById('alamat').value='';
       document.getElementById('cperson').value='';
           document.getElementById('kota').value='';
           document.getElementById('plafon').value='0';
           document.getElementById('method').value='insert';
           document.getElementById('tipe').disabled=false;
           document.getElementById('kdkelompok').disabled=false;
}

function saveSupplier()
{
           telp=document.getElementById('telp').value;
           kelompok=document.getElementById('kdkelompok').options[document.getElementById('kdkelompok').selectedIndex].value;
           fax=document.getElementById('fax').value;
           idsupplier=trim(document.getElementById('idsupplier').value);
           email=document.getElementById('email').value;
           namasupplier=document.getElementById('namasupplier').value;
           npwp=document.getElementById('npwp').value;
           alamat=document.getElementById('alamat').value;
       cperson=document.getElementById('cperson').value;
           kota=document.getElementById('kota').value;	   
           plafon=remove_comma(document.getElementById('plafon'));	 
           pkp=document.getElementById('pkp').value;  
           method=document.getElementById('method').value;


        param='telp='+telp+'&kelompok='+kelompok+'&fax='+fax+'&pkp='+pkp;
        param+='&idsupplier='+idsupplier+'&email='+email+'&namasupplier='+namasupplier;
        param+='&npwp='+npwp+'&cperson='+cperson+'&kota='+kota;
        param+='&plafon='+plafon+'&method='+method+'&alamat='+alamat;	
        tujuan='log_slave_save_supplier.php';
        //alert(param);
        if(idsupplier=='' || kelompok=='' )
                alert('Data incompete');
        else
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
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 		
}

function editSupplier(idsupplier,namasupplier,alamat,kontakperson,kota,telp,fax,email,npwp,plafon,pkp)
{
           objtipe=document.getElementById('tipe');
           tipe=idsupplier.substring(0,1);
           if(tipe=='S')
             tipe='SUPPLIER';
           else
             tipe='KONTRAKTOR';
           for(x=0;x<objtipe.length;x++)
           {
                 if(objtipe.options[x].value==tipe)
                 {
                        objtipe.options[x].selected=true;
                 }
           }
           objtipe.disabled=true;
           objkelompok=document.getElementById('kdkelompok')	
           kel=idsupplier.substring(0,4);
           for(x=0;x<objkelompok.length;x++)
           {
                  if(objkelompok.options[x].value==kel)
                  {
                        objkelompok.options[x].selected=true;
                  }
           }   	 
           objkelompok.disabled=true;	  

           document.getElementById('telp').value=telp;
           document.getElementById('fax').value=fax;
           document.getElementById('email').value=email;
           document.getElementById('namasupplier').value=namasupplier;
           document.getElementById('npwp').value=npwp;
           document.getElementById('alamat').value=alamat;
       document.getElementById('cperson').value=kontakperson;
           document.getElementById('kota').value=kota;
           document.getElementById('plafon').value=plafon;
           document.getElementById('method').value='update';
       document.getElementById('idsupplier').value=idsupplier;
       document.getElementById('pkp').value=pkp;
           change_number(document.getElementById('plafon'));	   	
}

function delSupplier(id,nama)
{
        if(confirm('Deleting '+nama+', Are you sure..?'))
        {
        param='idsupplier='+id+'&method=delete';
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
//========================================================sisi akunting

function findSupplier()
{
        txt=trim(document.getElementById('cari').value);
        if(txt=='')
        {
                alert('Please type supplier name');
        }
        else
        {
        param='txt='+txt;
                tujuan='log_slave_save_akun_supplier.php';
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

function editAkunSupplier(supplierid,namasupplier,noakun,nilaihutang,noseripajak,akunpajak,bank,rekening,an)
{
          document.getElementById('idsupplier').value	=supplierid;
          document.getElementById('bank').value			=bank;
          obj=document.getElementById('noakun');
          for(x=0;x<obj.length;x++)
          {
            if(obj.options[x].value==noakun)
                   obj.options[x].selected=true;
          }
          document.getElementById('rek').value			=rekening;
          document.getElementById('namasupplier').value	=namasupplier;
          document.getElementById('an').value			=an;

          obj1=document.getElementById('akunpajak');
          for(x=0;x<obj1.length;x++)
          {
            if(obj1.options[x].value==akunpajak)
                   obj1.options[x].selected=true;
          }

          document.getElementById('noseripajak').value	=noseripajak;
          document.getElementById('nilaihutang').value	=nilaihutang;
}

function cancelAkunSupplier()
{
          document.getElementById('idsupplier').value	='';
          document.getElementById('bank').value			='';
          obj=document.getElementById('noakun');
                   obj.options[0].selected=true;
          document.getElementById('rek').value			='';
          document.getElementById('namasupplier').value	='';
          document.getElementById('an').value			='';
          obj1=document.getElementById('akunpajak');
                   obj1.options[0].selected=true;
          document.getElementById('noseripajak').value	='';
          document.getElementById('nilaihutang').value	='0';	
}

function saveAkunSupplier()
{
          obj1		=document.getElementById('akunpajak');
          akunpajak	=obj1.options[obj1.selectedIndex].value;
          obj	=document.getElementById('noakun');	
          noakun=obj.options[obj.selectedIndex].value;  

          idsupplier=trim(document.getElementById('idsupplier').value);
          bank		=trim(document.getElementById('bank').value);
          rek		=trim(document.getElementById('rek').value);
          namasupplier	=trim(document.getElementById('namasupplier').value);
          an			=trim(document.getElementById('an').value);
          noseripajak	=trim(document.getElementById('noseripajak').value);
          nilaihutang	=remove_comma(document.getElementById('nilaihutang'));	
         param='noakun='+noakun+'&akunpajak='+akunpajak;
         param+='&idsupplier='+idsupplier+'&an='+an+'&bank='+bank;
         param+='&rek='+rek+'&namasupplier='+namasupplier;
         param+='&noseripajak='+noseripajak+'&nilaihutang='+nilaihutang;
         //method always update
         param+='&method=update';
         if(idsupplier=='')
         {
                alert('object id  undefined');
         } 
         else
         {
                tujuan='log_slave_save_akun_supplier.php';
                if(confirm('Saving Account for '+namasupplier+', Are you sure..?'))
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
                                                    cancelAkunSupplier();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }	 	 
}
function updateStatus(notrans,stat){
   
        param='method=updStatus'+'&supplierid='+notrans+'&status='+stat;	
        tujuan='log_slave_save_supplier.php';
        //alert(param);
        if(stat==1){
            dert="Are you sure deactive this supplier?";
        }else{
            dert="Are you sure active this supplier?";
        }
     if(confirm(dert)){
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
                                                   kdkelompok=notrans.substring(0,4);
                                                   getSupplierList(kdkelompok);
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 		
}