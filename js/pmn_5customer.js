// JavaScript Document

//search kelompok pelanggan
function searchGruop(title,content,ev)
{
        width='500';
        height='400';
        showDialog1(title,content,width,height,ev);
        //alert('asdasd');
}
function getCustomerNumber()
{       kdkelompok = document.getElementById('klcustomer_code').value;
        param='kelompok='+kdkelompok;
        tujuan='pmn_get_customer_number.php';
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
                                                        document.getElementById('kode_cus').value=trim(con.responseText);
                                                  
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      } 
         }      
}
function findGroup()
{
        txt_grp=trim(document.getElementById('group_name').value);
        if(txt_grp=='')
        {
                alert('Text is obligatory');
        }
        else
        {
                param='txtfind_klp='+txt_grp;
                tujuan='log_slave_get_grp_cus.php';
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
                                                        document.getElementById('container_cari').innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  	
}
function setGroup(kode,kelompok)
{
         document.getElementById('nama_group').value=kelompok;
         document.getElementById('klcustomer_code').value=kode;
         closeDialog();
}

////search kelompok akun
function searchAkun(title,content,ev)
{
        width='500';
        height='400';
        showDialog1(title,content,width,height,ev);
        //alert('asdasd');
}
function findAkun()
{
        txt=trim(document.getElementById('no_akun').value);
        if(txt=='')
        {
                alert('Text is obligatory');
        }
        else
        {
                param='txtfind='+txt;
                tujuan='log_slave_get_grp_cus.php';
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
                                                        document.getElementById('container_cari_akun').innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  	
}
function setNoakun(no_akun,namaakun)
{
         document.getElementById('nama_akun').value=namaakun;
         document.getElementById('akun_cust').value=no_akun;
         closeDialog();
}

////end dari search

////fungsi menghapus isi form-->reset
function batalPlgn()
{
		document.getElementById('pk').value='';
		document.getElementById('jpk').value='';
        document.getElementById('kode_cus').value='';
        document.getElementById('kode_cus').disabled=false;
        document.getElementById('klcustomer_code').value='';
        document.getElementById('nama_group').value='';
        document.getElementById('akun_cust').value='';
        document.getElementById('nama_akun').value='';
        document.getElementById('cust_nm').value='';
        document.getElementById('kta').value='';
        document.getElementById('tlp_cust').value='';
        document.getElementById('kntk_person').value='';
        document.getElementById('plafon_cus').value='';
        document.getElementById('n_hutang').value='';
        document.getElementById('npwp_no').value='';
        document.getElementById('seri_no').value='';
        document.getElementById('almt').value='';
}

////simpan data
function simpanPlgn()
{
        kodecustomer=trim(document.getElementById('kode_cus').value);
        namacustomer=trim(document.getElementById('cust_nm').value);
        alamat=trim(document.getElementById('almt').value);
        kota=trim(document.getElementById('kta').value);
        telepon=trim(document.getElementById('tlp_cust').value);
        kontakperson=trim(document.getElementById('kntk_person').value);
        akun=trim(document.getElementById('akun_cust').value);
        plafon=trim(document.getElementById('plafon_cus').value);
        nilaihutang=trim(document.getElementById('n_hutang').value);
        npwp=trim(document.getElementById('npwp_no').value);
        noseri=trim(document.getElementById('seri_no').value);
        klcustomer=trim(document.getElementById('klcustomer_code').value);
		
		pk=trim(document.getElementById('pk').value);
		jpk=trim(document.getElementById('jpk').value);
		
        method=document.getElementById('method').value;
                param='kodecustomer='+kodecustomer+'&namacustomer='+namacustomer+'&alamat='+alamat+'&kota='+kota+'&telepon='+telepon+'&kontakperson='+kontakperson+'&pk='+pk+'&jpk='+jpk;
                param+='&akun='+akun+'&plafon='+plafon+'&nilaihutang='+nilaihutang+'&npwp='+npwp+'&noseri='+noseri+'&klcustomer='+klcustomer+'&method='+method;
                tujuan='log_slave_save_cust.php';

        if (klcustomer=='' || kodecustomer == '' || namacustomer == '' || alamat=='' || kota=='' || telepon=='' || kontakperson=='') 
        {
                alert('Data inconsistent');
        }
        else {
                if(confirm('Are you sure?'))
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
                                                        batalPlgn();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 	
}

//get data from database terus ditampilkan ke dalam form
function fillField(kodecustomer,namacustomer,alamat,kota,telepon,kontakperson,akun,plafon,nilaihutang,npwp,noseri,klcustomer,namaakun,kelompok,pk,jpk)
{
        kode_cus		=document.getElementById('kode_cus');
        kode_cus.value	=kodecustomer;
        kode_cus.disabled=true;
        cust_nm			=document.getElementById('cust_nm');
        cust_nm.value	=namacustomer;
        almt		    =document.getElementById('almt');
        almt.value		=alamat;
        kta			=document.getElementById('kta');
        kta.value=kota;
        tlp_cust			=document.getElementById('tlp_cust');
        tlp_cust.value=telepon;
        kntk_person			=document.getElementById('kntk_person');
        kntk_person.value=kontakperson;
        akun_cust			=document.getElementById('akun_cust');
        akun_cust.value		=akun;
        plafon_cus			=document.getElementById('plafon_cus');
        plafon_cus.value=plafon;
        n_hutang			=document.getElementById('n_hutang');
        n_hutang.value=nilaihutang;
        npwp_no			=document.getElementById('npwp_no');
        npwp_no.value=npwp;
        seri_no			=document.getElementById('seri_no');
        seri_no.value=noseri;
        klcustomer_code			=document.getElementById('klcustomer_code');
        klcustomer_code.value=klcustomer;
        nama_akun			=document.getElementById('nama_akun');
        nama_akun.value=namaakun;
        nama_group			=document.getElementById('nama_group');
        nama_group.value=kelompok;
        cat=0;
		
		document.getElementById('pk').value=pk;
        document.getElementById('jpk').value=jpk;
		
		document.getElementById('method').value='update';
}

function delPlgn(kodecustomer)
{
        param='kodecustomer='+kodecustomer;
                param+='&method=delete';
                tujuan='log_slave_save_cust.php';
                if(confirm('Deleting, Are you sure?'))
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

function ubahStatusAktifasi(kodecustomer,tipe)
{
        param='kodecustomer='+kodecustomer;
		if(tipe == "aktif"){
			param+='&method=aktif';
		}else{
			param+='&method=non-aktif';
		}		
		tujuan='log_slave_save_cust.php';
		if(confirm('Modify, Are you sure?'))
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
                                                        location.reload();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 	
}
function trfWB(kodecustomer,namacustomer,alamat,kota)
{
        param='kodecustomer='+kodecustomer+'&namacustomer='+namacustomer+'&alamat='+alamat+'&kota='+kota;
       param+='&method=trfWB';
              
        tujuan='log_slave_save_cust.php';
        if(confirm('Kirim data ke Weight Bridge , Are you sure?'))
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
                                                    console.log('masuk');
                                                        location.reload();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      } 
         }  
}
