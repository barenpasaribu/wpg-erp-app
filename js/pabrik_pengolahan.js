// 06-07-2020

/*
    ALUR GET DATA
    - getDataTimbangan() [01]
        - hitungTotalTBS();
            - hitungTRatarataLori();
                - hitungTBSOlah();
            - hitungTBSOlah();
                - hitungTBSAkhir();
            - hitungTBSAkhir();
        - sumSortasiTidakOlah(); [02]
            - getTBSSisa(); [03]
                - hitungTotalTBS();
                    - hitungTRatarataLori();
                        - hitungTBSOlah();
                    - hitungTBSOlah();
                        - hitungTBSAkhir();
                    - hitungTBSAkhir();
                - getPengiriman();
                    - getReturn();

*/

function hitungPotongan() {
    tbs_masuk_bruto = document.getElementById('tbs_masuk_bruto').value;
    tbs_potongan = document.getElementById('tbs_potongan').value;

    if (tbs_masuk_bruto > 0) {
        document.getElementById('tbs_masuk_netto').value = tbs_masuk_bruto - tbs_potongan;
    }
    
    sumSortasiTidakOlah();
}

function sumSortasiTidakOlah() {
    kodeorg=document.getElementById('kodeorg').value;
	tanggal=document.getElementById('tanggal').value;
	param='kodeorg='+kodeorg+'&tanggal='+tanggal;
	param+='&proses=getSortasiTidakOlah';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
				busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    var data = JSON.parse(con.responseText);    
                    tbs_potongan = document.getElementById('tbs_potongan').value;  
                    tbs_potongan_olah = parseInt(tbs_potongan) + parseInt(data.sortasi);

                    document.getElementById('tbs_potongan_olah').value = tbs_potongan_olah;
                    tbs_bruto = document.getElementById('tbs_masuk_bruto').value;

                    if (tbs_bruto > 0) {
                        document.getElementById('tbs_masuk_netto').value = tbs_bruto - tbs_potongan;
                    }
                    
                    tbs_diolah = document.getElementById('tbs_diolah').value;
                    if (tbs_diolah > 0) {
                        document.getElementById('tbs_diolah_after').value = tbs_diolah - tbs_potongan_olah;
                    }
                    getTBSSisa();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }    
    post_response_text('pabrik_slave_get_data.php', param, respon);
}

function hitungTotalJamShift() {
    var total_jam_shift_1 = document.getElementById('total_jam_shift_1').value;
    var total_jam_shift_2 = document.getElementById('total_jam_shift_2').value;
    var total_jam_shift_3 = document.getElementById('total_jam_shift_3').value;

    total_jam_shift = parseFloat(total_jam_shift_1) + parseFloat(total_jam_shift_2) +  parseFloat(total_jam_shift_3);
    document.getElementById('total_jam_shift').value = total_jam_shift;
}

function hitungTotalJamPress() {
    var total_jam_press_shift_1 = document.getElementById('total_jam_press_shift_1').value;
    var total_jam_press_shift_2 = document.getElementById('total_jam_press_shift_2').value;
    var total_jam_press_shift_3 = document.getElementById('total_jam_press_shift_3').value;

    total_jam_press = parseFloat(total_jam_press_shift_1) + parseFloat(total_jam_press_shift_2) +  parseFloat(total_jam_press_shift_3);
    document.getElementById('total_jam_press').value = total_jam_press;
}

function hitungTotalJamOperasi() {
    var total_jam_operasi_shift_1 = document.getElementById('total_jam_operasi_shift_1').value;
    var total_jam_operasi_shift_2 = document.getElementById('total_jam_operasi_shift_2').value;
    var total_jam_operasi_shift_3 = document.getElementById('total_jam_operasi_shift_3').value;

    total_jam_operasi = parseFloat(total_jam_operasi_shift_1) + parseFloat(total_jam_operasi_shift_2) +  parseFloat(total_jam_operasi_shift_3);
    document.getElementById('total_jam_operasi').value = total_jam_operasi;
}

function hitungTotalJamIdle() {
    var jam_idle_shift_1 = document.getElementById('jam_idle_shift_1').value;
    var jam_idle_shift_2 = document.getElementById('jam_idle_shift_2').value;
    var jam_idle_shift_3 = document.getElementById('jam_idle_shift_3').value;

    total_jam_idle = parseFloat(jam_idle_shift_1) + parseFloat(jam_idle_shift_2) +  parseFloat(jam_idle_shift_3);
    document.getElementById('total_jam_idle').value = total_jam_idle;
}

function lihatDataTimbangan() {
    tanggal = document.getElementById('tanggal').value;
    kodeorg = document.getElementById('kodeorg').value;

    // Post to Slave
    if(tanggal=='' || tanggal==null || tanggal == "00-00-0000") {
        alert("Tanggal belum dipilih");
    }else{
        param = "tanggal="+tanggal;
        param += "&kodeorg="+kodeorg;
        showDialog1('Data Timbangan',"<iframe frameborder=0 style='width:795px;height:400px' src='pabrik_slave_pengolahan_list.php?"+param+"'></iframe>",'800','400','event');
        var dialog = document.getElementById('dynamic1');
        dialog.style.top = '100px';
        dialog.style.left = '15%';
    }

    
}

function hitungTotalLori() {
    var lori_olah_shift_1 = document.getElementById('lori_olah_shift_1').value;
    var lori_olah_shift_2 = document.getElementById('lori_olah_shift_2').value;
    var lori_olah_shift_3 = document.getElementById('lori_olah_shift_3').value;
    var lori_dalam_rebusan = document.getElementById('lori_dalam_rebusan').value;
    var restan_depan_rebusan = document.getElementById('restan_depan_rebusan').value;
    var restan_dibelakang_rebusan = document.getElementById('restan_dibelakang_rebusan').value;
    var estimasi_di_peron = document.getElementById('estimasi_di_peron').value;
    total_lori = parseInt(lori_olah_shift_1) + parseInt(lori_olah_shift_2) + parseInt(lori_olah_shift_3) + parseInt(lori_dalam_rebusan) + parseInt(restan_depan_rebusan) + parseInt(restan_dibelakang_rebusan) + parseInt(estimasi_di_peron);
    document.getElementById('total_lori').value = total_lori;
    hitungTRatarataLori();
}

function hitungTRatarataLori() {
    total_tbs = document.getElementById('total_tbs').value;
    total_lori = document.getElementById('total_lori').value;

    if (total_lori != 0) {
        rata_rata_lori = parseFloat(total_tbs) / parseFloat(total_lori);
    }else{
        rata_rata_lori = 0;
    }
    
    document.getElementById('rata_rata_lori').value = rata_rata_lori.toFixed(0);
    hitungTBSOlah();
}

function hitungTBSAkhir() {
    total_tbs = document.getElementById('total_tbs').value;
    tbs_diolah = document.getElementById('tbs_diolah').value;
    tbs_sisa = parseFloat(total_tbs) - parseFloat(tbs_diolah);
    document.getElementById('tbs_sisa').value = tbs_sisa;
}

function hitungTBS() {
    tbs_sisa_kemarin = document.getElementById('tbs_sisa_kemarin').value;
    tbs_masuk_bruto = document.getElementById('tbs_masuk_bruto').value;
    total_tbs = parseFloat(tbs_sisa_kemarin) + parseFloat(tbs_masuk_bruto);
    document.getElementById('total_tbs').value = total_tbs;

    hitungPotongan();
    hitungTRatarataLori();
    hitungTBSOlah();
    hitungTBSAkhir();
}

function hitungTBSOlah() {
    lori_olah_shift_1 = document.getElementById('lori_olah_shift_1').value;
    lori_olah_shift_2 = document.getElementById('lori_olah_shift_2').value;
    lori_olah_shift_3 = document.getElementById('lori_olah_shift_3').value;
    rata_rata_lori = document.getElementById('rata_rata_lori').value;
    tbs_potongan = document.getElementById('tbs_potongan_olah').value;
    
    tbs_diolah = (parseFloat(lori_olah_shift_1) + parseFloat(lori_olah_shift_2) + parseFloat(lori_olah_shift_3)) * parseFloat(rata_rata_lori);

    var bagi = tbs_diolah / 10;
    var round = Math.round(bagi);
    var hasil = round * 10; 
    document.getElementById('tbs_diolah').value = hasil;
    if (hasil > 0) {
        document.getElementById('tbs_diolah_after').value = hasil - tbs_potongan;
    }
    
    hitungTBSAkhir();
}



function hitungTotalTBS() {
    tbs_sisa_kemarin = document.getElementById('tbs_sisa_kemarin').value;
    tbs_masuk_bruto = document.getElementById('tbs_masuk_bruto').value;
    document.getElementById('total_tbs').value = parseFloat(tbs_sisa_kemarin) + parseFloat(tbs_masuk_bruto);
    hitungTRatarataLori();
    hitungTBSOlah();
    hitungTBSAkhir();
}

function getReturn() {
	kodeorg=document.getElementById('kodeorg').value;
	tanggal=document.getElementById('tanggal').value;
	param='kodeorg='+kodeorg+'&tanggal='+tanggal;
	param+='&proses=getReturn';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
				busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    var data = JSON.parse(con.responseText);                                       
                    document.getElementById('return_cpo').value = data.return_cpo;
                    document.getElementById('return_pk').value = data.return_pk;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }    
    post_response_text('pabrik_slave_get_data.php', param, respon);
}

function getPengiriman() {
	kodeorg=document.getElementById('kodeorg').value;
	tanggal=document.getElementById('tanggal').value;
	param='kodeorg='+kodeorg+'&tanggal='+tanggal;
	param+='&proses=getPengirimanHI';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
				busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    var data = JSON.parse(con.responseText);                                       
                    document.getElementById('despatch_cpo').value = data.cpo;
                    document.getElementById('despatch_pk').value = data.kernel;
                    document.getElementById('janjang_kosong').value = 0;
                    document.getElementById('limbah_cair').value = 0;
                    document.getElementById('solid_decnter').value = 0;
                    document.getElementById('abu_janjang').value = data.abu_janjang;
                    document.getElementById('cangkang').value = data.cangkang;
                    document.getElementById('fibre').value = data.fiber;
                    getReturn();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }    
    post_response_text('pabrik_slave_get_data.php', param, respon);
}

function getTBSSisa() {
	kodeorg=document.getElementById('kodeorg').value;
	tanggal=document.getElementById('tanggal').value;
	param='kodeorg='+kodeorg+'&tanggal='+tanggal;
	param+='&proses=getTBSSisaKemarinPengolahanPabrik';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
				busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    var data = JSON.parse(con.responseText);
                    var tbs_sisa = 0;
                    if(data != null){
                        tbs_sisa = data.tbs_sisa;
                    }
                    
                    document.getElementById('tbs_sisa_kemarin').value = tbs_sisa;
                    hitungTotalTBS();
                    getPengiriman();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }    
    post_response_text('pabrik_slave_get_data.php', param, respon);
}

function getDataTimbangan() {
	kodeorg=document.getElementById('kodeorg').value;
	tanggal=document.getElementById('tanggal').value;
	param='kodeorg='+kodeorg+'&tanggal='+tanggal;
	param+='&proses=getDataTimbanganPengolahanPabrik';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
				busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    var dataTBS = JSON.parse(con.responseText);
                    var tbs_bruto = 0;
                    var tbs_potongan = 0;
                    if(dataTBS.tbs_bruto != null){
                        tbs_bruto = dataTBS.tbs_bruto;
                        tbs_potongan = dataTBS.tbs_potongan;
                    }
                    
                    document.getElementById('tbs_masuk_bruto').value = tbs_bruto;
                    document.getElementById('tbs_masuk_netto').value = tbs_bruto - tbs_potongan;
                    document.getElementById('tbs_potongan').value = tbs_potongan;
                    document.getElementById('tbs_potongan_olah').value = tbs_potongan;
                    hitungTotalTBS();
                    sumSortasiTidakOlah();
                    
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }    
    post_response_text('pabrik_slave_get_data.php', param, respon);
}

function hitungJamIdle() {
    var total_jam_shift_1 = document.getElementById('total_jam_shift_1').value;
    var total_jam_operasi_shift_1 = document.getElementById('total_jam_operasi_shift_1').value;

    total_shift = parseFloat(total_jam_shift_1);
    total_operasi = parseFloat(total_jam_operasi_shift_1);

    total = total_shift - total_operasi;

    if (total < 0) {
        total = total * -1;
    }

    document.getElementById('jam_idle_shift_1').value = total;
    hitungTotalJamIdle();
}

function hitungJamIdle2() {
    var total_jam_shift_2 = document.getElementById('total_jam_shift_2').value;
    var total_jam_operasi_shift_2 = document.getElementById('total_jam_operasi_shift_2').value;

    total_shift = parseFloat(total_jam_shift_2);
    total_operasi = parseFloat(total_jam_operasi_shift_2);

    total = total_shift - total_operasi;

    if (total < 0) {
        total = total * -1;
    }

    document.getElementById('jam_idle_shift_2').value = total;
    hitungTotalJamIdle();
}

function hitungJamIdle3() {
    var total_jam_shift_3 = document.getElementById('total_jam_shift_3').value;
    var total_jam_operasi_shift_3 = document.getElementById('total_jam_operasi_shift_3').value;

    total_shift = parseFloat(total_jam_shift_3);
    total_operasi = parseFloat(total_jam_operasi_shift_3);

    total = total_shift - total_operasi;

    if (total < 0) {
        total = total * -1;
    }

    document.getElementById('jam_idle_shift_3').value = total;
    hitungTotalJamIdle();
}


function hitungJamOperasi1() {
    var jam_start_operasi_shift_1_jam = document.getElementById('jam_start_operasi_shift_1_jam').value;
    var jam_start_operasi_shift_1_menit = document.getElementById('jam_start_operasi_shift_1_menit').value;
    var jam_stop_operasi_shift_1_jam = document.getElementById('jam_stop_operasi_shift_1_jam').value;
    var jam_stop_operasi_shift_1_menit = document.getElementById('jam_stop_operasi_shift_1_menit').value;

    if (jam_start_operasi_shift_1_jam > jam_stop_operasi_shift_1_jam) {
        var jam_x = parseInt(jam_start_operasi_shift_1_jam) * 60;
        var menit_x = parseInt(jam_start_operasi_shift_1_menit);

        var jam_y = (parseInt(jam_stop_operasi_shift_1_jam) + 24) * 60;
        var menit_y = parseInt(jam_stop_operasi_shift_1_menit);
        
        var hasil_x = jam_x + menit_x;
        var hasil_y = jam_y + menit_y;
        var selisih = (hasil_y - hasil_x);
        var menit = selisih % 60;
        var jam = selisih - menit;
        menit = menit / 60;
        jam = jam / 60;
    }else{
        var jam_x = parseInt(jam_start_operasi_shift_1_jam) * 60;
        var menit_x = parseInt(jam_start_operasi_shift_1_menit);
        var jam_y = parseInt(jam_stop_operasi_shift_1_jam) * 60;
        var menit_y = parseInt(jam_stop_operasi_shift_1_menit);
        var hasil_x = jam_x + menit_x;
        var hasil_y = jam_y + menit_y;
        var selisih = (hasil_y - hasil_x);
        var menit = selisih % 60;
        var jam = selisih - menit;
        menit = menit / 60;
        jam = jam / 60;
    }

    
    var hasilWaktu = jam + menit;
    hasilWaktu = hasilWaktu.toFixed(2);
    document.getElementById('total_jam_operasi_shift_1').value = hasilWaktu;
    hitungTotalJamOperasi();
    hitungJamIdle();
    hitungJamIdle2();
    hitungJamIdle3();
}

function hitungJamOperasi2() {
    var jam_start_operasi_shift_2_jam = document.getElementById('jam_start_operasi_shift_2_jam').value;
    var jam_start_operasi_shift_2_menit = document.getElementById('jam_start_operasi_shift_2_menit').value;
    var jam_stop_operasi_shift_2_jam = document.getElementById('jam_stop_operasi_shift_2_jam').value;
    var jam_stop_operasi_shift_2_menit = document.getElementById('jam_stop_operasi_shift_2_menit').value;

    if (jam_start_operasi_shift_2_jam > jam_stop_operasi_shift_2_jam) {
        var jam_x = parseInt(jam_start_operasi_shift_2_jam) * 60;
        var menit_x = parseInt(jam_start_operasi_shift_2_menit);

        var jam_y = (parseInt(jam_stop_operasi_shift_2_jam) + 24) * 60;
        var menit_y = parseInt(jam_stop_operasi_shift_2_menit);
        
        var hasil_x = jam_x + menit_x;
        var hasil_y = jam_y + menit_y;
        var selisih = (hasil_y - hasil_x);
        var menit = selisih % 60;
        var jam = selisih - menit;
        menit = menit / 60;
        jam = jam / 60;
    }else{
        var jam_x = parseInt(jam_start_operasi_shift_2_jam) * 60;
        var menit_x = parseInt(jam_start_operasi_shift_2_menit);
        var jam_y = parseInt(jam_stop_operasi_shift_2_jam) * 60;
        var menit_y = parseInt(jam_stop_operasi_shift_2_menit);
        var hasil_x = jam_x + menit_x;
        var hasil_y = jam_y + menit_y;
        var selisih = (hasil_y - hasil_x);
        var menit = selisih % 60;
        var jam = selisih - menit;
        menit = menit / 60;
        jam = jam / 60;
    }
    var hasilWaktu = jam + menit;
    hasilWaktu = hasilWaktu.toFixed(2);
    document.getElementById('total_jam_operasi_shift_2').value = hasilWaktu;
    hitungTotalJamOperasi();
    hitungJamIdle();
    hitungJamIdle2();
    hitungJamIdle3();
}

function hitungJamOperasi3() {
    var jam_start_operasi_shift_3_jam = document.getElementById('jam_start_operasi_shift_3_jam').value;
    var jam_start_operasi_shift_3_menit = document.getElementById('jam_start_operasi_shift_3_menit').value;
    var jam_stop_operasi_shift_3_jam = document.getElementById('jam_stop_operasi_shift_3_jam').value;
    var jam_stop_operasi_shift_3_menit = document.getElementById('jam_stop_operasi_shift_3_menit').value;

    if (jam_start_operasi_shift_3_jam > jam_stop_operasi_shift_3_jam) {
        var jam_x = parseInt(jam_start_operasi_shift_3_jam) * 60;
        var menit_x = parseInt(jam_start_operasi_shift_3_menit);

        var jam_y = (parseInt(jam_stop_operasi_shift_3_jam) + 24) * 60;
        var menit_y = parseInt(jam_stop_operasi_shift_3_menit);
        
        var hasil_x = jam_x + menit_x;
        var hasil_y = jam_y + menit_y;
        var selisih = (hasil_y - hasil_x);
        var menit = selisih % 60;
        var jam = selisih - menit;
        menit = menit / 60;
        jam = jam / 60;
    }else{
        var jam_x = parseInt(jam_start_operasi_shift_3_jam) * 60;
        var menit_x = parseInt(jam_start_operasi_shift_3_menit);
        var jam_y = parseInt(jam_stop_operasi_shift_3_jam) * 60;
        var menit_y = parseInt(jam_stop_operasi_shift_3_menit);
        var hasil_x = jam_x + menit_x;
        var hasil_y = jam_y + menit_y;
        var selisih = (hasil_y - hasil_x);
        var menit = selisih % 60;
        var jam = selisih - menit;
        menit = menit / 60;
        jam = jam / 60;
    }
    var hasilWaktu = jam + menit;
    hasilWaktu = hasilWaktu.toFixed(2);
    document.getElementById('total_jam_operasi_shift_3').value = hasilWaktu;
    hitungTotalJamOperasi();
    hitungJamIdle();
    hitungJamIdle2();
    hitungJamIdle3();
}

function hitungJamShift1() {
    var jam_start_shift_1_jam = document.getElementById('jam_start_shift_1_jam').value;
    var jam_start_shift_1_menit = document.getElementById('jam_start_shift_1_menit').value;
    var jam_stop_shift_1_jam = document.getElementById('jam_stop_shift_1_jam').value;
    var jam_stop_shift_1_menit = document.getElementById('jam_stop_shift_1_menit').value;

    if (jam_start_shift_1_jam > jam_stop_shift_1_jam) {
        var jam_x = parseInt(jam_start_shift_1_jam) * 60;
        var menit_x = parseInt(jam_start_shift_1_menit);
        var jam_y = (parseInt(jam_stop_shift_1_jam) + 24) * 60;
        var menit_y = parseInt(jam_stop_shift_1_menit);
        
        var hasil_x = jam_x + menit_x;
        var hasil_y = jam_y + menit_y;
        var selisih = (hasil_y - hasil_x);
        var menit = selisih % 60;
        var jam = selisih - menit;
        menit = menit / 60;
        jam = jam / 60;
    }else{
        var jam_x = parseInt(jam_start_shift_1_jam) * 60;
        var menit_x = parseInt(jam_start_shift_1_menit);
        var jam_y = parseInt(jam_stop_shift_1_jam) * 60;
        var menit_y = parseInt(jam_stop_shift_1_menit);
        var hasil_x = jam_x + menit_x;
        var hasil_y = jam_y + menit_y;
        var selisih = (hasil_y - hasil_x);
        var menit = selisih % 60;
        var jam = selisih - menit;
        menit = menit / 60;
        jam = jam / 60;
    }
    var hasilWaktu = jam + menit;
    hasilWaktu = hasilWaktu.toFixed(2);
    document.getElementById('total_jam_shift_1').value = hasilWaktu;
    hitungTotalJamShift();
    hitungJamIdle();
    hitungJamIdle2();
    hitungJamIdle3();
}

function hitungJamStagnasi() {
    var jam_start_shift_1_jam = document.getElementById('start_time_stagnasi_jam').value;
    var jam_start_shift_1_menit = document.getElementById('start_time_stagnasi_menit').value;
    var jam_stop_shift_1_jam = document.getElementById('stop_time_stagnasi_jam').value;
    var jam_stop_shift_1_menit = document.getElementById('stop_time_stagnasi_menit').value;

    if (jam_start_shift_1_jam > jam_stop_shift_1_jam) {
        var jam_x = parseInt(jam_start_shift_1_jam) * 60;
        var menit_x = parseInt(jam_start_shift_1_menit);
        var jam_y = (parseInt(jam_stop_shift_1_jam) + 24) * 60;
        var menit_y = parseInt(jam_stop_shift_1_menit);
        
        var hasil_x = jam_x + menit_x;
        var hasil_y = jam_y + menit_y;
        var selisih = (hasil_y - hasil_x);
        var menit = selisih % 60;
        var jam = selisih - menit;
        menit = menit / 60;
        jam = jam / 60;
    }else{
        var jam_x = parseInt(jam_start_shift_1_jam) * 60;
        var menit_x = parseInt(jam_start_shift_1_menit);
        var jam_y = parseInt(jam_stop_shift_1_jam) * 60;
        var menit_y = parseInt(jam_stop_shift_1_menit);
        var hasil_x = jam_x + menit_x;
        var hasil_y = jam_y + menit_y;
        var selisih = (hasil_y - hasil_x);
        var menit = selisih % 60;
        var jam = selisih - menit;
        menit = menit / 60;
        jam = jam / 60;
    }
    var hasilWaktu = jam + menit;
    hasilWaktu = hasilWaktu.toFixed(2);
    document.getElementById('total_stagnasi').value = hasilWaktu;
}

function hitungJamShift2() {
    var jam_start_shift_2_jam = document.getElementById('jam_start_shift_2_jam').value;
    var jam_start_shift_2_menit = document.getElementById('jam_start_shift_2_menit').value;
    var jam_stop_shift_2_jam = document.getElementById('jam_stop_shift_2_jam').value;
    var jam_stop_shift_2_menit = document.getElementById('jam_stop_shift_2_menit').value;

    if (jam_start_shift_2_jam > jam_stop_shift_2_jam) {
        var jam_x = parseInt(jam_start_shift_2_jam) * 60;
        var menit_x = parseInt(jam_start_shift_2_menit);
        var jam_y = (parseInt(jam_stop_shift_2_jam) + 24) * 60;
        var menit_y = parseInt(jam_stop_shift_2_menit);
        
        var hasil_x = jam_x + menit_x;
        var hasil_y = jam_y + menit_y;
        var selisih = (hasil_y - hasil_x);
        var menit = selisih % 60;
        var jam = selisih - menit;
        menit = menit / 60;
        jam = jam / 60;
    }else{
        var jam_x = parseInt(jam_start_shift_2_jam) * 60;
        var menit_x = parseInt(jam_start_shift_2_menit);
        var jam_y = parseInt(jam_stop_shift_2_jam) * 60;
        var menit_y = parseInt(jam_stop_shift_2_menit);
        var hasil_x = jam_x + menit_x;
        var hasil_y = jam_y + menit_y;
        var selisih = (hasil_y - hasil_x);
        var menit = selisih % 60;
        var jam = selisih - menit;
        menit = menit / 60;
        jam = jam / 60;
    }
    var hasilWaktu = jam + menit;
    hasilWaktu = hasilWaktu.toFixed(2);
    document.getElementById('total_jam_shift_2').value = hasilWaktu;
    hitungTotalJamShift();
    hitungJamIdle();
    hitungJamIdle2();
    hitungJamIdle3();
}

function hitungJamShift3() {
    var jam_start_shift_3_jam = document.getElementById('jam_start_shift_3_jam').value;
    var jam_start_shift_3_menit = document.getElementById('jam_start_shift_3_menit').value;
    var jam_stop_shift_3_jam = document.getElementById('jam_stop_shift_3_jam').value;
    var jam_stop_shift_3_menit = document.getElementById('jam_stop_shift_3_menit').value;

    if (jam_start_shift_3_jam > jam_stop_shift_3_jam) {
        var jam_x = parseInt(jam_start_shift_3_jam) * 60;
        var menit_x = parseInt(jam_start_shift_3_menit);
        var jam_y = (parseInt(jam_stop_shift_3_jam) + 24) * 60;
        var menit_y = parseInt(jam_stop_shift_3_menit);
        
        var hasil_x = jam_x + menit_x;
        var hasil_y = jam_y + menit_y;
        var selisih = (hasil_y - hasil_x);
        var menit = selisih % 60;
        var jam = selisih - menit;
        menit = menit / 60;
        jam = jam / 60;
    }else{
        var jam_x = parseInt(jam_start_shift_3_jam) * 60;
        var menit_x = parseInt(jam_start_shift_3_menit);
        var jam_y = parseInt(jam_stop_shift_3_jam) * 60;
        var menit_y = parseInt(jam_stop_shift_3_menit);
        var hasil_x = jam_x + menit_x;
        var hasil_y = jam_y + menit_y;
        var selisih = (hasil_y - hasil_x);
        var menit = selisih % 60;
        var jam = selisih - menit;
        menit = menit / 60;
        jam = jam / 60;
    }
    var hasilWaktu = jam + menit;
    hasilWaktu = hasilWaktu.toFixed(2);
    document.getElementById('total_jam_shift_3').value = hasilWaktu;
    hitungTotalJamShift();
    hitungJamIdle();
    hitungJamIdle2();
    hitungJamIdle3();
}



function postingData(numRow) {
    var nopengolahan = document.getElementById('nopengolahan_'+numRow).getAttribute('value');
	//nopengolahan=trim(document.getElementById('nopengolahan'+numRow).innerHTML);
    var param = "nopengolahan="+nopengolahan;

    Swal.fire({
		title: 'Yakin untuk memposting?',
		text: "Data yang sudah diposting tidak dapat diedit maupun dihapus!",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Save'
		}).then((result) => {
			if (result.value) {
				post_response_text('pabrik_slave_pengolahan.php?proses=posting', param, respon);
			}
		});
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    // alert('ERROR TRANSACTION,\n' + con.responseText);
                    Swal.fire('Oops...', con.responseText, 'error');
                } else {
                    //=== Success Response
                    Swal.fire("Posting data berhasil.");
                   // javascript:location.reload(true);
                    x=document.getElementById('tr_'+numRow);
                    x.cells[5].innerHTML = "<img class=\"zImgOffBtn\" title=\"Posting\" src=\"images/skyblue/posted.png\">";
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function unpostingData(numRow) {
    var nopengolahan = document.getElementById('nopengolahan_'+numRow).getAttribute('value');
	//nopengolahan=trim(document.getElementById('nopengolahan'+numRow).innerHTML);
    var param = "nopengolahan="+nopengolahan;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    alert('Unposting Berhasil');
                   // javascript:location.reload(true);
                    x=document.getElementById('tr_'+numRow);
                    x.cells[5].innerHTML = "<img class=\"zImgOffBtn\" title=\"Posting\" src=\"images/skyblue/posting.png\">";
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    if(confirm('Apakah anda yakin untuk unposting transaksi : '+nopengolahan+
        '?')) {
        post_response_text('pabrik_slave_pengolahan.php?proses=unposting', param, respon);
    }
}


var showPerPage = 10;

function getValue(id) {
    var tmp = document.getElementById(id);
    
    if(tmp) {
        if(tmp.options) {
            return tmp.options[tmp.selectedIndex].value;
        } else if(tmp.nodeType=='checkbox') {
            if(tmp.checked==true) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return tmp.value;
        }
    } else {
        return false;
    }
}

/* Search
 * Filtering Data
 */
function searchTrans() {
    var notrans = document.getElementById('sNoTrans');
    var where = '[["nopengolahan","'+notrans.value+'"]]';
    
    goToPages(1,showPerPage,where);
}

/* Paging
 * Paging Data
 */
function defaultList() {
    goToPages(1,showPerPage);
}

function goToPages(page,shows,where) {
    if(typeof where != 'undefined') {
        var newWhere = where.replace(/'/g,'"');
    }
    var workField = document.getElementById('workField');
    var param = "page="+page;
    param += "&shows="+shows+"&tipe=KB";
    if(typeof where != 'undefined') {
        param+="&where="+newWhere;
    }
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    workField.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('pabrik_slave_pengolahan.php?proses=showHeadList', param, respon);
}

function choosePage(obj,shows,where) {
    var pageVal = obj.options[obj.selectedIndex].value;
    goToPages(pageVal,shows,where);
}

/* Halaman Manipulasi Data
 * Halaman add, edit, delete
 */
function showAdd() {
    var workField = document.getElementById('workField');
    var param = "";
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    workField.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('pabrik_slave_pengolahan.php?proses=showAdd', param, respon);
}
 
function showEditFromAdd() {
    var workField = document.getElementById('workField');
    var trans = document.getElementById('nopengolahan');
    var param = "nopengolahan="+trans.value;
    
    var kodeorg = document.getElementById('kodeorg').value;
    var nopengolahan = document.getElementById('nopengolahan').value;
    var tanggal = document.getElementById('tanggal').value;
    var status_olah = document.getElementById('status_olah').value;
    
    var mandor_shift_1 = document.getElementById('mandor_shift_1').value;
    var asisten_shift_1 = document.getElementById('asisten_shift_1').value;
    var jam_start_shift_1_jam = document.getElementById('jam_start_shift_1_jam').value;
    var jam_start_shift_1_menit = document.getElementById('jam_start_shift_1_menit').value;
    var jam_stop_shift_1_jam = document.getElementById('jam_stop_shift_1_jam').value;
    var jam_stop_shift_1_menit = document.getElementById('jam_stop_shift_1_menit').value;
    var total_jam_shift_1 = document.getElementById('total_jam_shift_1').value;
    var jam_start_operasi_shift_1_jam = document.getElementById('jam_start_operasi_shift_1_jam').value;
    var jam_start_operasi_shift_1_menit = document.getElementById('jam_start_operasi_shift_1_menit').value;
    var jam_stop_operasi_shift_1_jam = document.getElementById('jam_stop_operasi_shift_1_jam').value;
    var jam_stop_operasi_shift_1_menit = document.getElementById('jam_stop_operasi_shift_1_menit').value;
    var total_jam_operasi_shift_1 = document.getElementById('total_jam_operasi_shift_1').value;
    var total_jam_press_shift_1 = document.getElementById('total_jam_press_shift_1').value;
    var jam_idle_shift_1 = document.getElementById('jam_idle_shift_1').value;

    var mandor_shift_2 = document.getElementById('mandor_shift_2').value;
    var asisten_shift_2 = document.getElementById('asisten_shift_2').value;
    var jam_start_shift_2_jam = document.getElementById('jam_start_shift_2_jam').value;
    var jam_start_shift_2_menit = document.getElementById('jam_start_shift_2_menit').value;
    var jam_stop_shift_2_jam = document.getElementById('jam_stop_shift_2_jam').value;
    var jam_stop_shift_2_menit = document.getElementById('jam_stop_shift_2_menit').value;
    var total_jam_shift_2 = document.getElementById('total_jam_shift_2').value;
    var jam_start_operasi_shift_2_jam = document.getElementById('jam_start_operasi_shift_2_jam').value;
    var jam_start_operasi_shift_2_menit = document.getElementById('jam_start_operasi_shift_2_menit').value;
    var jam_stop_operasi_shift_2_jam = document.getElementById('jam_stop_operasi_shift_2_jam').value;
    var jam_stop_operasi_shift_2_menit = document.getElementById('jam_stop_operasi_shift_2_menit').value;
    var total_jam_operasi_shift_2 = document.getElementById('total_jam_operasi_shift_2').value;
    var total_jam_press_shift_2 = document.getElementById('total_jam_press_shift_2').value;
    var jam_idle_shift_2 = document.getElementById('jam_idle_shift_2').value;

    var mandor_shift_3 = document.getElementById('mandor_shift_3').value;
    var asisten_shift_3 = document.getElementById('asisten_shift_3').value;
    var jam_start_shift_3_jam = document.getElementById('jam_start_shift_3_jam').value;
    var jam_start_shift_3_menit = document.getElementById('jam_start_shift_3_menit').value;
    var jam_stop_shift_3_jam = document.getElementById('jam_stop_shift_3_jam').value;
    var jam_stop_shift_3_menit = document.getElementById('jam_stop_shift_3_menit').value;
    var total_jam_shift_3 = document.getElementById('total_jam_shift_3').value;
    var jam_start_operasi_shift_3_jam = document.getElementById('jam_start_operasi_shift_3_jam').value;
    var jam_start_operasi_shift_3_menit = document.getElementById('jam_start_operasi_shift_3_menit').value;
    var jam_stop_operasi_shift_3_jam = document.getElementById('jam_stop_operasi_shift_3_jam').value;
    var jam_stop_operasi_shift_3_menit = document.getElementById('jam_stop_operasi_shift_3_menit').value;
    var total_jam_operasi_shift_3 = document.getElementById('total_jam_operasi_shift_3').value;
    var total_jam_press_shift_3 = document.getElementById('total_jam_press_shift_3').value;
    var jam_idle_shift_3 = document.getElementById('jam_idle_shift_3').value;

    var total_jam_shift = document.getElementById('total_jam_shift').value;
    var total_jam_press = document.getElementById('total_jam_press').value;
    var total_jam_operasi = document.getElementById('total_jam_operasi').value;
    var total_jam_idle = document.getElementById('total_jam_idle').value;

    var jam_stagnasi = document.getElementById('jam_stagnasi').value;
    

    var lori_olah_shift_1 = document.getElementById('lori_olah_shift_1').value;
    var lori_olah_shift_2 = document.getElementById('lori_olah_shift_2').value;
    var lori_olah_shift_3 = document.getElementById('lori_olah_shift_3').value;
    var lori_dalam_rebusan = document.getElementById('lori_dalam_rebusan').value;
    var restan_depan_rebusan = document.getElementById('restan_depan_rebusan').value;
    var restan_dibelakang_rebusan = document.getElementById('restan_dibelakang_rebusan').value;
    var estimasi_di_peron = document.getElementById('estimasi_di_peron').value;
    var rata_rata_lori = document.getElementById('rata_rata_lori').value;
    
    var tbs_sisa_kemarin = document.getElementById('tbs_sisa_kemarin').value;
    var tbs_masuk_bruto = document.getElementById('tbs_masuk_bruto').value;
    var total_tbs = document.getElementById('total_tbs').value;
    var tbs_potongan = document.getElementById('tbs_potongan').value;
    var tbs_potongan_olah = document.getElementById('tbs_potongan_olah').value;
    var tbs_masuk_netto = document.getElementById('tbs_masuk_netto').value;
    var tbs_diolah = document.getElementById('tbs_diolah').value;
    var tbs_diolah_after = document.getElementById('tbs_diolah_after').value;
    var tbs_sisa = document.getElementById('tbs_sisa').value;

    
    var despatch_cpo = document.getElementById('despatch_cpo').value;
    var return_cpo = document.getElementById('return_cpo').value;
    var despatch_pk = document.getElementById('despatch_pk').value;
    var return_pk = document.getElementById('return_pk').value;
    var janjang_kosong = document.getElementById('janjang_kosong').value;
    var limbah_cair = document.getElementById('limbah_cair').value;
    var solid_decnter = document.getElementById('solid_decnter').value;
    var abu_janjang = document.getElementById('abu_janjang').value;
    var cangkang = document.getElementById('cangkang').value;
    var fibre = document.getElementById('fibre').value;


    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    workField.innerHTML = con.responseText;
                    showDetail();
                    document.getElementById('kodeorg').value = kodeorg;
                    document.getElementById('nopengolahan').value = nopengolahan;
                    document.getElementById('tanggal').value = tanggal;
                    document.getElementById('status_olah').value = status_olah;

                    document.getElementById('mandor_shift_1').value = mandor_shift_1;
                    document.getElementById('asisten_shift_1').value = asisten_shift_1;
                    document.getElementById('jam_start_shift_1_jam').value = jam_start_shift_1_jam;
                    document.getElementById('jam_start_shift_1_menit').value = jam_start_shift_1_menit;
                    document.getElementById('jam_stop_shift_1_jam').value = jam_stop_shift_1_jam;
                    document.getElementById('jam_stop_shift_1_menit').value = jam_stop_shift_1_menit;
                    document.getElementById('total_jam_shift_1').value = total_jam_shift_1;
                    document.getElementById('jam_start_operasi_shift_1_jam').value = jam_start_operasi_shift_1_jam;
                    document.getElementById('jam_start_operasi_shift_1_menit').value = jam_start_operasi_shift_1_menit;
                    document.getElementById('jam_stop_operasi_shift_1_jam').value = jam_stop_operasi_shift_1_jam;
                    document.getElementById('jam_stop_operasi_shift_1_menit').value = jam_stop_operasi_shift_1_menit;
                    document.getElementById('total_jam_operasi_shift_1').value = total_jam_operasi_shift_1;
                    document.getElementById('total_jam_press_shift_1').value = total_jam_press_shift_1;
                    document.getElementById('jam_idle_shift_1').value = jam_idle_shift_1;

                    document.getElementById('mandor_shift_2').value = mandor_shift_2;
                    document.getElementById('asisten_shift_2').value = asisten_shift_2;
                    document.getElementById('jam_start_shift_2_jam').value = jam_start_shift_2_jam;
                    document.getElementById('jam_start_shift_2_menit').value = jam_start_shift_2_menit;
                    document.getElementById('jam_stop_shift_2_jam').value = jam_stop_shift_2_jam;
                    document.getElementById('jam_stop_shift_2_menit').value = jam_stop_shift_2_menit;
                    document.getElementById('total_jam_shift_2').value = total_jam_shift_2;
                    document.getElementById('jam_start_operasi_shift_2_jam').value = jam_start_operasi_shift_2_jam;
                    document.getElementById('jam_start_operasi_shift_2_menit').value = jam_start_operasi_shift_2_menit;
                    document.getElementById('jam_stop_operasi_shift_2_jam').value = jam_stop_operasi_shift_2_jam;
                    document.getElementById('jam_stop_operasi_shift_2_menit').value = jam_stop_operasi_shift_2_menit;
                    document.getElementById('total_jam_operasi_shift_2').value = total_jam_operasi_shift_2;
                    document.getElementById('total_jam_press_shift_2').value = total_jam_press_shift_2;
                    document.getElementById('jam_idle_shift_2').value = jam_idle_shift_2;

                    document.getElementById('mandor_shift_3').value = mandor_shift_3;
                    document.getElementById('asisten_shift_3').value = asisten_shift_3;
                    document.getElementById('jam_start_shift_3_jam').value = jam_start_shift_3_jam;
                    document.getElementById('jam_start_shift_3_menit').value = jam_start_shift_3_menit;
                    document.getElementById('jam_stop_shift_3_jam').value = jam_stop_shift_3_jam;
                    document.getElementById('jam_stop_shift_3_menit').value = jam_stop_shift_3_menit;
                    document.getElementById('total_jam_shift_3').value = total_jam_shift_3;
                    document.getElementById('jam_start_operasi_shift_3_jam').value = jam_start_operasi_shift_3_jam;
                    document.getElementById('jam_start_operasi_shift_3_menit').value = jam_start_operasi_shift_3_menit;
                    document.getElementById('jam_stop_operasi_shift_3_jam').value = jam_stop_operasi_shift_3_jam;
                    document.getElementById('jam_stop_operasi_shift_3_menit').value = jam_stop_operasi_shift_3_menit;
                    document.getElementById('total_jam_operasi_shift_3').value = total_jam_operasi_shift_3;
                    document.getElementById('total_jam_press_shift_3').value = total_jam_press_shift_3;
                    document.getElementById('jam_idle_shift_3').value = jam_idle_shift_3
                    
                    document.getElementById('total_jam_shift').value = total_jam_shift;
                    document.getElementById('total_jam_press').value = total_jam_press;
                    document.getElementById('total_jam_operasi').value = total_jam_operasi;
                    document.getElementById('total_jam_idle').value = total_jam_idle;

                    document.getElementById('jam_stagnasi').value = jam_stagnasi;
                    
                    document.getElementById('lori_olah_shift_1').value = lori_olah_shift_1;
                    document.getElementById('lori_olah_shift_2').value = lori_olah_shift_2;
                    document.getElementById('lori_olah_shift_3').value = lori_olah_shift_3;
                    document.getElementById('lori_dalam_rebusan').value = lori_dalam_rebusan;
                    document.getElementById('restan_depan_rebusan').value = restan_depan_rebusan;
                    document.getElementById('restan_dibelakang_rebusan').value = restan_dibelakang_rebusan;
                    document.getElementById('estimasi_di_peron').value = estimasi_di_peron;
                    document.getElementById('rata_rata_lori').value = rata_rata_lori;

                    document.getElementById('tbs_sisa_kemarin').value = tbs_sisa_kemarin;
                    document.getElementById('tbs_masuk_bruto').value = tbs_masuk_bruto;
                    document.getElementById('total_tbs').value = total_tbs;
                    document.getElementById('tbs_potongan').value = tbs_potongan;
                    document.getElementById('tbs_potongan_olah').value = tbs_potongan_olah;
                    document.getElementById('tbs_masuk_netto').value = tbs_masuk_netto;
                    document.getElementById('tbs_diolah').value = tbs_diolah;
                    document.getElementById('tbs_diolah_after').value = tbs_diolah_after;
                    document.getElementById('tbs_sisa').value = tbs_sisa;

    
                    document.getElementById('despatch_cpo').value = despatch_cpo;
                    document.getElementById('return_cpo').value = return_cpo;
                    document.getElementById('despatch_pk').value = despatch_pk;
                    document.getElementById('return_pk').value = return_pk;
                    document.getElementById('janjang_kosong').value = janjang_kosong;
                    document.getElementById('limbah_cair').value = limbah_cair;
                    document.getElementById('solid_decnter').value = solid_decnter;
                    document.getElementById('abu_janjang').value = abu_janjang;
                    document.getElementById('cangkang').value = cangkang;
                    document.getElementById('fibre').value = fibre;
                    
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('pabrik_slave_pengolahan.php?proses=showEdit', param, respon);
}

function showEdit(num) {
    var workField = document.getElementById('workField');
    var trans = document.getElementById('nopengolahan_'+num);
    var param = "numRow="+num+"&nopengolahan="+trans.innerHTML;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    workField.innerHTML = con.responseText;
                    showDetail();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('pabrik_slave_pengolahan.php?proses=showEdit', param, respon);
}

/* Manipulasi Data
 * add, edit, delete
 */
function addDataTable() {
    var noP = document.getElementById('nopengolahan');
    var param = "kodeorg="+getValue('kodeorg')+"&nopengolahan="+getValue('nopengolahan');
    param += "&tanggal="+getValue('tanggal');
    param += "&status_olah="+getValue('status_olah');
    
    param += "&mandor_shift_1="+getValue('mandor_shift_1')+"&asisten_shift_1="+getValue('asisten_shift_1');
    param += "&jam_start_shift_1="+getValue('jam_start_shift_1_jam')+":"+getValue('jam_start_shift_1_menit')+":00";
    param += "&jam_stop_shift_1="+getValue('jam_stop_shift_1_jam')+":"+getValue('jam_stop_shift_1_menit')+":00";
    param += "&total_jam_shift_1="+getValue('total_jam_shift_1');
    param += "&jam_start_operasi_shift_1="+getValue('jam_start_operasi_shift_1_jam')+":"+getValue('jam_start_operasi_shift_1_menit')+":00";
    param += "&jam_stop_operasi_shift_1="+getValue('jam_stop_operasi_shift_1_jam')+":"+getValue('jam_stop_operasi_shift_1_menit')+":00";
    param += "&total_jam_operasi_shift_1="+getValue('total_jam_operasi_shift_1');
    param += "&total_jam_press_shift_1="+getValue('total_jam_press_shift_1');
    param += "&jam_idle_shift_1="+getValue('jam_idle_shift_1');
    
    param += "&mandor_shift_2="+getValue('mandor_shift_2')+"&asisten_shift_2="+getValue('asisten_shift_2');
    param += "&jam_start_shift_2="+getValue('jam_start_shift_2_jam')+":"+getValue('jam_start_shift_2_menit')+":00";
    param += "&jam_stop_shift_2="+getValue('jam_stop_shift_2_jam')+":"+getValue('jam_stop_shift_2_menit')+":00";
    param += "&total_jam_shift_2="+getValue('total_jam_shift_2');
    param += "&jam_start_operasi_shift_2="+getValue('jam_start_operasi_shift_2_jam')+":"+getValue('jam_start_operasi_shift_2_menit')+":00";
    param += "&jam_stop_operasi_shift_2="+getValue('jam_stop_operasi_shift_2_jam')+":"+getValue('jam_stop_operasi_shift_2_menit')+":00";
    param += "&total_jam_operasi_shift_2="+getValue('total_jam_operasi_shift_2');
    param += "&total_jam_press_shift_2="+getValue('total_jam_press_shift_2');
    param += "&jam_idle_shift_2="+getValue('jam_idle_shift_2');

    param += "&mandor_shift_3="+getValue('mandor_shift_3')+"&asisten_shift_3="+getValue('asisten_shift_3');
    param += "&jam_start_shift_3="+getValue('jam_start_shift_3_jam')+":"+getValue('jam_start_shift_3_menit')+":00";
    param += "&jam_stop_shift_3="+getValue('jam_stop_shift_3_jam')+":"+getValue('jam_stop_shift_3_menit')+":00";
    param += "&total_jam_shift_3="+getValue('total_jam_shift_3');
    param += "&jam_start_operasi_shift_3="+getValue('jam_start_operasi_shift_3_jam')+":"+getValue('jam_start_operasi_shift_3_menit')+":00";
    param += "&jam_stop_operasi_shift_3="+getValue('jam_stop_operasi_shift_3_jam')+":"+getValue('jam_stop_operasi_shift_3_menit')+":00";
    param += "&total_jam_operasi_shift_3="+getValue('total_jam_operasi_shift_3');
    param += "&total_jam_press_shift_3="+getValue('total_jam_press_shift_3');
    param += "&jam_idle_shift_3="+getValue('jam_idle_shift_3');

    param += "&total_jam_shift="+getValue('total_jam_shift');
    param += "&total_jam_press="+getValue('total_jam_press');
    param += "&total_jam_operasi="+getValue('total_jam_operasi');
    param += "&total_jam_idle="+getValue('total_jam_idle');

    param += "&jam_stagnasi="+getValue('jam_stagnasi');
    
    param += "&lori_olah_shift_1="+getValue('lori_olah_shift_1');
    param += "&lori_olah_shift_2="+getValue('lori_olah_shift_2');
    param += "&lori_olah_shift_3="+getValue('lori_olah_shift_3');
    param += "&lori_dalam_rebusan="+getValue('lori_dalam_rebusan');
    param += "&restan_depan_rebusan="+getValue('restan_depan_rebusan');
    param += "&restan_dibelakang_rebusan="+getValue('restan_dibelakang_rebusan');
    param += "&estimasi_di_peron="+getValue('estimasi_di_peron');
    param += "&total_lori="+getValue('total_lori');
    param += "&rata_rata_lori="+getValue('rata_rata_lori');

    param += "&tbs_sisa_kemarin="+getValue('tbs_sisa_kemarin');
    param += "&tbs_masuk_bruto="+getValue('tbs_masuk_bruto');
    param += "&total_tbs="+getValue('total_tbs');
    param += "&tbs_potongan="+getValue('tbs_potongan');
    param += "&tbs_masuk_netto="+getValue('tbs_masuk_netto');
    param += "&tbs_diolah="+getValue('tbs_diolah');
    param += "&tbs_diolah_after="+getValue('tbs_diolah_after');
    param += "&tbs_sisa="+getValue('tbs_sisa');

    param += "&despatch_cpo="+getValue('despatch_cpo');
    param += "&return_cpo="+getValue('return_cpo');
    param += "&despatch_pk="+getValue('despatch_pk');
    param += "&return_pk="+getValue('return_pk');
    param += "&janjang_kosong="+getValue('janjang_kosong');
    param += "&limbah_cair="+getValue('limbah_cair');
    param += "&solid_decnter="+getValue('solid_decnter');
    param += "&abu_janjang="+getValue('abu_janjang');
    param += "&cangkang="+getValue('cangkang');
    param += "&fibre="+getValue('fibre');

    Swal.fire({
		title: 'Yakin untuk menyimpan?',
		text: "Periksa kembali data yang sudah disimpan!",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Save'
		}).then((result) => {
			if (result.value) {
				post_response_text('pabrik_slave_pengolahan.php?proses=add', param, respon);
			}
		});
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    // alert('ERROR TRANSACTION,\n' + con.responseText);
                    Swal.fire('Oops...', con.responseText, 'error');
                } else {
                    Swal.fire("Simpan data berhasil.");
                    document.getElementById('nopengolahan').value = con.responseText;
                    showEditFromAdd();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    
}

function editDataTable() {
    var param = "kodeorg="+getValue('kodeorg')+"&nopengolahan="+getValue('nopengolahan');
    param += "&tanggal="+getValue('tanggal');
    param += "&status_olah="+getValue('status_olah');
    
    param += "&mandor_shift_1="+getValue('mandor_shift_1')+"&asisten_shift_1="+getValue('asisten_shift_1');
    param += "&jam_start_shift_1="+getValue('jam_start_shift_1_jam')+":"+getValue('jam_start_shift_1_menit')+":00";
    param += "&jam_stop_shift_1="+getValue('jam_stop_shift_1_jam')+":"+getValue('jam_stop_shift_1_menit')+":00";
    param += "&total_jam_shift_1="+getValue('total_jam_shift_1');
    param += "&jam_start_operasi_shift_1="+getValue('jam_start_operasi_shift_1_jam')+":"+getValue('jam_start_operasi_shift_1_menit')+":00";
    param += "&jam_stop_operasi_shift_1="+getValue('jam_stop_operasi_shift_1_jam')+":"+getValue('jam_stop_operasi_shift_1_menit')+":00";
    param += "&total_jam_operasi_shift_1="+getValue('total_jam_operasi_shift_1');
    param += "&total_jam_press_shift_1="+getValue('total_jam_press_shift_1');
    param += "&jam_idle_shift_1="+getValue('jam_idle_shift_1');
    
    param += "&mandor_shift_2="+getValue('mandor_shift_2')+"&asisten_shift_2="+getValue('asisten_shift_2');
    param += "&jam_start_shift_2="+getValue('jam_start_shift_2_jam')+":"+getValue('jam_start_shift_2_menit')+":00";
    param += "&jam_stop_shift_2="+getValue('jam_stop_shift_2_jam')+":"+getValue('jam_stop_shift_2_menit')+":00";
    param += "&total_jam_shift_2="+getValue('total_jam_shift_2');
    param += "&jam_start_operasi_shift_2="+getValue('jam_start_operasi_shift_2_jam')+":"+getValue('jam_start_operasi_shift_2_menit')+":00";
    param += "&jam_stop_operasi_shift_2="+getValue('jam_stop_operasi_shift_2_jam')+":"+getValue('jam_stop_operasi_shift_2_menit')+":00";
    param += "&total_jam_operasi_shift_2="+getValue('total_jam_operasi_shift_2');
    param += "&total_jam_press_shift_2="+getValue('total_jam_press_shift_2');
    param += "&jam_idle_shift_2="+getValue('jam_idle_shift_2');

    param += "&mandor_shift_3="+getValue('mandor_shift_3')+"&asisten_shift_3="+getValue('asisten_shift_3');
    param += "&jam_start_shift_3="+getValue('jam_start_shift_3_jam')+":"+getValue('jam_start_shift_3_menit')+":00";
    param += "&jam_stop_shift_3="+getValue('jam_stop_shift_3_jam')+":"+getValue('jam_stop_shift_3_menit')+":00";
    param += "&total_jam_shift_3="+getValue('total_jam_shift_3');
    param += "&jam_start_operasi_shift_3="+getValue('jam_start_operasi_shift_3_jam')+":"+getValue('jam_start_operasi_shift_3_menit')+":00";
    param += "&jam_stop_operasi_shift_3="+getValue('jam_stop_operasi_shift_3_jam')+":"+getValue('jam_stop_operasi_shift_3_menit')+":00";
    param += "&total_jam_operasi_shift_3="+getValue('total_jam_operasi_shift_3');
    param += "&total_jam_press_shift_3="+getValue('total_jam_press_shift_3');
    param += "&jam_idle_shift_3="+getValue('jam_idle_shift_3');

    param += "&total_jam_shift="+getValue('total_jam_shift');
    param += "&total_jam_press="+getValue('total_jam_press');
    param += "&total_jam_operasi="+getValue('total_jam_operasi');
    param += "&total_jam_idle="+getValue('total_jam_idle');

    param += "&jam_stagnasi="+getValue('jam_stagnasi');
    
    param += "&lori_olah_shift_1="+getValue('lori_olah_shift_1');
    param += "&lori_olah_shift_2="+getValue('lori_olah_shift_2');
    param += "&lori_olah_shift_3="+getValue('lori_olah_shift_3');
    param += "&lori_dalam_rebusan="+getValue('lori_dalam_rebusan');
    param += "&restan_depan_rebusan="+getValue('restan_depan_rebusan');
    param += "&restan_dibelakang_rebusan="+getValue('restan_dibelakang_rebusan');
    param += "&estimasi_di_peron="+getValue('estimasi_di_peron');
    param += "&total_lori="+getValue('total_lori');
    param += "&rata_rata_lori="+getValue('rata_rata_lori');

    param += "&tbs_sisa_kemarin="+getValue('tbs_sisa_kemarin');
    param += "&tbs_masuk_bruto="+getValue('tbs_masuk_bruto');
    param += "&total_tbs="+getValue('total_tbs');
    param += "&tbs_potongan="+getValue('tbs_potongan');
    param += "&tbs_masuk_netto="+getValue('tbs_masuk_netto');
    param += "&tbs_diolah="+getValue('tbs_diolah');
    param += "&tbs_diolah_after="+getValue('tbs_diolah_after');
    param += "&tbs_sisa="+getValue('tbs_sisa');

    param += "&despatch_cpo="+getValue('despatch_cpo');
    param += "&return_cpo="+getValue('return_cpo');
    param += "&despatch_pk="+getValue('despatch_pk');
    param += "&return_pk="+getValue('return_pk');
    param += "&janjang_kosong="+getValue('janjang_kosong');
    param += "&limbah_cair="+getValue('limbah_cair');
    param += "&solid_decnter="+getValue('solid_decnter');
    param += "&abu_janjang="+getValue('abu_janjang');
    param += "&cangkang="+getValue('cangkang');
    param += "&fibre="+getValue('fibre');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    // alert('ERROR TRANSACTION,\n' + con.responseText);
                    Swal.fire('Oops...', con.responseText, 'error');
                } else {
                    //=== Success Response
                    Swal.fire("Edit data berhasil.");
                    defaultList();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

    Swal.fire({
		title: 'Yakin untuk menyimpan?',
		text: "Periksa kembali data yang sudah diedit!",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Save'
		}).then((result) => {
			if (result.value) {
				post_response_text('pabrik_slave_pengolahan.php?proses=edit', param, respon);
			}
		});
    
    
}

/*
 * Detail
 */

function showDetail() {
    var detailField = document.getElementById('detailField');
    var notrans = document.getElementById('nopengolahan').value;
    var param = "nopengolahan="+notrans+"&kodeorg="+getValue('kodeorg');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    detailField.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('pabrik_slave_pengolahan_detail.php?proses=showDetail', param, respon);
}

function deleteData(num) {
    var notrans = document.getElementById('nopengolahan_'+num).innerHTML;
    var param = "nopengolahan="+notrans;

    Swal.fire({
		title: 'Yakin untuk mengapus?',
		text: "Data yang sudah dihapus tidak dapat dikembalikan kembali!",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Save'
		}).then((result) => {
			if (result.value) {
				post_response_text('pabrik_slave_pengolahan.php?proses=delete', param, respon);
			}
		});
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    // alert('ERROR TRANSACTION,\n' + con.responseText);
                    Swal.fire('Oops...', con.responseText, 'error');
                } else {
                    //=== Success Response
                    Swal.fire("Delete data berhasil.");
                    var tmp = document.getElementById('tr_'+num);
                    tmp.parentNode.removeChild(tmp);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }      
}

function printPDF(ev) {
    // Prep Param
    param = "proses=pdf";
    
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='pabrik_slave_pengolahan_print.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '50px';
    dialog.style.left = '15%';
}

function updMesin() {
    var mesin = document.getElementById('engine');
    var param = "station="+getValue('station');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    eval("var res = "+con.responseText+";");
                    mesin.options.length=0;
                    for(i in res) {
                        mesin.options[mesin.options.length] = new Option(res[i],i);
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('pabrik_slave_pengolahan_detail.php?proses=updMesin', param, respon);
}

function updMandorAst(mode) {
    var mandor = document.getElementById('mandor');
    var asisten = document.getElementById('asisten');
    var shift = document.getElementById('shift');
    if(shift.selectedIndex==-1) {
        var shiftVal = 'empty';
    } else {
        var shiftVal = getValue('shift');
    }
    var param = "tanggal="+getValue('tanggal')+"&shift="+shiftVal+"&mode="+mode;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    eval("var res = "+con.responseText+";");
                    if(res['shift']!='empty') {
                        shift.options.length=0;
                        for(i in res['shift']) {
                            shift.options[shift.options.length] = new Option(res['shift'][i],i);
                        }
                    }
                    mandor.options.length=0;
                    for(i in res['mandor']) {
                        mandor.options[mandor.options.length] = new Option(res['mandor'][i],i);
                    }
                    asisten.options.length=0;
                    for(i in res['asisten']) {
                        asisten.options[asisten.options.length] = new Option(res['asisten'][i],i);
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('pabrik_slave_pengolahan.php?proses=updMandorAst', param, respon);
}

function detailPDF(numRow,ev='event') {
    // Prep Param
    var nopengolahan = document.getElementById('nopengolahan_'+numRow).getAttribute('value');
    param = "proses=pdf&nopengolahan="+nopengolahan;
    
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='pabrik_slave_pengolahan_print_detail.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '150px';
    dialog.style.left = '15%';
}

function showMaterial(num,ev) {
    var station = document.getElementById('ftMesin_station_'+num).getAttribute('value');
    var mesin = document.getElementById('ftMesin_tahuntanam_'+num).getAttribute('value');
    
    var param = "nopengolahan="+getValue('nopengolahan')+
        "&kodeorg="+station+"&tahuntanam="+mesin+"&numRow="+num;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    showDialog1('Edit Material',con.responseText,'800','300',ev);
                    var dialog = document.getElementById('dynamic1');
                    dialog.style.top = '10%';
                    dialog.style.left = '15%';
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('pabrik_slave_pengolahan_material.php?proses=showMaterial', param, respon);
}