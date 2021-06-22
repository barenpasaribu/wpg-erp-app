/**

 * @author Developer

 */


function ambilPeriode(gudang) {
        param = 'gudang=' + gudang;
        tujuan = 'log_slave_getPeriode.php';
        post_response_text(tujuan, param, respog);

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        document.getElementById('periode').innerHTML = con.responseText;
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}

function getSTNKKendaraan() {
        unit = document.getElementById('unit');
        unit = unit.options[unit.selectedIndex].value;
        param = 'unit=' + unit;
        tujuan = 'vhc_slave_2stnkjt.php';
        post_response_text(tujuan, param, respog);

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        showById('printPanel');
                                        document.getElementById('container').innerHTML = con.responseText;
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}

function printFile(param, tujuan, title, ev) {
        tujuan = tujuan + "?" + param;
        width = '700';
        height = '400';
        content = "<iframe frameborder=0 width=100% height=100% src='" + tujuan + "'></iframe>"
        showDialog1(title, content, width, height, ev);
}

function viewDetail(ev, kodevhc, tanggalmulai, tanggalsampai, unit, periode, noakunawal, noakunakhir) {
        param = 'kodevhc=' + kodevhc + '&tanggalmulai=' + tanggalmulai + '&tanggalsampai=' + tanggalsampai + '&unit=' + unit + '&periode=' + periode;
        param += '&noakunawal=' + noakunawal + '&noakunakhir=' + noakunakhir;
        tujuan = 'vhc_slave_2biayatotalperkendaraandetail.php' + "?" + param;
        width = '500';
        height = '400';
        content = "<iframe frameborder=0 width=100% height=100% src='" + tujuan + "'></iframe>"
        showDialog1('Cost Detai By Unit ' + kodevhc, content, width, height, ev);
}

function detailAlokasi(ev, kdvhc, hrgsat) {
        tglAwl = document.getElementById('tglAwal').value;
        tglAkhr = document.getElementById('tglAkhir').value;
        param = 'kodevhc=' + kdvhc + '&hrgaSatuan=' + hrgsat;
        param += '&tglAkhir=' + tglAkhr + '&tglAwal=' + tglAwl;
        tujuan = 'vhc_slave_2biayaalokasiperkendaraandetail.php' + "?" + param;
        width = '800';
        height = '500';
        //alert(param);
        content = "<iframe frameborder=0 width=100% height=100% src='" + tujuan + "'></iframe>"
        showDialog2('Allocation Detail' + kdvhc, content, width, height, ev);
}

function detailExcel(ev) {
        width = '300';
        height = '100';
        kodevhc = document.getElementById('kodevhc').value;
        tanggalmulai = document.getElementById('tanggalmulai').value;
        tanggalsampai = document.getElementById('tanggalsampai').value;
        noakunawal = document.getElementById('noakunawal').value;
        noakunakhir = document.getElementById('noakunakhir').value;
        unit = document.getElementById('unit').value;
        param = 'kodevhc=' + kodevhc + '&tanggalmulai=' + tanggalmulai + '&tanggalsampai=' + tanggalsampai + '&unit=' + unit;
        param += '&noakunawal=' + noakunawal + '&noakunakhir=' + noakunakhir + '&type=excel';
        tujuan = 'vhc_slave_2biayatotalperkendaraandetail.php' + "?" + param;
        content = "<iframe frameborder=0 width=100% height=100% src='" + tujuan + "'></iframe>"
        showDialog1('Detail Cost By Vehicle', content, width, height, ev);
}

function detailData(ev, tujuan) {
        width = '300';
        height = '100';
        content = "<iframe frameborder=0 width=100% height=100% src='" + tujuan + "'></iframe>"
        showDialog1('Allocation Detail', content, width, height, ev);
}

function jtSTNKKeExcel(ev, tujuan) {
        unit = document.getElementById('unit');
        //        tglAwl=document.getElementById('tglAwal').value;
        //        tglAkhr=document.getElementById('tglAkhir').value;
        //	periode =document.getElementById('periode');
        unit = unit.options[unit.selectedIndex].value;
        //        periode	=periode.options[periode.selectedIndex].value;
        judul = 'Report Ms.Excel';
        //param='unit='+unit+'&periode='+periode;
        //        param='unit='+unit+'&tglAkhir='+tglAkhr+'&tglAwal='+tglAwl;
        param = 'unit=' + unit;
        printFile(param, tujuan, judul, ev)
}

function ambilPeriode2(unit) {
        param = 'unit=' + unit;
        tujuan = 'sdm_slave_getPeriode.php';
        post_response_text(tujuan, param, respog);

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        document.getElementById('periode').innerHTML = con.responseText;
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}