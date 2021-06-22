// JavaScript Document

//search kelompok pelanggan






////end dari search

////fungsi menghapus isi form-->reset
function ubahAntrian(noantrian,tanggal,nokendaraan,supir,nospb)
{
       document.getElementById('noantrian').value=noantrian;
       document.getElementById('tgl').value=tanggal;
        document.getElementById('nokendaraan').value=nokendaraan;
        document.getElementById('nospb').value=nospb;
        document.getElementById('supir').value=supir;
         document.getElementById('proses').value='update';

}
function batalAntrian()
{
       
        document.getElementById('nokendaraan').value='';
        document.getElementById('nospb').value='';
        document.getElementById('supir').value='';
         document.getElementById('proses').value='insert';

}

////simpan data
function simpanAntrian()
{
        tgl=trim(document.getElementById('tgl').value);
        nokendaraan=trim(document.getElementById('nokendaraan').value);
        noantrian=trim(document.getElementById('noantrian').value);
        supir=trim(document.getElementById('supir').value);
        nospb=trim(document.getElementById('nospb').value);
        
        method=document.getElementById('proses').value;
               console.log(tgl+'-'+nokendaraan+'-'+supir+'-'+nospb+'-'+method);
                param='tgl='+tgl+'&nokendaraan='+nokendaraan+'&noantrian='+noantrian+'&supir='+supir+'&nospb='+nospb+'&method='+method;
                tujuan='pabrik_slave_antriantb.php';

        if (tgl=='' || nokendaraan == '' || supir == '' || nospb=='' ) 
        {
                alert('Data Tidak Boleh Kosong');
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
                                                       con.responseText;
                                                      batalAntrian();
                                                      loadData();
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
        kode_cus        =document.getElementById('kode_cus');
        kode_cus.value  =kodecustomer;
        kode_cus.disabled=true;
        cust_nm         =document.getElementById('cust_nm');
        cust_nm.value   =namacustomer;
        almt            =document.getElementById('almt');
        almt.value      =alamat;
        kta         =document.getElementById('kta');
        kta.value=kota;
        tlp_cust            =document.getElementById('tlp_cust');
        tlp_cust.value=telepon;
        kntk_person         =document.getElementById('kntk_person');
        kntk_person.value=kontakperson;
        akun_cust           =document.getElementById('akun_cust');
        akun_cust.value     =akun;
        plafon_cus          =document.getElementById('plafon_cus');
        plafon_cus.value=plafon;
        n_hutang            =document.getElementById('n_hutang');
        n_hutang.value=nilaihutang;
        npwp_no         =document.getElementById('npwp_no');
        npwp_no.value=npwp;
        seri_no         =document.getElementById('seri_no');
        seri_no.value=noseri;
        klcustomer_code         =document.getElementById('klcustomer_code');
        klcustomer_code.value=klcustomer;
        nama_akun           =document.getElementById('nama_akun');
        nama_akun.value=namaakun;
        nama_group          =document.getElementById('nama_group');
        nama_group.value=kelompok;
        cat=0;
        
        document.getElementById('pk').value=pk;
        document.getElementById('jpk').value=jpk;
        
        document.getElementById('method').value='update';
}

function delAntrian(noantrian)
{
        param='noantrian='+noantrian;
                param+='&method=delete';
                tujuan='pabrik_slave_antriantb.php';
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
                                                        loadData();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      } 
         }  
}
function loadData()
{
        
                param='method=loadData';
                tujuan='pabrik_slave_antriantb.php';
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
function cariTransaksi()
{
                tgl = document.getElementById('tglCri').value;
                console.log(tgl);
                param='method=loadData'+'&tgl='+tgl;
                tujuan='pabrik_slave_antriantb.php';
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



