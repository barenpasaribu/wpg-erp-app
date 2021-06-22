/*
	update by Awan
	29 06 2020 
*/

/*
	Proses Penarikan Data
	- get bruto, netto, potongan, pengiriman dari timbangan, pengolahan pabrik dari pengoprasian pabrik (proses perbaikan)
	- tbs sisa, closing stock cpo dan kernel dari laporan kemarin
	- get cpo sounding
	- get kernel sounding
	- solar dari traksi
	- ?
	- ?
*/

function lihatDataTarikan() {
    tanggal = document.getElementById('tanggal').value;
    kodeorg = document.getElementById('kodeorg').value;

    // Post to Slave
    if(tanggal=='' || tanggal==null || tanggal == "00-00-0000") {
        alert("Tanggal belum dipilih");
    }else{
        param = "tanggal="+tanggal;
        param += "&kodeorg="+kodeorg;
        showDialog1('Data Timbangan',"<iframe frameborder=0 style='width:795px;height:400px' src='pabrik_slave_produksi_list.php?"+param+"'></iframe>",'800','400','event');
        var dialog = document.getElementById('dynamic1');
        dialog.style.top = '200px';
        dialog.style.left = '15%';
    }

    
}

function getHM(notransaksi, elementId) {
	param='notransaksi='+notransaksi;
	param+='&proses=getHM';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
				busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
					var data = JSON.parse(con.responseText);
					document.getElementById(elementId).value = data.jumlah.toFixed(2);
				}
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }    
    post_response_text('pabrik_slave_get_data.php', param, respon);
}

function hitungUtilitasFactorCormecial() {
	kodeorg=document.getElementById('kodeorg').value;
	tbs_diolah = document.getElementById('tbs_diolah').value;
	param='kodeorg='+kodeorg;
	param+='&tbs_diolah='+tbs_diolah;
	param+='&proses=calcUtilitasFactorCormecial';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
				busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
					// console.log(con.responseText);
					if (con.responseText != null) {
						document.getElementById('utility_factor_commercial').value = con.responseText;
					}
					
				}
				getCatatanStagnasi();
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }    
    post_response_text('pabrik_slave_get_data.php', param, respon);
}

function hitungUtilitasKapastitas() {
	kodeorg=document.getElementById('kodeorg').value;
	kapasitas_olah = document.getElementById('kapasitas_olah').value;
	param='kodeorg='+kodeorg;
	param+='&kapasitas_olah='+kapasitas_olah;
	param+='&proses=calcUtilitasKapasitas';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
				busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
					// console.log(con.responseText);
					if (con.responseText != null) {
						document.getElementById('utilitas_kapasitas').value = con.responseText;
					}
					hitungUtilitasFactorCormecial();
					
				}
            } else {
                busy_off();
                error_catch(con.status);
            }
		}
		
    }    
    post_response_text('pabrik_slave_get_data.php', param, respon);
}


function hitungTotalSolarGenset() {
	satu = document.getElementById('solar_genset_1').value;
	dua = document.getElementById('solar_genset_2').value;
	tiga = document.getElementById('solar_genset_3').value;
	document.getElementById('total_solar_genset').value = parseFloat(satu) + parseFloat(dua) + parseFloat(tiga);
	hitungRasioSolarGensetToHM();
}

function hitungTotalSolarLoader() {
	satu = document.getElementById('solar_loader_1').value;
	dua = document.getElementById('solar_loader_2').value;
	tiga = document.getElementById('solar_loader_3').value;
	rental = document.getElementById('solar_loader_rental').value;
	document.getElementById('total_solar_loader').value = parseFloat(satu) + parseFloat(dua) + parseFloat(tiga) + parseFloat(rental);
	hitungRasioSolarGensetToHM();
}

function hitungTotalHMGenset() {
	satu = document.getElementById('hm_genset_1').value;
	dua = document.getElementById('hm_genset_2').value;
	tiga = document.getElementById('hm_genset_3').value;
	document.getElementById('total_hm_genset').value = parseFloat(satu) + parseFloat(dua) + parseFloat(tiga);
	hitungRasioSolarGensetToHM();
}

function hitungTotalHMLoader() {
	satu = document.getElementById('hm_loader_1').value;
	dua = document.getElementById('hm_loader_2').value;
	tiga = document.getElementById('hm_loader_3').value;
	rental = document.getElementById('hm_loader_rental').value;
	document.getElementById('total_hm_loader').value = parseFloat(satu) + parseFloat(dua) + parseFloat(tiga) + parseFloat(rental);
	hitungRasioSolarLoaderToHM();
}

function hitungRasioSolarGensetToHM() {
	total_solar_genset = document.getElementById('total_solar_genset').value;
	total_hm_genset = document.getElementById('total_hm_genset').value;
	if (total_solar_genset == 0 || total_hm_genset == 0) {
		document.getElementById('rasio_total_solar_genset_hm_total_genset').value = 0;
	}else{
		rasio_total_solar_genset_hm_total_genset = parseFloat(total_solar_genset) / parseFloat(total_hm_genset);
		document.getElementById('rasio_total_solar_genset_hm_total_genset').value = rasio_total_solar_genset_hm_total_genset.toFixed(2);
	}
	
}

function hitungRasioSolarLoaderToHM() {
	total_solar_loader = document.getElementById('total_solar_loader').value;
	total_hm_loader = document.getElementById('total_hm_loader').value;

	if (total_solar_loader == 0 || total_hm_loader == 0) {
		document.getElementById('rasio_total_solar_loader_hm_total_loader').value = 0;
	}else{
		rasio_total_solar_loader_hm_total_loader = parseFloat(total_solar_loader) / parseFloat(total_hm_loader);
		document.getElementById('rasio_total_solar_loader_hm_total_loader').value = rasio_total_solar_loader_hm_total_loader.toFixed(2);
	}	
}

function getCatatanStagnasi() {
	kodeorg=document.getElementById('kodeorg').value;
	tanggal=document.getElementById('tanggal').value;
	param='kodeorg='+kodeorg+'&tanggal='+tanggal;
	param+='&proses=getCatatanStagnasi';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
				busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
					var data = JSON.parse(con.responseText);
					if (data != "awan") {
						document.getElementById('catatan1').value = data;
					}
				}
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }    
    post_response_text('pabrik_slave_get_data.php', param, respon);
}

function getSolar() {
	kodeorg=document.getElementById('kodeorg').value;
	tanggal=document.getElementById('tanggal').value;
	param='kodeorg='+kodeorg+'&tanggal='+tanggal;
	param+='&proses=getSolar';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
				busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
					var dataSolar = JSON.parse(con.responseText);
					if (dataSolar != null) {
						document.getElementById('solar_genset_1').value = dataSolar.genset1;
						document.getElementById('solar_genset_2').value = dataSolar.genset2;
						document.getElementById('solar_genset_3').value = dataSolar.genset3;
						document.getElementById('solar_loader_1').value = dataSolar.loader1;
						document.getElementById('solar_loader_2').value = dataSolar.loader2;
						document.getElementById('solar_loader_3').value = dataSolar.loader3;

						document.getElementById('hm_genset_1').value = dataSolar.hmgenset1;
						document.getElementById('hm_genset_2').value = dataSolar.hmgenset2;
						document.getElementById('hm_genset_3').value = dataSolar.hmgenset3;
						document.getElementById('hm_loader_1').value = dataSolar.hmloader1;
						document.getElementById('hm_loader_2').value = dataSolar.hmloader2;
						document.getElementById('hm_loader_3').value = dataSolar.hmloader3;
					}
					hitungTotalSolarGenset();
					hitungTotalSolarLoader();
					hitungTotalHMGenset();
					hitungTotalHMLoader();
					hitungRasioSolarGensetToHM();
					hitungRasioSolarLoaderToHM();
					hitungUtilitasKapastitas();
					
				}
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }    
    post_response_text('pabrik_slave_get_data.php', param, respon);
}

function hitungStagnasi() {
	kodeorg=document.getElementById('kodeorg').value;
	tanggal=document.getElementById('tanggal').value;
	param='kodeorg='+kodeorg+'&tanggal='+tanggal;
	param+='&proses=getSolar';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
				busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
					var data = JSON.parse(con.responseText);
					console.log(data);
					hitungTotalSolarGenset();
					hitungTotalSolarLoader();
					hitungTotalHMGenset();
					hitungTotalHMLoader();
					hitungRasioSolarGensetToHM();
					hitungRasioSolarLoaderToHM();
				}
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }    
    post_response_text('pabrik_slave_get_data.php', param, respon);
}

function hitungRendemen() {
	/*
		Rendemen CPO After Grading, satuan (%) -> Rumus=( Hasil sounding (Hari ini)   + Pengiriman Timbangan (Kemain) - Stok (kemarin ) - retur (jika ada)) / TBS After Grading ; 
		Rendemen CPO Before Grading, satuan (%) -> Rumus = ( Hasil sounding (Hari ini)   + Pengiriman Timbangan (Kemain) - Stok (kemarin ) - retur (jika ada)) / TBS diolah ; 
		Rendemen PK: Rendemen Before (%) -> Rumus = ( Hasil sounding (Hari ini)   + Pengiriman Timbangan (Kemain) - Stok (kemarin ) - retur (jika ada)) / TBS After Grading ; 
		Rendemen After=  ( Hasil sounding (Hari ini)   + Pengiriman Timbangan (Kemain) - Stok (kemarin ) - retur (jika ada)) / TBS diolah ; 

	*/
	cpo_produksi = document.getElementById('cpo_produksi').value;
	kernel_produksi = document.getElementById('kernel_produksi').value;
	tbs_diolah = document.getElementById('tbs_diolah').value;
	tbs_after_grading = document.getElementById('tbs_after_grading').value;

	if (cpo_produksi == 0 || kernel_produksi == 0 || tbs_diolah == 0 || tbs_after_grading == 0) {
		document.getElementById('rendemen_cpo_before').value = 0;
		document.getElementById('rendemen_cpo_after').value = 0;
		document.getElementById('rendemen_pk_before').value = 0;
		document.getElementById('rendemen_pk_after').value = 0;
	} else {
		document.getElementById('rendemen_cpo_before').value = (parseFloat(cpo_produksi) / parseFloat(tbs_diolah) * 100).toFixed(2);
		document.getElementById('rendemen_cpo_after').value = (parseFloat(cpo_produksi) / parseFloat(tbs_after_grading) * 100).toFixed(2);
		document.getElementById('rendemen_pk_before').value = (parseFloat(kernel_produksi) / parseFloat(tbs_diolah) * 100).toFixed(2);
		document.getElementById('rendemen_pk_after').value = (parseFloat(kernel_produksi) / parseFloat(tbs_after_grading) * 100).toFixed(2);
	}
	
	
}

function calcKapasitasPress() {
	total_jam_press = document.getElementById('total_jam_press').value;
	tbs_diolah = document.getElementById('tbs_diolah').value;
	if (total_jam_press == 0 || tbs_diolah == 0) {
		document.getElementById('kapasitas_press').value = 0;
	}else{
		document.getElementById('kapasitas_press').value = (parseFloat(tbs_diolah) / parseFloat(total_jam_press)).toFixed(2);
	}
	
}

// 10. hitung kalsium
function calcKalsium() {
	caco3 = document.getElementById('caco3').value;
	tbs_diolah = document.getElementById('tbs_diolah').value;
	kernel_produksi = document.getElementById('kernel_produksi').value;

	if (parseFloat(tbs_diolah) == 0) {
		document.getElementById('rasio_kalsium_tbs').value = 0;
		document.getElementById('rasio_kalsium_pk').value = 0;
	}else{
		document.getElementById('rasio_kalsium_tbs').value = (parseFloat(caco3) / parseFloat(tbs_diolah) * 1000).toFixed(2);
		document.getElementById('rasio_kalsium_pk').value = (parseFloat(caco3) / parseFloat(kernel_produksi) * 1000).toFixed(2);
	}
	
	
}
// 9. hitung produksi cpo kernel

function hitungProduksiPK() {
	closing_stock = document.getElementById('kernel_closing_stock').value;
	opening_stock = document.getElementById('kernel_opening_stock').value;
	pengiriman = document.getElementById('pengiriman_despatch_pk').value;
	return_pengiriman = document.getElementById('pengiriman_return_pk').value;
	
 	pk_produksi = parseFloat(closing_stock) - parseFloat(opening_stock) + parseFloat(pengiriman) - parseFloat(return_pengiriman);
	document.getElementById('kernel_produksi').value = pk_produksi;
}

function hitungProduksiCPO() {
	closing_stock = document.getElementById('cpo_closing_stock').value;
	opening_stock = document.getElementById('cpo_opening_stock').value;
	pengiriman = document.getElementById('pengiriman_despatch_cpo').value;
	return_pengiriman = document.getElementById('pengiriman_return_cpo').value;
	
 	cpo_produksi = parseFloat(closing_stock) - parseFloat(opening_stock) + parseFloat(pengiriman) - parseFloat(return_pengiriman);
	document.getElementById('cpo_produksi').value = cpo_produksi;
}

// 8. AMBIL SOUNDING
function getPKSounding() {
	kodeorg=document.getElementById('kodeorg').value;
	tanggal=document.getElementById('tanggal').value;
	param='kodeorg='+kodeorg+'&tanggal='+tanggal;
	param+='&proses=getPKSounding';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
				busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
					var data = JSON.parse(con.responseText);
					if (data.jumlah_pk_sounding != null) {
						document.getElementById('kernel_closing_stock').value = data.jumlah_pk_sounding;
					}else{
						document.getElementById('kernel_closing_stock').value = 0;
					}
					hitungProduksiCPO();
					hitungProduksiPK();
					hitungRendemen();
					getSolar();
				}
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }    
    post_response_text('pabrik_slave_get_data.php', param, respon);
}

function getCPOSounding() {
	kodeorg=document.getElementById('kodeorg').value;
	tanggal=document.getElementById('tanggal').value;
	param='kodeorg='+kodeorg+'&tanggal='+tanggal;
	param+='&proses=getCPOSounding';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
				busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
					var data = JSON.parse(con.responseText);
					if (data.jumlah_cpo_sounding != null) {
						document.getElementById('cpo_closing_stock').value = data.jumlah_cpo_sounding;
					}else{
						document.getElementById('cpo_closing_stock').value = 0;
					}
					getPKSounding();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }    
    post_response_text('pabrik_slave_get_data.php', param, respon);
}

// 3. setelah itu hitung sisa
function calcSisa() {
	TBSKemarin = document.getElementById('tbs_sisa_kemarin').value;
	TBSMasuk = document.getElementById('tbs_masuk_bruto').value;
	TBSDiolah = document.getElementById('tbs_diolah').value;

	document.getElementById('tbs_sisa').value = parseFloat(TBSKemarin) + parseFloat(TBSMasuk) - parseFloat(TBSDiolah) ;
}

// 2. get sisa kemarin
function ambilSisaTbsKemarin(){
	kodeorg=document.getElementById('kodeorg').value;
	tanggal=document.getElementById('tanggal').value;
	param='kodeorg='+kodeorg+'&tanggal='+tanggal;
	param+='&proses=getProduksiHarianTerakhir';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
				busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
					data = JSON.parse(con.responseText);
					if (data != null) {
						document.getElementById('cpo_opening_stock').value=data.cpo_closing_stock;
						document.getElementById('kernel_opening_stock').value=data.kernel_closing_stock;
						
					}else{
						document.getElementById('cpo_opening_stock').value=0;
						document.getElementById('kernel_opening_stock').value=0;
					}
					calcKapasitasPress();
					getCPOSounding();
					calcSisa();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }    
    post_response_text('pabrik_slave_get_data.php', param, respon);

}

// 1. KETIKA MEMILIH TANGGAL
function getDataPengolahan()
{
	kodeorg=document.getElementById('kodeorg').value;
	tanggal=document.getElementById('tanggal').value;
	param='proses=getDataPengolahan'+'&tanggal='+tanggal+'&kodeorg='+kodeorg;
	tujuan = 'pabrik_slave_get_data.php';
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
					data = JSON.parse(con.responseText);
					if (data == "awan") {
						document.getElementById('tbs_masuk_bruto').value = 0;
						document.getElementById('tbs_masuk_netto').value = 0;
						document.getElementById('tbs_potongan').value = 0;
						document.getElementById('tbs_sisa_kemarin').value = 0;
						// document.getElementById('tbs_sisa_kemarin').value = 0;

						// pengiriman
						document.getElementById('pengiriman_despatch_cpo').value = 0;
						document.getElementById('pengiriman_return_cpo').value = 0;
						document.getElementById('pengiriman_despatch_pk').value = 0;
						document.getElementById('pengiriman_return_pk').value = 0;
						document.getElementById('pengiriman_janjang_kosong').value = 0;
						document.getElementById('pengiriman_limbah_kosong').value = 0;
						document.getElementById('pengiriman_solid_decnter').value = 0;
						document.getElementById('pengiriman_abu_janjang').value = 0;
						document.getElementById('pengiriman_cangkang').value = 0;
						document.getElementById('pengiriman_fibre').value = 0;
						document.getElementById('jumlah_hari_olah').value = 0;
						document.getElementById('tbs_diolah').value = 0;
						document.getElementById('lori_olah').value = 0;
						document.getElementById('lori_dalam_rebusan').value = 0;
						document.getElementById('restan_depan_rebusan').value = 0;
						document.getElementById('restan_dibelakang_rebusan').value = 0;
						document.getElementById('estimasi_di_peron').value = 0;
						document.getElementById('total_lori').value = 0;
						document.getElementById('lori_rata_rata').value = 0;
						document.getElementById('total_jam_press').value = 0;
						document.getElementById('total_jam_operasi').value = 0;
						
						tbs_diolah = document.getElementById('tbs_diolah').value;
						document.getElementById('kapasitas_olah').value = 0;
				
					}else{
						document.getElementById('tbs_masuk_bruto').value = data.tbs_masuk_bruto;
						document.getElementById('tbs_masuk_netto').value = data.tbs_masuk_netto;
						document.getElementById('tbs_potongan').value = data.tbs_potongan;
						document.getElementById('tbs_sisa_kemarin').value = data.tbs_sisa_kemarin;
						// document.getElementById('tbs_sisa_kemarin') = data.tbs_sisa_kemarin;

						// pengiriman
						document.getElementById('pengiriman_despatch_cpo').value = data.despatch_cpo;
						document.getElementById('pengiriman_return_cpo').value = data.return_cpo;
						document.getElementById('pengiriman_despatch_pk').value = data.despatch_pk;
						document.getElementById('pengiriman_return_pk').value = data.return_pk;
						document.getElementById('pengiriman_janjang_kosong').value = data.janjang_kosong;
						document.getElementById('pengiriman_limbah_kosong').value = data.limbah_cair;
						document.getElementById('pengiriman_solid_decnter').value = data.solid_decnter;
						document.getElementById('pengiriman_abu_janjang').value = data.abu_janjang;
						document.getElementById('pengiriman_cangkang').value = data.cangkang;
						document.getElementById('pengiriman_fibre').value = data.fibre;

						document.getElementById('jumlah_hari_olah').value = data.status_olah;						
						
						document.getElementById('tbs_diolah').value = data.tbs_diolah;
						document.getElementById('tbs_after_grading').value = data.tbs_diolah_after;

						document.getElementById('lori_olah').value = parseFloat(data.lori_olah_shift_1) + parseFloat(data.lori_olah_shift_2) + parseFloat(data.lori_olah_shift_3);
						document.getElementById('lori_dalam_rebusan').value = data.lori_dalam_rebusan;
						document.getElementById('restan_depan_rebusan').value = data.restan_depan_rebusan;
						document.getElementById('restan_dibelakang_rebusan').value = data.restan_dibelakang_rebusan;
						document.getElementById('estimasi_di_peron').value = data.estimasi_di_peron;
						document.getElementById('total_lori').value = data.total_lori;
						document.getElementById('lori_rata_rata').value = data.rata_rata_lori;
						document.getElementById('total_jam_press').value = parseFloat(data.total_jam_press);
						document.getElementById('total_jam_operasi').value = parseFloat(data.total_jam_operasi);
						
						tbs_diolah = document.getElementById('tbs_diolah').value;
						if (parseFloat(data.tbs_diolah) == 0 || parseFloat(data.total_jam_operasi) == 0) {
							document.getElementById('kapasitas_olah').value = 0;
						} else {
							document.getElementById('kapasitas_olah').value = (parseFloat(data.tbs_diolah) / parseFloat(data.total_jam_operasi)).toFixed(2);
						}
						
					
					}
					

					
					// calcSisa();
					ambilSisaTbsKemarin();
					
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}	
	} 	
}

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

function calcRendemen() {
	
	TBSOlah = document.getElementById('tbsdiolah').value;
	CPOHasilSoundingHariIni = document.getElementById('CPOSoundingHariIni').value;
	CPOPengirimanKemarin = document.getElementById('CPOPengirimanKemarin').value;
	CPOStokKemarin = document.getElementById('stok_cpo_kemarin').value;
	CPOReturn = document.getElementById('return_cpo').value;
	
	rendemenCPOAfterGrading = parseFloat(TBSOlah) - parseFloat(CPOHasilSoundingHariIni) + parseFloat(CPOPengirimanKemarin) - parseFloat(CPOStokKemarin) - parseFloat(CPOReturn);
	document.getElementById('rendemen_cpo_after').value = rendemenCPOAfterGrading;
	
	RendemenCPOBeforeGrading = parseFloat(CPOHasilSoundingHariIni) + parseFloat(CPOPengirimanKemarin) - parseFloat(CPOStokKemarin) - parseFloat(CPOReturn);
	document.getElementById('rendemen_cpo_after').value = rendemenCPOAfterGrading;

	
}

function calcCPOKERNELKG() {
	CPOHasilSoundingHariIni = document.getElementById('CPOSoundingHariIni').value;
	CPOPengirimanHariIni = document.getElementById('cpo').value;
	CPOStokKemarin = document.getElementById('stok_cpo_kemarin').value;

	document.getElementById('oercpo').value = parseFloat(CPOHasilSoundingHariIni) + parseFloat(CPOPengirimanHariIni) - parseFloat(CPOStokKemarin);
	
	KERNELSoundingHariIni = document.getElementById('KERNELSoundingHariIni').value;
	KERNELPengirimanHariIni = document.getElementById('pk').value;
	KERNELStokKemarin = document.getElementById('stok_kernel_kemarin').value;

	document.getElementById('oerpk').value = parseFloat(KERNELSoundingHariIni) + parseFloat(KERNELPengirimanHariIni) - parseFloat(KERNELStokKemarin);
	calcRendemen();
}

function cetakPDF(kodeorg,tanggal, ev='event')
{
	param='kodeorg='+kodeorg+'&periode='+tanggal;
	param+='&method=preview'
    showDialog1('Preview Data',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='pabrik_slave_produksi_harian_excel.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '850px';
    dialog.style.left = '15%';
}

function simpanProduksi(){
	param = 'kodeorg='+getValue('kodeorg');
	param += '&tanggal='+getValue('tanggal');

	param += '&tbs_sisa_kemarin='+getValue('tbs_sisa_kemarin');
	param += '&tbs_masuk_bruto='+getValue('tbs_masuk_bruto');
	param += '&tbs_potongan='+getValue('tbs_potongan');
	param += '&tbs_masuk_netto='+getValue('tbs_masuk_netto');
	param += '&tbs_diolah='+getValue('tbs_diolah');
	param += '&tbs_after_grading='+getValue('tbs_after_grading');
	param += '&tbs_sisa='+getValue('tbs_sisa');
	
	param += '&lori_olah='+getValue('lori_olah');
	param += '&total_lori='+getValue('total_lori');
	param += '&lori_dalam_rebusan='+getValue('lori_dalam_rebusan');
	param += '&restan_depan_rebusan='+getValue('restan_depan_rebusan');
	param += '&restan_dibelakang_rebusan='+getValue('restan_dibelakang_rebusan');
	param += '&estimasi_di_peron='+getValue('estimasi_di_peron');
	param += '&lori_rata_rata='+getValue('lori_rata_rata');
	
	param += '&informasi_cuaca='+getValue('informasi_cuaca');

	param += '&rendemen_cpo_after='+getValue('rendemen_cpo_after');
	param += '&rendemen_cpo_before='+getValue('rendemen_cpo_before');
	param += '&rendemen_pk_before='+getValue('rendemen_pk_before');
	param += '&rendemen_pk_after='+getValue('rendemen_pk_after');

	param += '&cpo_opening_stock='+getValue('cpo_opening_stock');
	param += '&cpo_produksi='+getValue('cpo_produksi');
	param += '&cpo_closing_stock='+getValue('cpo_closing_stock');
	param += '&cpo_kotoran='+getValue('cpo_kotoran');
	param += '&cpo_kadar_air='+getValue('cpo_kadar_air');
	param += '&cpo_ffa='+getValue('cpo_ffa');
	param += '&cpo_dobi='+getValue('cpo_dobi');
	
	// param += '&cpo_loses_tanggal='+getValue('cpo_loses_tanggal');
	// param += '&cpo_usb='+getValue('cpo_usb');
	// param += '&cpo_empty_bunch='+getValue('cpo_empty_bunch');
	// param += '&cpo_fibre_cyclone='+getValue('cpo_fibre_cyclone');
	// param += '&cpo_nut_from_polishingdrum='+getValue('cpo_nut_from_polishingdrum');
	// param += '&cpo_effluent='+getValue('cpo_effluent');
	
	param += '&pengiriman_despatch_cpo='+getValue('pengiriman_despatch_cpo');
	param += '&pengiriman_return_cpo='+getValue('pengiriman_return_cpo');
	param += '&pengiriman_despatch_pk='+getValue('pengiriman_despatch_pk');
	param += '&pengiriman_return_pk='+getValue('pengiriman_return_pk');
	param += '&pengiriman_janjang_kosong='+getValue('pengiriman_janjang_kosong');
	param += '&pengiriman_limbah_kosong='+getValue('pengiriman_limbah_kosong');
	param += '&pengiriman_solid_decnter='+getValue('pengiriman_solid_decnter');
	param += '&pengiriman_abu_janjang='+getValue('pengiriman_abu_janjang');
	param += '&pengiriman_cangkang='+getValue('pengiriman_cangkang');
	param += '&pengiriman_fibre='+getValue('pengiriman_fibre');
	
	param += '&kernel_opening_stock='+getValue('kernel_opening_stock');
	param += '&kernel_produksi='+getValue('kernel_produksi');
	param += '&kernel_closing_stock='+getValue('kernel_closing_stock');
	param += '&kernel_kotoran='+getValue('kernel_kotoran');
	param += '&kernel_kadar_air='+getValue('kernel_kadar_air');
	param += '&kernel_inti_pecah='+getValue('kernel_inti_pecah');

	// param += '&kernel_loses_tanggal='+getValue('kernel_loses_tanggal');
	// param += '&kernel_loses_usb='+getValue('kernel_loses_usb');
	// param += '&kernel_loses_fibre_cyclone='+getValue('kernel_loses_fibre_cyclone');
	// param += '&kernel_loses_ltds_1='+getValue('kernel_loses_ltds_1');
	// param += '&kernel_loses_ltds_2='+getValue('kernel_loses_ltds_2');
	// param += '&kernel_loses_claybath='+getValue('kernel_loses_claybath');
	
	param += '&jumlah_hari_olah='+getValue('jumlah_hari_olah');
	param += '&kapasitas_olah='+getValue('kapasitas_olah');
	param += '&utilitas_kapasitas='+getValue('utilitas_kapasitas');
	param += '&utility_factor_commercial='+getValue('utility_factor_commercial');
	
	param += '&caco3='+getValue('caco3');
	param += '&rasio_kalsium_tbs='+getValue('rasio_kalsium_tbs');
	param += '&rasio_kalsium_pk='+getValue('rasio_kalsium_pk');
	
	param += '&total_jam_press='+getValue('total_jam_press');
	param += '&total_jam_operasi='+getValue('total_jam_operasi');
	param += '&kapasitas_press='+getValue('kapasitas_press');
	
	param += '&stock_product_janjang_kosong='+getValue('stock_product_janjang_kosong');
	param += '&stock_product_limbar_cair='+getValue('stock_product_limbar_cair');
	param += '&stock_product_cangkang='+getValue('stock_product_cangkang');
	param += '&stock_product_fibre='+getValue('stock_product_fibre');
	param += '&stock_product_abu_incenerator='+getValue('stock_product_abu_incenerator');
	
	param += '&solar_genset_1='+getValue('solar_genset_1');
	param += '&solar_genset_2='+getValue('solar_genset_2');
	param += '&solar_genset_3='+getValue('solar_genset_3');
	param += '&total_solar_genset='+getValue('total_solar_genset');

	param += '&solar_loader_1='+getValue('solar_loader_1');
	param += '&solar_loader_2='+getValue('solar_loader_2');
	param += '&solar_loader_3='+getValue('solar_loader_3');
	param += '&solar_loader_rental='+getValue('solar_loader_rental');
	param += '&total_solar_loader='+getValue('total_solar_loader');

	param += '&hm_genset_1='+getValue('hm_genset_1');
	param += '&hm_genset_2='+getValue('hm_genset_2');
	param += '&hm_genset_3='+getValue('hm_genset_3');
	param += '&total_hm_genset='+getValue('total_hm_genset');

	param += '&hm_loader_1='+getValue('hm_loader_1');
	param += '&hm_loader_2='+getValue('hm_loader_2');
	param += '&hm_loader_3='+getValue('hm_loader_3');
	param += '&hm_loader_rental='+getValue('hm_loader_rental');
	param += '&total_hm_loader='+getValue('total_hm_loader');

	param += '&rasio_total_solar_genset_hm_total_genset='+getValue('rasio_total_solar_genset_hm_total_genset');
	param += '&rasio_total_solar_loader_hm_total_loader='+getValue('rasio_total_solar_loader_hm_total_loader');

	param += '&catatan1='+getValue('catatan1');
	param += '&catatan2='+getValue('catatan2');
	param += '&catatan3='+getValue('catatan3');
	param += '&catatan4='+getValue('catatan4');

	param += '&method=insert';

	tujuan='pabrik_slave_save_produksi.php';

	Swal.fire({
		title: 'Yakin untuk menyimpan?',
		text: "Periksa kembali data yang sudah diinput!",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Save'
		}).then((result) => {
			if (result.value) {
				post_response_text(tujuan, param, respog);
			}
		});
	
	

	function respog()
	{
		if(con.readyState==4)
		{
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					// alert('ERROR TRANSACTION,\n' + con.responseText);
					Swal.fire('Oops...', con.responseText, 'error');
				}
				else {
					data = JSON.parse(con.responseText);
					if (data == "berhasil") {
						Swal.fire("Simpan data berhasil.");
						loadData();
					} else {
						Swal.fire('Oops...', data, 'error');
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

function delProduksi(kodeorg,tanggal)
{
	param = 'kodeorg='+kodeorg+'&tanggal='+tanggal;
	param += '&method=delete';
	tujuan = 'pabrik_slave_save_produksi.php';

	Swal.fire({
		title: 'Yakin untuk menghapus?',
		text: "Data yang dihapus tidak dapat dikembalikan lagi!",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Yes, delete it!'
		}).then((result) => {
			if (result.value) {
				post_response_text(tujuan, param, respog);
			}
		});

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
					data = JSON.parse(con.responseText);
					if (data == "berhasil") {
						Swal.fire("Hapus data berhasil.");
						loadData();
					} else {
						Swal.fire('Oops...', "Hapus data gagal.", 'error');
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

function loadData()
{
    param='method=loadData';
    tujuan='pabrik_slave_save_produksi.php';
    post_response_text(tujuan, param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
					document.getElementById('table_pabrik_produksi').innerHTML=con.responseText;
					$('#data_pabrik_produksi').DataTable();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}