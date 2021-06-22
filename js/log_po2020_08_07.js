// JavaScript Document
function clear_all_data(){
        status_inputan=0;
        document.getElementById('no_po').value='';
        document.getElementById('supplier_id').value='';
        //document.getElementById('tgl_krm').value='';
        //document.getElementById('tmpt_krm').value='';
        document.getElementById('bank_acc').value='';
        document.getElementById('npwp_sup').value='';
        document.getElementById('txtsearch').value='';
        document.getElementById('tgl_cari').value='';
        document.getElementById('proses').value='insert';
        document.getElementById('tgl_krm').value='';
        document.getElementById('term_pay').value='';
        document.getElementById('ketUraian').value='';
        document.getElementById('mtUang').value='';
        document.getElementById('persetujuan_id').value='';
}
function show_list_pp(){
        clear_all_data();

        document.getElementById('container_pp').innerHTML='';
        document.getElementById('list_po').style.display='none';
    document.getElementById('list_pp').style.display='block';
        document.getElementById('form_po').style.display='none';
        document.getElementById('kode_pt').value='';

                var tbl = document.getElementById("list_pp_table");
        var row = tbl.rows.length;
        row=row-2;
        for(i=1;i<=row;i++)
        {
         document.getElementById('plh_pp_'+i).checked=false;
        }
}

function cek_pp_pt(kdpt){
        clear_all_data();

   if(kdpt==''){

    kode_pt=document.getElementById('kode_pt').options[document.getElementById('kode_pt').selectedIndex].value;
   }
   else{
           show_list_pp();
           kode_pt=kdpt;
           document.getElementById('kode_pt').disabled=true;
           document.getElementById('kode_pt').value=kdpt;
   }
    user_id=trim(document.getElementById('user_id').value);
    param='kodept='+kode_pt+'&id_user='+user_id;
    param+="&proses=listPp";
  // alert(param);
//    return;
     tujuan='log_slave_po_detail.php';

    function respog(){
      if(con.readyState==4){
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                        alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                       //alert(con.responseText);
                        //show_form_po();
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
        {sat=document.getElementById('harga_satuan_'+id);
                change_number(sat);
                grnd_total();       
        }
        else
        {
                nilDis=document.getElementById('angDiskon');
                change_number(nilDis);
        }
}
function normal_number(id){
                satu=document.getElementById('harga_satuan_'+id);
                satu.value=remove_comma_var(satu.value);
}

function calculate(id){
    defult_tot=document.getElementById('realisasi_'+id).value;
    jmlh_brg=document.getElementById('jmlhDiminta_'+id).value;
    harga=document.getElementById('harga_satuan_'+id).value;

                if((parseFloat(jmlh_brg))>=(parseFloat(defult_tot)+1)){
                        alert('Quantity must equal or lower then total requested');
                        document.getElementById('jmlhDiminta_'+id).value='';
                        return;		
                }
                else{
                        if(jmlh_brg==''||harga=='')
                        {
                                a=document.getElementById('total_'+id);
                                a.value='';
                                a=parseFloat(a.value);
                        }
                        else
                        {
                                        harg=document.getElementById('harga_satuan_'+id);
                                        harg.value=remove_comma_var(harg.value);
					jmlh_sub=jmlh_brg*parseFloat(Math.round(harg.value*100)/100);
                                        if(isNaN(jmlh_sub)){
                                           jmlh_sub=0;
                                         }
                                        if(jmlh_sub==0){
                                                document.getElementById('total_'+id).value=0;
                                        }
                                        else{
                                                        as=document.getElementById('total_'+id);
                                                        as.value=jmlh_sub
                                                        change_number(as);
                                        }

                        }
                }
                //grnd_total();
                grnd_total();
                //grandTotal();

}

function grnd_total(){
    var tbl = document.getElementById("detailBody");
    var row = tbl.rows.length;
    row=row-9;
   total=0;
   for(i=0;i<row;i++)
       {
		    console.log("dillah : "+i);
		    if( document.getElementById('total_'+i) !== null){
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
       }
           document.getElementById('total_harga_po').value=total;
           tot=document.getElementById('total_harga_po');
           tot.value=total;
           change_number(tot);
           grandTotal();
}

function plusAll(id){
        grnd_total();
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
    nilPh=document.getElementById('ppH').value;
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
            totPlpph=parseFloat(misSub)*(parseFloat(nilPh)/100);
            if(isNaN(onkKrmdnppn)){
                grandtotal=parseFloat(misSub)+parseFloat(totPlppn)+parseFloat(msc.value)-parseFloat(totPlpph);
            }else{
                grandtotal=parseFloat(misSub)+parseFloat(totPlppn)+parseFloat(msc.value)+(parseFloat(onkKrmdnppn)+parseFloat(onkrm.value))-parseFloat(totPlpph);
            }
            document.getElementById('hslPPn').innerHTML=totPlppn;
            document.getElementById('ppn').value=totPlppn;
			document.getElementById('hslPPh').innerHTML=totPlpph;
            document.getElementById('pph').value=totPlpph;
            grandTota=document.getElementById('grand_total');
            grandTota.value=remove_comma(grandTota);//
            grandTota.value=grandtotal;
            change_number(sb_tot);
            change_number(miscPPn);
            change_number(grandTota);
        break;
        case 1:
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
            if(parseFloat(nilP) < 0){
                nilP = 0;
            }
            if(parseFloat(nilPh) < 0){
                nilPh = 0;
            }
            totPlppn=parseFloat(misSub)*(parseFloat(nilP)/100);
            totPlpph=parseFloat(misSub)*(parseFloat(nilPh)/100);
            grandtotal=parseFloat(misSub)+parseFloat(totPlppn)+parseFloat(msc.value)+parseFloat(totByKrm)+parseFloat(miscPPn.value)-parseFloat(totPlpph); // FA 20200203
            grandTota=document.getElementById('grand_total');
            grandTota.value=remove_comma(grandTota);//
            grandTota.value=grandtotal;
            change_number(sb_tot);
            change_number(miscPPn);
            change_number(onkrm);
        //     change_number(msc);
            change_number(grandTota);
        break;
        
            
    }
    
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
function calculateOngkirPPn(){
  calculateMiscPpn(1);
}
function calculate_diskon(){
        sb_tot=document.getElementById('total_harga_po');
        sb_tot.value=remove_comma_var(sb_tot.value);
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
                if(nil_dis>100)
                {	
                        alert('Discount must lower than 100%');
                        document.getElementById('diskon').value='';
                        document.getElementById('angDiskon').disabled=false;
                }
                else
                {
                        disc=(nil_dis*sb_tot.value)/100;
                }
                 //  	grnd_tot=(sb_tot.value-disc)+pn;
                        //document.getElementById('angDiskon').value=disc;
                        nilaiDis=document.getElementById('angDiskon');
                        nilaiDis.value=disc;
                        document.getElementById('nilai_diskon').value=disc;
                        change_number(nilaiDis);
                        calculateMiscPpn(0);
                        //grandTotal();
        }     
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
                        alert("Discount value is wrong");
                        document.getElementById('angDiskon').value='';
                        document.getElementById('diskon').value='';
                        document.getElementById('nilai_diskon').value='';
                        document.getElementById('diskon').disabled=false;
                }

                //nilDiskon=document.getElementById('angDiskon').value;
        calculateMiscPpn(0);
        //grandTotal();
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
        miscPPn=document.getElementById('miscNppn');
        miscPPn.value=remove_comma(miscPPn);
        //alert(reg);
        idbr=0;
        if(reg.test(nilP))
        {
                if(nilP==10)
                {
                        dis.value=remove_comma(dis);
                        subTot.value=remove_comma(subTot);
                        pn=((parseFloat((subTot.value-dis.value))+parseFloat(miscPPn.value*0.9))*10)/100;	
                        document.getElementById('hslPPn').innerHTML=pn;
                        document.getElementById('ppn').value=pn;
                }

                else if(nilP==0)
                {
                        dis.value=remove_comma(dis);
                        subTot.value=remove_comma(subTot);
                        pn=((parseFloat((subTot.value-dis.value))+parseFloat(miscPPn.value*0.9))*nilP)/100;	
                        document.getElementById('hslPPn').innerHTML=nilP;
                        document.getElementById('ppn').value=pn;
                }	
                else if(nilP==2)
                {
                        dis.value=remove_comma(dis);
                        subTot.value=remove_comma(subTot);
                        pn=((parseFloat((subTot.value-dis.value))+parseFloat(miscPPn.value*0.9))*nilP)/100;	
                        document.getElementById('hslPPn').innerHTML=nilP;
                        document.getElementById('ppn').value=pn;
                }	
        }
        else
        {
                alert("Please provide number between 0 and 10");
                document.getElementById('hslPPn').value='';
                document.getElementById('ppn').value='';
                return;

        }
                calculateMiscPpn(idbr);
                //grandTotal();
}
function calculatePph()
{
        var reg = /^[0-9]{1,2}$/;
        nilP=document.getElementById('ppH').value;
        dis=document.getElementById('nilai_diskon');
        subTot=document.getElementById('total_harga_po');
        miscPPn=document.getElementById('miscNppn');
        miscPPn.value=remove_comma(miscPPn);
        //alert(reg);
        idbr=0;
        if(reg.test(nilP))
        {
                if(nilP==10)
                {
                        dis.value=remove_comma(dis);
                        subTot.value=remove_comma(subTot);
                        pn=((parseFloat((subTot.value-dis.value))+parseFloat(miscPPn.value*0.9))*10)/100;	
                        document.getElementById('hslPPh').innerHTML=pn;
                        document.getElementById('pph').value=pn;
                }

                else if(nilP==0)
                {
                        dis.value=remove_comma(dis);
                        subTot.value=remove_comma(subTot);
                        pn=((parseFloat((subTot.value-dis.value))+parseFloat(miscPPn.value*0.9))*nilP)/100;	
                        document.getElementById('hslPPh').innerHTML=nilP;
                        document.getElementById('pph').value=pn;
                }	
                else if(nilP==2)
                {
                        dis.value=remove_comma(dis);
                        subTot.value=remove_comma(subTot);
                        pn=((parseFloat((subTot.value-dis.value))+parseFloat(miscPPn.value*0.9))*nilP)/100;	
                        document.getElementById('hslPPh').innerHTML=nilP;
                        document.getElementById('pph').value=pn;
                }	
        }
        else
        {
                alert("Please provide number between 0 and 10");
                document.getElementById('hslPPh').value='';
                document.getElementById('pph').value='';
                return;

        }
                calculateMiscPpn(idbr);
                //grandTotal();
}
nilPpn=0;
nilPph=0;
function grandTotal(){
        sb_tot=document.getElementById('total_harga_po');
        sb_tot.value=remove_comma(sb_tot);
        
        nilDiskon=document.getElementById('angDiskon');
        ppn=document.getElementById('ppN');
        pph=document.getElementById('ppH');
        if(nilDiskon.value!=""||nilDiskon.value!=0)
        {
                nilDiskon.value=remove_comma(nilDiskon);
                //nilPpn=document.getElementById('ppn').value;
        }
        else
        {
                document.getElementById('diskon').value=0;
                nilDiskon.value=0;
        }
        
            grnd_tot=parseFloat((sb_tot.value-nilDiskon.value));//miscNppn
            miscPPn=document.getElementById('miscNppn');
            miscPPn.value=remove_comma(miscPPn);
            onkrm=document.getElementById('ongKirim');
            onkrm.value=remove_comma(onkrm);
            msc=document.getElementById('misc');
            msc.value=remove_comma(msc);
            if((isNaN(msc.value)||(msc.value==''))){
                msc.value=0;
            }
            if((isNaN(miscPPn.value)||(miscPPn.value==''))){
                miscPPn.value=0;
            }
            onkppn=document.getElementById('ongKirimPPn').value;
            if(isNaN(onkppn)){
                onkppn=0;
            }else{
                onkppn=(parseFloat(onkppn)/100);
            }
            onkKrmdnppn=parseFloat(onkrm.value)*parseFloat(onkppn);
            misSub=parseFloat(grnd_tot)+parseFloat(miscPPn.value*0.9);
            totPlppn=parseFloat(misSub)*(parseFloat(ppn.value)/100);
            totPlpph=parseFloat(misSub)*(parseFloat(pph.value)/100);
            if(isNaN(onkKrmdnppn)){
                grandtotal=parseFloat(misSub)+parseFloat(totPlppn)+parseFloat(msc.value)-parseFloat(totPlpph);
				//alert(totPlpph); 
            }else{
                grandtotal=parseFloat(misSub)+parseFloat(totPlppn)+parseFloat(msc.value)+(parseFloat(onkKrmdnppn)+parseFloat(onkrm.value))-parseFloat(totPlpph);
				//alert(totPlpph);
            }
            if(ppn.value!=0||ppn.value!=''){
                document.getElementById('hslPPn').innerHTML=totPlppn;
                document.getElementById('ppn').value=totPlppn;
            }
            else{
                document.getElementById('ppN').value=0;
                document.getElementById('ppn').value=0;
                document.getElementById('hslPPn').innerHTML=0;
                nilPpn=0;
            }
			if(pph.value!=0||pph.value!=''){
                document.getElementById('hslPPh').innerHTML=totPlpph;
                document.getElementById('pph').value=totPlpph;
            }
            else{
                document.getElementById('ppH').value=0;
                document.getElementById('pph').value=0;
                document.getElementById('hslPPh').innerHTML=0;
                nilPph=0;
            }
        total=document.getElementById('grand_total');
        total.value=grandtotal;
        change_number(sb_tot);
        change_number(nilPpn);
        change_number(nilPph);
        change_number(total);			
}
function process()
{
        clear_all_data();
        var tbl = document.getElementById("container_pp");
        var row = tbl.rows.length;
        row=row-1;
        //alert(row);
        strUrl = '';
        for(i=1;i<=row;i++)
        {
                ar=document.getElementById('plh_pp_'+i);
                if(ar.checked==true)
                {         
                        try{
                                if(strUrl != '')
                                {
                                        strUrl += '&nopp[]='+trim(document.getElementById('nopp_x'+i).innerHTML)
                                                +'&kdbrg[]='+trim(document.getElementById('kdbrg_'+i).innerHTML);
                                }
                                else
                                {
                                        strUrl += '&nopp[]='+trim(document.getElementById('nopp_x'+i).innerHTML)
                                                +'&kdbrg[]='+trim(document.getElementById('kdbrg_'+i).innerHTML);
                                }
                        }
                        catch(e){}

                }
        }
        console.log(strUrl);
        //return;
        if(strUrl=='')
        {
                alert('Choose one');
                return;
        }
        else
        {
                kodePt=document.getElementById('kode_pt').options[document.getElementById('kode_pt').selectedIndex].value;
                param="proses=createTable"+"&baris="+row+'&kode_pt='+kodePt;
                param+=strUrl;
                //alert(param);
                tujuan='log_slave_po_detail.php';
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
                                document.getElementById('dataAtas').style.display='none';
                                show_form_po();
                                var a=con.responseText.split("###");
                                console.log(a);
                                document.getElementById('no_po').value=a[0];
                                document.getElementById('ppDetailTable').innerHTML=a[1];
                                // loadNotifikasi2();
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
    document.getElementById('dataAtas').style.display='block';
    document.getElementById('list_po').style.display='block';
    document.getElementById('list_pp').style.display='none';
    document.getElementById('form_po').style.display='none';
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
function save_headher(){
                var tbl = document.getElementById("ppDetailTable");
                var row = tbl.rows.length;
                row=row-9;
                strUrl2 = '';
                for(i=0;i<row;i++){
                    try{

                            if(strUrl2 != ''){					
                                //+'&kdbrg[]='+encodeURIComponent(trim(document.getElementById('rkdbrg_'+i).value))
                                    strUrl2 +='&nopp[]='+trim(document.getElementById('rnopp_'+i).value)
                                    +'&spekBrg[]='+encodeURIComponent(trim(document.getElementById('spek_brg_'+i).value))
                                    +'&rjmlh_psn[]='+encodeURIComponent(trim(document.getElementById('jmlhDiminta_'+i).value))
                                    +'&rhrg_sat[]='+document.getElementById('harga_satuan_'+i).value
                                    +'&kdbrg[]='+encodeURIComponent(trim(document.getElementById('rkdbrg_'+i).value))
                                    +'&rsatuan_unit[]='+encodeURIComponent(trim(document.getElementById('sat_'+i).value));
                            }
                            else{
                                    strUrl2 +='&nopp[]='+trim(document.getElementById('rnopp_'+i).value)
                                    +'&spekBrg[]='+encodeURIComponent(trim(document.getElementById('spek_brg_'+i).value))
                                    +'&rjmlh_psn[]='+encodeURIComponent(trim(document.getElementById('jmlhDiminta_'+i).value))
                                    +'&rhrg_sat[]='+document.getElementById('harga_satuan_'+i).value
                                    +'&kdbrg[]='+encodeURIComponent(trim(document.getElementById('rkdbrg_'+i).value))
                                    +'&rsatuan_unit[]='+encodeURIComponent(trim(document.getElementById('sat_'+i).value));

                           }
                    }
                        catch(e){}

                }
                //alert(document.getElementById('nopp_1').value);
                //alert(strUrl2);
                //grandTotal();
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
				nil_pph=document.getElementById('pph');
                nil_pph.value=remove_comma(nil_pph);
                nil_pph=nil_pph.value;
                tgl_deliver=document.getElementById('tgl_krm').value;
                delivery_loc=document.getElementById('tmpt_krm').value;
                cara_pem=document.getElementById('term_pay').value;
                grnd_tot=document.getElementById('grand_total');
                grnd_tot.value=remove_comma(grnd_tot);
                grnd_tot=grnd_tot.value;
                purchs=document.getElementById('user_id').value;
                lokasi_peng=document.getElementById('tmpt_krm').value;
                nil_diskon=document.getElementById('nilai_diskon').value;
                rproses=document.getElementById('proses').value;
                rek=document.getElementById('bank_acc').value;
                npwp=document.getElementById('npwp_sup').value;
                ketUrai=trim(document.getElementById('ketUraian').value);
                mataUang=document.getElementById('mtUang').options[document.getElementById('mtUang').selectedIndex].value;
                krs=trim(document.getElementById('Kurs').value);
                
                ttd=document.getElementById('persetujuan_id').options[document.getElementById('persetujuan_id').selectedIndex].value;
                ttd2=document.getElementById('persetujuan_id2').value;
                if(lokasi_peng==""){
                        lokasi_peng=document.getElementById('tmpt_krm1').options[document.getElementById('tmpt_krm1').selectedIndex].value;
                        lokasi_peng=parseInt(lokasi_peng);
                }
				
				 //indra tambahan : ongkrm=ongkrm.value; dan miscdt=miscdt.value dan miscppn=miscppn.value;
				
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
                param+='&diskon='+disc+'&ppn='+nil_ppn+'&pph='+nil_pph+'&tgl_krm='+tgl_deliver+'&lok_kirim='+delivery_loc+'&cara_pembayarn='+cara_pem+'&nildiskon='+nil_diskon; 
                param+='&proses='+rproses+'&rek='+rek+'&npwp='+npwp+'&ketUraian='+ketUrai;
                param+='&mtUang='+mataUang+'&Kurs='+krs+'&id_user='+ttd+'&ttd2='+ttd2+'&ongkirim='+ongkrm;
                param+='&miscppn='+miscppn+'&ongKirimPPn='+ongkrmppn+'&misc='+miscdt+'&crByr='+crbayar;
                param+=strUrl2;
              //  alert(param);
        //	return;Byr
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
                                                        //document.getElementById('contain').innerHTML=con.responseText;
                                                        document.getElementById('dataAtas').style.display='block';
                                                        displayList();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                                  }	
                         } 
                         if(confirm("Saving on :"+mataUang+' currency, are you sure?'))
                         {
                             post_response_text(tujuan, param, respog);	
                         }

}
function load_new_data()
{
        param='proses=update_data';
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
                                                                //  alert(con.responseText);
                                                                document.getElementById('contain').innerHTML=con.responseText;
                                                                loadNotifikasi();
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
function fillField(nopo,tgl_po,supplier_id,sub_tot,disc,nil_ppn,grnd_tot,rek,npwp,diskon_nilai,stat,tglKrm,matauang,angKurs,ttd,loKrm,tandtgn2,ongangkut,miscdata,miscppndata,ppnongangkut,nil_pph)
{

        if(stat==3){
                alert("This PO has been released");
                exit();
        }
        else
        {
                        status_inputan=1;
                        document.getElementById('dataAtas').style.display='none';
                        document.getElementById('no_po').value=nopo;
                        document.getElementById('tgl_po').value=tgl_po;
                        document.getElementById('supplier_id').value=supplier_id;
                        document.getElementById('bank_acc').value=rek;
                        document.getElementById('npwp_sup').value=npwp;
                        rproses=document.getElementById('proses').value='edit_po';
                        dnopp=document.getElementById('no_po').value=nopo;
                        param='nopo='+dnopp+'&proses='+rproses;
                        tujuan='log_slave_po_detail.php';
                        
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
                                                                        document.getElementById('pph').value=nil_pph;
                                                                        /*nppn=document.getElementById('ppn');
                                                                        change_number(nppn);*/
                                                                        document.getElementById('grand_total').value=grnd_tot;
             /*                       gr_total=document.getElementById('grand_total');
                                    change_number(gr_total);
                        */												document.getElementById('tgl_krm').value=tglKrm;
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
																		document.getElementById('hslPPh').innerHTML=nil_pph;
																		if(nil_pph!=0)
                                                                        {
                                                                                //document.getElementById('ppH').value=10;
                                                                            document.getElementById('ppH').value=(nil_pph/(sub_tot-diskon_nilai))*100;;
                                                                        }
                                                                        else
                                                                        {
                                                                                document.getElementById('ppH').value=0;
                                                                        }
                                                                        document.getElementById('mtUang').value=matauang;
                                                                        document.getElementById('Kurs').value=angKurs;
                                                                        document.getElementById('persetujuan_id').value=ttd;
                                                                        document.getElementById('persetujuan_id2').value=tandtgn2;
                                                                        document.getElementById('tmpt_krm').value=loKrm;
                                                                        document.getElementById('term_pay').value=ar[1];
                                                                        document.getElementById('ketUraian').value=ar[2];
                                                                        document.getElementById('ongKirim').value=ongangkut;
                                                                        document.getElementById('misc').value=miscdata;
                                                                        document.getElementById('miscNppn').value=miscppndata;
                                                                        document.getElementById('ongKirimPPn').value=ppnongangkut;
                                                                        //document.getElementById('proses').value="editHeader";

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
        //}
}
function loadNotifikasi()
{
        proses="getNotifikasi";
        param="proses="+proses;
        tujuan="log_slave_save_po.php";
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
                alert('Waiting Approval');
                return;
        }
        else
        {
                if(StatIns==0)
                {
                        if(confirm("Deleting, Are you sure?"))
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
                                if(confirm("Deleting, are you sure"))
                                 { 
                                        post_response_text(tujuan, param, respog);	
                                 }
                                 else
                                 {
                                         return;
                                 }
                }
        }
}
function delPo(nopo,stat)
{
/*	if(stat==1)
        {
                alert('Menunggu Persetujuan');
                return;
        }
        else if(stat==2)
        {
                alert("Porses Persetujuan Sudah Selesai");
                return;
        }
*/	
        if(stat==2)
        {
                alert('Being on correction progress');
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
                        if(confirm('Are you sure delete this PO and it`s items'))
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
        title="Persetujuan Atau Penolakan Form";
        showDialog1(title,content,width,height,ev);
        //get_data_pp();	
}
function koreksiForm(npo)
{
        width='400';
        height='160';
        //nopp=document.getElementById('nopp_'+id).value;
        content="<div id=isi></div>";
        ev='event';
        title=" Koreksi No PO  :"+npo;
        showDialog1(title,content,width,height,ev);
        //get_data_pp();	
}
function getKoreksi(npo)
{
        met=document.getElementById('proses').value;
        //rnopo=document.getElementById('no_po').value;
        rnopo=npo;
        met='getKoreksi';
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
                                        koreksiForm(npo);
                                                                                document.getElementById('isi').innerHTML=con.responseText;
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
function get_data_pp(npo)
{
        /*tbl=document.getElementById('ppDetailTable');
        row=tbl.rows.length;
        row=row-6;
       //alert(row);
        for(i=0;i<row;i++)
            {
                jmlh=document.getElementById('jmlhDiminta_'+i).value;
                harg_satuan=document.getElementById('harga_satuan_'+i).value;
                disk=document.getElementById('diskon').value;
                supp_id=document.getElementById('supplier_id').value;
                tgl_krm=document.getElementById('tlg_krm').value;
                loc_kirim=document.getElementById('tmpt_krm').value;
                paym_term=document.getElementById('term_pay').value;
                realis=document.getElementById('realisasi_'+i).value;
                kd_brg=document.getElementById('rkdbrg_'+i).value;
                if((jmlh=='')||(harg_satuan=='')||(disk=='')||(supp_id=='')||(tgl_kirim='')||(paym_term==''))
                    {
                        alert('Please Complete The Form First');
                        return;
                    }
            }*/

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
        rnopo=document.getElementById('snopo').value;
        param='nopo='+rnopo+'&proses=proses_release_po';
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
                                                                //document.getElementById('close_container').innerHTML=con.responseText;	
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
function clearRow(id)
{
        /*document.getElementById('harga_satuan_'+id).value=0;
        document.getElementById('total_'+id).value=0;	
        document.getElementById('rnopp_'+id).value='';
        document.getElementById('rkdbrg_'+id).value='';
        document.getElementById('jmlhDiminta_'+id).value='';*/
        //alert(id);
        tabel=document.getElementById("detailBody");
        tabel.removeChild(tabel.rows[id]);

}
/* Function deleteDelete(id)
 * Fungsi untuk menghapus data Detail
 * I : id row (urutan row pada table Detail)
 * P : Menghapus data pada tabel Detail
 * O : Menghapus baris pada tabel Detail
 */
 pengurang=5;
function deleteDetail(id) {
        var tbl = document.getElementById("detailBody");
                var baris = tbl.rows.length;
                baris=baris-9;
        //	alert(baris);
                //return;
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

                        //alert(tabel.rows[id]);

                        //tabel.removeChild(tabel.rows[id]);
                        //elem.parentNode.removeChild(elem);
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
                                        //alert(id);
                                        //baris=row;
                                        //tabel=document.getElementById("detailBody");
                                        //tabel.removeChild(tabel.rows[id]);

                                        row = document.getElementById("detail_tr_"+id);
                                        if(row) 
                                        {
                                                //
                                                document.getElementById('harga_satuan_'+id).value=0;
                                                document.getElementById('total_'+id).value=0;	
                                                document.getElementById('dtNopp_'+id).innerHTML="";
                                                document.getElementById('dtKdbrg_'+id).innerHTML="";
                                                document.getElementById('jmlhDiminta_'+id).value="";
                                                row.style.display="none";
                                                //pengurang+=1;
                                                plusAll();
                                        } 
                                        else 
                                        {
                                                alert("Row undetected");
                                        }

                                        }
                                        } else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                                }
                        }

                                if(confirm('Deleting, are you sure?'))
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
        met=met.value='cari_nopo';
        met=trim(met);

        param='txtSearch='+txtSearch+'&tglCari='+tglCari+'&proses='+met;
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
                tujuan = 'log_slave_save_po.php';
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
function checkIt(id)
{
        brs=document.getElementById('plh_pp_'+id);
        if(brs.checked!=true)
        {
                brs.checked=true;
        }
        else if(brs.checked==true)
        {
                brs.checked=false;
        }

}
/*function checkStat(id)
{
        ar=document.getElementById('plh_pp_'+id);
        if(ar.checked==true)
        {
                ar.checked==true;
        }
        else if(ar.checked!=true)
        {
                ar.checked==false;
        }
}
*/
function doneKoreksi()
{
        rnopo=document.getElementById('rnopp').value;
        param='nopo='+rnopo+'&proses=updateKoreksi';
        tujuan='log_slave_save_po.php';
        if(confirm("Correction confirmation?"))
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
                                                displayList();
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
function berubahForm(){
    pild=document.getElementById('crByr');
    pild=pild.options[pild.selectedIndex].value;
    dtpay=document.getElementById('term_pay');
    if(pild=='CASH'){
        dtpay.value="";
        dtpay.disabled=true;
    }else{
        dtpay.disabled=false;
    }
}

function validat(ev)
{
  key=getKey(ev);
  if(key==13){
    cariNopo();
  } else {
  return tanpa_kutip_dan_sepasi(ev);	
  }	
}