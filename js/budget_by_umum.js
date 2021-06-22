/**
 * @author antoniuslouis
 */
// dhyaz sep 22, 2011

function bersihkanDonk()
{
    for(zx=1;zx<13;zx++)
        {
            document.getElementById('ss'+zx).value=0;
        }
}

function sebarkanBoo(kunci,baris,obj,rupe,fis)
{
    document.getElementById('baris'+baris).style.backgroundColor='orange';
    var1=parseInt(document.getElementById('ss1').value);
    var2=parseInt(document.getElementById('ss2').value);
    var3=parseInt(document.getElementById('ss3').value);
    var4=parseInt(document.getElementById('ss4').value);
    var5=parseInt(document.getElementById('ss5').value);
    var6=parseInt(document.getElementById('ss6').value);
    var7=parseInt(document.getElementById('ss7').value);
    var8=parseInt(document.getElementById('ss8').value);
    var9=parseInt(document.getElementById('ss9').value);
    var10=parseInt(document.getElementById('ss10').value);
    var11=parseInt(document.getElementById('ss11').value);
    var12=parseInt(document.getElementById('ss12').value);
    zz=var1+var2+var3+var4+var5+var6+var7+var8+var9+var10+var11+var12;
    if(zz && zz>0)
    { 
    param='cekapa=sebarDoong&kunci='+kunci;
    param+='&var1='+(var1/zz)+'&var2='+(var2/zz)+'&var3='+(var3/zz)+'&var4='+(var4/zz)+'&var5='+(var5/zz);
    param+='&var6='+(var6/zz)+'&var7='+(var7/zz)+'&var8='+(var8/zz)+'&var9='+(var9/zz)+'&var10='+(var10/zz);
    param+='&var11='+(var11/zz)+'&var12='+(var12/zz)+'&rupe='+rupe+'&fis='+fis;
    tujuan='budget_slave_by_umum.php';
    if(obj.checked)
        post_response_text(tujuan, param, respog);            
    }
    else
    {
        alert('Distribution incorrect');
    }

    function respog()
    {
        if(con.readyState==4)
        {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    document.getElementById('baris'+baris).style.backgroundColor='red';
                }
                else {
//                    updateTab4();
                    document.getElementById('baris'+baris).style.backgroundColor='green';
                }
            }
            else {
                busy_off();
                error_catch(con.status);
                document.getElementById('baris'+baris).style.backgroundColor='red';
            }
        }	
    } 
}

function simpan()
{
    kodebudget =document.getElementById('kodebudget');
    jumlahbiaya =document.getElementById('jumlahbiaya');
    tipebudget =document.getElementById('tipebudget');
    tahunbudget =document.getElementById('tahunbudget');
    jenisbiaya =document.getElementById('jenisbiaya');
    ket =document.getElementById('ketUmum');
    ktrngan=ket.value;
    kodebudgetV =kodebudget.value;
    jumlahbiayaV	=jumlahbiaya.value;
    tipebudgetV =tipebudget.value;
    tahunbudgetV =tahunbudget.value;
    jenisbiayaV =jenisbiaya.options[jenisbiaya.selectedIndex].value;
    kodevhc=document.getElementById('kodevhc').options[document.getElementById('kodevhc').selectedIndex].value;
    jamperthn=document.getElementById('jamperthn').value;

    if(tipebudgetV==''){
        alert('Budget type is empty.');
        return;
    }
    if(tahunbudgetV==''){
        alert('Budget year is empty.');
        return;
    }
    if(kodebudgetV==''){
        alert('Budget code is empty.');
        return;
    }
    if(jenisbiayaV==''){
        alert('Cost type is empty.');
        return;
    }
    if((jumlahbiayaV=='')||(parseFloat(jumlahbiayaV)==0)){
        alert('Amount is empty.');
        return;
    }
    
    param='cekapa=saveatas&tahunbudget='+tahunbudgetV+'&tipebudget='+tipebudgetV+'&jenisbiaya='+jenisbiayaV+'&kodebudget='+kodebudgetV+'&jumlahbiaya='+jumlahbiayaV+'&ktrngan='+ktrngan+'&kodevhc='+kodevhc+'&jamperthn='+jamperthn;
    param2='cekapa=cekclose&tahunbudget='+tahunbudgetV+'&tipebudget='+tipebudgetV+'&jenisbiaya='+jenisbiayaV+'&kodebudget='+kodebudgetV+'&jumlahbiaya='+jumlahbiayaV+'&ktrngan='+ktrngan+'&kodevhc='+kodevhc+'&jamperthn='+jamperthn;
    
    tujuan='budget_slave_by_umum.php';
//tambah baru    
    post_response_text(tujuan, param2, respon2);
    function respon2() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
//ada error                    
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
//tidak ada error, cek response                    
                    if(con.responseText==''){
                        post_response_text(tujuan, param, respon);
                    }else{
                        alert(con.responseText);
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
//ada error                    
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
//tidak ada error, cek response                    
                    if(con.responseText==''){
                        alert('Done');
//                        document.getElementById('kodebudget0').value='';
                        document.getElementById('jumlahbiaya').value='';
                        document.getElementById('jenisbiaya').value='';
                        updateTab();    
                    }else{
                        alert(con.responseText);
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    } 
}

function updateTabs2()
{
    document.getElementById('container2').innerHTML='';
    document.getElementById('tutup2').disabled=true;    
}    

function updateTahun()
{
    hidden0 =document.getElementById('hidden0');
    hidden0V =hidden0.value;
    hidden1 =document.getElementById('hidden1');
    hidden1V =hidden1.value;
    hidden2 =document.getElementById('hidden2');
    hidden2V =hidden2.value;
    param='cekapa=updatetahun';
    tujuan='budget_slave_by_umum.php';
    post_response_text(tujuan, param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
//ada error                    
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
//tidak ada error, cek response                    
                    if(con.responseText==''){
//                        alert('Done');
                    }else{
                            document.getElementById('pilihtahun0').innerHTML=con.responseText;
                            document.getElementById('pilihtahun0').value=hidden0V;                            
                            document.getElementById('pilihtahun1').innerHTML=con.responseText;
                            document.getElementById('pilihtahun1').value=hidden1V;                            
                            document.getElementById('pilihtahun2').innerHTML=con.responseText;
                            document.getElementById('pilihtahun2').value=hidden2V;                            
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    } 
}


function updateTab()
{
    tipebudget =document.getElementById('tipebudget');
    tipebudgetV	=tipebudget.value;
    tahunbudget =document.getElementById('tahunbudget');
    tahunbudgetV	=tahunbudget.value;
    jenisbiaya =document.getElementById('jenisbiaya');
    jenisbiayaV	=jenisbiaya.options[jenisbiaya.selectedIndex].value;
    pilihtahun0 =document.getElementById('pilihtahun0');
    pilihtahun0V	=pilihtahun0.options[pilihtahun0.selectedIndex].value;
    param='cekapa=tab&tipebudget='+tipebudgetV+'&tahunbudget='+tahunbudgetV+'&jenisbiaya='+jenisbiayaV+'&pilihtahun0='+pilihtahun0V;
    tujuan='budget_slave_by_umum.php'; 
    post_response_text(tujuan, param, respog);

    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
//                    alert(con.responseText);
                    document.getElementById('container0').innerHTML=con.responseText;
//                    if(apa=='all')updateTab1('all');
                    updateTabs();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
        }
        }
    }		
}

function updateTabs(tahunsebelah)
{
    tipebudget =document.getElementById('tipebudget');
    tipebudgetV	=tipebudget.value;
    tahunbudget =document.getElementById('tahunbudget');
    tahunbudgetV	=tahunbudget.value;
    jenisbiaya =document.getElementById('jenisbiaya');
    jenisbiayaV	=jenisbiaya.options[jenisbiaya.selectedIndex].value;
    pilihtahun1 =document.getElementById('pilihtahun1');
    pilihtahun1V	=pilihtahun1.options[pilihtahun1.selectedIndex].value;
    param='cekapa=tabs&tipebudget='+tipebudgetV+'&tahunbudget='+tahunbudgetV+'&jenisbiaya='+jenisbiayaV+'&pilihtahun1='+pilihtahun1V;
    tujuan='budget_slave_by_umum.php'; 
//alert(tujuan+' '+param);    
    post_response_text(tujuan, param, respog);

    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
//                    alert(con.responseText);
                    document.getElementById('container1').innerHTML=con.responseText;
                    updateTahun();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
        }
        }
    }		
}

function persiapantutup2()
{
    updateTab2();
    document.getElementById('tutup2').disabled=false;
    
}

function updateTab2(apa)
{
    pilihtahun2 =document.getElementById('pilihtahun2');
    pilihtahun2V	=pilihtahun2.options[pilihtahun2.selectedIndex].value;
    param='cekapa=tab2&pilihtahun2='+pilihtahun2V;
    tujuan='budget_slave_by_umum.php'; 
    post_response_text(tujuan, param, respog);
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('container2').innerHTML=con.responseText;
//                    if(apa=='all')updateTab5('all');
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }			
}

function tutup2(row)
{
    kunci =document.getElementById('kunci_'+row).value;
    param='cekapa=tutup&kunci='+kunci;
    tujuan='budget_slave_by_umum.php';
    if(confirm('Tutup?\nJika sudah Tutup, tidak dapat menambah/mengubah data.'))post_response_text(tujuan, param, respon);
    document.getElementById('baris_'+row).style.backgroundColor='orange';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
//ada error                    
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    document.getElementById('baris_'+row).style.backgroundColor='red';
                } else {
//tidak ada error, hilangkan baris                    
                    document.getElementById('baris_'+row).style.display='none';
                    try{
//coba, apakah baris terakhir
                        x=row+1;
                        if(document.getElementById('baris_'+x))
                        {
//kalo bukan, looping ke awal fungsi                            
                            row=x;
                            tutup2(row);
                        } else {
//baris terakhir, hapus header, berikan pesan DONE                            
                            alert('Done');
                            document.getElementById('baris_0').style.display='none';
                            updateTab0('all');
                        }
                    }
                    catch(e)
                    {
                        document.getElementById('baris_0').style.display='none';
                        updateTab0('all');
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }    
}

function deleteRow(kunci)
{
    {
        param='cekapa=delete&kunci='+kunci;
        tujuan='budget_slave_by_umum.php';
        if(confirm('Delete?'))post_response_text(tujuan, param, respog);		
    }

    function respog()
    {
        if(con.readyState==4)
        {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    alert('Done.');
                    updateTab();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }		
}

function sebaran(kunci,ev)
{
   param='cekapa=sebaran&kunci='+kunci;
   tujuan='budget_slave_by_umum.php'+"?"+param;  
   width='300';
   height='350';
  
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1('Sebaran '+kunci,content,width,height,ev); 
	
}

function ubahNilai(persen,total,source)
{
     comp='persenPrdksi';
 tot=0;
 for(x=1;x<13;x++)
     {
          if(document.getElementById(comp+x).value=='')
             document.getElementById(comp+x).value=0; 
         tot+=parseFloat(document.getElementById(comp+x).value);
         document.getElementById(source+x).value=0;
     }
 if(tot>0){     
  for(x=1;x<13;x++)
     {
         document.getElementById(source+x).value=0;
     }    
 }
 for(x=1;x<13;x++)
     {
         if(document.getElementById(comp+x).value!='' || document.getElementById(comp+x).value!=0)
            {
               z=parseFloat(document.getElementById(comp+x).value);
              if(tot>0)
               document.getElementById(source+x).value=((z/tot)*total).toFixed(2);
            }
     }  
}
function clearForm()
{ 
    if(confirm("truncate, are you sure?"))
    {
     for(sr=1;sr<13;sr++)
     {
         document.getElementById('brt_x'+sr).value='';
         document.getElementById('persenPrdksi'+sr).value='';
     }
    }
    else
        {
            return;
        }
}

function simpansebaran(kunci,total,ev)
{
	strUrl = '';
       
    for(i=1;i<=12;i++)
    {
     	try
		{
         	if(strUrl != '')
            {
            	strUrl += '&arrBrt['+i+']='+document.getElementById('brt_x'+i).value;
           	}
           	else
            {
                strUrl += '&arrBrt['+i+']='+document.getElementById('brt_x'+i).value;
            }
        }
        catch(e)
		{
		}
    }
 	param='cekapa=insertDistribusi&kunci='+kunci+'&totalSetahn='+total;
// alert(param);
 if(strUrl!='')
    {    
    	param+=strUrl;
    }

    tujuan='budget_slave_by_umum.php';
	post_response_text(tujuan, param, respog);
	function respog()
	{
		if(con.readyState==4)
		{
			if (con.status == 200)
			{
				busy_off();
				if (!isSaveResponse(con.responseText))
				{
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else 
				{
                                    alert('Done');
                                   
                                    parent.updateTabs();    
                                    parent.closeDialog();
				}
			}
			else
			{
				busy_off();
				error_catch(con.status);
			}
		}	
	 } 	
	
}
function angka_doangsamaminus(e)//only numeric e is event
{
    key=getKey(e);
//    if((key<48 || key>57) && (key!=8 && key != 45 && key != 150 && key!=46  && key!=127 && key!=true)) // 45 hypen
    if((key<48 || key>57) && (key!=8 && key != 150 && key!=46  && key!=127 && key!=true))
        return false;
    else
    {
        return true;
    }
}

function kalikanRp()
{
    kodevhc=document.getElementById('kodevhc').options[document.getElementById('kodevhc').selectedIndex].value;
    jamperthn=document.getElementById('jamperthn').value;
    param='cekapa=vhc&kodevhc='+kodevhc+'&jamperthn='+jamperthn;


    tujuan='budget_slave_by_umum.php';
	post_response_text(tujuan, param, respog);
	function respog()
	{
		if(con.readyState==4)
		{
			if (con.status == 200)
			{
				busy_off();
				if (!isSaveResponse(con.responseText))
				{
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else 
				{
                                  document.getElementById('jumlahbiaya').value=con.responseText;                                  
				}
			}
			else
			{
				busy_off();
				error_catch(con.status);
			}
		}	
	 }    
}