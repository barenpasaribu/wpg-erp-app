// JavaScript Document
// JavaScript Document
function clear_all_data()
{
        document.getElementById('no_po').value="";
        document.getElementById('supplier_id').value="";
        document.getElementById('tgl_krm').value="";
        document.getElementById('tmpt_krm').value="";
        document.getElementById('bank_acc').value="";
        document.getElementById('npwp_sup').value="";
        document.getElementById('txtsearch').value="";
        document.getElementById('tgl_cari').value="";
        document.getElementById('proses').value='insert';
        document.getElementById('tmpt_krm').value="";
        document.getElementById('term_pay').value="";
        document.getElementById('ketUraian').value="";
}
function show_list_pp()
{	
clear_all_data();
//cek_pp_pt();
document.getElementById('list_po').style.display='none';
document.getElementById('list_pp').style.display='none';
//document.getElementById('form_po').style.display='none';
document.getElementById('form_po_lokal').style.display='block';
}

function cek_pp_pt(kdpt)
{
    document.getElementById('kode_pt').disabled=true;
   if(kdpt=='0')
   {
           //alert("test");
           kode_pt=document.getElementById('kode_pt').options[document.getElementById('kode_pt').selectedIndex].value;
   }
   else
   {
          // show_list_pp();
           kode_pt=kdpt;
           document.getElementById('kode_pt').value=kdpt;
   }
    user_id=trim(document.getElementById('user_id').value);
    param='kodept='+kode_pt+'&id_user='+user_id;
    param+="&proses=listPp";
  // alert(param);
//    return;
     tujuan='log_slave_po_lokal_detail.php';

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
                        //show_form_po();
                        clear_all_data();
                        document.getElementById('list_po').style.display='none';
                        document.getElementById('list_pp').style.display='block';
                        document.getElementById('form_po').style.display='none';
                        document.getElementById('container_pp').innerHTML=con.responseText;
                }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
      }
     }
     post_response_text(tujuan, param, respog);
}

function display_number(id)
{
        if(id!='')
        {	sat=document.getElementById('harga_satuan_'+id);
                change_number(sat);
                grnd_total();       
        }
        else
        {
                nilDis=document.getElementById('angDiskon');
                change_number(nilDis);
        }
}
function normal_number(id)
{
                satu=document.getElementById('harga_satuan_'+id);
                satu.value=remove_comma(satu);
}

function calculate(id)
{

        //alert(row);
    //defult_tot=document.getElementById('realisasi_'+id).value;
    jmlh_brg=document.getElementById('jmlhDiminta_'+id).value;
    harga=document.getElementById('harga_satuan_'+id).value;

                //if((parseFloat(jmlh_brg))>=(parseFloat(defult_tot)+1))
                if((parseFloat(jmlh_brg))<0)
                        {
                                alert('Quantity must equal or lower then total requested');
                                document.getElementById('jmlhDiminta_'+id).value='';
                                return;		
                        }
                        else
                        {
                                        if(jmlh_brg==''||harga=='')
                                        {
                                                a=document.getElementById('total_'+id);
                                                a.value='';
                                                a=parseFloat(a.value);
                                        }
                                        else
                                        {
                                                        harg=document.getElementById('harga_satuan_'+id);
                                                        harg.value=remove_comma(harg);
                                                        jmlh_sub=jmlh_brg*harg.value;

                                                        if(jmlh_sub==0)
                                                        {
                                                                document.getElementById('total_'+id).value='';
                                                        }
                                                        else
                                                        {
                                                                        as=document.getElementById('total_'+id);
                                                                        as.value=jmlh_sub
                                                                        change_number(as);
                                                        }

                                        }
                }
                                grnd_total();

}

function grnd_total()
{

     var tbl = document.getElementById("detailBody");
    var row = tbl.rows.length;
    row=row-9;    
    //a=document.getElementById('total_'+row);
   // alert(row);
   total=0;
   for(i=0;i<row;i++)
       {
            b=document.getElementById('total_'+i);
            b.value=remove_comma(b);
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
           document.getElementById('total_harga_po').value=total;
           tot=document.getElementById('total_harga_po');
           tot.value=total;
           change_number(tot);
                   grandTotal();
}

function plusAll(id)
{
        isiData = document.getElementById("detailBody");
        barisIsi = isiData.rows.length;
        barisIsi=barisIsi-5;
        total=0;
        for(i=0;i<barisIsi;i++)
        {
                b=document.getElementById('total_'+i);
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
        document.getElementById('total_harga_po').value=total;
        tot=document.getElementById('total_harga_po');
        tot.value=total;
        change_number(tot);


        //hitung diskon
        //nilPpn=document.getElementById('ppn').value;
        nil_dis=document.getElementById('diskon').value;
        angk=document.getElementById('angDiskon').value;
        if(nil_dis!="")
        {
        disc=(nil_dis*total)/100;
                nilaiDis=document.getElementById('angDiskon');
                nilaiDis.value=disc;
                change_number(nilaiDis);
                document.getElementById('nilai_diskon').value=disc;		
        }
        else
        {
                document.getElementById('diskon').value=0;
                disc=(nil_dis*total)/100;
                nilaiDis=document.getElementById('angDiskon');
                nilaiDis.value=disc;
                change_number(nilaiDis);
                document.getElementById('nilai_diskon').value=disc;	
                /*document.getElementById('ppN').value=0;
                document.getElementById('ppn').value=0;
                nilPpn=0;*/
        }

        //ppn
        nPPn=document.getElementById('ppN').value;
        if(nPPn!="")
        {
                //nilP=document.getElementById('ppN').value;
                //dis=document.getElementById('nilai_diskon');
                //subTot=document.getElementById('total_harga_po');
                //dis.value=remove_comma(dis);
                //subTot.value=remove_comma(subTot);
                nilPpn=(parseFloat((total-disc))*nPPn)/100;	
                document.getElementById('hslPPn').innerHTML=nilPpn;
                document.getElementById('ppn').value=nilPpn;
        }
        else
        {
                document.getElementById('ppN').value=0;
                document.getElementById('ppn').value=0;
                nilPpn=0;
        }
        //alert(total+"__"+disc+"___"+nilPpn);
        grnd_tot=parseFloat((total-disc))+parseFloat(nilPpn);
    test=document.getElementById('grand_total');
        test.value=grnd_tot;
        change_number(sb_tot);
        change_number(nilPpn);
        change_number(total);

}
function getZero()
{
        dis=document.getElementById('diskon');
        if(dis.value=="")
        {
                dis.value=0;
        }
        nPpn=document.getElementById('ppN');
        if(nPpn.value=="")
        {
                nPpn.value=0;
        }
        angdis=document.getElementById('angDiskon');
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
                alert('Please complete the form');
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
function calculate_diskon()
{
        sb_tot=document.getElementById('total_harga_po');
    sb_tot.value=remove_comma(sb_tot);
        nil_dis=document.getElementById('diskon').value;
        angk=document.getElementById('angDiskon').value;
        if((nil_dis==0)||(angk==0))
        {
                document.getElementById('angDiskon').disabled=false;
                document.getElementById('diskon').disabled=false;
        }

        if((nil_dis!=0)||(angk!=0))
        {
                document.getElementById('angDiskon').disabled=true;
                if(nil_dis>=100)
                {	
                        alert(' Discount must lower than 100%');
                        document.getElementById('diskon').value='';
                        document.getElementById('angDiskon').disabled=false;
                }
                else
                {
                        disc=(nil_dis*sb_tot.value)/100;

                }

                 //  	grnd_tot=(sb_tot.value-disc)+pn;
                        document.getElementById('angDiskon').value=disc;
                        document.getElementById('nilai_diskon').value=disc;
                        nilaiDis=document.getElementById('angDiskon');
                        change_number(nilaiDis);
/*			grnd_tot=parseFloat((sb_tot.value-disc));
                        document.getElementById('grand_total').value=grnd_tot;
                        total=document.getElementById('grand_total');
                        change_number(total);
*/	
                calculatePpn();
                grandTotal();
        }


/*	document.getElementById('ppn').value=pn;
        pn=document.getElementById('ppn');
        change_number(pn);
*/	
                /*document.getElementById('grand_total').value=grnd_tot;
        total=document.getElementById('grand_total');
        change_number(total);
*/        

}
function calculate_angDiskon()
{
        nilDis=document.getElementById('angDiskon');
        nilDis.value=remove_comma(nilDis);
        if(nilDis.value!=0)
        {
                document.getElementById('diskon').disabled=true;
                subTot=document.getElementById('total_harga_po');
                subTot.value=remove_comma(subTot);
                if(nilDis.value!=subTot.value)
                {
                        persenDis=parseFloat(nilDis.value/subTot.value)*100;
                }
                if(persenDis<100)
                {
                        persen=Math.ceil(persenDis);
                        document.getElementById('nilai_diskon').value=nilDis.value;
                        document.getElementById('diskon').value=persen;
                        //sbTot=document.getElementById('total_harga_po').value
                }
                else 
                {
                        alert("Discount value too large");
                        document.getElementById('angDiskon').value='';
                        document.getElementById('diskon').value='';
                        document.getElementById('nilai_diskon').value='';
                        document.getElementById('diskon').disabled=false;
                }

                //nilDiskon=document.getElementById('angDiskon').value;
        grandTotal();
        }
        else if(nilDis.value==0)
        {
                document.getElementById('diskon').disabled=false;
        }
}
function calculatePpn()
{
        var reg = /^[0-9]{1,2}$/;
        nilP=document.getElementById('ppN').value;
        dis=document.getElementById('nilai_diskon');
        subTot=document.getElementById('total_harga_po');
        //alert(reg);
        if(reg.test(nilP))
        {
                if(nilP<10)
                {
                        dis.value=remove_comma(dis);
                        subTot.value=remove_comma(subTot);
                        pn=(parseFloat((subTot.value-dis.value))*nilP)/100;	
                        document.getElementById('hslPPn').innerHTML=pn;
                        document.getElementById('ppn').value=pn;
                }
                else if(nilP==10)
                {
                        dis.value=remove_comma(dis);
                        subTot.value=remove_comma(subTot);
                        pn=(parseFloat((subTot.value-dis.value))*nilP)/100;	
                        document.getElementById('hslPPn').innerHTML=pn;
                        document.getElementById('ppn').value=pn;
                }



                //else if(nilP==0)
//		{
//			dis.value=remove_comma(dis);
//			subTot.value=remove_comma(subTot);
//			pn=(parseFloat((subTot.value-dis.value))*nilP)/100;	
//			document.getElementById('hslPPn').innerHTML=nilP;
//			document.getElementById('ppn').value=pn;
//		}	
        }
        else
        {
                alert("Wrong value on vat field");
                document.getElementById('hslPPn').value='';
                document.getElementById('ppn').value='';
                return;

        }

                grandTotal();
}
nilPpn=0;
function grandTotal()
{
        sb_tot=document.getElementById('total_harga_po');
    sb_tot.value=remove_comma(sb_tot);
        nilDiskon=document.getElementById('angDiskon');
         ppn=document.getElementById('ppN');
        if(nilDiskon.value!=""||nilDiskon.value!=0)
        {
                nilDiskon.value=remove_comma(nilDiskon);
                nilPpn=document.getElementById('ppn').value;
        }
        else
        {
                document.getElementById('diskon').value=0;
                nilDiskon.value=0;

        }
        if(ppn.value!=0||ppn.value!='')
        {
            nilPpn=(parseFloat((sb_tot.value-nilDiskon.value))*ppn.value)/100;	
            document.getElementById('hslPPn').innerHTML=nilPpn;
            document.getElementById('ppn').value=nilPpn;   
        }
        else
        {
            document.getElementById('ppN').value=0;
            document.getElementById('ppn').value=0;
            document.getElementById('hslPPn').innerHTML=0;
            nilPpn=0;
        }
        grnd_tot=parseFloat((sb_tot.value-nilDiskon.value))+parseFloat(nilPpn);
    total=document.getElementById('grand_total');
        total.value=grnd_tot;
        change_number(sb_tot);
        change_number(nilPpn);
        change_number(total);			
}
function process()
{  //clear_all_data();
    var tbl = document.getElementById("list_pp_table");
    var row = tbl.rows.length;
    row=row-2;
        //alert(row);
        strUrl = '';
    for(i=1;i<=row;i++)
        {
                   if(document.getElementById('plh_pp_'+i).checked==true)
                   {
                        //alert(i);
                        try{
                                if(strUrl != '')
                                {
                                        strUrl += '&nopp[]='+trim(document.getElementById('nopp_'+i).innerHTML)
                               +'&kdbrg[]='+trim(document.getElementById('kdbrg_'+i).innerHTML);
                                }
                                else
                                {
                                        strUrl += '&nopp[]='+trim(document.getElementById('nopp_'+i).innerHTML)
                                               +'&kdbrg[]='+trim(document.getElementById('kdbrg_'+i).innerHTML);
                                }
                        }
                        catch(e){}

                   }

        }

                //return;
                if(strUrl=='')
                {
                        alert('Please choose one');
                        return;
                }
                else
                {
                    kodePt=document.getElementById('kode_pt').options[document.getElementById('kode_pt').selectedIndex].value;
                    param="proses=createTable"+"&baris="+row+'&kode_pt='+kodePt;
                        //param="proses=createTable";
                        param+=strUrl;
                //alert(param);
                        tujuan='log_slave_po_lokal_detail.php';
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
                                                                        // document.getElementById('detail_content').innerHTML=con.responseText;
                                                                        //generate_nopo();
                                                                        // window.alert(a[0] + " " + a[1]);
                                                                        show_form_po();

                                    var a=con.responseText.split("###");
                                                                        //alert(a[0]);
                                                                        //alert(a[1]);
                                                                        document.getElementById('no_po').value=a[0];                            
                                                                        document.getElementById('ppDetailTable').innerHTML=a[1];
                                                                }
                                                        }
                                                        else {
                                                                busy_off();
                                                                error_catch(con.status);
                                                        }
                                          }	
                         } 
                         post_response_text(tujuan, param, respog);
                }
                //alert(strUrl);
}



function show_form_po()
{
    document.getElementById('list_po').style.display='none';
    document.getElementById('list_pp').style.display='none';
    document.getElementById('form_po').style.display='block';
}
function displayList()
{
        document.getElementById('list_po').style.display='block';
		document.getElementById('list_pp').style.display='none';
        //document.getElementById('form_po').style.display='none';
        document.getElementById('form_po_lokal').style.display='none';
        load_new_data();
        clear_all_data();
		
		
}
function get_supplier()
{
        id_sup=document.getElementById('supplier_id').options[document.getElementById('supplier_id').selectedIndex].value;
        param='supplier_id='+id_sup;
        param+='&proses=cek_supplier';
        tujuan='log_slave_save_po.php';
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
                                                                var a=con.responseText.split(",");
                                                                        // window.alert(a[0] + " " + a[1]);
                                                                document.getElementById('bank_acc').value=a[0];
                                                                //  alert(con.responseText);
                                                                document.getElementById('npwp_sup').value=a[1];
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                                  }	
                         } 
                         post_response_text(tujuan, param, respog);

}
function save_headher()
{
	if(confirm("Anda ingin menyimpan data ini ?"))
	{ 
		var tbl = document.getElementById("ppDetailTable");
		var row = tbl.rows.length;
		row=row-9;
		strUrl2 = '';

		for(i=0;i<row;i++)
		{

						try{

										if(strUrl2 != '')
										{					
												strUrl2 +='&nopp[]='+trim(document.getElementById('rnopp_'+i).value)
												+'&kdbrg[]='+encodeURIComponent(trim(document.getElementById('kd_brg_'+i).value))
												+'&spekBrg[]='+encodeURIComponent(trim(document.getElementById('spek_brg_'+i).value))
												+'&rsatuan_unit[]='+encodeURIComponent(trim(document.getElementById('sat_'+i).value))
												+'&rjmlh_psn[]='+encodeURIComponent(trim(document.getElementById('jmlhDiminta_'+i).value))
												+'&rhrg_sat[]='+document.getElementById('harga_satuan_'+i).value ;
												

												}
												else
												{
												strUrl2 +='&nopp[]='+trim(document.getElementById('rnopp_'+i).value)
												+'&kdbrg[]='+encodeURIComponent(trim(document.getElementById('kd_brg_'+i).value))
												+'&spekBrg[]='+encodeURIComponent(trim(document.getElementById('spek_brg_'+i).value))
												+'&rsatuan_unit[]='+encodeURIComponent(trim(document.getElementById('sat_'+i).value))
												+'&rjmlh_psn[]='+encodeURIComponent(trim(document.getElementById('jmlhDiminta_'+i).value))
												+'&rhrg_sat[]='+document.getElementById('harga_satuan_'+i).value ;
												
												}

								}
				catch(e){}

		}
		//alert(document.getElementById('nopp_1').value);
		//console.log(strUrl2);
		nopo=document.getElementById('no_po').value;
		tgl_po=document.getElementById('tgl_po').value;
		supplier_id=document.getElementById('supplier_id').options[document.getElementById('supplier_id').selectedIndex].value;
		sub_tot=document.getElementById('total_harga_po');
		sub_tot.value=remove_comma(sub_tot);
		sub_tot=sub_tot.value;
		disc=document.getElementById('diskon').value;
		nil_ppn=document.getElementById('ppn');
		nil_ppn.value=remove_comma(nil_ppn);
		nil_ppn=nil_ppn.value;
		tgl_deliver=document.getElementById('tgl_krm').value;
		delivery_loc=document.getElementById('tmpt_krm').value;
		cara_pem=document.getElementById('term_pay').value;
		grnd_tot=document.getElementById('grand_total');
		grnd_tot.value=remove_comma(grnd_tot);
		grnd_tot=grnd_tot.value;
		purchs=document.getElementById('user_id').value;
		lokasi_peng=document.getElementById('tmpt_krm').value;
		nil_diskon=document.getElementById('angDiskon').value;
		rproses=document.getElementById('proses').value;
		rek=document.getElementById('bank_acc').value;
		npwp=document.getElementById('npwp_sup').value;
		ketUrai=trim(document.getElementById('ketUraian').value);
		mataUang=document.getElementById('mtUang').options[document.getElementById('mtUang').selectedIndex].value;
		krs=trim(document.getElementById('Kurs').value);
		//alert(row);
		ttd=document.getElementById('persetujuan_id').options[document.getElementById('persetujuan_id').selectedIndex].value;
		ttd2=document.getElementById('persetujuan_id2').value;
		if(lokasi_peng==""){
				lokasi_peng=document.getElementById('tmpt_krm1').options[document.getElementById('tmpt_krm1').selectedIndex].value;
				lokasi_peng=parseInt(lokasi_peng);
		}
		
		miscppn=document.getElementById('miscNppn');
		miscppn.value=remove_comma(miscppn);
		miscppn=miscppn.value;
		
		//ongkrm=document.getElementById('ongKirim').value;
		ongkrm=document.getElementById('ongKirim');
		ongkrm.value=remove_comma(ongkrm);
		ongkrm=ongkrm.value;
		
		//ongkrmppn=document.getElementById('ongKirimPPn').value;//ongKirimPPn
		
		ongkrmppn=document.getElementById('ongKirimPPn');
		ongkrmppn=ongkrmppn.value;
		//  alert(ongkrmppn);
		
		miscdt=document.getElementById('misc');
		miscdt.value=remove_comma(miscdt);
		miscdt=miscdt.value;
		
		crbayar=document.getElementById('crByr');
		crbayar=crbayar.options[crbayar.selectedIndex].value;
		


		param='nopo='+nopo+'&tglpo='+tgl_po+'&supplier_id='+supplier_id+'&subtot='+sub_tot+'&grand_total='+grnd_tot+'&purchser_id='+purchs+'&lokasi_krm='+lokasi_peng;
		param+='&diskon='+disc+'&ppn='+nil_ppn+'&tgl_krm='+tgl_deliver+'&lok_kirim='+delivery_loc+'&cara_pembayarn='+cara_pem+'&nildiskon='+nil_diskon;
		param+='&proses='+rproses+'&rek='+rek+'&npwp='+npwp+'&ketUraian='+ketUrai;
		param+='&mtUang='+mataUang+'&Kurs='+krs+'&id_user='+ttd+'&ttd2='+ttd2+'&ongkirim='+ongkrm;
		param+='&miscppn='+miscppn+'&ongKirimPPn='+ongkrmppn+'&misc='+miscdt+'&crByr='+crbayar;
		param+=strUrl2;
		//alert(param);
		//return;
		tujuan='log_slave_save_po_lokal.php';
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
												console.log(con.responseText);
												if( con.responseText == "success"){													
													document.getElementById('form_po_lokal').style.display='none';
													location.reload();
												}
													   // return;
												//document.getElementById('contain').innerHTML=con.responseText;
												//displayList();
										}
								}
								else {
										busy_off();
										error_catch(con.status);
								}
						  }	
				 } 
				 post_response_text(tujuan, param, respog);
	}
}
function loadNotifikasi()
{
        proses="getNotifikasi";
        param="proses="+proses;
        tujuan="log_slave_save_po_lokal.php";
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
                                                        //displayList();
                                                        document.getElementById('notifikasiKerja').innerHTML=con.responseText;
                                        }
                        }
                        else {
                                        busy_off();
                                        error_catch(con.status);
                        }
                }
        }

}
function load_new_data()
{
        param='proses=update_data';
        tujuan='log_slave_save_po_lokal.php';
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
                                                                //  alert(con.responseText);
                                                                document.getElementById('contain').innerHTML=con.responseText;
                                                                //loadNotifikasi();
                                                        //displayList();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                                  }	
                         } 
        post_response_text(tujuan, param, respog);	
}
function fillField(nopo,tgl_po,supplier_id,sub_tot,disc,nil_ppn,grnd_tot,rek,npwp,diskon_nilai,stat,tglKrm)
{
        if(stat==1)
        {
                alert('Waiting for approval');
                return;
        }
        else if(stat==2)
        {
                alert("Approval finished");
                return;
        }
        else
        {
                        status_inputan=1;
            document.getElementById('no_po').value=nopo;
                        document.getElementById('tgl_po').value=tgl_po;
                        document.getElementById('supplier_id').value=supplier_id;
                        //document.getElementById('tmpt_krm').value=delivery_loc;
                        //document.getElementById('term_pay').value=cara_pem;
                        //document.getElementById('user_id').value=purchs;
                        document.getElementById('bank_acc').value=rek;
                        document.getElementById('npwp_sup').value=npwp;
                        rproses=document.getElementById('proses').value='edit_po';
                        dnopp=document.getElementById('no_po').value=nopo;
                        param='nopo='+dnopp+'&proses='+rproses;
                        tujuan='log_slave_po_lokal_detail.php';
                        /*alert(param);
                        return;*/
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

                                                                        show_form_po();
                                                                        ar=con.responseText.split("###");
                                                                        document.getElementById('ppDetailTable').innerHTML=ar[0];

                                                                        document.getElementById('total_harga_po').value=sub_tot;
                                                                        test=document.getElementById('total_harga_po');
                                                                        change_number(test);
                                                                        document.getElementById('diskon').value=disc;
                                                                        document.getElementById('ppn').value=nil_ppn;
                                                                        nppn=document.getElementById('ppn');
                                                                        change_number(nppn);
                                                                        document.getElementById('grand_total').value=grnd_tot;
                                                                        gr_total=document.getElementById('grand_total');
                                                                        change_number(gr_total);

                                                                        document.getElementById('tgl_krm').value=tglKrm;
                                                                        document.getElementById('nilai_diskon').value=diskon_nilai;
                                                                        document.getElementById('angDiskon').value=diskon_nilai;
                                                                        document.getElementById('hslPPn').innerHTML=nil_ppn;
                                                                        if(nil_ppn!=0)
                                                                        {
                                                                                document.getElementById('ppN').value=10;
                                                                        }
                                                                        else
                                                                        {
                                                                                document.getElementById('ppN').value=0;
                                                                        }
                                                                        if(ar[1]!="")
                                                                        {
                                                                        document.getElementById('tmpt_krm').value=ar[1];
                                                                        }
                                                                        if(ar[2]!="")
                                                                        {
                                                                        document.getElementById('term_pay').value=ar[2];
                                                                        }

                                                                        document.getElementById('ketUraian').value=ar[3];

                                                                        if(ar[4]!="")
                                                                        {
                                                                        document.getElementById('purchaser_id').value=ar[4];
                                                                        }
                                                                }
                                                        }
                                                        else {
                                                                busy_off();
                                                                error_catch(con.status);
                                                        }
                                                  }	
                                         } 
                        post_response_text(tujuan, param, respog);	
        }
}
status_inputan=0;
function cancel_headher()
{
        if(status_inputan==0)
            {
                nopo=document.getElementById('no_po').value;
                document.getElementById('proses').value='';
                ar=document.getElementById('proses');
                ar.value='delete_all';
                /*alert(document.getElementById('proses').value);
                return;*/
                ar=ar.value;
                param='nopo='+nopo+'&proses='+ar;
                /*alert(param);
                return;*/
                tujuan='log_slave_save_po.php';
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
                                                                displayList();
                                                                //document.getElementById('contain').innerHTML=con.responseText;

                                                        }
                                                }
                                                else {
                                                        busy_off();
                                                        error_catch(con.status);
                                                }
                                          }
                                 }
                post_response_text(tujuan, param, respog);
            }
            else
            {
                displayList();
            }

}
function delPoDetail(nopo,stat,StatIns)
{
        if(stat==1)
        {
                alert('Waiting for approval');
                return;
        }
        else
        {
                if(StatIns==0)
                {
                        if(confirm("Are you sure delete ?"))
                        { 
                          // alert("berhasil");
                                displayList();
                        }
                        else
                        {
                           return;
                        }		
                }
                else
                {
                                document.getElementById('proses').value='';
                                ar=document.getElementById('proses');
                                ar.value='delete_all';
                                /*alert(document.getElementById('proses').value);
                                return;*/
                                ar=ar.value;
                                param='nopo='+nopo+'&proses='+ar;
                                /*alert(param);
                                return;*/
                                tujuan='log_slave_save_po_lokal.php';
                                 if(confirm("Are you sure delete ? "))
                                 { 
                                        post_response_text(tujuan, param, respog);	
                                 }
                                 else
                                 {
                                         return;
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
                                                                                //document.getElementById('contain').innerHTML=con.responseText;
                                                                                displayList();
                                                                        }
                                                                }
                                                                else {
                                                                        busy_off();
                                                                        error_catch(con.status);
                                                                }
                                                          }	
                                                 }

                }
        }
}
function delPo(nopo,stat)
{
        if(stat==1)
        {
                alert('Waiting for approval');
                return;
        }
        else if(stat==2)
        {
                alert("Approval finished");
                return;
        }
        else
        {
                        document.getElementById('proses').value='';
                        ar=document.getElementById('proses');
                        ar.value='delete_all';
                //	alert(document.getElementById('proses').value);
//			return;
                        ar=ar.value;
                        param='nopo='+nopo+'&proses='+ar;
                        //alert(param);
//			return;
                        tujuan='log_slave_save_po_lokal.php';
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
                                                                        displayList();
                                                                        //document.getElementById('contain').innerHTML=con.responseText;

                                                                }
                                                        }
                                                        else {
                                                                busy_off();
                                                                error_catch(con.status);
                                                        }
                                                  }	
                                         } 
                        if(confirm('Are You Sure Delete This All Data'))
                        {
                                post_response_text(tujuan, param, respog);	
                        }
                        else
                        {
                                return;
                        }
        }
}
function agree_po()
{
        width='400';
        height='200';
        //nopp=document.getElementById('nopp_'+id).value;
        content="<div id=container></div>";
        ev='event';
        title="Penanda Tanganan";
        showDialog1(title,content,width,height,ev);
        //get_data_pp();	
}
function get_data_pp(npo)
{
        

        met=document.getElementById('proses').value;
        //rnopo=document.getElementById('no_po').value;
        rnopo=npo;
        met='get_form_approval';
        param='proses='+met+'&nopo='+rnopo;
        tujuan='log_slave_save_po.php';
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
                                                                        /*alert(con.responseText);
                                                                        return;*/
                                        agree_po();
                                                                                document.getElementById('container').innerHTML=con.responseText;
                                                                                return con.responseText;
                                                                }
                                                        }
                                                        else {
                                                                busy_off();
                                                                error_catch(con.status);
                                                        }
                                          }	
                         }
        post_response_text(tujuan, param, respog);


}
function forward_po()
{
        nik=document.getElementById('persetujuan_id').value;
        snopo=document.getElementById('rnopp').value;
        met=document.getElementById('proses');
        met=met.value='insert_forward_po';
        param='id_user='+nik+'&proses='+met+'&nopo='+snopo;
        tujuan='log_slave_save_po.php';
        //alert(param);
        //return;
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
                                                                closeDialog();
                                                                displayList();

                                                        }
                                                }
                                                else {
                                                        busy_off();
                                                        error_catch(con.status);
                                                }
                                  }	
                 } 	
                 post_response_text(tujuan, param, respog);	
}
function close_po_a()
{
        document.getElementById('close_po').style.display='block';
        document.getElementById('test').style.display='none';

}
function proses_release_po()
{
        //document.getElementById('snopo').value=nopo;
        id_user=document.getElementById('persetujuan_id').options[document.getElementById('persetujuan_id').selectedIndex].value;
        rnopo=document.getElementById('rnopo').value;
        param='nopo='+rnopo+'&proses=proses_release_po'+'&id_user='+id_user;
        tujuan='log_slave_save_po_lokal.php';
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
                                                                //document.getElementById('close_container').innerHTML=con.responseText;	
                                                                closeDialog();
                                                                displayList();							
                                                        }
                                                }
                                                else {
                                                        busy_off();
                                                        error_catch(con.status);
                                                }
                                  }	
                 } 	
                 post_response_text(tujuan, param, respog);	


}
function cancel_po()
{
        closeDialog();
        displayList();
}
/* Function deleteDelete(id)
 * Fungsi untuk menghapus data Detail
 * I : id row (urutan row pada table Detail)
 * P : Menghapus data pada tabel Detail
 * O : Menghapus baris pada tabel Detail
 */
 pengurang=6;
function deleteDetail(id) {
        var tbl = document.getElementById("ppDetailTable");
                var baris = tbl.rows.length;
                baris=baris-pengurang;
                //alert(baris);
                if(baris==1)
                {
                        nopo=document.getElementById('no_po').value;
                stat=0;
                        StatIns=1;
                        delPoDetail(nopo,stat,StatIns);
                }
   else if(baris>1)
                {
                        //alert(baris);
                        var detKode = document.getElementById('no_po');
                        var rkd_brg = document.getElementById('rkdbrg_'+id);
                        var nopp = document.getElementById('rnopp_'+id);
                        var purchas= document.getElementById('user_id');

                        param = "proses=detail_delete";
                        param += "&nopo="+detKode.value;
                        param += "&kd_brg="+rkd_brg.value;
                        param += "&nopp="+nopp.value;
                        param += "&purchaser="+purchas.value;

                        function respon(){
                                if (con.readyState == 4) {
                                        if (con.status == 200) {
                                                busy_off();
                                                if (!isSaveResponse(con.responseText)) {
                                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                                } else {
                                                        // Success Response

                                        //baris=row;
                                        row = document.getElementById("detail_tr_"+id);
                                        if(row) {
                                        row.style.display="none";
                                        document.getElementById('harga_satuan_'+id).value=0;
                                        document.getElementById('total_'+id).value=0;	
                                        document.getElementById('dtNopp_'+id).innerHTML="";
                                        document.getElementById('dtKdbrg_'+id).innerHTML="";
                                        document.getElementById('jmlhDiminta_'+id).value="";

                                                //pengurang+=1;
                                        plusAll();
                                        pengurang+=1;
                                        plusAll(id);
                                        /*subTot=document.getElementById('total_harga_po');
                                        subTot.value=remove_comma(subTot);

                                        totPeng=document.getElementById('total_'+id);
                                        totPeng.value=remove_comma(totPeng);
                                        total=parseFloat(subTot.value-totPeng.value);
                                                        tot.value=total;
                                                        change_number(tot);*/

                                        } else {
                                        alert("Row undetected");
                                        }
                                                                                        }
                                        } else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                                }
                        }

                                if(confirm('Are You Sure Delete This Data!!!'))
                                {
                                        post_response_text('log_slave_po_lokal_detail.php', param, respon);	
                                }
                                else
                                {
                                        return;
                                }
                }
}

function cariNopo()
{
        txtSearch=trim(document.getElementById('txtsearch').value);
        tglCari=trim(document.getElementById('tgl_cari').value);
        met=document.getElementById('proses');
        met='cari_nopo';
        met=trim(met);

        param='txtSearch='+txtSearch+'&tglCari='+tglCari+'&proses='+met;
        tujuan='log_slave_save_po_lokal.php';
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
                                                                document.getElementById('contain').innerHTML=con.responseText;							
                                                        }
                                                }
                                                else {
                                                        busy_off();
                                                        error_catch(con.status);
                                                }
                                  }	
                 }
                 post_response_text(tujuan, param, respog);

}
function cek_pembuat(nopo)
{
        rnop=nopo;
        //alert(rnop);
        param='nopo='+rnop+'&proses=cek_pembuat_po';
        tujuan='log_slave_save_po.php';
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
                                post_response_text(tujuan, param, respog);
}
function cariBast(num)
{
                param='proses=update_data';
                param+='&page='+num;
                tujuan = 'log_slave_save_po_lokal.php';
                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('contain').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}
function release_po(id)
{
        nopo=document.getElementById('td_nopo_'+id).innerHTML;
//	alert(nopo);
        param='nopo='+nopo+'&proses=insert_release_po';
        tujuan='log_slave_save_po_lokal.php';
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
                                                                                agree_po();
                                                                                //alert(con.responseText);
                                                                                document.getElementById('container').innerHTML=con.responseText;
                                                                                return con.responseText;
                                                                }
                                                        }
                                                        else {
                                                                busy_off();
                                                                error_catch(con.status);
                                                        }
                                          }	
                         }
        post_response_text(tujuan, param, respog);
}
function searchSupplier(title,content,ev)
{
        width='500';
        height='400';
        showDialog1(title,content,width,height,ev);
        //alert('asdasd');
}
function findSupplier()
{
    nmSupplier=document.getElementById('nmSupplier').value;
    param='proses=getSupplierNm'+'&nmSupplier='+nmSupplier;
    tujuan='log_slave_save_po_lokal.php';
    post_response_text(tujuan, param, respog);			

    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                                  document.getElementById('containerSupplier').innerHTML=con.responseText;
                        }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }	
}
function setData(kdSupp)
{
    l=document.getElementById('supplier_id');

    for(a=0;a<l.length;a++)
        {
            if(l.options[a].value==kdSupp)
                {
                    l.options[a].selected=true;
                }
        }
       closeDialog();
           get_supplier();
}

function normalmiscppn(idkebrp){
    switch(idkebrp){
        case 0:
            satu=document.getElementById('miscNppn');
            satu.value=remove_comma_var(satu.value);
        break;
        case 1:
            satu=document.getElementById('ongKirim');
            satu.value=remove_comma_var(satu.value);
        break;
        case 2:
            satu=document.getElementById('misc');
            satu.value=remove_comma_var(satu.value);
        break;
    }
    
}

function calculateMiscPpn(idkebrp){
    sb_tot=document.getElementById('total_harga_po');
    sb_tot.value=remove_comma(sb_tot);//angDiskon
    disc=document.getElementById('angDiskon');
    disc.value=remove_comma(disc);
    if(isNaN(disc.value)){
        disc.value=0;
    }
    diskon=disc.value;
    sbtotal=sb_tot.value;
    nilP=document.getElementById('ppN').value;
    subtotal=(parseFloat(sbtotal)-parseFloat(diskon))
    
    switch(idkebrp){
        case 0:
            miscPPn=document.getElementById('miscNppn');
            miscPPn.value=remove_comma(miscPPn);
            onkrm=document.getElementById('ongKirim');
            onkrm.value=remove_comma(onkrm);
            msc=document.getElementById('misc');
            msc.value=remove_comma(msc);
            if((isNaN(msc.value)||(msc.value==''))){
                msc.value=0;
            }
            onkppn=document.getElementById('ongKirimPPn').value;
            if(isNaN(onkppn)){
                onkppn=0;
            }else{
                onkppn=(parseFloat(onkppn)/100);
            }
            onkKrmdnppn=parseFloat(onkrm.value)*parseFloat(onkppn);
            //misSub=parseFloat(subtotal)+parseFloat(miscPPn.value); FA 20190306 - miscPPn sudah ada PPn nya
            misSub=parseFloat(subtotal);
            totPlppn=parseFloat(misSub)*(parseFloat(nilP)/100);
            if(isNaN(onkKrmdnppn)){
                grandtotal=parseFloat(misSub)+parseFloat(totPlppn)+parseFloat(msc.value);
            }else{
                grandtotal=parseFloat(misSub)+parseFloat(totPlppn)+parseFloat(msc.value)+(parseFloat(onkKrmdnppn)+parseFloat(onkrm.value));
            }
            document.getElementById('hslPPn').innerHTML=totPlppn;
            document.getElementById('ppn').value=totPlppn;
            grandTota=document.getElementById('grand_total');
            grandTota.value=remove_comma(grandTota);//
            grandTota.value=grandtotal;
            change_number(sb_tot);
            change_number(miscPPn);
            change_number(grandTota);
        break;
        case 1 :
        case 2:
            //ongKirim
            miscPPn2=document.getElementById('ppn');
            miscPPn=document.getElementById('miscNppn');
            miscPPn.value=remove_comma(miscPPn);
            miscPPn2.value=remove_comma(miscPPn2);
            diskon=disc.value;
            onkrm=document.getElementById('ongKirim');
            onkrm.value=remove_comma(onkrm);
            msc=document.getElementById('misc');
            msc.value=remove_comma(msc);
            if((isNaN(msc.value)||(msc.value==''))){
                msc.value=0;
            }
            onkppn=document.getElementById('ongKirimPPn').value;
            if(isNaN(onkppn)){
                onkppn=0;
            }else{
                onkppn=(parseFloat(onkppn)/100);
            }
            onkKrmdnppn=parseFloat(onkrm.value)*parseFloat(onkppn);
            if(isNaN(onkKrmdnppn)){
                totByKrm=parseFloat(onkrm.value);
            }else{
                totByKrm=parseFloat(onkrm.value)+parseFloat(onkKrmdnppn);
            }
            misSub=parseFloat(subtotal);
            totPlppn=parseFloat(misSub)*(parseFloat(nilP)/100);
            grandtotal=parseFloat(misSub)+parseFloat(totPlppn)+parseFloat(msc.value)+parseFloat(totByKrm)+parseFloat(miscPPn.value);
            grandTota=document.getElementById('grand_total');
            grandTota.value=remove_comma(grandTota);//
            grandTota.value=grandtotal;
            change_number(sb_tot);
            change_number(miscPPn);
            change_number(onkrm);
            change_number(msc);
            change_number(grandTota);
        break;
        
            
    }
    
}
function calculateOngkirPPn(){
  calculateMiscPpn(1);
}

function searchBrg(title,content,ev,focus)
{
        width='500';
        height='400';
        showDialog1(title,content,width,height,ev);
        //alert('asdasd');
}

function findBrg()
{
        txt=trim(document.getElementById('no_brg').value);
        if(txt=='')
        {
                alert('Text is obligatory');
        }
        else if(txt.length<3)
        {
                alert('Too short words');
        }
        else
        {
                param='txtfind='+txt+'&method=cariBarangDlmDtBs';
                tujuan='log_slave_save_log_pp.php';
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

function setBrg(no_brg,namabrg,satuan,nomor)
{
    nomor=document.getElementById('nomor').value;
    document.getElementById('kd_brg_'+nomor).value=no_brg;
    document.getElementById('oldKdbrg_'+nomor).value=no_brg;
    document.getElementById('nm_brg_'+nomor).value=namabrg;
    document.getElementById('sat_'+nomor).value=satuan;
    closeDialog();
}

function getKurs()
{
        mtung=document.getElementById('mtUang').options[document.getElementById('mtUang').selectedIndex].value;
        tgl=document.getElementById('tgl_po').value;
        param='mtUang='+mtung+'&proses=getKurs'+'&tglpo='+tgl;
        tujuan='log_slave_save_po.php';
        post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('Kurs').value=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}