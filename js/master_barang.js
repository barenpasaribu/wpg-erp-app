/**
 * @author antoniuslouis
 */

function getMaterialNumber(mayor)
{
                param='mayor='+mayor;
                tujuan='log_slave_get_material_number.php';
        post_response_text(tujuan, param, respog);
                document.getElementById('caption').innerHTML=document.getElementById('kelompokbarang').options[document.getElementById('kelompokbarang').selectedIndex].text;
   //change search option selected index
        ser=document.getElementById('optcari');
        for(g=0;g<ser.length;g++)
        {
                if(ser.options[g].value==mayor)
                {
                   ser.options[g].selected=true;	
                }
        }

        ser=document.getElementById('kelompokbarang');
        for(g=0;g<ser.length;g++)
        {
                if(ser.options[g].value==mayor)
                {
                   ser.options[g].selected=true;	
                }
        }	
        function respog()
        {
                      if(con.readyState==4)
                      {
                                if (con.status == 200) {
                                                busy_off();
                                                if (!isSaveResponse(con.responseText)) {
                                                        //alert('ERROR TRANSACTION,\n' + con.responseText);
                                                    document.getElementById('kodebarang').value = con.responseText;
                                                    getMaterialMember(mayor);
                                                }
                                                else {
                                                        document.getElementById('kodebarang').value=con.responseText;
                                                    getMaterialMember(mayor);
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }	
}
function getMaterialMember(mayor)
{
                param='mayor='+mayor;
                tujuan='log_slave_get_material_member.php';
        post_response_text(tujuan, param, respog);
                document.getElementById('caption').innerHTML=document.getElementById('kelompokbarang').options[document.getElementById('kelompokbarang').selectedIndex].text;

        function respog()
        {
                      if(con.readyState==4)
                      {
                                if (con.status == 200) {
                                                busy_off();
                                                if (!isSaveResponse(con.responseText)) {
                                                        //alert('ERROR TRANSACTION,\n' + con.responseText);
                                                    document.getElementById('container').innerHTML = con.responseText;
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
        document.getElementById('method').value='insert';		
}

function fillField(kelompokbarang,kodebarang,namabarang,satuan,minstok,nokartubin,konversi)
{
    kel=document.getElementById('kelompokbarang');
        for(g=0;g<kel.length;g++)
        {
                if(kel.options[g].value==kelompokbarang)
                {
                   kel.options[g].selected=true;	
                }
        }

    document.getElementById('kodebarang').value=kodebarang;
    sat=document.getElementById('satuan');
        for(g=0;g<sat.length;g++)
        {		
                if(sat.options[g].value==satuan)
                {
                   sat.options[g].selected=true;	
                }
        }	
        document.getElementById('namabarang').value=namabarang;
        document.getElementById('minstok').value=minstok;
        document.getElementById('nokartu').value=nokartubin;
        if(konversi=='1')
        {
                document.getElementById('konversi').options[0].selected=true;
        }
        else
        {
                document.getElementById('konversi').options[1].selected=true;
        }
        document.getElementById('method').value='update';
}

function delBarang(kodebarang,mayor)
{
  tujuan='log_slave_get_material_member.php';
   param='kodebarang='+kodebarang+'&mayor='+mayor+'&method=delete';
   if(confirm('Deleting '+kodebarang+' .., Are you sure..?'))
   {
         post_response_text(tujuan, param, respog);
   }
        function respog()
        {
                      if(con.readyState==4)
                      {
                                if (con.status == 200) {
                                                busy_off();
                                                if (!isSaveResponse(con.responseText)) {
                                                    //alert('ERROR TRANSACTION,\n' + con.responseText);
                                                    document.getElementById('container').innerHTML = con.responseText;
                                                    cancelBarang();
                                                }
                                                else {
                                                        //alert(con.responseText);
                                                        document.getElementById('container').innerHTML=con.responseText;
                                                   cancelBarang();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  	
}
function cancelBarang(){

        kdbarang=document.getElementById('kodebarang').value;
        document.getElementById('method').value='insert';	
        document.getElementById('namabarang').value='';
        document.getElementById('minstok').value='0';
        document.getElementById('nokartu').value='';
//get current number
        kl		=document.getElementById('kelompokbarang');
        kl		=trim(kl.options[kl.selectedIndex].value);
        getMaterialNumber(kl);
}

function simpanBarangBaru()
{
        tujuan='log_slave_get_material_member.php';
        kl		=document.getElementById('kelompokbarang');
        kl		=trim(kl.options[kl.selectedIndex].value);

        method	=document.getElementById('method').value;	
        kdbarang=trim(document.getElementById('kodebarang').value);	
        nmbrg	=trim(document.getElementById('namabarang').value);
        minstok	=document.getElementById('minstok').value;
        nokartu =document.getElementById('nokartu').value;

        konversi=document.getElementById('konversi');
        konversi=konversi.options[konversi.selectedIndex].value;

        satuan	=document.getElementById('satuan');
        satuan	=satuan.options[satuan.selectedIndex].value;

        param='mayor='+kl+'&kodebarang='+kdbarang+'&namabarang='+nmbrg;
        param+='&satuan='+satuan+'&minstok='+minstok+'&konversi='+konversi;
        param+='&nokartu='+nokartu+'&method='+method;

   if(confirm('Saving/Simpan '+kdbarang+' .., Are you sure..?'))
   {
         if(nmbrg=='' || kl=='' || kdbarang=='')
                alert('Material group/code/name is obligatory');
         else
           post_response_text(tujuan, param, respog);
   }
        function respog()
        {
                      if(con.readyState==4)
                      {
                                if (con.status == 200) {
                                                busy_off();
                                                if (!isSaveResponse(con.responseText)) {
                                                    //alert('ERROR TRANSACTION,\n' + con.responseText);
                                                    document.getElementById('container').innerHTML = con.responseText;
                                                    alert('Done');
                                                    cancelBarang();
                                                    increaseKodeBarang(kdbarang);
                                                }
                                                else {
                                                        //alert(con.responseText);
                                                        document.getElementById('container').innerHTML=con.responseText;
                                                    alert('Done');
                                                        cancelBarang();
                                                        increaseKodeBarang(kdbarang);
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  	

}

function increaseKodeBarang(kdbarang)
{
  x=parseInt(kdbarang);
  x=x+1;
  if(x<10)
     x='0000'+x;
  else if(x<100)
     x='000'+x;
  else if(x<1000)
     x='00'+x;
  else if(x<10000)
     x='0'+x;	 	 	 

  document.getElementById('kodebarang').value=x;	
}
function cariBarang()
{

        tujuan='log_slave_get_material_member.php';
        txtcari=document.getElementById('txtcari').value;
        ongroup=document.getElementById('optcari');
        mayor=ongroup.options[ongroup.selectedIndex].value;
        param='txtcari='+txtcari+'&mayor='+mayor;

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
                                                        //cancelBarang();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  	

}

function masterbarangPDF(ev)
{
  //ambil kelompok barang dari pilihan pada form
  klbarang=document.getElementById('kelompokbarang');
  klbarang=trim(klbarang.options[klbarang.selectedIndex].value);
  if(klbarang=='')
  alert('Choose material group');
  else
  {
    //nilai parameter
        namatable='log_5masterbarang';
        kondisi  ='kelompokbarang=\''+klbarang+'\'';
        kolom	 ='kelompokbarang,kodebarang,namabarang,satuan';

        //=========================
        param='table='+namatable+'&kondisi='+kondisi+'&kolom='+kolom+'&klbarang='+klbarang;
        content="<iframe src=\"log_slave_5masterbarang_pdf.php?"+param+"\" style='width:498px;height:398px;'></iframe>";
    showDialog1("MASTER BARANG",content,'500','400',ev);
  }
}

nameV='winbarang';
x=0;
function editDetailbarang(kodebarang,ev)
{
        x+=1;
        nx=nameV+x;
        tujuan='log_slave_edit_material_detail.php?kodebarang='+kodebarang;
    content="<iframe name="+nx+" src="+tujuan+" frameborder=0 width=590px height=490px></iframe>";   
    showDialog1("Edit Detail Barang:"+kodebarang,content,'500','300',ev);
}

function simpanPhoto(){
        nx=nameV+x;
        spec=eval(nx+".document.getElementById('spec').value");
        kodebarang=eval(nx+".document.getElementById('kodebarangx').value");

        if(spec=='')
        {
                if(confirm('Spec is empty, Save..?'))
                {
                        simpan(kodebarang,nx);
                }
        }
        else
        {
                simpan(kodebarang,nx);
        }
        function simpan(kodebarang,nx)
        {
                eval(nx+".document.getElementById('"+kodebarang+"').action='log_slave_savePhotoBarang.php'");	
                eval(nx+".document.getElementById('"+kodebarang+"').submit()");
        }

}

function viewDetailbarang(kodebarang,ev)
{
        tujuan='log_slave_material_picture_detail.php?kodebarang='+kodebarang;
    content="<iframe name=disPhotobarang src="+tujuan+" frameborder=0 width=680px height=380px></iframe>";   
    showDialog1("Detail:"+kodebarang,content,'700','400',ev);

}

function setInactive(kodebarang)
{
        xstatus=document.getElementById('br'+kodebarang).checked==true?1:0;
        param='kodebarang='+kodebarang+'&status='+xstatus;
    tujuan='log_slave_update_masterBarang.php';
         post_response_text(tujuan, param, respog);

        function respog()
        {
                      if(con.readyState==4)
                      {
                                if (con.status == 200) {
                                                busy_off();
                                                if (!isSaveResponse(con.responseText)) {
                                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                                        if(document.getElementById('br'+kodebarang).checked==true)
                                                            document.getElementById('br'+kodebarang).checked=false;
                                                        else
                                                                document.getElementById('br'+kodebarang).checked=true;
                                                }
                                                else {

                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                                        if(document.getElementById('br'+kodebarang).checked==true)
                                                            document.getElementById('br'+kodebarang).checked=false;
                                                        else
                                                                document.getElementById('br'+kodebarang).checked=true;						
                                        }
                      }	
         }  	

}
