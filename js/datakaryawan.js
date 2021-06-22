/**
 * @author antoniuslouis
 * @editor awan
 * 28/05/2020
 */

function getSub(lokasitugas, subbagian, karyawanid = null) {


        //  if(lokasitugas==undefined)
        //{
        lokasitugas = document.getElementById('lokasitugas').value;
        //}
        /*	if(subbagian==undefined)
        	{
        	   subbagian='a';
        	}
           	else if(lokasitugas==undefined && subbagian==undefined)
        	{
           		lokasitugas=lokasitugas;
           		subbagian=subbagian;
        	}*/
        param = 'method=getSub' + '&lokasitugas=' + lokasitugas + '&subbagian=' + subbagian;

        //alert(param);
        tujuan = 'sdm_slave_datakaryawan_cekNik.php';
        post_response_text(tujuan, param, respon);

        function respon() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        // Success Response
                                        //document.getElementById('subbagian').innerHTML='';
                                        document.getElementById('subbagian').innerHTML = con.responseText;
                                        loadExperience('queryonly', karyawanid);
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}

function x(lokasitugas, subbagian) {

        // alert(lokasitugas);
        if (lokasitugas == undefined)
                //alert('masuk');
                lokasitugas = document.getElementById('lokasitugas').value;
        else
                lokasitugas = lokasitugas;

        param = 'lokasitugas=' + lokasitugas + '&method=getSub';
        //alert(param);
        tujuan = 'sdm_slave_datakaryawan_cekNik.php';
        post_response_text(tujuan, param, respon);

        function respon() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        // Success Response
                                        document.getElementById('subbagian').innerHTML = '';
                                        document.getElementById('subbagian').innerHTML = con.responseText;

                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}


function cekNik() {

        nik = document.getElementById('nik').value;
        param = 'method=cekNik' + '&nik=' + nik;
        tujuan = 'sdm_slave_datakaryawan_cekNik.php';
        post_response_text(tujuan, param, respog);

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        //alert(con.responseText);
                                        //alert('Please input the reason out company');
                                        //document.getElementById('formKeluar').innerHTML=con.responseText;
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}


function saveFormKeluar(tanggalkeluar, karyawanid) {
        tanggalkeluar = document.getElementById('tanggalkeluar').value;
        karyawanid = document.getElementById('karyawanid').value;
        alasan = document.getElementById('alasan').value;
        method = document.getElementById('method').value;

        //param='kodeproject='+kodeproject+'&kodekegiatan='+kodekegiatan+'&kodeBarangForm='+kodeBarangForm+'&jumlahBarangForm='+jumlahBarangForm+'&method='+saveFormBarang;
        param = 'method=saveFormKeluar' + '&tanggalkeluar=' + tanggalkeluar + '&karyawanid=' + karyawanid + '&alasan=' + alasan;
        tujuan = 'sdm_slave_formKeluarKaryawan.php';
        post_response_text(tujuan, param, respog);





        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {

                                        alert('Done');
                                        closeDialog();
                                        //alert(con.responseText
                                        //cancelFormBarang(kegiatan,kodeproject);

                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }

}




function cekKeluar(event) {
        tanggalkeluar = document.getElementById('tanggalkeluar').value;
        karyawanid = document.getElementById('karyawanid').value;
        //alert(karyawanid);
        if (tanggalkeluar == '' || tanggalkeluar == '0000-00-00' || tanggalkeluar == '00-00-0000') {} else {
                //alert('masuk');
                //getKeluar(tanggalkeluar);
                ev = 'event';
                title = 'Reason Form : ' + tanggalkeluar;
                width = '525';
                height = '340';
                content = "<div id=formKeluar ></div>";
                showDialog1(title, content, width, height, ev);
                getFormKeluar(tanggalkeluar, karyawanid);
        }

}




function getFormKeluar(tanggalkeluar, karyawanid) {
        param = 'method=getFormKeluar' + '&tanggalkeluar=' + tanggalkeluar + '&karyawanid=' + karyawanid;
        //	alert(param);
        tujuan = 'sdm_slave_formKeluarKaryawan.php';
        post_response_text(tujuan, param, respog);

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        //alert(con.responseText);
                                        alert('Please input the reason out company');
                                        document.getElementById('formKeluar').innerHTML = con.responseText;
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }

}

function pendi() {
        document.getElementById('levelpendidikan').value;
        //document.getElementById('levelpendidikan2').value;

        param = 'levelpendidikan=' + levelpendidikan + '&method=cek';
        alert(param);
        tujuan = 'sdm_slave_save_datakaryawan.php';
        post_response_text(tujuan, param, respog);

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {}
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}
function cekBlackList() {
        namakaryawan = trim(document.getElementById('namakaryawan').value);
        //document.getElementById('levelpendidikan2').value;
        tempatlahir = trim(document.getElementById('tempatlahir').value);
        tanggallahir = trim(document.getElementById('tanggallahir').value);
        param = 'namakaryawan=' + namakaryawan + '&tempatlahir=' + tempatlahir;
        param += '&tanggallahir=' + tanggallahir ;
        param += '&method=CekBlacklist';
        tujuan = 'sdm_slave_save_datakaryawan.php';
        post_response_text(tujuan, param, respog);

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        if(con.responseText!='lanjut'){
                                           if (confirm(con.responseText+'Saving data for ' + namakaryawan + ', are you sure ?'))
                                                simpanKaryawan();
                                        }else{
                                            simpanKaryawan();
                                        }
        
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}


function simpanKaryawan() {
        //get input text and textarea value	

        nik = trim(document.getElementById('nik').value);
        namakaryawan = trim(document.getElementById('namakaryawan').value);
        tempatlahir = trim(document.getElementById('tempatlahir').value);
        tanggallahir = trim(document.getElementById('tanggallahir').value);
        noktp = trim(document.getElementById('noktp').value);
        nopassport = trim(document.getElementById('nopassport').value);
        npwp = trim(document.getElementById('npwp').value);
        kodepos = trim(document.getElementById('kodepos').value);
        alamataktif = trim(document.getElementById('alamataktif').value);
        kota = trim(document.getElementById('kota').value);
        noteleponrumah = trim(document.getElementById('noteleponrumah').value);
        nohp = trim(document.getElementById('nohp').value);
        norekeningbank = trim(document.getElementById('norekeningbank').value);
        namabank = trim(document.getElementById('namabank').value);
        tanggalmasuk = trim(document.getElementById('tanggalmasuk').value);
        tanggalkeluar = trim(document.getElementById('tanggalkeluar').value);
        tanggalmenikah = trim(document.getElementById('tanggalmenikah').value);
        catu = document.getElementById('natura');
        catu = trim(catu.options[catu.selectedIndex].value);
        jumlahanak = trim(document.getElementById('jumlahanak').value);
        jumlahtanggungan = trim(document.getElementById('jumlahtanggungan').value);
        tanggalmenikah = trim(document.getElementById('tanggalmenikah').value);
        notelepondarurat = trim(document.getElementById('notelepondarurat').value);
        email = trim(document.getElementById('email').value);
        method = trim(document.getElementById('method').value);
        karyawanid = trim(document.getElementById('karyawanid').value);
        jms = trim(document.getElementById('jms').value);
        bpjskes = trim(document.getElementById('bpjskes').value);
        kecamatan = trim(document.getElementById('kecamatan').value);
        desa = trim(document.getElementById('desa').value);
        pangkat = trim(document.getElementById('pangkat').value);
        //get options value	
        jeniskelamin = document.getElementById('jeniskelamin');
        jeniskelamin = trim(jeniskelamin.options[jeniskelamin.selectedIndex].value);
        agama = document.getElementById('agama');
        agama = trim(agama.options[agama.selectedIndex].value);
        bagian = document.getElementById('bagian');
        bagian = trim(bagian.options[bagian.selectedIndex].value);
        kodejabatan = document.getElementById('kodejabatan');
        kodejabatan = trim(kodejabatan.options[kodejabatan.selectedIndex].value);
        kodegolongan = document.getElementById('kodegolongan');
        kodegolongan = trim(kodegolongan.options[kodegolongan.selectedIndex].value);
        lokasitugas = document.getElementById('lokasitugas');
        lokasitugas = trim(lokasitugas.options[lokasitugas.selectedIndex].value);
        kodeorganisasi = document.getElementById('kodeorganisasi');
        kodeorganisasi = trim(kodeorganisasi.options[kodeorganisasi.selectedIndex].value);
        tipekaryawan = document.getElementById('tipekaryawan');
        tipekaryawan = trim(tipekaryawan.options[tipekaryawan.selectedIndex].value);
        warganegara = document.getElementById('warganegara');
        warganegara = trim(warganegara.options[warganegara.selectedIndex].value);

        paramC = '';
        var isduplicateC =  document.getElementById("isduplicate");
        if (isduplicateC.checked) {
                paramC='&isduplicate=1';
        }else{
                paramC='&isduplicate=0';
        }

        lokasipenerimaan = document.getElementById('lokasipenerimaan').value;
        //lokasipenerimaan=trim(lokasipenerimaan.options[lokasipenerimaan.selectedIndex].value);
        statuspajak = document.getElementById('statuspajak');
        statuspajak = trim(statuspajak.options[statuspajak.selectedIndex].value);
        provinsi = document.getElementById('provinsi');
        try {
                provinsi = trim(provinsi.options[provinsi.selectedIndex].value);
        } catch (e) {}
        sistemgaji = document.getElementById('sistemgaji');
        sistemgaji = trim(sistemgaji.options[sistemgaji.selectedIndex].value);
        golongandarah = document.getElementById('golongandarah');
        golongandarah = trim(golongandarah.options[golongandarah.selectedIndex].value);
        alokasi = document.getElementById('alokasi');
        alokasi = trim(alokasi.options[alokasi.selectedIndex].value);
        alokasi = document.getElementById('alokasi');
        alokasi = trim(alokasi.options[alokasi.selectedIndex].value);

        /*subbagian	=document.getElementById('subbagian');
        subbagian	=trim(subbagian.options[subbagian.selectedIndex].value);*/
        subbagian = document.getElementById('subbagian').value;
        catu	=document.getElementById('natura');
         catu	=trim(catu.options[catu.selectedIndex].value);
        while (golongandarah.indexOf("+") > -1) {
                golongandarah = golongandarah.replace("+", "%2B");
        }
        statusperkawinan = document.getElementById('statusperkawinan');
        statusperkawinan = trim(statusperkawinan.options[statusperkawinan.selectedIndex].value);
        levelpendidikan = document.getElementById('levelpendidikan');
        levelpendidikan = trim(levelpendidikan.options[levelpendidikan.selectedIndex].value);
        dert = document.getElementById('dptPremi');
        statPremi = 0;
        /*if (tanggalkeluar == '--')
        {
            tanggalkeluar = '';
        }
        if (tanggalmenikah == '--') {
            tanggalmenikah = '';
        }*/
        if (dert.checked == true) {
                statPremi = 1;
        }
        if (noktp == '' || alamataktif == '' || kota == '' || tempatlahir == '' || tanggallahir.length != 10 || tanggalmasuk.length != 10 || jms == '') {
                alert('ID.Num/KTP, Address/Alamat, City/Kota,\nPlace Of Birth/Tempat lahir, Birth.Date/Tgl.lahir,\nJoin.date/Tgl.Masuk, JMS  are Obligatory');
        } else {
                param = 'nik=' + nik + '&namakaryawan=' + namakaryawan + '&tempatlahir=' + tempatlahir;
                param += '&tanggallahir=' + tanggallahir + '&noktp=' + noktp;
                param += '&nopassport=' + nopassport + '&npwp=' + npwp + '&kodepos=' + kodepos;
                param += '&alamataktif=' + alamataktif + '&kota=' + kota + '&noteleponrumah=' + noteleponrumah
                param += '&nohp=' + nohp + '&norekeningbank=' + norekeningbank + '&namabank=' + namabank + '&tanggalmasuk=' + tanggalmasuk;
                param += '&tanggalkeluar=' + tanggalkeluar + '&jumlahanak=' + jumlahanak;
                param += '&jumlahtanggungan=' + jumlahtanggungan + '&tanggalmenikah=' + tanggalmenikah;
                param += '&notelepondarurat=' + notelepondarurat + '&email=' + email;
                param += '&jeniskelamin=' + jeniskelamin + '&agama=' + agama;
                param += '&bagian=' + bagian + '&kodejabatan=' + kodejabatan;
                param += '&kodegolongan=' + kodegolongan + '&lokasitugas=' + lokasitugas;
                param += '&kodeorganisasi=' + kodeorganisasi + '&tipekaryawan=' + tipekaryawan;
                param += '&warganegara=' + warganegara + '&lokasipenerimaan=' + lokasipenerimaan;
                param += '&statuspajak=' + statuspajak + '&provinsi=' + provinsi;
                param += '&sistemgaji=' + sistemgaji + '&golongandarah=' + golongandarah;
                param += '&statusperkawinan=' + statusperkawinan + '&levelpendidikan=' + levelpendidikan;
                param += '&catu=' + catu;
                param += '&method=' + method + '&karyawanid=' + karyawanid + '&alokasi=' + alokasi;
                param += '&subbagian=' + subbagian + '&jms=' + jms + '&bpjskes=' + bpjskes;
                // param+='&catu='+catu+'&statPremi='+statPremi;
                param += '&statPremi=' + statPremi;
                param += paramC;
                param += '&kecamatan=' + kecamatan + '&desa=' + desa + '&pangkat=' + pangkat;
                //alert(param);
                tujuan = 'sdm_slave_save_datakaryawan.php';
                if (confirm('Saving data for ' + namakaryawan + ', are you sure ?'))
                        post_response_text(tujuan, param, respog);
        }

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {

                                        alert('Done');
                                        //alert(con.responseText);
                                        controlThisForm(con.responseText);
                                        enableOtherButton();
                                        //pendi();
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}




function controlThisForm(tex) {
        xml = tex.toString();
        xmlobject = (new DOMParser()).parseFromString(xml, "text/xml");
        getId = xmlobject.getElementsByTagName('karyawanid')[0].firstChild.nodeValue;
        getNama = xmlobject.getElementsByTagName('namakaryawan')[0].firstChild.nodeValue;
        if (trim(getId) != '') {
                //Change first Tab Caption
                document.getElementById('tabFRM0').innerHTML = getNama;
                //change to update method
                document.getElementById('method').value = 'update';
                document.getElementById('karyawanid').value = getId;
        } else {
                alert('Last transaction has nothing affected');
        }
}

function cancelDataKaryawan() //indra
{
        document.getElementById('lokasitugas').value = '';
        document.getElementById('subbagian').value = '';

        document.getElementById('tanggalmasuk').disabled = false;
        document.getElementById('namakaryawan').disabled = false;
        document.getElementById('nik').disabled = false;
        document.getElementById('lokasitugas').disabled = false;
        document.getElementById('nik').value = '';
        document.getElementById('namakaryawan').value = '';
        document.getElementById('tempatlahir').value = '';
        document.getElementById('tanggallahir').value = '';
        document.getElementById('noktp').value = '';
        document.getElementById('nopassport').value = '';
        document.getElementById('npwp').value = '';
        document.getElementById('alamataktif').value = '';
        document.getElementById('kota').value = '';
        document.getElementById('provinsi').options[0].selected = true;
        document.getElementById('kodepos').value = '';
        document.getElementById('noteleponrumah').value = '';
        document.getElementById('nohp').value = '';
        document.getElementById('norekeningbank').value = '';
        document.getElementById('namabank').value = '';
        document.getElementById('sistemgaji').options[0].selected = true;
        document.getElementById('tanggalmasuk').value = '';
        document.getElementById('tanggalkeluar').value = '';
        document.getElementById('statusperkawinan').options[0].selected = true;
        document.getElementById('natura').options[0].selected = true;
        document.getElementById('tanggalmenikah').value = '';
        document.getElementById('jumlahanak').value = '';
        document.getElementById('jumlahtanggungan').value = '';
        document.getElementById('tanggalmenikah').value = '';
        document.getElementById('notelepondarurat').value = '';
        document.getElementById('karyawanid').value = '';
        document.getElementById('email').value = '';
        document.getElementById('dptPremi').checked = false;
        document.getElementById('method').value = 'insert';
        document.getElementById('tabFRM0').innerHTML = 'New';
        document.getElementById('container').innerHTML = '';
        document.getElementById('containerpendidikan').innerHTML = '';
        document.getElementById('containertraining').innerHTML = '';
        document.getElementById('containerkeluarga').innerHTML = '';
        document.getElementById('containeralamat').innerHTML = '';
        document.getElementById('displayphoto').removeAttribute('src');
        document.getElementById('displayphoto').setAttribute('src', '');
        document.getElementById('jms').value = '';
        document.getElementById('bpjskes').value = '';
        disableOtherButton();
        cancelPhoto();
}

function enableOtherButton() {
        //after success saving then activate sumbit button on each tab 
        document.getElementById('btncv').disabled = false;
        document.getElementById('btnpendidikan').disabled = false;
        document.getElementById('btntraining').disabled = false;
        document.getElementById('btnphoto').disabled = false;
        document.getElementById('btnalamat').disabled = false;
        document.getElementById('btnkontrak').disabled = false;
        document.getElementById('btnkeluarga').disabled = false;
}

function disableOtherButton() {
        //after success saving then activate sumbit button on each tab 
        document.getElementById('btncv').disabled = true;
        document.getElementById('btnpendidikan').disabled = true;
        document.getElementById('btntraining').disabled = true;
        document.getElementById('btnphoto').disabled = true;
        document.getElementById('btnalamat').disabled = true;
        document.getElementById('btnkontrak').disabled = true;
        document.getElementById('btnkeluarga').disabled = true;
}
//========================tab pengalaman
function simpanPengalaman() {
        namaperusahaan = trim(document.getElementById('namaperusahaan').value);
        bidangusaha = (document.getElementById('bidangusaha').value);

        blnmasuk = document.getElementById('blnmasuk');
        blnmasuk = blnmasuk.options[blnmasuk.selectedIndex].value;
        thnmasuk = document.getElementById('thnmasuk');
        thnmasuk = thnmasuk.options[thnmasuk.selectedIndex].value;
        blnkeluar = document.getElementById('blnkeluar');
        blnkeluar = blnkeluar.options[blnkeluar.selectedIndex].value;
        thnkeluar = document.getElementById('thnkeluar');
        thnkeluar = thnkeluar.options[thnkeluar.selectedIndex].value;

        jabatan = trim(document.getElementById('pengalamanjabatan').value);
        bagian = document.getElementById('pengalamanbagian').value;
        alamat = document.getElementById('pengalamanalamat').value;
        karyawanid = document.getElementById('karyawanid').value;

        if (blnmasuk == '' || thnmasuk == '' || blnkeluar == '' || thnkeluar == '') {
                alert('Incorrect period');
        } else if (namaperusahaan == '' || bidangusaha == '' || jabatan == '') {
                alert('Data Incomplete');
        } else {
                param = 'namaperusahaan=' + namaperusahaan + '&bidangusaha=' + bidangusaha;
                param += '&blnmasuk=' + blnmasuk + '&thnmasuk=' + thnmasuk;
                param += '&blnkeluar=' + blnkeluar + '&thnkeluar=' + thnkeluar;
                param += '&jabatan=' + jabatan + '&bagian=' + bagian;
                param += '&alamat=' + alamat + '&karyawanid=' + karyawanid;

                tujuan = 'sdm_slave_save_riwayat_pekerjaan.php';
                post_response_text(tujuan, param, respog);
        }

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        document.getElementById('container').innerHTML = con.responseText;
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }


}

function delPengalaman(karyawanid, nomor) {
        param = 'nomor=' + nomor + '&karyawanid=' + karyawanid + '&del=true';
        tujuan = 'sdm_slave_save_riwayat_pekerjaan.php';
        if (confirm('Deleting, are you sure..?')) {
                post_response_text(tujuan, param, respog);
        }

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        document.getElementById('container').innerHTML = con.responseText;
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}
//=================tab pendidikan

function updatelv() {
        document.getElementById('levelpendidikan').value;
        document.getElementById('levelpendidikan2').value;
        if (levelpendidikan2 > levelpendidikan) {
                alert('Education level greater than listed in main data');
                return;
        }
}

function simpanPendidikan() {
        document.getElementById('levelpendidikan').value;
        document.getElementById('levelpendidikan2').value;
        levelpendidikan2 = document.getElementById('levelpendidikan2');
        levelpendidikan2 = trim(levelpendidikan2.options[levelpendidikan2.selectedIndex].value);
        tahunlulus = document.getElementById('tahunlulus');
        tahunlulus = trim(tahunlulus.options[tahunlulus.selectedIndex].value);
        spesialisasi = trim(document.getElementById('spesialisasi').value);
        gelar = trim(document.getElementById('gelar').value);
        namasekolah = trim(document.getElementById('namasekolah').value);
        nilai = document.getElementById('nilai').value;
        pendidikankota = trim(document.getElementById('pendidikankota').value);
        pendidikanketerangan = trim(document.getElementById('pendidikanketerangan').value);
        karyawanid = document.getElementById('karyawanid').value;

        if (tahunlulus == '' || namasekolah == '') {
                alert('Data incomplete');
        } else if (levelpendidikan2 > levelpendidikan) {
                alert('Education level greater than listed in main data');
                return;
        } else {
                param = 'levelpendidikan=' + levelpendidikan2 + '&tahunlulus=' + tahunlulus;
                param += '&spesialisasi=' + spesialisasi + '&gelar=' + gelar;
                param += '&namasekolah=' + namasekolah + '&nilai=' + nilai;
                param += '&pendidikankota=' + pendidikankota + '&pendidikanketerangan=' + pendidikanketerangan;
                param += '&karyawanid=' + karyawanid;
                //alert('param');
                tujuan = 'sdm_slave_save_riwayat_pendidikan.php';
                post_response_text(tujuan, param, respog);
        }

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        document.getElementById('containerpendidikan').innerHTML = con.responseText;
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}





function delPendidikan(karyawanid, kode) {
        param = 'kode=' + kode + '&karyawanid=' + karyawanid + '&del=true';
        tujuan = 'sdm_slave_save_riwayat_pendidikan.php';
        if (confirm('Deleting, are you sure..?')) {
                post_response_text(tujuan, param, respog);
        }

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        document.getElementById('containerpendidikan').innerHTML = con.responseText;
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}
//=================training
function simpanTraining() {
        jenistraining = trim(document.getElementById('jenistraining').value);
        judultraining = trim(document.getElementById('judultraining').value);
        penyelenggara = trim(document.getElementById('penyelenggara').value);
        trainingblnmulai = document.getElementById('trainingblnmulai');
        trainingblnmulai = trim(trainingblnmulai.options[trainingblnmulai.selectedIndex].value);
        trainingthnmulai = document.getElementById('trainingthnmulai');
        trainingthnmulai = trim(trainingthnmulai.options[trainingthnmulai.selectedIndex].value);
        trainingblnselesai = document.getElementById('trainingblnselesai');
        trainingblnselesai = trim(trainingblnselesai.options[trainingblnselesai.selectedIndex].value);
        trainingthnselesai = document.getElementById('trainingthnselesai');
        trainingthnselesai = trim(trainingthnselesai.options[trainingthnselesai.selectedIndex].value);
        sertifikat = document.getElementById('sertifikat');
        sertifikat = trim(sertifikat.options[sertifikat.selectedIndex].value);
        karyawanid = document.getElementById('karyawanid').value;
        biaya = document.getElementById('biaya').value;
        if (jenistraining == '' || judultraining == '' || penyelenggara == '') {
                alert('Data incomplete');
        } else {
                param = 'jenistraining=' + jenistraining + '&judultraining=' + judultraining;
                param += '&penyelenggara=' + penyelenggara + '&trainingblnmulai=' + trainingblnmulai;
                param += '&trainingthnmulai=' + trainingthnmulai + '&trainingblnselesai=' + trainingblnselesai;
                param += '&trainingthnselesai=' + trainingthnselesai + '&sertifikat=' + sertifikat;
                param += '&karyawanid=' + karyawanid + '&biaya=' + biaya;
                tujuan = 'sdm_slave_save_riwayat_training.php';
                post_response_text(tujuan, param, respog);
        }

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        document.getElementById('containertraining').innerHTML = con.responseText;
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}

function delTraining(karyawanid, nomor) {
        param = 'nomor=' + nomor + '&karyawanid=' + karyawanid + '&del=true';
        tujuan = 'sdm_slave_save_riwayat_training.php';
        if (confirm('Deleting, are you sure..?')) {
                post_response_text(tujuan, param, respog);
        }

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        document.getElementById('containertraining').innerHTML = con.responseText;
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}

//==================keluarga
function simpanKeluarga() {
        keluarganama = trim(document.getElementById('keluarganama').value);
        keluargatmplahir = document.getElementById('keluargatmplahir').value;
        keluargatgllahir = document.getElementById('keluargatgllahir').value;
        keluargapekerjaan = document.getElementById('keluargapekerjaan').value;
        keluargatelp = document.getElementById('keluargatelp').value;
        keluargaemail = document.getElementById('keluargaemail').value;
        karyawanid = document.getElementById('karyawanid').value;
        method = document.getElementById('keluargamethod').value;
        nomor = document.getElementById('keluarganomor').value;

        keluargajk = document.getElementById('keluargajk');
        keluargajk = keluargajk.options[keluargajk.selectedIndex].value;
        hubungankeluarga = document.getElementById('hubungankeluarga');
        hubungankeluarga = hubungankeluarga.options[hubungankeluarga.selectedIndex].value;
        keluargastatus = document.getElementById('keluargastatus');
        keluargastatus = keluargastatus.options[keluargastatus.selectedIndex].value;
        keluargapendidikan = document.getElementById('keluargapendidikan');
        keluargapendidikan = keluargapendidikan.options[keluargapendidikan.selectedIndex].value;
        keluargatanggungan = document.getElementById('keluargatanggungan');
        keluargatanggungan = keluargatanggungan.options[keluargatanggungan.selectedIndex].value;


        if (keluarganama == '') {
                alert('Data incomplete');
        } else {
                param = 'keluarganama=' + keluarganama + '&keluargajk=' + keluargajk;
                param += '&keluargatmplahir=' + keluargatmplahir + '&keluargatgllahir=' + keluargatgllahir;
                param += '&keluargapekerjaan=' + keluargapekerjaan + '&keluargatelp=' + keluargatelp;
                param += '&keluargaemail=' + keluargaemail + '&karyawanid=' + karyawanid;
                param += '&hubungankeluarga=' + hubungankeluarga + '&keluargastatus=' + keluargastatus;
                param += '&keluargapendidikan=' + keluargapendidikan + '&keluargatanggungan=' + keluargatanggungan;
                param += '&method=' + method + '&nomor=' + nomor;
                //alert(param);
                tujuan = 'sdm_slave_save_keluarga.php';
                post_response_text(tujuan, param, respog);
        }

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        document.getElementById('containerkeluarga').innerHTML = con.responseText;
                                        clearKeluarga();
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}

function delKeluarga(karyawanid, nomor) {
        param = 'nomor=' + nomor + '&karyawanid=' + karyawanid + '&del=true';
        tujuan = 'sdm_slave_save_keluarga.php';
        if (confirm('Deleting, are you sure..?')) {
                post_response_text(tujuan, param, respog);
        }

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        document.getElementById('containerkeluarga').innerHTML = con.responseText;
                                        clearKeluarga();
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}

function clearKeluarga() {
        document.getElementById('keluarganama').value = '';
        document.getElementById('keluargatmplahir').value = '';
        document.getElementById('keluargatgllahir').value = '';
        document.getElementById('keluargapekerjaan').value = '';
        document.getElementById('keluargatelp').value = '';
        document.getElementById('keluargaemail').value = '';
        document.getElementById('keluargamethod').value = 'insert';

}

function fillField(nama, jeniskelamin, tempatlahir, tanggallahir, hubungankeluarga, status, levelpendidikan, pekerjaan, telp, email, tanggungan, nomor) {
        document.getElementById('keluargamethod').value = 'update';
        document.getElementById('keluarganomor').value = nomor;
        document.getElementById('keluarganama').value = nama;
        document.getElementById('keluargatmplahir').value = tempatlahir;
        document.getElementById('keluargatgllahir').value = tanggallahir;
        document.getElementById('keluargapekerjaan').value = pekerjaan;
        document.getElementById('keluargatelp').value = telp;
        document.getElementById('keluargaemail').value = email;

        jk = document.getElementById('keluargajk');
        for (x = 0; x < jk.length; x++) {
                if (jk.options[x].value == jeniskelamin) {
                        jk.options[x].selected = true;
                }
        }
        hk = document.getElementById('hubungankeluarga');
        for (x = 0; x < hk.length; x++) {
                if (hk.options[x].value == hubungankeluarga) {
                        hk.options[x].selected = true;
                }
        }
        st = document.getElementById('keluargastatus');
        for (x = 0; x < st.length; x++) {
                if (st.options[x].value == status) {
                        st.options[x].selected = true;
                }
        }
        lp = document.getElementById('keluargapendidikan');
        for (x = 0; x < lp.length; x++) {
                if (lp.options[x].value == levelpendidikan) {
                        lp.options[x].selected = true;
                }
        }
        tgx = document.getElementById('keluargatanggungan');
        for (x = 0; x < tgx.length; x++) {
                if (tgx.options[x].value == tanggungan) {
                        tgx.options[x].selected = true;
                }
        }
}
//=================tab photo
function cancelPhoto() {
        winForm.document.getElementById('frmUpload').reset();
}

function simpanPhoto() {
        winForm.document.getElementById('karyawanid').value = document.getElementById('karyawanid').value;
        winForm.document.getElementById('frmUpload').submit();
}

//==============tab alamat
function simpanAlamat() {
        karyawanid = document.getElementById('karyawanid').value;
        alamatalamat = trim(document.getElementById('alamatalamat').value);
        alamatkota = document.getElementById('alamatkota').value;
        alamatkodepos = document.getElementById('alamatkodepos').value;
        alamattelepon = document.getElementById('alamattelepon').value;
        alamatemplasement = document.getElementById('alamatemplasement').value;
        alamatstatus = document.getElementById('alamatstatus');
        alamatstatus = alamatstatus.options[alamatstatus.selectedIndex].value;
        alamatprovinsi = document.getElementById('alamatprovinsi');
        alamatprovinsi = alamatprovinsi.options[alamatprovinsi.selectedIndex].value;
        if (alamatalamat == '') {
                alert('Data incomplete');
        } else {
                param = 'alamatalamat=' + alamatalamat + '&karyawanid=' + karyawanid;
                param += '&alamatkota=' + alamatkota + '&alamatkodepos=' + alamatkodepos;
                param += '&alamattelepon=' + alamattelepon + '&alamatemplasement=' + alamatemplasement;
                param += '&alamatstatus=' + alamatstatus + '&alamatprovinsi=' + alamatprovinsi;
                tujuan = 'sdm_slave_save_alamat_karyawan.php';
                if (confirm('Saving, are you sure..?')) {
                        post_response_text(tujuan, param, respog);
                }
        }

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        document.getElementById('containeralamat').innerHTML = con.responseText;
                                        clearAlamat();
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}

function clearAlamat() {
        document.getElementById('alamatalamat').value = '';
        document.getElementById('alamatkota').value = '';
        document.getElementById('alamatkodepos').value = '';
        document.getElementById('alamattelepon').value = '';
        document.getElementById('alamatemplasement').value = '';

}

function delAlamat(karyawanid, nomor) {
        param = 'nomor=' + nomor + '&karyawanid=' + karyawanid + '&del=true';
        tujuan = 'sdm_slave_save_alamat_karyawan.php';
        if (confirm('Deleting, are you sure.?')) {
                post_response_text(tujuan, param, respog);
        }

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        document.getElementById('containeralamat').innerHTML = con.responseText;
                                        clearAlamat();
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}
//=====================list click
function displayList() {
        document.getElementById('frminput').style.display = 'none';
        document.getElementById('searchplace').style.display = '';
        loadEmployeeList();
}

function displayFormInput() {
        document.getElementById('frminput').style.display = '';
        document.getElementById('searchplace').style.display = 'none';
        cancelDataKaryawan();
}

function loadEmployeeList() {
        param = '';
        tujuan = 'sdm_slave_load_employee_list.php';
        post_response_text(tujuan, param, respog);

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        document.getElementById('searchplaceresult').innerHTML = con.responseText;
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}

function prefDatakaryawan(btn, curval) {
        cariKaryawan(curval);
        if (curval == 0) {} else
                btn.value = parseInt(curval) - 1;
        document.getElementById('nextbtn').value = parseInt(btn.value) + 2;

}

function nextDatakaryawan(btn, curval) {
        cariKaryawan(curval);
        btn.value = parseInt(curval) + 1;
        document.getElementById('prefbtn').value = parseInt(btn.value) - 2;
}


function cariKaryawan(page) {
        displayList();
        txtsearch = trim(document.getElementById('txtsearch').value);
        niksch = trim(document.getElementById('niksch').value);
        schorg = document.getElementById('schorg');
        schtipe = document.getElementById('schtipe');
        schstatus = document.getElementById('schstatus');
        schjk = document.getElementById('schjk');
        //schjk=schjk.options[schjk.selectedIndex].value;
        schorg = schorg.options[schorg.selectedIndex].value;
        schtipe = schtipe.options[schtipe.selectedIndex].value;
        schstatus = schstatus.options[schstatus.selectedIndex].value;

        param = 'txtsearch=' + txtsearch;
        param += '&orgsearch=' + schorg;
        param += '&tipesearch=' + schtipe;
        param += '&statussearch=' + schstatus;
        param += '&niksch=' + niksch;
        //patam+='&schjk='+schjk;
        param += '&page=' + page;

        //alert(param);	

        tujuan = 'sdm_slave_load_employee_list.php';
        post_response_text(tujuan, param, respog);

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        document.getElementById('searchplaceresult').innerHTML = con.responseText;
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}

function changeCaption(text) {
        document.getElementById('cap1').innerHTML = text;
}

function changeCaption1(text) {
        document.getElementById('cap2').innerHTML = text;
}

function editKaryawan(karyawanid, namakaryawan) {
        param = 'karyawanid=' + karyawanid;
        tujuan = 'sdm_slave_get_employee_for_edit.php';
        post_response_text(tujuan, param, respog);

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        loadFormKaryawan(con.responseText);
                                        document.getElementById('tanggalmasuk').disabled = true;
                                        document.getElementById('namakaryawan').disabled = true;
                                        document.getElementById('nik').disabled = true;
                                        document.getElementById('lokasitugas').disabled = true;
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}

function editKaryawan2(karyawanid, namakaryawan) {
        param = 'karyawanid=' + karyawanid;
        tujuan = 'sdm_slave_get_employee_for_edit.php';
        post_response_text(tujuan, param, respog);

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        loadFormKaryawan(con.responseText);
                                        document.getElementById('tanggalmasuk').disabled = true;
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}

function loadFormKaryawan(tex) {
        //display input form
        displayFormInput();
        //parse XML
        xml = tex.toString();
        xmlobject = (new DOMParser()).parseFromString(xml, "text/xml");

        //Extract XML
        karyawanid = xmlobject.getElementsByTagName('karyawanid')[0].firstChild.nodeValue;
        nik = xmlobject.getElementsByTagName('nik')[0].firstChild.nodeValue;
        nik = nik.replace("*", "");
        namakaryawan = xmlobject.getElementsByTagName('namakaryawan')[0].firstChild.nodeValue;
        namakaryawan = namakaryawan.replace("*", "");
        tempatlahir = xmlobject.getElementsByTagName('tempatlahir')[0].firstChild.nodeValue;
        tempatlahir = tempatlahir.replace("*", "");
        tanggallahir = xmlobject.getElementsByTagName('tanggallahir')[0].firstChild.nodeValue;
        warganegara = xmlobject.getElementsByTagName('warganegara')[0].firstChild.nodeValue;
        warganegara = warganegara.replace("*", "");
        jeniskelamin = xmlobject.getElementsByTagName('jeniskelamin')[0].firstChild.nodeValue;
        jeniskelamin = jeniskelamin.replace("*", "");
        statusperkawinan = xmlobject.getElementsByTagName('statusperkawinan')[0].firstChild.nodeValue;
        statusperkawinan = statusperkawinan.replace("*", "");
        catu = xmlobject.getElementsByTagName('catu')[0].firstChild.nodeValue;
        catu = catu.replace("*", "");
        tanggalmenikah = xmlobject.getElementsByTagName('tanggalmenikah')[0].firstChild.nodeValue;
        agama = xmlobject.getElementsByTagName('agama')[0].firstChild.nodeValue;
        agama = agama.replace("*", "");
        golongandarah = xmlobject.getElementsByTagName('golongandarah')[0].firstChild.nodeValue;
        golongandarah = golongandarah.replace("*", "");
        levelpendidikan = xmlobject.getElementsByTagName('levelpendidikan')[0].firstChild.nodeValue;
        levelpendidikan = levelpendidikan.replace("*", "");
        alamataktif = xmlobject.getElementsByTagName('alamataktif')[0].firstChild.nodeValue;
        alamataktif = alamataktif.replace("*", "");
        provinsi = xmlobject.getElementsByTagName('provinsi')[0].firstChild.nodeValue;
        provinsi = provinsi.replace("*", "");
        kota = xmlobject.getElementsByTagName('kota')[0].firstChild.nodeValue;
        kota = kota.replace("*", "");
        kodepos = xmlobject.getElementsByTagName('kodepos')[0].firstChild.nodeValue;
        kodepos = kodepos.replace("*", "");
        noteleponrumah = xmlobject.getElementsByTagName('noteleponrumah')[0].firstChild.nodeValue;
        noteleponrumah = noteleponrumah.replace("*", "");
        nohp = xmlobject.getElementsByTagName('nohp')[0].firstChild.nodeValue;
        nohp = nohp.replace("*", "");
        norekeningbank = xmlobject.getElementsByTagName('norekeningbank')[0].firstChild.nodeValue;
        norekeningbank = norekeningbank.replace("*", "");
        namabank = xmlobject.getElementsByTagName('namabank')[0].firstChild.nodeValue;
        namabank = namabank.replace("*", "");
        sistemgaji = xmlobject.getElementsByTagName('sistemgaji')[0].firstChild.nodeValue;
        sistemgaji = sistemgaji.replace("*", "");
        nopaspor = xmlobject.getElementsByTagName('nopaspor')[0].firstChild.nodeValue;
        nopaspor = nopaspor.replace("*", "");
        noktp = xmlobject.getElementsByTagName('noktp')[0].firstChild.nodeValue;
        noktp = noktp.replace("*", "");
        notelepondarurat = xmlobject.getElementsByTagName('notelepondarurat')[0].firstChild.nodeValue;
        notelepondarurat = notelepondarurat.replace("*", "");
        tanggalmasuk = xmlobject.getElementsByTagName('tanggalmasuk')[0].firstChild.nodeValue;
        tanggalkeluar = xmlobject.getElementsByTagName('tanggalkeluar')[0].firstChild.nodeValue;
        tipekaryawan = xmlobject.getElementsByTagName('tipekaryawan')[0].firstChild.nodeValue;
        tipekaryawan = tipekaryawan.replace("*", "");
        jumlahanak = xmlobject.getElementsByTagName('jumlahanak')[0].firstChild.nodeValue;
        jumlahanak = jumlahanak.replace("*", "");
        jumlahtanggungan = xmlobject.getElementsByTagName('jumlahtanggungan')[0].firstChild.nodeValue;
        jumlahtanggungan = jumlahtanggungan.replace("*", "");
        statuspajak = xmlobject.getElementsByTagName('statuspajak')[0].firstChild.nodeValue;
        statuspajak = statuspajak.replace("*", "");
        npwp = xmlobject.getElementsByTagName('npwp')[0].firstChild.nodeValue;
        npwp = npwp.replace("*", "");

        lokasipenerimaan = xmlobject.getElementsByTagName('lokasipenerimaan')[0].firstChild.nodeValue;
        lokasipenerimaan = lokasipenerimaan.replace("*", "");

        kodeorganisasi = xmlobject.getElementsByTagName('kodeorganisasi')[0].firstChild.nodeValue;
        kodeorganisasi = kodeorganisasi.replace("*", "");
        bagian = xmlobject.getElementsByTagName('bagian')[0].firstChild.nodeValue;
        bagian = bagian.replace("*", "");
        kodejabatan = xmlobject.getElementsByTagName('kodejabatan')[0].firstChild.nodeValue;
        kodejabatan = kodejabatan.replace("*", "");
        kodegolongan = xmlobject.getElementsByTagName('kodegolongan')[0].firstChild.nodeValue;
        kodegolongan = kodegolongan.replace("*", "");
        lokasitugas = xmlobject.getElementsByTagName('lokasitugas')[0].firstChild.nodeValue;
        lokasitugas = lokasitugas.replace("*", "");
        photo = xmlobject.getElementsByTagName('photo')[0].firstChild.nodeValue;
        photo = photo.replace("*", "");
        email = xmlobject.getElementsByTagName('email')[0].firstChild.nodeValue;
        email = email.replace("*", "");
        alokasi = xmlobject.getElementsByTagName('alokasi')[0].firstChild.nodeValue;
        alokasi = alokasi.replace("*", "");
        subbagian = xmlobject.getElementsByTagName('subbagian')[0].firstChild.nodeValue;
        subbagian = subbagian.replace("*", "");
        jms = xmlobject.getElementsByTagName('jms')[0].firstChild.nodeValue;
        jms = jms.replace("*", "");
        bpjskes = xmlobject.getElementsByTagName('bpjskes')[0].firstChild.nodeValue;
        bpjskes = bpjskes.replace("*", "");
        kecamatan = xmlobject.getElementsByTagName('kecamatan')[0].firstChild.nodeValue;
        kecamatan = kecamatan.replace("*", "");
        desa = xmlobject.getElementsByTagName('desa')[0].firstChild.nodeValue;
        desa = desa.replace("*", "");
        pangkat = xmlobject.getElementsByTagName('pangkat')[0].firstChild.nodeValue;
        pangkat = pangkat.replace("*", "");
        isduplicate = xmlobject.getElementsByTagName('isduplicate')[0].firstChild.nodeValue;
        isduplicate = isduplicate.replace("*", "");
        //catu		=xmlobject.getElementsByTagName('catu')[0].firstChild.nodeValue;
        //catu=catu.replace("*","");
        dptPremi = xmlobject.getElementsByTagName('dptPremi')[0].firstChild.nodeValue;
        //load form from extracted valiable
        document.getElementById('nik').value = nik;
        document.getElementById('namakaryawan').value = namakaryawan;
        document.getElementById('tempatlahir').value = tempatlahir;
        document.getElementById('tanggallahir').value = tanggallahir;




        jk = document.getElementById('jeniskelamin');
        for (x = 0; x < jk.length; x++) {
                if (jk.options[x].value == jeniskelamin) {
                        jk.options[x].selected = true;
                }
        }
        ag = document.getElementById('agama');
        for (x = 0; x < ag.length; x++) {
                if (ag.options[x].value == agama) {
                        ag.options[x].selected = true;
                }
        }
        bg = document.getElementById('bagian');
        for (x = 0; x < bg.length; x++) {
                if (bg.options[x].value == bagian) {
                        bg.options[x].selected = true;
                }
        }
        jbt = document.getElementById('kodejabatan');
        for (x = 0; x < jbt.length; x++) {
                if (jbt.options[x].value == kodejabatan) {
                        jbt.options[x].selected = true;
                }
        }
        gol = document.getElementById('kodegolongan');
        for (x = 0; x < gol.length; x++) {
                if (gol.options[x].value == kodegolongan) {
                        gol.options[x].selected = true;
                }
        }
        tgs = document.getElementById('lokasitugas');
        for (x = 0; x < tgs.length; x++) {
                if (tgs.options[x].value == lokasitugas) {
                        tgs.options[x].selected = true;
                }
        }
        org = document.getElementById('kodeorganisasi');
        for (x = 0; x < org.length; x++) {
                if (org.options[x].value == kodeorganisasi) {
                        org.options[x].selected = true;
                }
        }
        tik = document.getElementById('tipekaryawan');
        for (x = 0; x < tik.length; x++) {
                if (tik.options[x].value == tipekaryawan) {
                        tik.options[x].selected = true;
                }
        }

        tok = document.getElementById('subbagian');
        for (x = 0; x < tok.length; x++) {
                if (tok.options[x].value == subbagian) {
                        tok.options[x].selected = true;
                }
        }

        natura = document.getElementById('natura');
        for (x = 0; x < natura.length; x++) {
                if (natura.options[x].value == catu) {
                        natura.options[x].selected = true;
                }
        }


        /* cat=document.getElementById('catu');
                 for(x=0;x<cat.length;x++)
                 {
                         if(cat.options[x].value==catu)
                         {
                                 cat.options[x].selected=true;
                         }
                 }*/

        document.getElementById('noktp').value = noktp;
        document.getElementById('nopassport').value = nopaspor;
        wni = document.getElementById('warganegara');
        for (x = 0; x < wni.length; x++) {
                if (wni.options[x].value == warganegara) {
                        wni.options[x].selected = true;
                }
        }
        /*        poh=document.getElementById('lokasipenerimaan');
                        for(x=0;x<poh.length;x++)
                        {
                                if(poh.options[x].value==lokasipenerimaan)
                                {
                                        poh.options[x].selected=true;
                                }
                        }*/

        document.getElementById('lokasipenerimaan').value = lokasipenerimaan;

        stpj = document.getElementById('statuspajak');
        for (x = 0; x < stpj.length; x++) {
                if (stpj.options[x].value == statuspajak) {
                        stpj.options[x].selected = true;
                }
        }
        document.getElementById('npwp').value = npwp;
        document.getElementById('alamataktif').value = alamataktif;
        document.getElementById('kota').value = kota;
        prov = document.getElementById('provinsi');
        for (x = 0; x < prov.length; x++) {
                if (prov.options[x].value == provinsi) {
                        prov.options[x].selected = true;
                }
        }
        document.getElementById('kodepos').value = kodepos;
        document.getElementById('noteleponrumah').value = noteleponrumah;
        document.getElementById('nohp').value = nohp;
        document.getElementById('norekeningbank').value = norekeningbank;
        document.getElementById('namabank').value = namabank;

        stmgj = document.getElementById('sistemgaji');
        for (x = 0; x < stmgj.length; x++) {
                if (stmgj.options[x].value == sistemgaji) {
                        stmgj.options[x].selected = true;
                }
        }

        goldar = document.getElementById('golongandarah');
        for (x = 0; x < goldar.length; x++) {
                if (goldar.options[x].value == golongandarah) {
                        goldar.options[x].selected = true;
                }
        }
        document.getElementById('tanggalmasuk').value = tanggalmasuk;
        document.getElementById('tanggalkeluar').value = tanggalkeluar;
        stk = document.getElementById('statusperkawinan');
        for (x = 0; x < stk.length; x++) {
                if (stk.options[x].value == statusperkawinan) {
                        stk.options[x].selected = true;
                }
        }
        document.getElementById('tanggalmenikah').value = tanggalmenikah;
        document.getElementById('jumlahanak').value = jumlahanak;
        document.getElementById('jumlahtanggungan').value = jumlahtanggungan;
        document.getElementById('jms').value = jms;
        document.getElementById('bpjskes').value = bpjskes;
        lvlpndk = document.getElementById('levelpendidikan');
        for (x = 0; x < lvlpndk.length; x++) {
                if (lvlpndk.options[x].value == levelpendidikan) {
                        lvlpndk.options[x].selected = true;
                }
        }
        document.getElementById('notelepondarurat').value = notelepondarurat;
        document.getElementById('email').value = email;
        document.getElementById('alokasi').value = alokasi;
        //change the method to update===========================	
        document.getElementById('method').value = 'update';
        document.getElementById('karyawanid').value = karyawanid;
        document.getElementById('tabFRM0').innerHTML = namakaryawan;
        document.getElementById('dptPremi').checked = false;
        if (dptPremi == 1) {
                document.getElementById('dptPremi').checked = true;
        }
        document.getElementById('kecamatan').value = kecamatan;
        document.getElementById('desa').value = desa;
        document.getElementById('pangkat').value = pangkat;

        if(isduplicate == 1){
                document.getElementById("isduplicate").checked = true;
        }else{
                document.getElementById("isduplicate").checked = false;
        }
        //alert(lokasitugas);


        //=========================		
        //enable save button each tab
        enableOtherButton();
        
        //loac photo
        document.getElementById('displayphoto').removeAttribute('src');
        document.getElementById('displayphoto').setAttribute('src', photo);


        getSub(lokasitugas, subbagian, karyawanid);
        
}

function loadExperience(x, karyawanid) {
        param = 'queryonly=' + x + '&karyawanid=' + karyawanid;
        tujuan = 'sdm_slave_save_riwayat_pekerjaan.php';

        post_response_text(tujuan, param, respog);

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        document.getElementById('container').innerHTML = con.responseText;
                                        loadPendidikan('queryonly', karyawanid);
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}

function loadPendidikan(x, karyawanid) {
        param = 'queryonly=' + x + '&karyawanid=' + karyawanid;
        tujuan = 'sdm_slave_save_riwayat_pendidikan.php';

        post_response_text(tujuan, param, respog);

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        document.getElementById('containerpendidikan').innerHTML = con.responseText;
                                        loadKursus(x, karyawanid);
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}

function loadKursus(x, karyawanid) {
        param = 'queryonly=' + x + '&karyawanid=' + karyawanid;
        tujuan = 'sdm_slave_save_riwayat_training.php';
        post_response_text(tujuan, param, respog);

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        document.getElementById('containertraining').innerHTML = con.responseText;
                                        loadKeluarga(x, karyawanid);
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}

function loadKeluarga(x, karyawanid) {
        param = 'queryonly=' + x + '&karyawanid=' + karyawanid;
        tujuan = 'sdm_slave_save_keluarga.php';
        post_response_text(tujuan, param, respog);

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        document.getElementById('containerkeluarga').innerHTML = con.responseText;
                                        loadAlamat(x, karyawanid);
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}

function loadAlamat(x, karyawanid) {
        param = 'queryonly=' + x + '&karyawanid=' + karyawanid;
        tujuan = 'sdm_slave_save_alamat_karyawan.php';
        post_response_text(tujuan, param, respog);

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        document.getElementById('containeralamat').innerHTML = con.responseText;
                                        loadKontrak(karyawanid);
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}

function loadKontrak(karyawanid) {
        param = 'proses=loadData' + '&karyawanid=' + karyawanid;
        tujuan = 'sdm_slave_save_kontrak.php';
        post_response_text(tujuan, param, respog);

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        console.log(con.responseText);
                                        document.getElementById('kontrakcontainer').innerHTML = con.responseText;
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}

function simpanKontrak() {
        karyawanid = document.getElementById('karyawanid').value;
        kontrak_nama_perusahaan = document.getElementById('kontrak_nama_perusahaan').value;
        kontrak_bidang_usaha = document.getElementById('kontrak_bidang_usaha').value;
        tanggal_mulai_kontrak = document.getElementById('tanggal_mulai_kontrak').value;
        tanggal_selesai_kontrak = document.getElementById('tanggal_selesai_kontrak').value;
        kontrak_jabatan = document.getElementById('kontrak_jabatan').value;
        kontrak_bagian = document.getElementById('kontrak_bagian').value;
        kontrak_alamat = document.getElementById('kontrak_alamat').value;
        kontrak_tipe_karyawan = document.getElementById('kontrak_tipe_karyawan').value;
        kontrak_status_cuti = document.getElementById('kontrak_status_cuti').value;
        
        if (kontrak_nama_perusahaan == '' || kontrak_bidang_usaha == '' || tanggal_mulai_kontrak == '' || kontrak_jabatan == '' || kontrak_bagian == '' || kontrak_nama_perusahaan == '') {
                alert('Data incomplete');
        } else {
                param = 'karyawanid=' + karyawanid + '&nama_perusahaan=' + kontrak_nama_perusahaan;
                param += '&bidang=' + kontrak_bidang_usaha;
                param += '&tipe_karyawan=' + kontrak_tipe_karyawan;
                param += '&status_cuti=' + kontrak_status_cuti;
                param += '&tanggal_mulai=' + tanggal_mulai_kontrak + '&tanggal_akhir=' + tanggal_selesai_kontrak;
                param += '&jabatan=' + kontrak_jabatan + '&bagian=' + kontrak_bagian + '&alamat=' + kontrak_alamat;
                param += '&proses=simpan';
                tujuan = 'sdm_slave_save_kontrak.php';
                if (confirm('Saving, are you sure..?')) {
                        post_response_text(tujuan, param, respog);
                }
        }

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        alert(con.responseText);
                                        clearKontrak();
                                        loadKontrak(karyawanid);
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}
function deleteKontrak(idkontrak) {
        param = 'idkontrak=' + idkontrak;
        param += '&proses=delete';
        tujuan = 'sdm_slave_save_kontrak.php';
        if (confirm('Delete, are you sure..?')) {
                post_response_text(tujuan, param, respog);
        }

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        alert(con.responseText);
                                        loadKontrak(karyawanid);
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}

function clearKontrak() {
        document.getElementById('kontrak_nama_perusahaan').value = '';
        document.getElementById('kontrak_bidang_usaha').value = '';
        document.getElementById('tanggal_mulai_kontrak').value = '';
        document.getElementById('tanggal_selesai_kontrak').value = '';
        document.getElementById('kontrak_jabatan').value = '';
        document.getElementById('kontrak_bagian').value = '';
        document.getElementById('kontrak_alamat').value = '';

}

function previewKaryawan(karid, nama, ev) {
        param = 'karyawanid=' + karid + '&namakaryawan=' + nama;
        tujuan = 'sdm_slave_get_karyawan_preview.php';
        post_response_text(tujuan, param, respog);

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        content = con.responseText;
                                        //display window
                                        title = nama;
                                        width = '700';
                                        height = '400';
                                        showDialog1(title, content, width, height, ev);
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}

function previewKaryawanPDF(karid, nama, ev) {
        param = 'karyawanid=' + karid + '&namakaryawan=' + nama;
        tujuan = 'sdm_slave_get_karyawan_preview_pdf.php?' + param;
        //display window
        title = nama;
        width = '700';
        height = '400';
        content = "<iframe frameborder=0 width=100% height=100% src='" + tujuan + "'></iframe>"
        showDialog1(title, content, width, height, ev);
}



//---------------------------------------------------------------------------------------------------------------------------------


function ubah_listmsk(page) {


        txtsearch = document.getElementById('txtsearch').value; //options[document.getElementById('schorg').selectedIndex].value;
        schorg = document.getElementById('schorg').options[document.getElementById('schorg').selectedIndex].value;
        schtipe = document.getElementById('schtipe').options[document.getElementById('schtipe').selectedIndex].value;
        schstatus = document.getElementById('schstatus').options[document.getElementById('schstatus').selectedIndex].value;
        schjk = document.getElementById('schjk').options[document.getElementById('schjk').selectedIndex].value;

        thnmsk = document.getElementById('thnmsk').options[document.getElementById('thnmsk').selectedIndex].value;
        blnmsk = document.getElementById('blnmsk').options[document.getElementById('blnmsk').selectedIndex].value;
        thnkel = document.getElementById('thnkel').options[document.getElementById('thnkel').selectedIndex].value;
        blnkel = document.getElementById('blnkel').options[document.getElementById('blnkel').selectedIndex].value;


        param = 'thnmsk=' + thnmsk + '&blnmsk=' + blnmsk + '&thnkel=' + thnkel + '&blnkel=' + blnkel + '&schorg=' + schorg + '&schtipe=' + schtipe + '&schstatus=' + schstatus + '&txtsearch=' + txtsearch + '&schjk=' + schjk;

        //alert (param);
        tujuan = 'sdm_slave_load_employeeLaporan.php';
        post_response_text(tujuan, param, respog);

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        //alert(con.responseText);
                                        document.getElementById('searchplaceresult').innerHTML = con.responseText;
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}


//INIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIII
function cariKaryawanLaporan1(page) {

        document.getElementById('thnmsk').value = '';
        document.getElementById('blnmsk').value = '';
        document.getElementById('thnkel').value = '';
        document.getElementById('blnkel').value = '';



        schjk = document.getElementById('schjk');
        txtsearch = trim(document.getElementById('txtsearch').value);
        schorg = document.getElementById('schorg');
        schtipe = document.getElementById('schtipe');
        schstatus = document.getElementById('schstatus');
        schorg = schorg.options[schorg.selectedIndex].value;
        schtipe = schtipe.options[schtipe.selectedIndex].value;
        schstatus = schstatus.options[schstatus.selectedIndex].value;

        param = 'txtsearch=' + txtsearch;


        param += '&orgsearch=' + schorg;
        param += '&tipesearch=' + schtipe;
        param += '&statussearch=' + schstatus;
        param += '&schjk=' + schjk;
        param += '&page=' + page;

        //alert(param);

        tujuan = 'sdm_slave_load_employeeLaporan.php';
        post_response_text(tujuan, param, respog);

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        document.getElementById('searchplaceresult').innerHTML = con.responseText;
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}





function cariKaryawanLaporan(page) {
        //alert('MASUK');
        thnmsk = trim(document.getElementById('thnmsk').value);
        blnmsk = trim(document.getElementById('blnmsk').value);
        thnkel = trim(document.getElementById('thnkel').value);
        blnkel = trim(document.getElementById('blnkel').value);
        document.getElementById('prefbtn').value = 0;
        document.getElementById('nextbtn').value = 2;

        
        txtsearch = trim(document.getElementById('txtsearch').value);
        nik = document.getElementById('nik').value;

        schorg = document.getElementById('schorg');
        schtipe = document.getElementById('schtipe');
        schstatus = document.getElementById('schstatus');
        schjk = document.getElementById('schjk');
        tipe_duplikat = document.getElementById('tipe_duplikat');
        kode_resign = document.getElementById('kode_resign');

        schorg = schorg.options[schorg.selectedIndex].value;
        schtipe = schtipe.options[schtipe.selectedIndex].value;
        schstatus = schstatus.options[schstatus.selectedIndex].value;
        schjk = schjk.options[schjk.selectedIndex].value;
        tipe_duplikat = tipe_duplikat.options[tipe_duplikat.selectedIndex].value;
        kode_resign = kode_resign.options[kode_resign.selectedIndex].value;
        
        param = 'txtsearch=' + txtsearch;
        param += '&nik=' + nik;

        param += '&orgsearch=' + schorg;
        param += '&tipesearch=' + schtipe;
        param += '&statussearch=' + schstatus;
        param += '&schjk=' + schjk;
        param += '&tipe_duplikat=' + tipe_duplikat;
        param += '&kode_resign=' + kode_resign;

        param += '&thnmsk=' + thnmsk;
        param += '&blnmsk=' + blnmsk;
        param += '&thnkel=' + thnkel;
        param += '&blnkel=' + blnkel;

        param += '&page=' + page;

        //	alert(param);

        tujuan = 'sdm_slave_load_employeeLaporan.php';
        post_response_text(tujuan, param, respog);

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        document.getElementById('searchplaceresult').innerHTML = con.responseText;
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}

function prefDatakaryawan1(btn, curval) {
        cariKaryawanLaporan(curval);
        if (curval == 0) {} else
                btn.value = parseInt(curval) - 1;
        document.getElementById('nextbtn').value = parseInt(btn.value) + 1;

}

function nextDatakaryawan1(btn, curval) {
        cariKaryawanLaporan(curval);
        btn.value = parseInt(curval) + 1;
        document.getElementById('prefbtn').value = parseInt(btn.value) - 2;
}



function datakaryawanExcel(ev, tujuan) {

        schjk = document.getElementById('schjk');
        schjk = schjk.options[schjk.selectedIndex].value;
        txtsearch = trim(document.getElementById('txtsearch').value);
        schorg = document.getElementById('schorg');
        schtipe = document.getElementById('schtipe');
        schstatus = document.getElementById('schstatus');
        schorg = schorg.options[schorg.selectedIndex].value;
        schtipe = schtipe.options[schtipe.selectedIndex].value;
        schstatus = schstatus.options[schstatus.selectedIndex].value;
        param = 'txtsearch=' + txtsearch;

        thnmsk = trim(document.getElementById('thnmsk').value);
        blnmsk = trim(document.getElementById('blnmsk').value);
        thnkel = trim(document.getElementById('thnkel').value);
        blnkel = trim(document.getElementById('blnkel').value);


        param += '&orgsearch=' + schorg;
        param += '&tipesearch=' + schtipe;
        param += '&statussearch=' + schstatus;
        param += '&thnmsk=' + thnmsk;
        param += '&schjk=' + schjk;
        param += '&blnmsk=' + blnmsk;
        param += '&thnkel=' + thnkel;
        param += '&blnkel=' + blnkel;


        tujuan = 'sdm_slave_datakaryawan_Excel.php?' + param;
        title = 'Download';
        width = '500';
        height = '400';
        content = "<iframe frameborder=0 width=100% height=100% src='" + tujuan + "'></iframe>"
        showDialog1(title, content, width, height, ev);
}



function delKaryawan(ki, nm) {
        param = 'method=delete' + '&ki=' + ki;
        //alert(param);
        tujuan = 'sdm_slave_get_employee_for_delete.php';
        if (confirm("Are you sure delete all data for employee: " + nm + " ?? ")) {
                post_response_text(tujuan, param, respog);
        }

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        loadEmployeeList();

                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}

function validat(ev) {
        key = getKey(ev);
        if (key == 13) {
                cariKaryawan(1);
                document.getElementById('txtsearch').value = '';
                document.getElementById('niksch').value = '';
        } else {
                return tanpa_kutip_dan_sepasi(ev);
        }
}