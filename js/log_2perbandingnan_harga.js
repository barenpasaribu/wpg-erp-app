function simpanSemua(brs,totRow)
{
    nopp=document.getElementById('nopp').options[document.getElementById('nopp').selectedIndex].value;
    if(nopp=='')
        {
            nopp=document.getElementById('nopp2').options[document.getElementById('nopp2').selectedIndex].value;
        }
    no_prmntan=document.getElementById('no_prmntan_'+brs).value;
    nilDiskon=document.getElementById('angDiskon_'+brs).value;
    diskonPersen=document.getElementById('diskon_'+brs).value;
    nilPPn=document.getElementById('ppn_'+brs).value;
    nilaiPermintaan=document.getElementById('grand_total_'+brs).innerHTML;
    subTotal=document.getElementById('total_harga_po_'+brs).innerHTML;
    termPay=document.getElementById('term_pay_'+brs).options[document.getElementById('term_pay_'+brs).selectedIndex].value;
    idFranco=document.getElementById('tmpt_krm_'+brs).options[document.getElementById('tmpt_krm_'+brs).selectedIndex].value;
    stockId=document.getElementById('stockId_'+brs).options[document.getElementById('stockId_'+brs).selectedIndex].value;
    ketUraian=document.getElementById('ketUraian_'+brs).value;
    mtng=document.getElementById('mtUang_'+brs).options[document.getElementById('mtUang_'+brs).selectedIndex].value;
    krs=document.getElementById('Kurs_'+brs).value;
    tgldari=document.getElementById('tgl_dari_'+brs).value;
    tglsmp=document.getElementById('tgl_smp_'+brs).value;
    if((subTotal=='0')||(subTotal==''))
        {
            subTotal=nilDiskon=diskonPersen=nilPPn=0;
        }
    
    var row = totRow+1;
    strUrl = '';
    for(i=1;i<row;i++)
    {
                    try{
                            if(strUrl != '')
                            {
                                    strUrl += '&kdbrg[]='+encodeURIComponent(trim(document.getElementById('kd_brg_'+i).innerHTML))
                                    +'&merk[]='+document.getElementById('merk_'+i+'_'+brs).value
                                    +'&price[]='+document.getElementById('price_'+i+'_'+brs).value
                                    +'&jmlh[]='+document.getElementById('jumlah_'+i).innerHTML;
                            }
                            else
                            {
                                    strUrl += '&kdbrg[]='+encodeURIComponent(trim(document.getElementById('kd_brg_'+i).innerHTML))
                                    +'&merk[]='+document.getElementById('merk_'+i+'_'+brs).value
                                    +'&price[]='+document.getElementById('price_'+i+'_'+brs).value
                                    +'&jmlh[]='+document.getElementById('jumlah_'+i).innerHTML;	
                            }
                    }
                    catch(e){}
    }
    param='ckno_permintaan='+no_prmntan+'&nopp='+nopp;
    param+='&nilDiskon='+nilDiskon+'&diskonPersen='+diskonPersen+'&nilPPn='+nilPPn+'&nilaiPermintaan='+nilaiPermintaan;
    param+='&subTotal='+subTotal+'&termPay='+termPay+'&idFranco='+idFranco+'&stockId='+stockId+'&ketUraian='+ketUraian;
    param+='&tglDari='+tgldari+'&tglSmp='+tglsmp+'&mtUang='+mtng+'&kurs='+krs;
    param+=strUrl;
    tujuan='log_slave_2perbandingan_harga.php';
 //   alert(param);
//  return;
    post_response_text(tujuan+'?proses=update', param, respog);
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
    
                                            }
                                    }
                                    else {
                                            busy_off();
                                            error_catch(con.status);
                                    }
                  }
     }
  
}
function simpanSemua2(brs,totRow)
{
    
    nopp=document.getElementById('nopp2').options[document.getElementById('nopp2').selectedIndex].value;
    if(nopp=='')
        {
           nopp=document.getElementById('noppr').value;
        }
    no_prmntan=document.getElementById('no_prmntan_'+brs).value;
    nilDiskon=document.getElementById('angDiskon_'+brs).value;
    diskonPersen=document.getElementById('diskon_'+brs).value;
    supplierId=document.getElementById('supplierId_'+brs).value;
    nilPPn=document.getElementById('ppn_'+brs).value;
    nilaiPermintaan=document.getElementById('grand_total_'+brs).innerHTML;
    subTotal=document.getElementById('total_harga_po_'+brs).innerHTML;
    termPay=document.getElementById('term_pay_'+brs).options[document.getElementById('term_pay_'+brs).selectedIndex].value;
    idFranco=document.getElementById('tmpt_krm_'+brs).options[document.getElementById('tmpt_krm_'+brs).selectedIndex].value;
    stockId=document.getElementById('stockId_'+brs).options[document.getElementById('stockId_'+brs).selectedIndex].value;
    ketUraian=document.getElementById('ketUraian_'+brs).value;
    mtng=document.getElementById('mtUang_'+brs).options[document.getElementById('mtUang_'+brs).selectedIndex].value;
    krs=document.getElementById('Kurs_'+brs).value;
    tgldari=document.getElementById('tgl_dari_'+brs).value;
    tglsmp=document.getElementById('tgl_smp_'+brs).value;
    if((subTotal=='0')||(subTotal==''))
        {
            subTotal=nilDiskon=diskonPersen=nilPPn=0;
        }
    
    var row = totRow+1;
    strUrl = '';
    for(i=1;i<row;i++)
    {
                    try{
                            if(strUrl != '')
                            {
                                    strUrl += '&kdbrg[]='+encodeURIComponent(trim(document.getElementById('kd_brg_'+i).innerHTML))
                                    +'&merk[]='+document.getElementById('merk_'+i+'_'+brs).value
                                    +'&price[]='+document.getElementById('price_'+i+'_'+brs).value
                                    +'&jmlh[]='+document.getElementById('jumlah_'+i).innerHTML;
                            }
                            else
                            {
                                    strUrl += '&kdbrg[]='+encodeURIComponent(trim(document.getElementById('kd_brg_'+i).innerHTML))
                                    +'&merk[]='+document.getElementById('merk_'+i+'_'+brs).value
                                    +'&price[]='+document.getElementById('price_'+i+'_'+brs).value
                                    +'&jmlh[]='+document.getElementById('jumlah_'+i).innerHTML;	
                            }
                    }
                    catch(e){}
    }
    param='ckno_permintaan='+no_prmntan+'&nopp2='+nopp;
    param+='&nilDiskon='+nilDiskon+'&diskonPersen='+diskonPersen+'&nilPPn='+nilPPn+'&nilaiPermintaan='+nilaiPermintaan;
    param+='&subTotal='+subTotal+'&termPay='+termPay+'&idFranco='+idFranco+'&stockId='+stockId+'&ketUraian='+ketUraian;
    param+='&tglDari='+tgldari+'&tglSmp='+tglsmp+'&mtUang='+mtng+'&kurs='+krs+'&supplierId='+supplierId;
    param+=strUrl;
    tujuan='log_slave_2perbandingan_harga.php';
   
//  return;
    post_response_text(tujuan+'?proses=update', param, respog);
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
                                                    alert("Done");
                                            }
                                    }
                                    else {
                                            busy_off();
                                            error_catch(con.status);
                                    }
                  }
     }
  
}

function display_number(id)
{
        price=document.getElementById('price_'+id);
        change_number(price);
		jmlh=document.getElementById('jumlah_'+id);
        change_number(jmlh);     
}
function normal_number(id)
{
	satu=document.getElementById('harga_satuan_'+id);
	satu.value=remove_comma(satu);
}

function calculate(id,row,totRow)
{
    jmlh_brg=document.getElementById('jumlah_'+id).innerHTML;
    harga=document.getElementById('price_'+id+'_'+row).value;
	
        if(jmlh_brg==''||harga=='')
        {
                a=document.getElementById('total_'+id+'_'+row);
                a.value='';
                a=parseFloat(a.value);
        }
        else
        {
                        harg=document.getElementById('price_'+id+'_'+row);
                        harg.value=remove_comma_var(harg.value);
                        jmlh_sub=jmlh_brg*harg.value;

                        if(jmlh_sub==0)
                        {
                                document.getElementById('total_'+id+'_'+row).value=0;
                        }
                        else
                        {
                                        as=document.getElementById('total_'+id+'_'+row);
                                        as.value=jmlh_sub
                                        change_number(as);
                        }

        }
                
				grnd_total(row,totRow);
				//grandTotal();
			
}

function grnd_total(brs,totRow)
{ 
   row=totRow+1;
   total=0;
   for(i=1;i<row;i++)
       {
            b=document.getElementById('total_'+i+'_'+brs);
            b.value=remove_comma_var(b.value);
            total+=parseFloat(b.value);
            change_number(b);
           // alert(b+"------"+total);
            //alert(b.value);
            //change_number(b);
            if(isNaN(total))
               {
                   total=0;
               }
       }
           document.getElementById('total_harga_po_'+brs).innerHTML=total;
           tot=document.getElementById('total_harga_po_'+brs);
           tot.innerHTML=total;
           //change_number(tot);
           grandTotal(brs);
}

function getZero(brs)
{
	dis=document.getElementById('diskon_'+brs);
	if(dis.value=="")
	{
		dis.value=0;
	}
	nPpn=document.getElementById('ppN_'+brs);
	if(nPpn.value=="")
	{
		nPpn.value=0;
	}
	angdis=document.getElementById('angDiskon_'+brs);
	//angdis.value=remove_comma(angdis);
	if(angdis.value=="")
	{
		angdis.value=0;
	}
}
function periksa_isi(obj)
{
	if(trim(obj.value)=='')	
	{
		alert('Please Complete The Form');
		obj.focus();
		return;
	}
}
function cek_isi(obj)
{
	if(trim(obj.value)!='')	
	{
		change_number(obj.value);
	}
	else
	{
		change_number(obj.value);
	}
}
function calculate_diskon(brs)
{
	sb_tot=document.getElementById('total_harga_po_'+brs);
        sb_tot.innerHTML=remove_comma_var(sb_tot.innerHTML);
	nil_dis=document.getElementById('diskon_'+brs).value;
	angk=document.getElementById('angDiskon_'+brs).value;
	if((nil_dis==0)||(angk==0))
	{
		document.getElementById('angDiskon_'+brs).disabled=false;
		document.getElementById('diskon_'+brs).disabled=false;
	}
	if((nil_dis!=0)||(angk!=0))
	{
		document.getElementById('angDiskon_'+brs).disabled=true;
		if(nil_dis>100)
		{	
			alert('Diskon Tidak Lebih Dari 100%');
			document.getElementById('diskon_'+brs).value='';
			document.getElementById('angDiskon_'+brs).disabled=false;
		}
		else
		{
			disc=(nil_dis*sb_tot.innerHTML)/100;
		}
		 //  	grnd_tot=(sb_tot.value-disc)+pn;
			//document.getElementById('angDiskon').value=disc;
			nilaiDis=document.getElementById('angDiskon_'+brs);
			nilaiDis.value=disc;
			//change_number(nilaiDis);
                        calculatePpn(brs);
                        grandTotal(brs);
	}

}
function calculate_angDiskon(brs)
{
	nilDis=document.getElementById('angDiskon_'+brs);
	//nilDis.value=remove_comma(nilDis);
	if(nilDis.value!=0)
	{
		document.getElementById('diskon_'+brs).disabled=true;
		subTot=document.getElementById('total_harga_po_'+brs);
		//subTot.innerHTML=remove_comma(subTot);
		if(nilDis.value!=subTot.innerHTML)
		{
			persenDis=parseFloat(nilDis.value/subTot.innerHTML)*100;
		}
		if(persenDis<100)
		{
			persen=Math.ceil(persenDis);
			document.getElementById('diskon_'+brs).value=persen;
			//sbTot=document.getElementById('total_harga_po').value
		}
		else 
		{
			alert("Nilai Diskon Terlalu Besar");
			document.getElementById('angDiskon_'+brs).value='';
			document.getElementById('diskon_'+brs).value='';
			document.getElementById('diskon_'+brs).disabled=false;
		}
		
		//nilDiskon=document.getElementById('angDiskon').value;
	calculatePpn(brs);
	grandTotal(brs);
	}
	else if(nilDis.value==0)
	{
		document.getElementById('diskon_'+brs).disabled=false;
	}
}
function calculatePpn(brs)
{
	var reg = /^[0-9]{1,2}$/;
	nilP=document.getElementById('ppN_'+brs).value;
	dis=document.getElementById('angDiskon_'+brs);
	subTot=document.getElementById('total_harga_po_'+brs);
	//alert(reg);
	if(reg.test(nilP))
	{
		if(nilP==10)
		{
			//dis.value=remove_comma(dis);
			//subTot.innerHTML=remove_comma(subTot);
			pn=(parseFloat((subTot.innerHTML-dis.value))*10)/100;	
			if(isNaN(pn))
                        {
                            pn=0;
                        }
			document.getElementById('ppn_'+brs).value=pn;
		}
	
		else if(nilP==0)
		{
			//dis.value=remove_comma(dis);
			//subTot.innerHTML=remove_comma(subTot);
			pn=(parseFloat((subTot.innerHTML-dis.value))*nilP)/100;	
			document.getElementById('ppn_'+brs).value=pn;
		}	
		else if(nilP==2)
		{
			//dis.value=remove_comma(dis);
			//subTot.value=remove_comma(subTot);
			pn=(parseFloat((subTot.innerHTML-dis.value))*nilP)/100;	
                        if(isNaN(pn))
                        {
                            pn=0;
                        }
			document.getElementById('ppn_'+brs).value=pn;
		}	
	}
	else
	{
		alert("Angka yang Valid 0 dan 10");
		document.getElementById('ppn_'+brs).value='0';
                document.getElementById('ppN_'+brs).value='0';
		return;
	}
	
		grandTotal(brs);
}
nilPpn=0;
function grandTotal(brs)
{
	sb_tot=document.getElementById('total_harga_po_'+brs);
	nilDiskon=document.getElementById('angDiskon_'+brs);
        ppn=document.getElementById('ppN_'+brs);
	
        if(ppn.value!=0||ppn.value!='')
        {
            nilPpn=(parseFloat((sb_tot.innerHTML-nilDiskon.value))*ppn.value)/100;	
            document.getElementById('ppn_'+brs).value=nilPpn;   
        }
        else
        {
            document.getElementById('ppN_'+brs).value=0;
            document.getElementById('ppn_'+brs).value=0;
            nilPpn=0;
        }
	
	grnd_tot=parseFloat((sb_tot.innerHTML-nilDiskon.value))+parseFloat(nilPpn);
        total=document.getElementById('grand_total_'+brs);
	total.innerHTML=grnd_tot;
		
}