// JavaScript Document

function copybudget(bloktujuan,tahunbudget,bloksumber,kegiatan,tipebudget)
{
    param='bloktujuan='+bloktujuan+'&tahunbudget='+tahunbudget+'&bloksumber='+bloksumber+'&kegiatan='+kegiatan+'&tipebudget='+tipebudget+'&proses=copyblok';
    tujuan='bgt_budget_slave_kebun.php';
    document.getElementById('alternatelock').style.display='';  
    post_response_text(tujuan, param, respog);
    function respog()
    {
        if(con.readyState==4)
        {
           document.getElementById('alternatelock').style.display='none';            
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    alert('Done.');
                }
            }
            else {
            busy_off();
            error_catch(con.status);
            }
        }	
    }  
}

function getKodeblok(thn,kdblk,kdkeg)
{
        if((thn=='0')||(kdblk=='0'))
        {
            thn=document.getElementById('thnBudget').value;
            if(thn=='')
                {
                    alert("Tahun Budget Tidak Boleh Kosong");
                    return;
                }
            param='thnBudget='+thn+'&proses=getBlok';
        }
        else
        {
            thnBudget=thn;
            kdBlok=kdblk;
            param='thnBudget='+thnBudget+'&proses=getBlok'+'&kdBlok='+kdBlok;
        }
	tujuan='bgt_budget_slave_kebun.php';
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
                                 //	alert(con.responseText);
                                  document.getElementById('kdBlok').innerHTML='';
                                  document.getElementById('kdBlok').innerHTML=con.responseText;
                                  if(kdkeg!=0)
                                  {
                                      getKegiatan(kdBlok,kdkeg);
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
function getKegiatan(kdblk,kdkeg)
{
    if(kdblk==0||kdkeg==0)
    {
        kdBlok=document.getElementById('kdBlok').options[document.getElementById('kdBlok').selectedIndex].value;
        param='kdBlok='+kdBlok+'&proses=getKegiatan';
    }
    else
    {
        kdBlok=kdblk;
        kegId=kdkeg;
        param='kegId='+kegId+'&proses=getKegiatan'+'&kdBlok='+kdBlok;
    }
   
    tujuan='bgt_budget_slave_kebun.php';
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
                             //	alert(con.responseText);
                             // ar=con.responseText.split("###");
                              document.getElementById('kegId').innerHTML=con.responseText;
                              
                              if(kdkeg!=0)
                              {
                                  saveData();
                              }
                              else
                                  {
                                    document.getElementById('satKeg').value='';
                                    document.getElementById('noAkun').value='';
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
function getSatuan()
{
   kegId=document.getElementById('kegId').options[document.getElementById('kegId').selectedIndex].value;
   kdBlok=document.getElementById('kdBlok').options[document.getElementById('kdBlok').selectedIndex].value;
   param='kegId='+kegId+'&proses=getSatuan'+'&kdBlok='+kdBlok;
   tujuan='bgt_budget_slave_kebun.php';
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
                                 //	alert(con.responseText);
                                 ar=con.responseText.split("###");
                                  document.getElementById('satKeg').value=ar[0];
                                  document.getElementById('noAkun').value=ar[1];

                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
	 }  
    
}
function saveData()
{
        thnBdget=document.getElementById('thnBudget').value;
        tpBudget=document.getElementById('tipeBudget').value;
        noAkun=document.getElementById('noAkun').value;
        volKeg=document.getElementById('volKeg').value;
        satuan=document.getElementById('satKeg').value;
        rotThn=document.getElementById('rotThn').value;
        kegId=document.getElementById('kegId').options[document.getElementById('kegId').selectedIndex].value;
        kdBlok=document.getElementById('kdBlok').options[document.getElementById('kdBlok').selectedIndex].value;
	param='thnBudget='+thnBdget+'&tpBudget='+tpBudget+'&proses=cekSave'+'&noAkun='+noAkun+'&kegId='+kegId;
        param+='&kdBlok='+kdBlok+'&rotThn='+rotThn+'&volKeg='+volKeg+'&satuan='+satuan;
	tujuan='bgt_budget_slave_kebun.php';
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
						 //	alert(con.responseText);
                                                        ar=con.responseText.split("###");
                                                        document.getElementById('hkEfektif').value=ar[0];
                                                        document.getElementById('kdVhc').innerHTML=ar[1];
                                                        document.getElementById('kdBudget').innerHTML=ar[2];
                                                        document.getElementById('thnBudget').disabled=true;
                                                        document.getElementById('tipeBudget').disabled=true;
                                                        document.getElementById('noAkun').disabled=true;
                                                        document.getElementById('volKeg').disabled=true;
                                                        document.getElementById('satKeg').disabled=true;
                                                        document.getElementById('rotThn').disabled=true;
							document.getElementById('saveData').disabled=true;
                                                        document.getElementById('kdBlok').disabled=true;
                                                        document.getElementById('kegId').disabled=true;
                                                        document.getElementById('formIsian').style.display='block';
                                                        document.getElementById('listDatHeader').style.display='none';
                                                        document.getElementById('volKontrak').value=volKeg;
                                                        document.getElementById('satKontrak').value=satuan;
                                                         loadDataSdm(1);
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  
		
}
function filFieldHead(tahunbudget,kodeorg, tipebudget,noakun, kegiatan, volume, satuanv,rotasi)
{
    vol=volume/rotasi;
    vol=vol.toPrecision(4);
    document.getElementById('thnBudget').disabled=true;
    document.getElementById('tipeBudget').disabled=true;
    document.getElementById('noAkun').disabled=true;
    document.getElementById('volKeg').disabled=true;
    document.getElementById('satKeg').disabled=true;
    document.getElementById('rotThn').disabled=true;
    document.getElementById('saveData').disabled=true;
    document.getElementById('kdBlok').disabled=true;
    document.getElementById('kegId').disabled=true;
    document.getElementById('thnBudget').value=tahunbudget;
    document.getElementById('volKeg').value=vol;
    document.getElementById('satKeg').value=satuanv;
    document.getElementById('rotThn').value=rotasi;
    document.getElementById('noAkun').value=noakun;
    getKodeblok(tahunbudget,kodeorg,kegiatan);
    
}

function delFieldHead(tahunbudget,kodeorg,kegiatan)
{
        param='proses=delKodeblok&thnBudget='+tahunbudget+'&kdBlok='+kodeorg+'&kegId='+kegiatan;
	tujuan='bgt_budget_slave_kebun.php';
        if(confirm("Anda yaking ingin menghapus"))
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
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                 //	alert(con.responseText);
                                  dataHeader();
//                                  document.getElementById('kdBlok').innerHTML=con.responseText;
//                                  if(kdkeg!=0)
//                                  {
//                                      getKegiatan(kdBlok,kdkeg);
//                                  }

                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
	 }  
    
}

function jumlahkan(x)
{
        thnBdget=document.getElementById('thnBudget').value;
        kdBlok=document.getElementById('kdBlok').options[document.getElementById('kdBlok').selectedIndex].value;
        param='thnBudget='+thnBdget+'&kdBlok='+kdBlok;
	   if(x==1)
            {
                personel=document.getElementById('jmlh_'+x).value;
                hkEfektip=document.getElementById('hkEfektif').value;
                if(personel=='')
                    {
                        document.getElementById('jmlh_'+x).value='0';
                    }
                kdGol=document.getElementById('kdBudget').options[document.getElementById('kdBudget').selectedIndex].value;
                param+='&proses=getUpah'+'&jmlhPerson='+personel+'&kdGol='+kdGol+'&hkEfektif='+hkEfektip;
            }
            if(x==2)
                {
                    kdBudget=document.getElementById('kdBudgetM').options[document.getElementById('kdBudgetM').selectedIndex].value;
                    kdBrg=document.getElementById('kdBarang').value;
                    jmlhBrg=document.getElementById('jmlh_'+x).value;
                    param+='&kdBudget='+kdBudget+'&kdBrg='+kdBrg+'&jmlhBrg='+jmlhBrg+'&proses=getHarga';
                }
        if(x==3)
        {
            kdBrgL=document.getElementById('kdBarangL').value;
            jmlhBrgL=document.getElementById('jmlh_'+x).value;
            param+='&kdBrgL='+kdBrgL+'&jmlhBrgL='+jmlhBrgL+'&proses=getHargaL';
        }
        //alert(param);
	tujuan='bgt_budget_slave_kebun.php';
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
                                        if(x==1)
                                        {
                                        document.getElementById('totBiaya').value=con.responseText;
                                        }
                                        if(x==2)
                                        {
                                           document.getElementById('totHarga').value=con.responseText;
                                        }
                                        if(x==3)
                                        {
                                           document.getElementById('totHargaL').value=con.responseText;
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
function saveBudget(x)
{
        thnBdget=document.getElementById('thnBudget').value;
        tpBudget=document.getElementById('tipeBudget').value;
        noAkun=document.getElementById('noAkun').value;
        volKeg=document.getElementById('volKeg').value;
        satuan=document.getElementById('satKeg').value;
        rotThn=document.getElementById('rotThn').value;
        kegId=document.getElementById('kegId').options[document.getElementById('kegId').selectedIndex].value;
        kdBlok=document.getElementById('kdBlok').options[document.getElementById('kdBlok').selectedIndex].value;
	param='thnBudget='+thnBdget+'&tpBudget='+tpBudget+'&noAkun='+noAkun+'&kegId='+kegId;
        param+='&kdBlok='+kdBlok+'&rotThn='+rotThn+'&volKeg='+volKeg+'&satuan='+satuan;
	   if(x==1)
           {
                personel=document.getElementById('jmlh_'+x).value;
                hkEfektip=document.getElementById('hkEfektif').value;
                kdGol=document.getElementById('kdBudget').options[document.getElementById('kdBudget').selectedIndex].value;
                totBiaya=document.getElementById('totBiaya').value;
                param+='&proses=saveSdm'+'&jmlhPerson='+personel+'&kdGol='+kdGol+'&hkEfektif='+hkEfektip+'&totBiaya='+totBiaya;
           }
           if(x==2)
               {
                    kdBudget=document.getElementById('kdBudgetM').options[document.getElementById('kdBudgetM').selectedIndex].value;
                    kdBrg=document.getElementById('kdBarang').value;
                    jmlhBrg=document.getElementById('jmlh_'+x).value;
                    totHarga=document.getElementById('totHarga').value;
                    satuanBrg=document.getElementById('satuan').innerHTML;
                    param+='&kdBudget='+kdBudget+'&kdBrg='+kdBrg+'&jmlhBrg='+jmlhBrg+'&totHarga='+totHarga+'&proses=saveMat'+'&satuanBrg='+satuanBrg;
               }
               if(x==3)
               {
                    kdBudgetL=document.getElementById('kdBudgetL').options[document.getElementById('kdBudgetL').selectedIndex].value;
                    kdBrgL=document.getElementById('kdBarangL').value;
                    jmlhBrgL=document.getElementById('jmlh_'+x).value;
                    totHargaL=document.getElementById('totHargaL').value;
                    satuanBrgL=document.getElementById('satuanL').innerHTML;
                    param+='&kdBudgetL='+kdBudgetL+'&kdBrgL='+kdBrgL+'&jmlhBrgL='+jmlhBrgL+'&totHargaL='+totHargaL+'&proses=saveTool'+'&satuanBrgL='+satuanBrgL;
               }
               if(x==4)
                   {
                        kdBudgetK=document.getElementById('kdBudgetK').options[document.getElementById('kdBudgetK').selectedIndex].value;
                        volKontrak=document.getElementById('volKontrak').value;
                        satKontrak=document.getElementById('satKontrak').value;
                        totBiayaK=document.getElementById('totBiayaK').value;
                        param+='&kdBudgetK='+kdBudgetK+'&volKontrak='+volKontrak+'&satKontrak='+satKontrak+'&proses=saveKontrak'+'&totBiayaK='+totBiayaK;
                   }
                   if(x==5)
                   {
                        kdVhc=document.getElementById('kdVhc').options[document.getElementById('kdVhc').selectedIndex].value;
                        kdBudgetV=document.getElementById('kdBudgetV').options[document.getElementById('kdBudgetV').selectedIndex].value;
                        jmlhJam=document.getElementById('jmlhJam').value;
                        satVhc=document.getElementById('satVhc').value;
                        totBiayaKend=document.getElementById('totBiayaKend').value;
                        param+='&kdVhc='+kdVhc+'&kdBudgetV='+kdBudgetV+'&jmlhJam='+jmlhJam+'&proses=saveKendaran'+'&satVhc='+satVhc+'&totBiayaKend='+totBiayaKend;  
                   }
        //alert(param);
	tujuan='bgt_budget_slave_kebun.php';
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
                                          //	alert(con.responseText);
                                               if(x==1)
                                               {
                                                   clearSdm();
                                                   loadDataSdm();
                                               }
                                               if(x==2)
                                               {
                                                   clearMat();
                                                   loadDtMaterail();
                                               }
                                               if(x==4)
                                               {
                                                    clearKontrak();
                                                    loadDtLain();
                                               }
                                               if(x==3)
                                               {
                                                   clearMatL();
                                                   loadDataTool();
                                               }
                                               if(x==5)
                                               {
                                                   clearKendaraan();
                                                   loadDataKend();
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
function clearSdm()
{
    document.getElementById('jmlh_1').value='';
    document.getElementById('kdBudget').value='';
    document.getElementById('totBiaya').value='0';
}
function getKlmpkbrg()
{
    klmpkBrg=document.getElementById('kdBudgetM').options[document.getElementById('kdBudgetM').selectedIndex].value;
    param='klmpkBrg='+klmpkBrg+'&proses=setKdBrg';
    tujuan='bgt_budget_slave_kebun.php';
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
                                          //	alert(con.responseText);
                                            document.getElementById('kdBarang').value=con.responseText;
                                            document.getElementById('jmlh_2').value='';
                                            document.getElementById('namaBrg').innerHTML='';
                                            // document.getElementById('kdBarang').value='';
                                            // document.getElementById('kdBudgetM').value='';
                                            document.getElementById('totHarga').value='0';
                                            document.getElementById('satuan').innerHTML='';
                                                searchBrg('Cari Nama Barang','<fieldset><legend>Cari Nama Barang</legend>Cari <input type=text class=myinputtext id=nmBrg><button class=mybutton onclick=findBrg()>Cari</button></fieldset><div id=containerBarang style=overflow=auto;height=380;width=485></div>','event');
                                              }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }	
	 }  
}
function clearMat()
{
    document.getElementById('jmlh_2').value='';
    document.getElementById('namaBrg').innerHTML='';
    document.getElementById('kdBarang').value='';
    //document.getElementById('kdBudgetM').value='';
    document.getElementById('totHarga').value='0';
    document.getElementById('satuan').innerHTML='';
}
function clearMatL()
{
    document.getElementById('jmlh_3').value='';
    document.getElementById('namaBrgL').innerHTML='';
    document.getElementById('kdBudgetL').value='';
    document.getElementById('kdBarangL').value='';
    document.getElementById('totHargaL').value='0';
    document.getElementById('satuanL').innerHTML='';
}
function clearKontrak()
{
    document.getElementById('kdBudgetK').value='';
    //document.getElementById('volKontrak').value='';
    //document.getElementById('satKontrak').value='';
    document.getElementById('totBiayaK').value='0';
}
function clearKendaraan()
{
    document.getElementById('kdVhc').value='';
    document.getElementById('jmlhJam').value='';
    document.getElementById('totBiayaKend').value='0';
}

function newData()
{
    clearKontrak();
    clearMatL();
    clearMat();
    clearSdm();
    clearKendaraan();
    document.getElementById('formIsian').style.display='none';
    document.getElementById('hkEfektif').value='';
   // document.getElementById('thnBudget').value='';
    //document.getElementById('satKeg').value='';
    document.getElementById('volKeg').value='';
    //document.getElementById('noAkun').value='';
   // document.getElementById('kdBlok').innerHTML="<option value=''>"+pilh+"</option>";
   // document.getElementById('kegId').innerHTML="<option value=''>"+pilh+"</option>";
    document.getElementById('thnBudget').disabled=false;
    document.getElementById('kdBlok').disabled=false;
    document.getElementById('kegId').disabled=false;
    document.getElementById('volKeg').disabled=false;
    document.getElementById('satKeg').disabled=false;
    document.getElementById('rotThn').disabled=false;
    document.getElementById('saveData').disabled=false;
    document.getElementById('listDatHeader').style.display='block';
    document.getElementById('thnbudgetHeader').value='';
    document.getElementById('rotThn').value='1';
    dataHeader();
    
}
function deleteSdm(id,hal)
{
        param='idData='+id+'&proses=delData';
        tujuan='budget_slave_vhc.php';
        if(confirm("Anda yaking ingin menghapus"))
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
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                 //	alert(con.responseText);
                                        if(hal==1)
                                        {
                                           clearSdm();
                                           loadDataSdm();
                                        }
                                        if(hal==2)
                                        {
                                           clearMat();
                                           loadDtMaterail();
                                        }
                                        if(hal==4)
                                        {
                                            clearKontrak();
                                            loadDtLain();
                                        }
                                        if(hal==3)
                                        {
                                           clearMatL();
                                           loadDataTool();
                                        }
                                        if(hal==5)
                                        {
                                           clearKendaraan();
                                           loadDataKend();
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
function ambil_biaya()
{
    kd=document.getElementById('kdVhc').options[document.getElementById('kdVhc').selectedIndex].value;
    jmlhJam=document.getElementById('jmlhJam').value;
    thnBdget=document.getElementById('thnBudget').value;
    param='kdVhc='+kd+'&thnBudget='+thnBdget+'&proses=getBiaya'+'&jmlhJam='+jmlhJam;
    //alert(param);
    tujuan='bgt_budget_slave_kebun.php';
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
                                          // alert(con.responseText);
                                           document.getElementById('totBiayaKend').value=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }	
	 } 
}
function searchBrg(title,content,ev)
{
        klmpk=document.getElementById('kdBudgetM').options[document.getElementById('kdBudgetM').selectedIndex].value;
        if(klmpk=='')
            {
                alert("Kode Budget Tidak Boleh Kosong!!");
                return;
            }
            idKlmpk="<input type='hidden' id='idKlmpk' value='"+klmpk+"' />"
            content=content+idKlmpk;
	width='500';
	height='400';
	showDialog1(title,content,width,height,ev);
        //findBrg();
	//alert('asdasd');
}
function findBrg()
{
    klmpkBrg=document.getElementById('idKlmpk').value;
    nmBrg=document.getElementById('nmBrg').value;
    kdBarang=document.getElementById('kdBarang').value;
    param='klmpkBrg='+klmpkBrg+'&nmBrg='+nmBrg+'&proses=getBarang';
    tujuan='budget_slave_vhc.php';
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
                                          //	alert(con.responseText);
                                           document.getElementById('containerBarang').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }	
	 }  
		
}
function setData(kdbrg,namaBarang,sat)
{
    document.getElementById('kdBarang').value=kdbrg;
    document.getElementById('namaBrg').innerHTML=namaBarang;
    document.getElementById('satuan').innerHTML=sat;
    closeDialog();
}
function searchBrgPros(title,content,baris,ev)
{
	klmpk=document.getElementById('kdBudgetAll').options[document.getElementById('kdBudgetAll').selectedIndex].value;
        if(klmpk=='')
            {
                alert("Kode Budget Tidak Boleh Kosong!!");
                return;
            }
            idKlmpk="<input type='hidden' id='idKlmpk' value='"+klmpk+"' /><input type='hidden' id='barisData' value='"+baris+"' />";
            content=content+idKlmpk;
        width='500';
	height='400';
	showDialog1(title,content,width,height,ev);
}
function findBrgPros()
{
    klmpkBrg=document.getElementById('idKlmpk').value;
    brs=document.getElementById('barisData').value;
    nmBrg=document.getElementById('nmBrg').value;
    thnBdget=document.getElementById('thnBudgetPros').value;
    param='klmpkBrg='+klmpkBrg+'&nmBrg='+nmBrg+'&proses=getBarang';
    param+='&baris='+brs+'&thnBudget='+thnBdget;
    tujuan='bgt_budget_slave_kebun_pros.php';
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
                                          //	alert(con.responseText);
                                           document.getElementById('containerBarang').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }	
	 }  
		
}
function setDataPros(kdbrg,namaBarang,sat,brs)
{
    document.getElementById('kdBrg_'+brs).value=kdbrg;
    document.getElementById('nmBrg_'+brs).value=namaBarang;
    document.getElementById('satuanj_'+brs).value=sat;
    closeDialog();
}
function searchBrgL(title,content,ev)
{
	width='500';
	height='400';
	showDialog1(title,content,width,height,ev);
	//alert('asdasd');
}
function findBrgL()
{
    nmBrgL=document.getElementById('nmBrgL').value;
    param='nmBrgL='+nmBrgL+'&proses=getBarangL';
    tujuan='bgt_budget_slave_kebun.php';
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
                                          //	alert(con.responseText);
                                           document.getElementById('containerBarangL').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }	
	 }  
		
}

function setDataL(kdbrg,namaBarang,sat)
{
    document.getElementById('kdBarangL').value=kdbrg;
    document.getElementById('namaBrgL').innerHTML=namaBarang;
    document.getElementById('satuanL').innerHTML=sat;
    closeDialog();
}
function loadDataSdm(b)
{
    thnBdget=document.getElementById('thnBudget').value;
    tpBudget=document.getElementById('tipeBudget').value;
    noAkun=document.getElementById('noAkun').value;
    volKeg=document.getElementById('volKeg').value;
    satuan=document.getElementById('satKeg').value;
    rotThn=document.getElementById('rotThn').value;
    kegId=document.getElementById('kegId').options[document.getElementById('kegId').selectedIndex].value;
    kdBlok=document.getElementById('kdBlok').options[document.getElementById('kdBlok').selectedIndex].value;
    param='thnBudget='+thnBdget+'&tpBudget='+tpBudget+'&noAkun='+noAkun+'&kegId='+kegId;
    param+='&kdBlok='+kdBlok+'&rotThn='+rotThn+'&volKeg='+volKeg+'&satuan='+satuan;
    param+='&proses=loadDataSdm';
    tujuan='bgt_budget_slave_kebun.php';
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
                                          //	alert(con.responseText);
                                              document.getElementById('containDataSDM').innerHTML=con.responseText;
                                                if(b==1)
                                                {
                                                  loadDtMaterail(b);
                                                }
                                                else
                                                  {loadDetailTotal();}
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }	
	 }  
}

function loadDtMaterail(c)
{
    thnBdget=document.getElementById('thnBudget').value;
    tpBudget=document.getElementById('tipeBudget').value;
    noAkun=document.getElementById('noAkun').value;
    volKeg=document.getElementById('volKeg').value;
    satuan=document.getElementById('satKeg').value;
    rotThn=document.getElementById('rotThn').value;
    kegId=document.getElementById('kegId').options[document.getElementById('kegId').selectedIndex].value;
    kdBlok=document.getElementById('kdBlok').options[document.getElementById('kdBlok').selectedIndex].value;
    param='thnBudget='+thnBdget+'&tpBudget='+tpBudget+'&noAkun='+noAkun+'&kegId='+kegId;
    param+='&kdBlok='+kdBlok+'&rotThn='+rotThn+'&volKeg='+volKeg+'&satuan='+satuan;
    param+='&proses=loadDataMat';
    tujuan='bgt_budget_slave_kebun.php';
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
                                          //	alert(con.responseText);
                                              
                                              document.getElementById('containDataBrg').innerHTML=con.responseText;
                                              if(c==1)
                                              {loadDataTool(c);}
                                              else
                                                  {loadDetailTotal();}
                                               
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }	
	 }  
}
function loadDataTool(d)
{
      thnBdget=document.getElementById('thnBudget').value;
    tpBudget=document.getElementById('tipeBudget').value;
    noAkun=document.getElementById('noAkun').value;
    volKeg=document.getElementById('volKeg').value;
    satuan=document.getElementById('satKeg').value;
    rotThn=document.getElementById('rotThn').value;
    kegId=document.getElementById('kegId').options[document.getElementById('kegId').selectedIndex].value;
    kdBlok=document.getElementById('kdBlok').options[document.getElementById('kdBlok').selectedIndex].value;
    param='thnBudget='+thnBdget+'&tpBudget='+tpBudget+'&noAkun='+noAkun+'&kegId='+kegId;
    param+='&kdBlok='+kdBlok+'&rotThn='+rotThn+'&volKeg='+volKeg+'&satuan='+satuan;
    param+='&proses=loadDataTool';
    tujuan='bgt_budget_slave_kebun.php';
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
                                          //	alert(con.responseText);
                                              
                                              document.getElementById('containDataTool').innerHTML=con.responseText;
                                              if(d==1)
                                                  {loadDtLain(d);}
                                                  else
                                                  {loadDetailTotal();}
                                               
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }	
	 }  
}
function loadDtLain(e)
{
    thnBdget=document.getElementById('thnBudget').value;
    tpBudget=document.getElementById('tipeBudget').value;
    noAkun=document.getElementById('noAkun').value;
    volKeg=document.getElementById('volKeg').value;
    satuan=document.getElementById('satKeg').value;
    rotThn=document.getElementById('rotThn').value;
    kegId=document.getElementById('kegId').options[document.getElementById('kegId').selectedIndex].value;
    kdBlok=document.getElementById('kdBlok').options[document.getElementById('kdBlok').selectedIndex].value;
    param='thnBudget='+thnBdget+'&tpBudget='+tpBudget+'&noAkun='+noAkun+'&kegId='+kegId;
    param+='&kdBlok='+kdBlok+'&rotThn='+rotThn+'&volKeg='+volKeg+'&satuan='+satuan;
    param+='&proses=loadDtLain';
    tujuan='bgt_budget_slave_kebun.php';
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
                                          //	alert(con.responseText);
                                              
                                              document.getElementById('containDataLain').innerHTML=con.responseText;
                                              if(e==1)
                                                  {
                                                      loadDataKend(e);
                                                  }
                                                  else
                                                  {loadDetailTotal();}
                                               
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }	
	 }  
}
function loadDataKend(f)
{
    thnBdget=document.getElementById('thnBudget').value;
    tpBudget=document.getElementById('tipeBudget').value;
    noAkun=document.getElementById('noAkun').value;
    volKeg=document.getElementById('volKeg').value;
    satuan=document.getElementById('satKeg').value;
    rotThn=document.getElementById('rotThn').value;
    kegId=document.getElementById('kegId').options[document.getElementById('kegId').selectedIndex].value;
    kdBlok=document.getElementById('kdBlok').options[document.getElementById('kdBlok').selectedIndex].value;
    param='thnBudget='+thnBdget+'&tpBudget='+tpBudget+'&noAkun='+noAkun+'&kegId='+kegId;
    param+='&kdBlok='+kdBlok+'&rotThn='+rotThn+'&volKeg='+volKeg+'&satuan='+satuan;
    param+='&proses=loadDataKend';
    tujuan='bgt_budget_slave_kebun.php';
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
                                          //	alert(con.responseText);
                                              
                                              document.getElementById('containDataKend').innerHTML=con.responseText; 
                                              if(f==1)
                                              {loadDetailTotal();}
                                              else
                                                  {loadDetailTotal();}
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }	
	 }  
}
function loadDetailTotal()
{
        page=document.getElementById('pageSebaran').options[document.getElementById('pageSebaran').selectedIndex].value;
        noakunCari=document.getElementById('kdNoakunData').options[document.getElementById('kdNoakunData').selectedIndex].value;
        kdBlok=document.getElementById('kdblokSebaran').options[document.getElementById('kdblokSebaran').selectedIndex].value;
        afDeling=document.getElementById('AfdSebaran').options[document.getElementById('AfdSebaran').selectedIndex].value;
        thnbudgetHeader=document.getElementById('thnBudget').value;
        param='proses=loadDetailTotal'+'&noakunCari='+noakunCari+'&thnbudgetHeader='+thnbudgetHeader+'&kdBlok='+kdBlok;
        param+='&afd='+afDeling;
        if(page!='')
            {
                param+='&page='+page;
            }
        
        tujuan='bgt_budget_slave_kebun.php';
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
                                          //	alert(con.responseText);
                                              dapetData=con.responseText.split("###");
                                              document.getElementById('containDataTotal').innerHTML=dapetData[0]; 
                                              document.getElementById('pageSebaran').innerHTML=dapetData[1]; 
                                              document.getElementById('totalPageSebaran').innerHTML=dapetData[2];
                                              document.getElementById('awalPageSebaran').innerHTML=dapetData[3];
                                              document.getElementById('kdblokSebaran').innerHTML=dapetData[4];
                                              getThnBudget();
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }	
	 }  
}
function ubah_list2()
{
        thnBudget=document.getElementById('thnbudgetHeader2').options[document.getElementById('thnbudgetHeader2').selectedIndex].value;
        kdBlok=document.getElementById('kdBlokCari2').options[document.getElementById('kdBlokCari2').selectedIndex].value;
        noakunCari=document.getElementById('noakunCari2').options[document.getElementById('noakunCari2').selectedIndex].value
        param='proses=loadDetailTotal'+'&thnbudgetHeader='+thnBudget+'&kdBlok='+kdBlok+'&noakunCari='+noakunCari;
        // alert(param);
        tujuan='bgt_budget_slave_kebun.php';
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
							document.getElementById('containDataTotal').innerHTML=con.responseText; 
                                             
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 } 
}
function getForm(title,content,rupiah,jumlah,kodebudget,ev)
{
	width='400px';
	height='400px';
	showDialog1(title,content,width,height,ev);
        getFormSebarang(rupiah,jumlah,kodebudget);
	//alert('asdasd');
}
function closForm()
{
    loadDetailTotal();
    closeDialog();
}
function getFormSebarang(rupiah,jumlah,kodebudget)
{
        
        keyId=document.getElementById('keyId').value;
        param='proses=getForm'+'&keyId='+keyId+'&rupiah='+rupiah+'&jumlah='+jumlah+'&kodebudget='+kodebudget;
        tujuan='bgt_budget_slave_kebun.php';
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
                                          //	alert(con.responseText);
                                              
                                              document.getElementById('containerForm').innerHTML=con.responseText; 
                                              
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }	
	 }  
}
function saveRupiah(totRow,max)
{
        strUrl = '';
       keyId=document.getElementById('keyId').value;
        for(i=1;i<=totRow;i++)
        {
            try{
                if(strUrl != '')
                {
                        strUrl += '&arrRup['+i+']='+document.getElementById('rupiah_'+i).value;
                }
                else
                {
                     strUrl += '&arrRup['+i+']='+document.getElementById('rupiah_'+i).value;
                }
            }
            catch(e){}
        }
	param='keyId='+keyId+'&proses=saveRupiah'+'&totRow='+totRow;
        param+=strUrl;
         totrp=0;
         for(x=1;x<13;x++)
          {
             totrp+=parseFloat(document.getElementById('rupiah_'+x).value);
          }
          if(totrp>max)
              {
                alert('Total melebihi jumlah maximal('+max+')');  
              }
          else
              {
                tujuan='bgt_budget_slave_kebun.php';
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
function saveFisik(totRow,max)
{
        strUrl = '';
       keyId=document.getElementById('keyId').value;
        for(i=1;i<=totRow;i++)
        {
            try{
                if(strUrl != '')
                {
                        strUrl += '&arrFisik['+i+']='+document.getElementById('fisik_'+i).value;
                }
                else
                {
                     strUrl += '&arrFisik['+i+']='+document.getElementById('fisik_'+i).value;
                }
            }
            catch(e){}
        }
	param='keyId='+keyId+'&proses=saveFisik'+'&totRow='+totRow;
        param+=strUrl;
     
         totfis=0;
         for(x=1;x<13;x++)
          {
             totfis+=parseFloat(document.getElementById('fisik_'+x).value);
          }
          if(totfis>max)
              {
                alert('Total melebihi jumlah maximal('+max+')');  
              }
          else
              {
                tujuan='bgt_budget_slave_kebun.php';
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
							loadDetailTotal();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 } 	
	
}

function viewOtherBlok(tahunbudget,kodeblok,tipebudget,noakun,kegiatan,volume,satuanvolume,rotasi,ev)
{
   param='tahunbudget='+tahunbudget+'&kodeblok='+kodeblok+'&tipebudget='+tipebudget+'&noakun='+noakun+'&kegiatan='+kegiatan+'&volume='+volume+'&satuanvolume='+satuanvolume+'&rotasi='+rotasi+'&proses=otherblok';
//   tujuan='pabrik_slave_2pengolahandetail.php'+"?"+param;  
   tujuan='bgt_budget_slave_kebun.php'+"?"+param;  
   width='700';
   height='400';
  
   content="<iframe id=alternateframe name=alternateframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1('Copy To Blok '+kegiatan,content,width,height,ev); 
	
}

function dataHeader()
{
        param='proses=getDetailData';
        tujuan='bgt_budget_slave_kebun.php';
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
							document.getElementById('listDatHeader2').innerHTML=con.responseText;
                                                        getThnBudget();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 } 
}
function ubah_list()
{
        thnBudget=document.getElementById('thnbudgetHeader').options[document.getElementById('thnbudgetHeader').selectedIndex].value;
        kdBlok=document.getElementById('kdBlokCari').options[document.getElementById('kdBlokCari').selectedIndex].value;
        noakunCari=document.getElementById('noakunCari').options[document.getElementById('noakunCari').selectedIndex].value
        param='proses=getDetailData'+'&thnbudgetHeader='+thnBudget+'&kdBlok='+kdBlok+'&noakunCari='+noakunCari;
       // alert(param);
        tujuan='bgt_budget_slave_kebun.php';
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
							document.getElementById('listDatHeader2').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 } 
}
function cariTrans(num)
{
                thnBudget=document.getElementById('thnbudgetHeader').options[document.getElementById('thnbudgetHeader').selectedIndex].value;
                kdBlok=document.getElementById('kdBlokCari').options[document.getElementById('kdBlokCari').selectedIndex].value;
                //document.getElementById('thnbudgetHeader').options[document.getElementById('thnbudgetHeader').selectedIndex].value
                param='proses=getDetailData'+'&thnbudgetHeader='+thnBudget+'&kdBlok='+kdBlok;
		param+='&page='+num;
                tujuan='bgt_budget_slave_kebun.php';
		
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('listDatHeader2').innerHTML=con.responseText;
                                               
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
		param='proses=getDetailData';
		param+='&page='+num;
                tujuan='bgt_budget_slave_kebun.php';
		
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('listDatHeader2').innerHTML=con.responseText;
                                                getThnBudget();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}
function closeBudget()
{
    thnBudgetTtp=document.getElementById('thnBudgetTutup').options[document.getElementById('thnBudgetTutup').selectedIndex].value;
    param='proses=closeBudget'+'&thnBudget='+thnBudgetTtp;
    tujuan='bgt_budget_slave_kebun.php';
    if(confirm("Anda Yakin Menutup Budget  Tahun "+thnBudgetTtp+"?? Setelah di tutup tidak dapat di ubah kembali"))
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
//                                                $test=document.getElementById('tabFRM0');
//                                                tabAction($test,0,'FRM',2);
                                                    newData();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  
}
function getThnBudget()
{
    param='proses=getThnBudget';
    tujuan='bgt_budget_slave_kebun.php';
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
                                                document.getElementById('thnBudgetTutup').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  
}

function ubahNilai(persen,total,source,total2)
{
 if(source=='rupiah_')
 {
     comp='rppersen';
     comp2='fispersen';
     source2='fisik_'
 for(x=1;x<13;x++)
     {
         document.getElementById(comp2+x).value=document.getElementById(comp+x).value; 
     }
 }    
 else
 {
     comp='fispersen';
 }
 tot=0;
 tot2=0;
 for(x=1;x<13;x++)
     {
          if(document.getElementById(comp+x).value=='')
             document.getElementById(comp+x).value=0; 
         tot+=parseFloat(document.getElementById(comp+x).value);
         document.getElementById(source+x).value=0;
 if(source=='rupiah_')
 {
          if(document.getElementById(comp2+x).value=='')
             document.getElementById(comp2+x).value=0; 
         tot2+=parseFloat(document.getElementById(comp2+x).value);
         document.getElementById(source+x).value=0;
 }    
     }
 if(tot>0){     
  for(x=1;x<13;x++)
     {
         document.getElementById(source+x).value=0;
     }    
 }
 if(tot2>0){     
  for(x=1;x<13;x++)
     {
         document.getElementById(source2+x).value=0;
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
 if(source=='rupiah_')
 {
         if(document.getElementById(comp2+x).value!='' || document.getElementById(comp2+x).value!=0)
            {
               z=parseFloat(document.getElementById(comp2+x).value);
              if(tot2>0)
               document.getElementById(source2+x).value=((z/tot2)*total2).toFixed(2);
            }
     } 
     }
}
function clearRupiah()
{ 
    if(confirm("Anda yakin ingin mengkosongkan form??"))
    {
     for(sr=1;sr<13;sr++)
     {
         document.getElementById('rupiah_'+sr).value='';
         document.getElementById('rppersen'+sr).value='';
     }
    }
    else
        {
            return;
        }
}
function clearFisik()
{
    if(confirm("Anda yakin ingin mengkosongkan form??"))
    {
        
     for(sr=1;sr<13;sr++)
     {
         document.getElementById('fisik_'+sr).value='';
         document.getElementById('fispersen'+sr).value='';
     }
    }
    else
        {
            return;
        }
}

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
        param='proses=sebarDoong&kunci='+kunci;
        param+='&var1='+(var1/zz)+'&var2='+(var2/zz)+'&var3='+(var3/zz)+'&var4='+(var4/zz)+'&var5='+(var5/zz);
        param+='&var6='+(var6/zz)+'&var7='+(var7/zz)+'&var8='+(var8/zz)+'&var9='+(var9/zz)+'&var10='+(var10/zz);
        param+='&var11='+(var11/zz)+'&var12='+(var12/zz)+'&rupe='+rupe+'&fis='+fis;
        tujuan='bgt_budget_slave_kebun.php';
        if(obj.checked)
             post_response_text(tujuan, param, respog);            
        }
     else
     {
         alert('Sebaran salah');
     }
 //============

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

function isiLuas(obj)
{
    blok=obj.options[obj.selectedIndex].value;
    param='proses=getLuas&blok='+blok;
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
                        document.getElementById('volKeg').value=con.responseText;
                        gantiKegiatan(blok);
                        }
                    }
                    else {
                        busy_off();
                        error_catch(con.status);
                    }
          }	
	 } 
}

function gantiKegiatan(blok){
//    alert(blok);
        thn=document.getElementById('thnBudget').value;
        if(thn=='')
        {
            alert("Tahun Budget Tidak Boleh Kosong");
            return;
        }
        param='proses=gantiKegiatan&kdBlok='+blok+'&thnBudget='+thn;
 //alert(param);
        tujuan='bgt_budget_slave_kebun.php';
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
                              document.getElementById('kegId').innerHTML=con.responseText;
                        }
                    }
                    else {
                        busy_off();
                        error_catch(con.status);
                    }
          }	
	 }     
}

function prosUlang()
{
    thn=document.getElementById('thnBudget').value;
    if(thn=='')
    {
        alert("Tahun Budget Tidak Boleh Kosong");
        return;
    }
    param='thnBudget='+thn+'&proses=getHk';
    tujuan='bgt_budget_slave_kebun_pros.php';
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
                        ar=con.responseText.split("###");
                        document.getElementById('formHeadBudget').style.display='none';
                        document.getElementById('formIsian').style.display='none';
                        document.getElementById('listDatHeader').style.display='none';
                        document.getElementById('prosUl').style.display='block';
                        document.getElementById('hkEfektif2').value=ar[0];
                        document.getElementById('thnBudgetPros').value=ar[1];
         }
                }
                else {
                    busy_off();
                    error_catch(con.status);
                    
                }
      }	
     }  
}
function getDataInputan()
{
    kdBlok=document.getElementById('kdBlokPros').options[document.getElementById('kdBlokPros').selectedIndex].value;
    thnBudget=document.getElementById('thnBudgetPros').value;
    kegId=document.getElementById('KegPros').options[document.getElementById('KegPros').selectedIndex].value;
    param='thnBudget='+thnBudget+'&kdBlok='+kdBlok+'&kegId='+kegId+'&proses=getData';
    tujuan='bgt_budget_slave_kebun_pros.php';
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
                   ar=con.responseText.split("###");
                   if(ar[0]!='')
                   {
                        document.getElementById('KegPros').disabled=true;
                        document.getElementById('kdBlokPros').disabled=true;
                        document.getElementById('saveDataPros').disabled=false;
                        
                   }
                   document.getElementById('noAkunPros').value=ar[0];
                   document.getElementById('volKegPros').value=ar[1];
                   document.getElementById('satKegPros').value=ar[2];
                   document.getElementById('rotThnPros').value=ar[3];
                   
                    }
                }
                else {
                    busy_off();
                    error_catch(con.status);
                    
                }
      }	
     } 
}
function batalFormPros()
{
    document.getElementById('KegPros').disabled=false;
    document.getElementById('kdBlokPros').disabled=false;
    document.getElementById('saveDataPros').disabled=true;
    document.getElementById('hkEfektif2').value='';
}
function saveDataPros()
{
    kdBlok=document.getElementById('kdBlokPros').options[document.getElementById('kdBlokPros').selectedIndex].value;
    thnBudget=document.getElementById('thnBudgetPros').value;
    kegId=document.getElementById('KegPros').options[document.getElementById('KegPros').selectedIndex].value;
    noAkun=document.getElementById('noAkunPros').value;
    volKeg=document.getElementById('volKegPros').value;
    satuan=document.getElementById('satKegPros').value;
    rotThn=document.getElementById('rotThnPros').value;
    param='thnBudget='+thnBudget+'&kdBlok='+kdBlok+'&kegId='+kegId+'&proses=saveData';
    param+='&noAkun='+noAkun+'&volKeg='+volKeg+'&satuan='+satuan+'&rotThn='+rotThn;
    tujuan='bgt_budget_slave_kebun_pros.php';
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
                     
                       alert('Done');
                       batalFormPros();
                    }
                }
                else {
                    busy_off();
                    error_catch(con.status);
                    
                }
      }	
     } 
}
function kunciFormData()
{
    //document.getElementById('thnBudgetPros2').disabled=true;
    document.getElementById('KegPros2').disabled=true;
    //document.getElementById('saveDataPros').disabled=true;
}
function batalFormPros2()
{
    //document.getElementById('thnBudgetPros2').disabled=false;
    document.getElementById('KegPros2').disabled=false;
}
function gantiKeg()
{
    document.getElementById('kdBudgetAll').innerHTML=pilh;
    //document.getElementById('kdBlokPros2').value='';
    
}
function getKdBudget()
{
    
    thnBudget=document.getElementById('thnBudgetPros').value;
    kdBlok=document.getElementById('kdBlokPros2').options[document.getElementById('kdBlokPros2').selectedIndex].value;
    kegId=document.getElementById('KegPros2').options[document.getElementById('KegPros2').selectedIndex].value;
    param='thnBudget='+thnBudget+'&kdBlok='+kdBlok+'&kegId='+kegId+'&proses=getkdBudget';
    tujuan='bgt_budget_slave_kebun_pros.php';
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
                     //document.getElementById('KegPros2').disabled=true;
                     document.getElementById('kdBudgetAll').innerHTML=con.responseText;
                    }
                }
                else {
                    busy_off();
                    error_catch(con.status);
                    
                }
      }	
     } 
}

function getFormDetail()
{
    thnBudget=document.getElementById('thnBudgetPros').value;
    kdBlok=document.getElementById('kdBlokPros2').options[document.getElementById('kdBlokPros2').selectedIndex].value;
    kegId=document.getElementById('KegPros2').options[document.getElementById('KegPros2').selectedIndex].value;
    kdBudget=document.getElementById('kdBudgetAll').options[document.getElementById('kdBudgetAll').selectedIndex].value;
    param='thnBudget='+thnBudget+'&kdBlok='+kdBlok+'&kegId='+kegId+'&proses=getFormDetail'+'&kdBudget='+kdBudget;
    tujuan='bgt_budget_slave_kebun_pros.php';
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
                     document.getElementById('formDetail').innerHTML=con.responseText;
                    }
                }
                else {
                    busy_off();
                    error_catch(con.status);
                    
                }
      }	
     } 
}

function jumlahkan2(x)
{
        thnBdget=document.getElementById('thnBudget2').value;
        kdBlok=document.getElementById('kdBlokPros2').options[document.getElementById('kdBlokPros2').selectedIndex].value;
        param='thnBudget='+thnBdget+'&kdBlok='+kdBlok;
	   if(x==1)
            {
                personel=document.getElementById('jmlh2_'+x).value;
                hkEfektip=document.getElementById('hkEfektif2').value;
                if(personel=='')
                    {
                        document.getElementById('jmlh2_'+x).value='0';
                    }
                kdGol=document.getElementById('kdBudget2').options[document.getElementById('kdBudget2').selectedIndex].value;
                param+='&proses=getUpah'+'&jmlhPerson='+personel+'&kdGol='+kdGol+'&hkEfektif='+hkEfektip;
            }
            if(x==2)
                {
                    kdBudget=document.getElementById('kdBudgetM2').options[document.getElementById('kdBudgetM2').selectedIndex].value;
                    kdBrg=document.getElementById('kdBarang2').value;
                    jmlhBrg=document.getElementById('jmlh2_'+x).value;
                    param+='&kdBudget='+kdBudget+'&kdBrg='+kdBrg+'&jmlhBrg='+jmlhBrg+'&proses=getHarga';
                }
        if(x==3)
        {
            kdBrgL=document.getElementById('kdBarangL2').value;
            jmlhBrgL=document.getElementById('jmlh2_'+x).value;
            param+='&kdBrgL='+kdBrgL+'&jmlhBrgL='+jmlhBrgL+'&proses=getHargaL';
        }
        //alert(param);
	tujuan='bgt_budget_slave_kebun.php';
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
                                        if(x==1)
                                        {
                                        document.getElementById('totBiaya2').value=con.responseText;
                                        }
                                        if(x==2)
                                        {
                                           document.getElementById('totHarga2').value=con.responseText;
                                        }
                                        if(x==3)
                                        {
                                           document.getElementById('totHargaL2').value=con.responseText;
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
function getKlmpkbrg2()
{
    klmpkBrg=document.getElementById('kdBudgetM2').options[document.getElementById('kdBudgetM2').selectedIndex].value;
    param='klmpkBrg='+klmpkBrg+'&proses=setKdBrg';
    tujuan='bgt_budget_slave_kebun.php';
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
                                          //	alert(con.responseText);
                                            document.getElementById('kdBarang').value=con.responseText;
                                            document.getElementById('jmlh_2').value='';
                                            document.getElementById('namaBrg').innerHTML='';
                                            // document.getElementById('kdBarang').value='';
                                            // document.getElementById('kdBudgetM').value='';
                                            document.getElementById('totHarga').value='0';
                                            document.getElementById('satuan').innerHTML='';
                                                searchBrg('Cari Nama Barang','<fieldset><legend>Cari Nama Barang</legend>Cari <input type=text class=myinputtext id=nmBrg><button class=mybutton onclick=findBrg()>Cari</button></fieldset><div id=containerBarang style=overflow=auto;height=380;width=485></div>','event');
                                              }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }	
	 }  
}
function jumlahkanPros(x)
{
    hrgSat=document.getElementById('hrgSat_'+x).value;
    jmlhKan=document.getElementById('jmlhPros_'+x).value;
    totKali=hrgSat*jmlhKan;
    if(isNaN(totKali))
        {
            totKali=0;
        }
     document.getElementById('ruPPros_'+x).value=totKali;   
}
function getBiaya(x)
{
    kd=document.getElementById('kdvhcPros_'+x).options[document.getElementById('kdvhcPros_'+x).selectedIndex].value;
    jmlhJam=document.getElementById('jmlhPros_'+x).value;
    thnBdget=document.getElementById('thnBudgetPros').value;
    param='kdVhc='+kd+'&thnBudget='+thnBdget+'&proses=getBiaya'+'&jmlhJam='+jmlhJam;
    //alert(param);
    tujuan='bgt_budget_slave_kebun_pros.php';
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
                                          // alert(con.responseText);
                                           document.getElementById('ruPPros_'+x).value=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }	
	 } 
}
function saveDataDetail(knci,brs)
{
    thnBdget=document.getElementById('thnBudgetPros').value;
    kdBlok=document.getElementById('kdBlokPros2').options[document.getElementById('kdBlokPros2').selectedIndex].value;
    kegId=document.getElementById('KegPros2').options[document.getElementById('KegPros2').selectedIndex].value;
    kdBudget=document.getElementById('kdBudgetAll').options[document.getElementById('kdBudgetAll').selectedIndex].value;
    rotasi=document.getElementById('rotThnPros2').value;
    kdOrg=document.getElementById('kdOrg_'+brs).value;
    rpPros=document.getElementById('ruPPros_'+brs).value;
    kdBrg=document.getElementById('kdBrg_'+brs).value;
    if(kdBudget=='VHC')
    {
       kdVhc=document.getElementById('kdvhcPros_'+brs).options[document.getElementById('kdvhcPros_'+brs).selectedIndex].value;
    }
    jumlah=document.getElementById('jmlhPros_'+x).value;
    sat=document.getElementById('satuanj_'+x).value;
    param='kdBlok='+kdBlok+'&thnBudget='+thnBdget+'&proses=saveDetailData'+'&jumlah='+jumlah;
    param+='&kegId='+kegId+'&kdBudget='+kdBudget+'&rotasi='+rotasi+'&kdOrg='+kdOrg;
    param+='&rpPros='+rpPros+'&sat='+sat+'&kunci='+knci;
    if(kdBuget=='VHC')
    {
        param+='&kdVhc='+kdVhc;
    }
    //alert(param);
    tujuan='bgt_budget_slave_kebun_pros.php';
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
                                      // alert(con.responseText);
                                       //document.getElementById('ruPPros_'+x).value=con.responseText;
                                    }
                            }
                            else {
                                    busy_off();
                                    error_catch(con.status);
                            }
          }	
     } 
    
}
var brsSebaran=1;
function saveSebaran(x)
{
	//document.getElementById('tmblPrev').disabled=true;
        //document.getElementById('thnBudget').disabled=true;
        document.getElementById('save_kepala').disabled=true;
        kunci=document.getElementById('key_'+x).innerHTML;
        rupe=document.getElementById('hrg_'+x).innerHTML;
        fis=document.getElementById('vol_'+x).innerHTML;
        totRow=document.getElementById('jmlhRow').value;
       
	//param='proses=insertAllData'+'&kunci='+kunci;
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
            param='proses=sebarDoong&kunci='+kunci;
            param+='&var1='+(var1/zz)+'&var2='+(var2/zz)+'&var3='+(var3/zz)+'&var4='+(var4/zz)+'&var5='+(var5/zz);
            param+='&var6='+(var6/zz)+'&var7='+(var7/zz)+'&var8='+(var8/zz)+'&var9='+(var9/zz)+'&var10='+(var10/zz);
            param+='&var11='+(var11/zz)+'&var12='+(var12/zz)+'&rupe='+rupe+'&fis='+fis;
            tujuan='bgt_budget_slave_kebun.php';
            
                // post_response_text(tujuan, param, respog);            
        }
        else
        {
         alert('Sebaran salah');
        }
//	alert(param);
//        return;
	//tujuan='bgt_slave_alokasi_supervisi.php';
	if(x==1 && confirm('Anda Yakin Melakukan Proses Ini?'))
        {post_response_text(tujuan, param, respog);}
        else if(x!=1)
        {post_response_text(tujuan, param, respog);}
	document.getElementById('baris'+x).style.backgroundColor='orange';
	function respog()
        {
          if(con.readyState==4)
          {
            if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                            alert('ERROR TRANSACTION,\n' + con.responseText);
			    document.getElementById('baris'+x).style.backgroundColor='red';
                            document.getElementById('lnjutSebaran').style.display='';
                    }
                    else {
                           // alert(con.responseText);
                          //document.getElementById('container').innerHTML=con.responseText;
                            //return con.responseText;
                        b=x;
                        brsSebaran=x;
                        row=x+1;
                        x=row;
                        
                        if(x<=totRow)
                         {   
			     document.getElementById('baris'+b).style.backgroundColor='green';
                             document.getElementById('baris'+b).style.display='none';
                             saveSebaran(x);
                         }
                         else
                         {
                             
                             document.getElementById('tmblPrev').disabled=false;
                             document.getElementById('thnBudget').disabled=false;
                             document.getElementById('save_kepala').disabled=false;
                             document.getElementById('contentSebaran').innerHTML='';
                             alert('Done');
                         }
                    }
            }
            else {
                    busy_off();
                    document.getElementById('lnjutSebaran').style.display='';
                    error_catch(con.status);
            }
          }	
     } 	    

}
function reSave(){
    saveSebaran(brsSebaran);
    document.getElementById('lnjutSebaran').style.display='none';
}