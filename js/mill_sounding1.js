// JavaScript Document

function get_kd(notrans)

{

        //alert("test");

        if(notrans=='')

        {

                jns_id=document.getElementById('jns_vhc').value;

                traksi_id=document.getElementById('kodetraksi').value;

                strAll='jns_id='+jns_id+'&traksi_id='+traksi_id+'&proses=getKodeVhc';

        }

        else

        {

                /*jnsid=jns;

                kd_vhc=kdvhc;*/

                strAll='no_trans='+notrans;

                strAll+='&proses=getKodeVhc';



        }

    //alert(param);

        param=strAll;

        //alert(param);

        tujuan='vhc_slave_save_sounding.php';

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

                                                        document.getElementById('kde_vhc').innerHTML=con.responseText;

                                                        load_data_pekerjaan();

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

function numberWithCommas(xx) {
        /*
        var parts=n.toString().split(".");
        return parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",") + (parts[1] ? "." + parts[1] : "");
        */
       var	reverse = xx.toString().split('').reverse().join(''),
       ribuan 	= reverse.match(/\d{1,3}/g);
       var ribuan = ribuan.join('.').split('').reverse().join('');
       return  ribuan;
    //   document.write(ribuan); 
    }



function fillFieldDetail(id,trans_sounding,kodetangki,konstanta,hasil_ukur,suhu,faktorsuhu,stok){
   
        //function editRow(kodeorg,tanggal){
            document.getElementById('ids').value = id;
            document.getElementById('no_trans').value = trans_sounding;
            document.getElementById('kodetangki').value = kodetangki;
            document.getElementById('konstanta').value = konstanta;
            document.getElementById('hasil_ukur').value = hasil_ukur;
            document.getElementById('suhu').value = suhu;
            document.getElementById('faktorsuhu').value = faktorsuhu;
            document.getElementById('stok').value = stok;
            load_data2(trans_sounding)

}


function fillField(kodeorg,trans_sounding,tanggal,stokawal,stokterjual,stokselisih,OERHarian,totalstok){
   
        //function editRow(kodeorg,tanggal){

               
           resultx=numberWithCommas(stokselisih);
             
            document.getElementById('no_trans').value = trans_sounding;
            document.getElementById('KbnId').value = kodeorg;
            document.getElementById('tanggal').value = tanggal;
            document.getElementById('stokawal').value = numberWithCommas(stokawal);
            document.getElementById('stokterjual').value = numberWithCommas(stokterjual);
            document.getElementById('stokselisih').value = numberWithCommas(stokselisih);
            
            document.getElementById('OERHarian').value = OERHarian;
            document.getElementById('totalstok').value = totalstok;
            document.getElementById('proses').value = trans_sounding;
            document.getElementById('no_trans_sounding').value = trans_sounding;

            load_data2(trans_sounding)

}

function fillField2(noTrans,Thn)

{

        
        
        unlock_header_form();

        notrn=noTrans;

        param='no_trans='+notrn+'&proses=getData';

        tujuan='vhc_slave_save_sounding.php';

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

                                                ar=con.responseText.split("####");

                                                document.getElementById('no_trans').value=ar[0];

                                                document.getElementById('no_trans_pekerjaan').value=ar[0];

                                                document.getElementById('no_trans_opt').value=ar[0];

                                                document.getElementById('jns_vhc').value=ar[1];

                                                document.getElementById('kodetraksi').value=ar[7];

                                                //document.getElementById('kde_vhc').value=KdVhc;

                                                document.getElementById('tgl_pekerjaan').value=ar[2];

                                                document.getElementById('tgl_pekerjaan').disabled=true;

                                                //document.getElementById('kmhm_awal').value=ar[3];

                                                //document.getElementById('kmhm_akhir').value=ar[4];

                                                //document.getElementById('stn').value=ar[5];

                                                document.getElementById('jns_bbm').value=ar[3];

                                                document.getElementById('jmlh_bbm').value=ar[4];

                                                document.getElementById('KbnId').disabled=true;

                                                document.getElementById('KbnId').value=ar[5];

                                                //document.getElementById('thnKntrk').value=ar[9];

                                                document.getElementById('kode_karyawan').innerHTML=ar[6];





                                                if(ar[6]=='')

                                                {

                                                        ar[6]="<option value''></options>";

                                                }

                                                //document.getElementById('noKntrk').innerHTML=ar[10];

                                                document.getElementById('proses').value='update_head';

                                                get_kd(noTrans);

                                                }

                                        }

                                        else {

                                                busy_off();

                                                error_catch(con.status);

                                        }

                      }	

         }  



/*	document.getElementById('no_trans').value=noTrans;

        document.getElementById('no_trans_pekerjaan').value=noTrans;

        document.getElementById('no_trans_opt').value=noTrans;

        document.getElementById('jns_vhc').value=jnsVhc;

        //document.getElementById('kde_vhc').value=KdVhc;

        document.getElementById('tgl_pekerjaan').value=tglKrja;

        document.getElementById('kmhm_awal').value=kmhmA;

        document.getElementById('kmhm_akhir').value=kmhmR;

        document.getElementById('stn').value=sat;

        document.getElementById('jns_bbm').value=jnsBbm;

        document.getElementById('jmlh_bbm').value=jmlhBbm;

        document.getElementById('thnKntrk').value=Thn;

        //document.getElementById('noKntrk').value=nkntrk;



        document.getElementById('proses').value='update_head';

        get_kd(noTrans);*/

}

function createNew()

{

        get_notransaksi();

        //load_data_pekerjaan();

        //document.getElementById('create_new').style.display='none';

        document.getElementById('done_entry').disabled=true;

        document.getElementById('save_kepala').disabled=false;

        document.getElementById('cancel_kepala').disabled=false;

        document.getElementById('proses').value='insert_header';

        //document.getElementById('premiStat').disabled=false;

        document.getElementById('jns_vhc').disabled=false;

        document.getElementById('kodetraksi').disabled=false;

        document.getElementById('kde_vhc').disabled=false;

        document.getElementById('tgl_pekerjaan').disabled=false;

        document.getElementById('kmhm_awal').disabled=false;

        document.getElementById('kmhm_akhir').disabled=false;	

        document.getElementById('stn').disabled=false;	

        document.getElementById('jns_bbm').disabled=false;	

        document.getElementById('jmlh_bbm').disabled=false;	

        //document.getElementById('noKntrk').disabled=false;	

        //document.getElementById('thnKntrk').disabled=false;	

        //document.getElementById('noKntrk').innerHTML='';

        //document.getElementById('thnKntrk').value='';

}


function get_notransaksi2()

{

        kdOrg=document.getElementById('KbnId').options[document.getElementById('KbnId').selectedIndex].value;

        param='proses=get_no_transaksi'+'&kdOrg='+kdOrg;

        tujuan='vhc_slave_save_sounding.php';

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

                                                        ac=con.responseText.split("####");

                                                        document.getElementById('no_trans').value=ac[0];
                                                        document.getElementById('stokawal').value=ac[0];
                                                        ar=document.getElementById('no_trans').value;
                                                        ar=document.getElementById('stokawal').value;

                                                        document.getElementById('no_trans_pekerjaan').value=ar;

                                                        document.getElementById('no_trans_opt').value=ar;

                                                        document.getElementById('kode_karyawan').innerHTML=ac[1];

                                                        load_data();



                                                }

                                        }

                                        else {

                                                busy_off();

                                                error_catch(con.status);

                                        }

                      }	

         }  	

}




function get_stokterjual()

{

        no_trans=document.getElementById('no_trans').value;
        tanggal=document.getElementById('tanggal').value;

        param='no_trans='+no_trans+'&tanggal='+tanggal;

        tujuan='setup_mill_stokterjual.php';

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

                                                        ac=con.responseText.split("####");

                                                      //  document.getElementById('no_trans').value=ac[0];
                                                        document.getElementById('stokterjual').value=ac[0];
                                                        
                                                        ar=document.getElementById('no_trans').value;
                                                        ar=document.getElementById('stokterjual').value;

                                                        document.getElementById('no_trans_pekerjaan').value=ar;

                                                        document.getElementById('no_trans_opt').value=ar;

                                                        document.getElementById('kode_karyawan').innerHTML=ac[1];

                                                        load_data();



                                                }

                                        }

                                        else {

                                                busy_off();

                                                error_catch(con.status);

                                        }

                      }	

         }  	

}




function get_notransaksi()

{

        kdOrg=document.getElementById('KbnId').options[document.getElementById('KbnId').selectedIndex].value;

        param='proses=get_no_transaksi'+'&kdOrg='+kdOrg;

        tujuan='vhc_slave_save_sounding.php';

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

                                                        ac=con.responseText.split("####");

                                                        document.getElementById('no_trans').value=ac[0];
                                                        ar=document.getElementById('no_trans').value;

                                                        document.getElementById('no_trans_pekerjaan').value=ar;

                                                        document.getElementById('no_trans_opt').value=ar;

                                                        document.getElementById('kode_karyawan').innerHTML=ac[1];

                                                        load_data();



                                                }

                                        }

                                        else {

                                                busy_off();

                                                error_catch(con.status);

                                        }

                      }	

         }  	

}


function simpan(){

        kodeOrg=document.getElementById('KbnId').options[document.getElementById('KbnId').selectedIndex].value;

        no_trans=document.getElementById('no_trans').value;

        tanggal=document.getElementById('tanggal').value;

        stokawal=document.getElementById('stokawal').value;

        totalstok=document.getElementById('totalstok').value;

        stokterjual=document.getElementById('stokterjual').value;

        stokselisih=document.getElementById('stokselisih').value;

        harian=document.getElementById('OERHarian').value;

        proses=document.getElementById('proses').value;



    if(proses=="insert_header"){

        param='stokawal='+stokawal+'&stokterjual='+stokterjual+'&stokselisih='+stokselisih+'&kodeOrg='+kodeOrg+'&tanggal='+tanggal;

        param+='&harian='+harian+'&totalstok='+totalstok+'&no_trans='+no_trans;

        param+="&proses=insert_header";

        function respon() {

            if (con.readyState == 4) {
    
                if (con.status == 200) {
    
                    busy_off();
    
                    if (!isSaveResponse(con.responseText)) {
    
                        alert('ERROR TRANSACTION,\n' + con.responseText);
    
                    } else {
    
                        // Success Response
    /*
                        var res = document.getElementById('kodeorg');
    
                        res.value = con.responseText;
    
                        
                        alert(con.responseText);
    
                        location.reload(true);
                        
      */
                  load_data();

                                                        location.reload(true);	
    
                    }
    
                } else {
    
                    busy_off();
    
                    error_catch(con.status);
    
                }
    
            }
    
        }    
    
    
        post_response_text('vhc_slave_save_sounding.php?', param,respon);	 


    }

    else {
        param='stokawal='+stokawal+'&stokterjual='+stokterjual+'&stokselisih='+stokselisih+'&kodeOrg='+kodeOrg+'&tanggal='+tanggal;

        param+='&harian='+harian+'&totalstok='+totalstok+'&no_trans='+no_trans;

        param+="&proses=update_header";        
        function respon() {

            if (con.readyState == 4) {
    
                if (con.status == 200) {
    
                    busy_off();
    
                    if (!isSaveResponse(con.responseText)) {
    
                        alert('ERROR TRANSACTION,\n' + con.responseText);
    
                    } else {
    
                        // Success Response
    
                        var res = document.getElementById('kodeorg');
    
                        res.value = con.responseText;
    
                        alert(con.responseText);
    
                        load_data();

                        location.reload(true);	

    
                    }
    
                } else {
    
                    busy_off();
    
                    error_catch(con.status);
    
                }
    
            }
    
        }    
    
    
        post_response_text('vhc_slave_save_sounding.php?', param,respon);	

    }

 


}


function save_header()

{

    

        kodeOrg=document.getElementById('KbnId').options[document.getElementById('KbnId').selectedIndex].value;

        no_trans=document.getElementById('no_trans').value;

        tanggal=document.getElementById('tanggal').value;

        stokawal=document.getElementById('stokawal').value;

        totalstok=document.getElementById('totalstok').value;

        stokterjual=document.getElementById('stokterjual').value;

        stokselisih=document.getElementById('stokselisih').value;

        harian=document.getElementById('OERHarian').value;

        proses=document.getElementById('proses').value;

        param='stokawal='+stokawal+'&stokterjual='+stokterjual+'&stokselisih='+stokselisih+'&kodeOrg='+kodeOrg+'&tanggal='+tanggal;

        param+='&harian='+harian+'&totalstok='+totalstok+'&no_trans='+no_trans;

        param+="&proses=insert_header";

       
        tujuan='vhc_slave_save_sounding.php';

        post_response_text(tujuan, param, respog);

        if(proses=="insert_header")
        {
                alert ('insert');
        }

        else {

                alert('xxx');
        }



        function respog()

        {


                if (con.status == 200) {
    
                        busy_off();
        
                        if (!isSaveResponse(con.responseText)) {
        
                            alert('ERROR TRANSACTION,\n' + con.responseText);
        
                        } else {
        
                            // Success Response
        
                            var res = document.getElementById('kodeorg');
        
                            res.value = con.responseText;
        
                            alert(con.responseText);
        
                            location.reload(true);			
        
                        }
        
                    }


         }     
        





}





function lock_header_form()

{

        //jns_vhc,kde_vhc,tgl_pekerjaan,kmhm_awal,kmhm_akhir,stn,jns_bbm,jmlh_bbm

        document.getElementById('jns_vhc').disabled=true;

        document.getElementById('kodetraksi').disabled=true;

        document.getElementById('kde_vhc').disabled=true;

        document.getElementById('tgl_pekerjaan').disabled=true;



        document.getElementById('jns_bbm').disabled=true;

        document.getElementById('jmlh_bbm').disabled=true;

        document.getElementById('save_kepala').disabled=true;

        document.getElementById('cancel_kepala').disabled=true;

        document.getElementById('done_entry').disabled=false;

        //document.getElementById('thnKntrk').disabled=true;

        //document.getElementById('noKntrk').disabled=true;

        //document.getElementById('premiStat').disabled=true;

        document.getElementById('KbnId').disabled=true;

}

function unlock_header_form()

{

        document.getElementById('jns_vhc').disabled=false;

        document.getElementById('kodetraksi').disabled=false;

        document.getElementById('kde_vhc').disabled=false;

        document.getElementById('tgl_pekerjaan').disabled=false;

//	document.getElementById('kmhm_awal').disabled=false;

//	document.getElementById('kmhm_akhir').disabled=false;

//	document.getElementById('stn').disabled=false;

        document.getElementById('jns_bbm').disabled=false;

        document.getElementById('jmlh_bbm').disabled=false;

        document.getElementById('save_kepala').disabled=false;

        document.getElementById('cancel_kepala').disabled=false;

        document.getElementById('done_entry').disabled=true;

        document.getElementById('KbnId').disabled=false;

        //document.getElementById('create_new').style.display='none';

        //document.getElementById('thnKntrk').disabled=false;

        //document.getElementById('noKntrk').disabled=false;

        //document.getElementById('premiStat').disabled=false;

}

function clear_form()

{

        document.getElementById('no_trans').value='';

        document.getElementById('jns_vhc').value='';

        document.getElementById('kodetraksi').value='';

        document.getElementById('kde_vhc').innerHTML="<option value=''>"+dataKdvhc+"</option>";

        document.getElementById('tgl_pekerjaan').value='';



        document.getElementById('jns_bbm').value='';

        document.getElementById('jmlh_bbm').value='';

        document.getElementById('save_kepala').value='';

        document.getElementById('cancel_kepala').value='';

        document.getElementById('KbnId').value='';

        document.getElementById('KbnId').disabled=false;

}

function doneEntry()

{

        if(confirm("Are you sure..?"))

        {

                cancel_kepala_form();

                bersih_form_pekerjaan();

                clear_operator();

        }

        else

        {

                return;

        }

}

function cancel_kepala_form()

{

        clear_form();

        document.getElementById('save_kepala').disabled=true;

        document.getElementById('cancel_kepala').disabled=true;

        document.getElementById('done_entry').disabled=true;

        //document.getElementById('create_new').style.display='block';

        document.getElementById('no_trans_pekerjaan').value='';

        document.getElementById('no_trans_opt').value='';

}


function load_data21()

{
        alert('load sukses');




}

function load_data2(trans_sounding)

{

        //alert("test");

        param='proses=load_detail&trans_sounding='+trans_sounding;

        tujuan='vhc_slave_save_sounding.php';

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

                                                     //   document.getElementById('tgl_cari').value='';

                                                      //  document.getElementById('txtCari').value='';

                                                        document.getElementById('containPekerja').innerHTML=con.responseText;

                                                      // getUmr();

                                                        //load_data();

                                                }

                                        }

                                        else {

                                                busy_off();

                                                error_catch(con.status);

                                        }

                      }	

         }



}

function load_data()

{

        //alert("test");

        param='proses=load_data_header';

        tujuan='vhc_slave_save_sounding.php';

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

                                                        document.getElementById('tgl_cari').value='';

                                                        document.getElementById('txtCari').value='';

                                                        document.getElementById('contain').innerHTML=con.responseText;

                                                        getUmr();

                                                        //load_data();

                                                }

                                        }

                                        else {

                                                busy_off();

                                                error_catch(con.status);

                                        }

                      }	

         }



}

function cariDataTransaksi()

{

        txtTgl=document.getElementById('tgl_cari').value;

        txtCari=document.getElementById('txtCari').value;

        statData=document.getElementById('statusInputan').options[document.getElementById('statusInputan').selectedIndex].value;

        param="txtTgl="+txtTgl+"&txtCari="+txtCari+'&statData='+statData;

        param+="&proses=cariTransaksi";

        //alert(param);

        tujuan='vhc_slave_save_sounding.php';

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

function cariData(num)

{

                txtTgl=document.getElementById('tgl_cari').value;

                txtCari=document.getElementById('txtCari').value;

                statData=document.getElementById('statusInputan').options[document.getElementById('statusInputan').selectedIndex].value;

                param="txtTgl="+txtTgl+"&txtCari="+txtCari+'&statData='+statData;

                param+="&proses=cariTransaksi";

                param+='&page='+num;

                //alert(param);

                tujuan = 'vhc_slave_save_sounding.php';



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

function load_data_operator()

{

        alert('ada');
        //alert(document.getElementById('no_trans_opt').value);

        if(document.getElementById('no_trans').value!='')

        {


                

                no_tans=document.getElementById('no_trans_opt').value;

                param='proses=load_data_opt';

                param+='&notrans='+no_tans;

                //alert(param);

                tujuan='vhc_detailPekerjaan.php';	

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

                                                document.getElementById('containOperator').innerHTML=con.responseText;

                                                //load_data_pekerjaan();+

                                                noTrans=document.getElementById('no_trans_opt').value;

                                        //	getKntrk(thn,nokntrak);





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

function load_data_pekerjaan()

{

        //alert(document.getElementById('no_trans_pekerjaan').value);

        if(document.getElementById('no_trans_pekerjaan').value!='')

        {

                no_trans=document.getElementById('no_trans_pekerjaan').value;

                param='notrans='+no_trans;

                param+='&proses=load_data_kerjaan';

                //alert(param);

                tujuan='vhc_detailPekerjaan.php';



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

                                              alert(con.responseText);

                                                document.getElementById('containPekerja').innerHTML=con.responseText;

                                                load_data_operator();

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



function getKntrk(thn,nokntrak)

{

        if((thn=='')&&(nokntrak==''))

        {

                //alert("masuk");

                thnKntrk=document.getElementById('thnKntrk').options[document.getElementById('thnKntrk').selectedIndex].value;

                param='thnKntrk='+thnKntrk+'&proses=getKntrk';

        }

        else

        {

                thnKntrk=thn;

                noKntrak=nokntrak;

                param='thnKntrk='+thnKntrk+'&proses=getKntrk'+'&noKntrak='+noKntrak;

        }

        tujuan='vhc_detailPekerjaan.php';

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



                                                        document.getElementById('noKntrk').innerHTML=con.responseText;

                                                }

                                        }

                                        else {

                                                busy_off();

                                                error_catch(con.status);

                                        }

                      }	

         } 

}



function getSisaawalxxxx()

{
        alert('test');
        kodeorg=document.getElementById('kodeorg').value;
        tanggal=document.getElementById('tanggal').value;
        param='kodeorg='+kodeorg+'&tanggal='+tanggal;
      
        post_response_text('setup_mill_stokterjual.php', param, respon);

        function respon(){

        if (con.readyState == 4) {

            if (con.status == 200) {

                busy_off();

                if (!isSaveResponse(con.responseText)) {

                    alert('ERROR TRANSACTION,\n' + con.responseText);

                } else {

                    // Success Response

                                               // ar=con.responseText.split("###");
                                               ac=con.responseText.split("####");
                                                        document.getElementById('stokterjual').value=ac[0];
                                                        ar=document.getElementById('stokterjual').value;

                                               var res = document.getElementById('stokterjual');
                                                res.value = con.responseText;
                                              //  document.getElementById('vhc_code').innerHTML=ar[0];

                     }

            } else {

                busy_off();

                error_catch(con.status);

            }

        }

      

    }
   // getPotsortasi();

}


function searchLok(title,content,ev)

{

        width='500';

        height='400';

        showDialog1(title,content,width,height,ev);

}

function findLok()

{

        txt=trim(document.getElementById('txtinputan').value);

        if(txt=='')

        {

                alert('Text is obligatory');

        }

        else if(txt.length<3)

        {

                alert('Too short');

        }

        else

        {

                param='txtinputan='+txt+'&proses=cari_lokasi';

                tujuan='vhc_slave_save_sounding.php';

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

function throwThisRow(kd_org,nm_org)

{

     document.getElementById('lokasi_kerja_nm').value=nm_org;

         document.getElementById('lokasi_kerja').value=kd_org;

         closeDialog();

}

function fillFieldKrj(jnsKrj,lokKrj,brtMuat,jmlhRit,ktr,bya,kmawl,kmakhr,stn)

{

        //document.getElementById('no_trans_pekerjaan').value=noTrans;



        document.getElementById('jns_kerja').value=jnsKrj;

        document.getElementById('old_jnskerja').value=jnsKrj;

        document.getElementById('brt_muatan').value=brtMuat;

        document.getElementById('jmlh_rit').value=jmlhRit;

        document.getElementById('biaya').value=bya;

        document.getElementById('ket').value=ktr;

        document.getElementById('kmhm_awal').value=kmawl;

        document.getElementById('kmhm_akhir').value=kmakhr;

        document.getElementById('stn').value=stn;

        document.getElementById('proses_pekerjaan').value='update_kerja';

        document.getElementById('old_jnskerja').value=jnsKrj;



        //document.getElementById('jns_kerja').disabled=true;

        //document.getElementById('lokasi_kerja').disabled=true;

        //document.getElementById('blok').disabled=true;

        if(lokKrj.length>4)

        {

                kd=lokKrj.substr(0,4);

                //alert(kd);

                document.getElementById('lokasi_kerja').value=kd;

                document.getElementById('old_lokkerja').value=kd;

                getBlok(kd,lokKrj);

                //document.getElementById('blok').value=lokKrj;

        }

        else

        {

                document.getElementById('old_lokkerja').value=lokKrj;

                document.getElementById('lokasi_kerja').value=lokKrj;

                document.getElementById('blok').innerHTML="<option value=''>"+dataKdvhc+"</option>";

        }

}



function getPotsortasi()

{
   alert('sss');

        kodeorg=document.getElementById('kodeorg').value;
        tanggal=document.getElementById('tanggal').value;
        param='kodeorg='+kodeorg+'&tanggal='+tanggal;
      
        post_response_text('setup_mill_potongansortasi.php', param, respon);

        function respon(){

        if (con.readyState == 4) {

            if (con.status == 200) {

                busy_off();

                if (!isSaveResponse(con.responseText)) {

                    alert('ERROR TRANSACTION,\n' + con.responseText);

                } else {

                    // Success Response

                                               // ar=con.responseText.split("###");
                                               var res = document.getElementById('kgpotsortasi');
                                                res.value = con.responseText;
                                              //  document.getElementById('vhc_code').innerHTML=ar[0];

                     }

            } else {

                busy_off();

                error_catch(con.status);

            }

        }

    }


}



function save_pekerjaan()

{
        ids=document.getElementById('ids').value;
        no_trans_sounding=document.getElementById('no_trans_sounding').value;

        kodetangki=document.getElementById('kodetangki').value;

        konstanta=document.getElementById('konstanta').value;

        hasil_ukur=document.getElementById('hasil_ukur').value;

        suhu=document.getElementById('suhu').value;

        faktorsuhu=document.getElementById('faktorsuhu').value;

        stok=document.getElementById('stok').value;


        param='ids='+ids+'&no_trans_sounding='+no_trans_sounding+'&kodetangki='+kodetangki+'&konstanta='+konstanta+'&hasil_ukur='+hasil_ukur;

        param+='&suhu='+suhu+'&faktorsuhu='+faktorsuhu+'&stok='+stok;

        if(ids==""){
                param+="&proses=insert_detailsounding";
        }
        else{
                
                param+="&proses=update_detailsounding";

        }




        //alert(param);

        tujuan='vhc_slave_save_sounding.php';

        post_response_text(tujuan, param, respog);
        
       // load_data2(no_trans_sounding)
        
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

                                                        //document.getElementById('container').innerHTML=con.responseText;

                                                        bersih_form_pekerjaan();

                                                        isidt=0;

                                                        if(con.responseText!='')

                                                        {

                                                            isidt=parseInt(con.responseText);

                                                        }

                                                        document.getElementById('kmhm_awal').disabled=true;

                                                        document.getElementById('kmhm_awal').value=isidt;


                                                      //  load_data2();
                                                       // load_datadetail();
                                                       //load_data2(no_trans_sounding);
                                                       //location.reload(true);
                                                        
                                                      //  location.reload(true);
                                                        //document.getElementById('containPekerja').innerHTML=con.responseText;
                                                        //location.reload(true);	
                                                     //   load_data2(no_trans_sounding)

                                                       // load_data_pekerjaan();

                                                       // alert('sukses');

                                                   //    load_data();

                                                        
                                                   load_data();

                                                   location.reload(true);

                                                }

                                        }

                                        else {

                                                busy_off();

                                                error_catch(con.status);

                                        }

                      }	

         }  	



}


function delDetail(id)

{
        
        ids=id;
        param='ids='+ids+'&proses=deleteDetail';
        tujuan='vhc_slave_save_sounding.php';

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

                                                        //document.getElementById('contain').value=con.responseText;

                                                        load_data();

                                                        location.reload(true);

                                                }

                                        }

                                        else {

                                                busy_off();

                                                error_catch(con.status);

                                        }

                      }	

         }

         if(confirm("detail wil be deleted, are you sure?"))

         {

                post_response_text(tujuan, param, respog);

         }

         else

         {

                 return;

         }

}



function delHead(noTran)

{

        notrans=noTran;

        param='no_trans='+notrans+'&proses=deleteHead';

        tujuan='vhc_slave_save_sounding.php';

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

                                                        //document.getElementById('contain').value=con.responseText;

                                                        load_data();





                                                }

                                        }

                                        else {

                                                busy_off();

                                                error_catch(con.status);

                                        }

                      }	

         }

         if(confirm("Header dan detail wil be deleted, are you sure?"))

         {

                post_response_text(tujuan, param, respog);

         }

         else

         {

                 return;

         }

}



function bersih_form_pekerjaan()

{
        no_trans=document.getElementById('no_trans').value;
       // location.reload(true);	
        load_data2(no_trans);

        document.getElementById('jns_kerja').value='';

        document.getElementById('jns_kerja').disabled=false;

        document.getElementById('lokasi_kerja').value='';

        document.getElementById('lokasi_kerja').disabled=false;

        document.getElementById('brt_muatan').value=0;

        document.getElementById('jmlh_rit').value=0;

        document.getElementById('ket').value='';

        document.getElementById('biaya').value=0;

        document.getElementById('blok').value="<option value=''>"+dataKdvhc+"</options>";

        //document.getElementById('kmhm_awal').value=0;

        document.getElementById('kmhm_akhir').value=0;

        document.getElementById('stn').value=0;

}

function delDataKrj(noTrans,jnsKerja)

{

        no_trans=document.getElementById('no_trans_pekerjaan').value=noTrans;

        jns_kerja=document.getElementById('jns_kerja').value=jnsKerja;

        param='notrans='+no_trans+'&jnsPekerjaan='+jns_kerja+'&proses=deleteKrj';

        tujuan='vhc_detailPekerjaan.php';

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

                                        load_data_pekerjaan();

                                }

                        }

                        else {

                                busy_off();

                                error_catch(con.status);

                        }

          }	

        } 	

        if(confirm("Delete, are you sure?"))

        {

                post_response_text(tujuan, param, respog);

        }

        else

        {

                return;

        }





}

stat_opt=0;

function delData(noTrans,Kdkry)

{

        no_trans=document.getElementById('no_trans_opt').value=noTrans;

        kdKry=document.getElementById('kode_karyawan').value=Kdkry;

        pros=document.getElementById('prosesOpt');

        //pros.value=;

        param='noOptrans='+no_trans+'&kdKry='+kdKry+'&proses=delete_opt';

        tujuan='vhc_detailPekerjaan.php';



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

                                        //document.getElementById('containPekerja').innerHTML=con.responseText;

                                        load_data_operator();

                                }

                        }

                        else {

                                busy_off();

                                error_catch(con.status);

                        }

          }	

        } 	

        if(confirm("Delete, are you sure?"))

        {

                post_response_text(tujuan, param, respog);

        }

        else

        {

                return;

        }

}

function clear_operator()

{

        document.getElementById('kode_karyawan').value='';

        document.getElementById('uphOprt').value=0;

        document.getElementById('prmiOprt').value=0;

        document.getElementById('pnltyOprt').value=0;

        document.getElementById('prosesOpt').value='insert_operator';

}

function save_operator()

{

        notrans=document.getElementById('no_trans_opt').value;

        kdKry=document.getElementById('kode_karyawan').options[document.getElementById('kode_karyawan').selectedIndex].value;

        posisi=document.getElementById('posisi').options[document.getElementById('posisi').selectedIndex].value;

        uphoprt=document.getElementById('uphOprt').value;

        prmiOprt=document.getElementById('prmiOprt').value;

        pnltyOprt=document.getElementById('pnltyOprt').value;

        tglTrans=document.getElementById('tgl_pekerjaan').value;

        pros=document.getElementById('prosesOpt');

        param='notrans='+notrans+'&kdKry='+kdKry+'&posisi='+posisi;

        param+='&proses='+pros.value+'&pnltyOprt='+pnltyOprt+'&prmiOprt='+prmiOprt+'&uphOprt='+uphoprt+'&tglTrans='+tglTrans;

        tujuan='vhc_detailPekerjaan.php';

        //alert(param);

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

                                        //document.getElementById('containPekerja').innerHTML=con.responseText;

                                        load_data_operator();

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

                param='proses=load_data_header';

                param+='&page='+num;

                tujuan = 'vhc_slave_save_sounding.php';

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

function cariBastKrj(num)

{

                param='proses=load_data_kerjaan';

                param+='&page='+num;

                tujuan = 'vhc_detailPekerjaan.php';

                post_response_text(tujuan, param, respog);			

                function respog(){

                        if (con.readyState == 4) {

                                if (con.status == 200) {

                                        busy_off();

                                        if (!isSaveResponse(con.responseText)) {

                                                alert('ERROR TRANSACTION,\n' + con.responseText);

                                        }

                                        else {

                                                document.getElementById('containPekerja').innerHTML=con.responseText;

                                        }

                                }

                                else {

                                        busy_off();

                                        error_catch(con.status);

                                }

                        }

                }	

}

function cariBastOpt(num)

{

                param='proses=load_data_opt';

                param+='&page='+num;

                tujuan = 'vhc_detailPekerjaan.php';

                post_response_text(tujuan, param, respog);			

                function respog(){

                        if (con.readyState == 4) {

                                if (con.status == 200) {

                                        busy_off();

                                        if (!isSaveResponse(con.responseText)) {

                                                alert('ERROR TRANSACTION,\n' + con.responseText);

                                        }

                                        else {

                                                document.getElementById('containOperator').innerHTML=con.responseText;

                                        }

                                }

                                else {

                                        busy_off();

                                        error_catch(con.status);

                                }

                        }

                }	

}

function getUmr()

{

        //kdKry

        kdkry=document.getElementById('kode_karyawan').options[document.getElementById('kode_karyawan').selectedIndex].value;

        tanggal=document.getElementById('tgl_pekerjaan').value;

        tahun=tanggal.substr(6, 4);

        no_tans=document.getElementById('no_trans_opt').value;

        param='proses=getUmr'+'&kdKry='+kdkry+'&tahun='+tahun+'&notrans='+no_tans;

        tujuan='vhc_detailPekerjaan.php';

        post_response_text(tujuan, param, respog);			

                function respog(){

                        if (con.readyState == 4) {

                                if (con.status == 200) {

                                        busy_off();

                                        if (!isSaveResponse(con.responseText)) {

                                                alert('ERROR TRANSACTION,\n' + con.responseText);

                                        }

                                        else {



												noTrans=document.getElementById('no_trans_opt').value;

                                                document.getElementById('uphOprt').value=trim(con.responseText);

                                                document.getElementById('premidasar').value=trim(con.responseText);

                                        }

                                }

                                else {

                                        busy_off();

                                        error_catch(con.status);

                                }

                        }

                }	

}

function getBlok(kdkbn,kdblok)

{

        if((kdkbn=='')&&(kdblok==''))

        {

                locationKerja=document.getElementById('lokasi_kerja').options[document.getElementById('lokasi_kerja').selectedIndex].value;

                param='locationKerja='+locationKerja+'&proses=getBlok';

        }

        else

        {

                locationKerja=kdkbn;

                Blok=kdblok;

                param='locationKerja='+locationKerja+'&Blok='+Blok+'&proses=getBlok';

        }

        tujuan='vhc_detailPekerjaan.php';

        post_response_text(tujuan, param, respog);			

                function respog(){

                        if (con.readyState == 4) {

                                if (con.status == 200) {

                                        busy_off();

                                        if (!isSaveResponse(con.responseText)) {

                                                alert('ERROR TRANSACTION,\n' + con.responseText);

                                        }

                                        else {



                                                document.getElementById('blok').innerHTML=con.responseText;

                                                document.getElementById('old_blok').value=kdblok;

                                        }

                                }

                                else {

                                        busy_off();

                                        error_catch(con.status);

                                }

                        }

                }	



}



function ubahpremi(){

	premi=parseInt(document.getElementById('premidasar').value);

	persen=parseInt(document.getElementById('persen').value);

	document.getElementById('uphOprt').value=Math.round(premi*persen/100);

}