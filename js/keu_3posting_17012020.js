/* listPosting
 * Fungsi untuk men-generate list dari transaksi yang dapat di posting
 */
function listPosting() {
    var listPost = document.getElementById('listPosting');
    var param = "kodeorg="+getValue('kodeorg')+"&periode="+getValue('periode')+"&jenisdata="+getValue('jenisData');

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    listPost.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    x=getValue('jenisData');
    if(x=='gudang')
         post_response_text('keu_slave_3posting.php', param, respon);
    else if(x=='gaji')
         post_response_text('keu_slave_3gajikaryawan.php', param, respon);
    else if(x=='depresiasi') 
          post_response_text('keu_slave_3depresiasi.php', param, respon);
    else if(x=='alokasi') 
          post_response_text('keu_slave_3traksi.php', param, respon);   
          //post_response_text('keu_slave_3traksi_tes.php', param, respon);   
    else if(x=='gajiharilibur') 
        post_response_text('keu_slave_3gajiharilibur.php', param, respon); 
    else
        post_response_text('keu_slave_3pengakuanPotongan.php', param, respon);
    //else if(x=='potongan') 
    //      post_response_text('keu_slave_3pengakuanPotongan.php', param, respon);       
}

function prosesGudang(row)
{
    document.getElementById('btnproses').disabled=true;
    tipetransaksi   =document.getElementById('tipetransaksi'+row).innerHTML;
    notransaksi     =document.getElementById('notransaksi'+row).innerHTML;
    kodebarang      =document.getElementById('kodebarang'+row).innerHTML;
    jumlah          =document.getElementById('jumlah'+row).innerHTML;
    satuan          =document.getElementById('satuan'+row).innerHTML;
    idsupplier      =document.getElementById('idsupplier'+row).innerHTML;
    gudangx         =document.getElementById('gudangx'+row).innerHTML;
    untukunit       =document.getElementById('untukunit'+row).innerHTML;
    kodeblok        =document.getElementById('kodeblok'+row).innerHTML;
    kodemesin       =document.getElementById('kodemesin'+row).innerHTML;
    kodekegiatan    =document.getElementById('kodekegiatan'+row).innerHTML;
    hartot          =document.getElementById('hartot'+row).innerHTML;
    nopo            =document.getElementById('nopo'+row).innerHTML;
    kodegudang      =document.getElementById('kodegudang'+row).innerHTML;
    tanggal         =document.getElementById('tanggal'+row).innerHTML;
    keterangan      =document.getElementById('keterangan'+row).innerHTML;

    param='tipetransaksi='+tipetransaksi+'&notransaksi='+notransaksi+
          '&kodebarang='+kodebarang+
          '&jumlah='+jumlah+'&satuan='+satuan+'&idsupplier='+idsupplier+
          '&gudangx='+gudangx+'&untukunit='+untukunit+'&kodeblok='+kodeblok+
          '&kodemesin='+kodemesin+'&kodekegiatan='+kodekegiatan+
          '&hartot='+hartot+'&nopo='+nopo+'&kodegudang='+kodegudang+'&tanggal='+tanggal+
          '&keterangan='+keterangan;
    tujuan='keu_slave_prosesGudangAkhirbulan.php';
    post_response_text(tujuan, param, respon);
    document.getElementById('row'+row).style.backgroundColor='orange';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    document.getElementById('row'+row).style.backgroundColor='red';
                } else {
                    document.getElementById('row'+row).style.display='none';
                    try{
                        x=row+1;
                        if(document.getElementById('row'+x))
                         {   
                             row=x;
                             prosesGudang(row);
                         }
                                                 else
                                                 {
                                                    alert('Done');
                                                 }
                    }
                    catch(e)
                    {
                        alert('Done');
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    } 
}

function prosesGajiOld(row)
{
    document.getElementById('btnproses').disabled=true;
    namakaryawan   =document.getElementById('namakaryawan'+row).innerHTML;
    karyawanid     =document.getElementById('karyawanid'+row).innerHTML;
    komponen       =document.getElementById('komponen'+row).innerHTML;
    namakomponen   =document.getElementById('namakomponen'+row).innerHTML;
    subbagian      =document.getElementById('subbagian'+row).innerHTML;
    mesin          =document.getElementById('mesin'+row).innerHTML;
    jumlah         =document.getElementById('jumlah'+row).innerHTML;
    tipeorganisasi =document.getElementById('tipeorganisasi'+row).innerHTML;
    periode        =document.getElementById('periode'+row).innerHTML;

    param='namakaryawan='+namakaryawan+'&karyawanid='+karyawanid+
          '&komponen='+komponen+'&namakomponen='+namakomponen+
        '&subbagian=' + subbagian + '&mesin=' + mesin + '&jumlah=' + remove_comma_var(jumlah)+
          '&tipeorganisasi='+tipeorganisasi+'&periode='+periode+'&row='+row;    
    tujuan='keu_slave_prosesAlokasiGajiAkhirbulan.php';
 if(row==1 && confirm('Anda yakin melakukan proses pengalokasian gaji?'))
        post_response_text(tujuan, param, respon);
 else
        post_response_text(tujuan, param, respon);

    document.getElementById('row'+row).style.backgroundColor='orange';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    document.getElementById('row'+row).style.backgroundColor='red';
                } else {
                    document.getElementById('row'+row).style.display='none';
                    try{
                        x=row+1;
                        if(document.getElementById('row'+x))
                         {   
                             row=x;
                             prosesGaji(row);
                         }
                         else
                         {
                            alert('Done');
                         }
                    }
                    catch(e)
                    {
                        alert('Done');
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    } 
}
function prosesGajiBulanan(row) {
    document.getElementById('btnprosesHO').disabled = true;

    komponen = document.getElementById('komponen' + row).value;
    tottunjtetap = document.getElementById('tottunjtetap' + row).innerHTML;

    komponenlembur = document.getElementById('komponenlembur' + row).value;
    totlembur = document.getElementById('totlembur' + row).innerHTML;

    komponentunjpremi = document.getElementById('komponentunjpremi' + row).value;
    totpremi = document.getElementById('totpremi' + row).innerHTML;

    komponentunjkom = document.getElementById('komponentunjkom' + row).value;
    tottunjkom = document.getElementById('tottunjkom' + row).innerHTML;

    komponentunjlok = document.getElementById('komponentunjlok' + row).value;
    tottunjlok = document.getElementById('tottunjlok' + row).innerHTML;

    komponentunjprt = document.getElementById('komponentunjprt' + row).value;
    tottunjprt = document.getElementById('tottunjprt' + row).innerHTML;

    komponentunjbbm = document.getElementById('komponentunjbbm' + row).value;
    tottunjbbm = document.getElementById('tottunjbbm' + row).innerHTML;

    komponentunjair = document.getElementById('komponentunjair' + row).value;
    tottunjair = document.getElementById('tottunjair' + row).innerHTML;

    komponentunjspart = document.getElementById('komponentunjspart' + row).value;
    tottunjspart = document.getElementById('tottunjspart' + row).innerHTML;

    komponentunjharian = document.getElementById('komponentunjharian' + row).value;
    tottunjharian = document.getElementById('tottunjharian' + row).innerHTML;

    komponentunjdinas = document.getElementById('komponentunjdinas' + row).value;
    tottunjdinas = document.getElementById('tottunjdinas' + row).innerHTML;

    komponentunjcuti = document.getElementById('komponentunjcuti' + row).value;
    tottunjcuti = document.getElementById('tottunjcuti' + row).innerHTML;

    komponentunjlistrik = document.getElementById('komponentunjlistrik' + row).value;
    tottunjlistrik = document.getElementById('tottunjlistrik' + row).innerHTML;

    komponentunjlain = document.getElementById('komponentunjlain' + row).value;
    tottunjlain = document.getElementById('tottunjlain' + row).innerHTML;

    komponentunjrapel = document.getElementById('komponentunjrapel' + row).value;
    tottunjrapel = document.getElementById('tottunjrapel' + row).innerHTML;

    komponentunjjkk = document.getElementById('komponentunjjkk' + row).value;
    tottunjjkk = document.getElementById('tottunjjkk' + row).innerHTML;

    komponentunjjkm = document.getElementById('komponentunjjkm' + row).value;
    tottunjjkm = document.getElementById('tottunjjkm' + row).innerHTML;

    komponentunjbpjskes = document.getElementById('komponentunjbpjskes' + row).value;
    tottunjbpjskes = document.getElementById('tottunjbpjskes' + row).innerHTML;

//=============================================================================================================

    komponenpotbiayajab = document.getElementById('komponenpotbiayajab' + row).value;
    totpotbiayajab = document.getElementById('totpotbiayajab' + row).innerHTML;

    komponenpotjhtkar = document.getElementById('komponenpotjhtkar' + row).value;
    totpotjhtkar = document.getElementById('totpotjhtkar' + row).innerHTML;

    komponenpotjpkar = document.getElementById('komponenpotjpkar' + row).value;
    totpotjpkar = document.getElementById('totpotjpkar' + row).innerHTML;

    komponenpotpph21 = document.getElementById('komponenpotpph21' + row).value;
    totpotpph21 = document.getElementById('totpotpph21' + row).innerHTML;

    komponenpotkoperasi = document.getElementById('komponenpotkoperasi' + row).value;
    totpotkoperasi = document.getElementById('totpotkoperasi' + row).innerHTML;

    komponenpotvop = document.getElementById('komponenpotvop' + row).value;
    totpotvop = document.getElementById('totpotvop' + row).innerHTML;

    komponenpotmotor = document.getElementById('komponenpotmotor' + row).value;
    totpotmotor = document.getElementById('totpotmotor' + row).innerHTML;

    komponenpotlaptop = document.getElementById('komponenpotlaptop' + row).value;
    totpotlaptop = document.getElementById('totpotlaptop' + row).innerHTML;

    komponenpotdenda = document.getElementById('komponenpotdenda' + row).value;
    totpotdenda = document.getElementById('totpotdenda' + row).innerHTML;

    //komponenpotdendapanen = document.getElementById('komponenpotdendapanen' + row).value;
    //totpotdendapanen = document.getElementById('totpotdendapanen' + row).innerHTML;

    komponenpotbpjskes = document.getElementById('komponenpotbpjskes' + row).value;
    totpotbpjskes = document.getElementById('totpotbpjskes' + row).innerHTML;
    
    periode = document.getElementById('periode' + row).innerHTML;

    param = 'tottunjtetap=' + remove_comma_var(tottunjtetap) + '&totlembur=' + remove_comma_var(totlembur) + '&totpremi=' + remove_comma_var(totpremi) + '&tottunjkom=' + remove_comma_var(tottunjkom) + '&tottunjlok=' + remove_comma_var(tottunjlok) + '&tottunjprt=' + remove_comma_var(tottunjprt) + '&tottunjbbm=' + remove_comma_var(tottunjbbm) + '&tottunjair=' + remove_comma_var(tottunjair) + '&tottunjspart=' + remove_comma_var(tottunjspart) + '&tottunjharian=' + remove_comma_var(tottunjharian) + '&tottunjdinas=' + remove_comma_var(tottunjdinas) + '&tottunjcuti=' + remove_comma_var(tottunjcuti) + '&tottunjlistrik=' + remove_comma_var(tottunjlistrik) + '&tottunjlain=' + remove_comma_var(tottunjlain) + '&tottunjrapel=' + remove_comma_var(tottunjrapel) + '&tottunjjkk=' + remove_comma_var(tottunjjkk) + '&tottunjjkm=' + remove_comma_var(tottunjjkm) + '&tottunjbpjskes=' + remove_comma_var(tottunjbpjskes) + '&totpotbiayajab=' + remove_comma_var(totpotbiayajab) + '&totpotjhtkar=' + remove_comma_var(totpotjhtkar) + '&totpotjpkar=' + remove_comma_var(totpotjpkar) + '&totpotpph21=' + remove_comma_var(totpotpph21) + '&totpotkoperasi=' + remove_comma_var(totpotkoperasi) + '&totpotvop=' + remove_comma_var(totpotvop) + '&totpotmotor=' + remove_comma_var(totpotmotor) + '&totpotlaptop=' + remove_comma_var(totpotlaptop) + '&totpotdenda=' + remove_comma_var(totpotdenda) + '&totpotbpjskes=' + remove_comma_var(totpotbpjskes) + '&periode=' + periode + '&komponen=' + komponen + '&komponenlembur=' + komponenlembur + '&komponentunjpremi=' + komponentunjpremi + '&komponentunjkom=' + komponentunjkom + '&komponentunjlok=' + komponentunjlok + '&komponentunjprt=' + komponentunjprt + '&komponentunjbbm=' + komponentunjbbm + '&komponentunjair=' + komponentunjair + '&komponentunjspart=' + komponentunjspart + '&komponentunjharian=' + komponentunjharian + '&komponentunjdinas=' + komponentunjdinas + '&komponentunjcuti=' + komponentunjcuti + '&komponentunjlistrik=' + komponentunjlistrik + '&komponentunjlain=' + komponentunjlain + '&komponentunjrapel=' + komponentunjrapel + '&komponentunjjkk=' + komponentunjjkk + '&komponentunjjkm=' + komponentunjjkm + '&komponentunjbpjskes=' + komponentunjbpjskes + '&row=' + row;
    param += '&komponenpotbiayajab=' + komponenpotbiayajab + '&komponenpotjhtkar=' + komponenpotjhtkar + '&komponenpotjpkar=' + komponenpotjpkar + '&komponenpotpph21=' + komponenpotpph21;
    param += '&komponenpotkoperasi=' + komponenpotkoperasi + '&komponenpotvop=' + komponenpotvop + '&komponenpotmotor=' + komponenpotmotor + '&komponenpotlaptop=' + komponenpotlaptop;
    param += '&komponenpotdenda=' + komponenpotdenda + '&komponenpotbpjskes=' + komponenpotbpjskes;
    //alert(param);
    //exit();
    tujuan = 'keu_slave_prosesAlokasiGajiAkhirbulan.php';
    if (row == 1 && confirm('Anda yakin melakukan proses pengalokasian gaji karyawan Staff ?'))
        post_response_text(tujuan, param, respon);
    else
        post_response_text(tujuan, param, respon);

    document.getElementById('row' + row).style.backgroundColor = 'orange';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    document.getElementById('row' + row).style.backgroundColor = 'red';
                } else {
                    document.getElementById('row' + row).style.display = 'none';
                    try {
                        x = row + 1;
                        if (document.getElementById('row' + x)) {
                            row = x;
                            prosesGajiBulanan(row);
                        }
                        else {
                            alert('Done');
                        }
                    }
                    catch (e) {
                        alert('Done');
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function prosesGaji(row) {
    document.getElementById('btnproses').disabled = true;

    komponen = document.getElementById('komponen' + row).value;
    tottunjtetap = document.getElementById('tottunjtetap' + row).innerHTML;

    komponenlembur = document.getElementById('komponenlembur' + row).value;
    totlembur = document.getElementById('totlembur' + row).innerHTML;

    komponentunjpremi = document.getElementById('komponentunjpremi' + row).value;
    totpremi = document.getElementById('totpremi' + row).innerHTML;

    komponentunjkom = document.getElementById('komponentunjkom' + row).value;
    tottunjkom = document.getElementById('tottunjkom' + row).innerHTML;

    komponentunjlok = document.getElementById('komponentunjlok' + row).value;
    tottunjlok = document.getElementById('tottunjlok' + row).innerHTML;

    komponentunjprt = document.getElementById('komponentunjprt' + row).value;
    tottunjprt = document.getElementById('tottunjprt' + row).innerHTML;

    komponentunjbbm = document.getElementById('komponentunjbbm' + row).value;
    tottunjbbm = document.getElementById('tottunjbbm' + row).innerHTML;

    komponentunjair = document.getElementById('komponentunjair' + row).value;
    tottunjair = document.getElementById('tottunjair' + row).innerHTML;

    komponentunjspart = document.getElementById('komponentunjspart' + row).value;
    tottunjspart = document.getElementById('tottunjspart' + row).innerHTML;

    komponentunjharian = document.getElementById('komponentunjharian' + row).value;
    tottunjharian = document.getElementById('tottunjharian' + row).innerHTML;

    komponentunjdinas = document.getElementById('komponentunjdinas' + row).value;
    tottunjdinas = document.getElementById('tottunjdinas' + row).innerHTML;

    komponentunjcuti = document.getElementById('komponentunjcuti' + row).value;
    tottunjcuti = document.getElementById('tottunjcuti' + row).innerHTML;

    komponentunjlistrik = document.getElementById('komponentunjlistrik' + row).value;
    tottunjlistrik = document.getElementById('tottunjlistrik' + row).innerHTML;

    komponentunjlain = document.getElementById('komponentunjlain' + row).value;
    tottunjlain = document.getElementById('tottunjlain' + row).innerHTML;

    komponentunjrapel = document.getElementById('komponentunjrapel' + row).value;
    tottunjrapel = document.getElementById('tottunjrapel' + row).innerHTML;

    komponentunjjkk = document.getElementById('komponentunjjkk' + row).value;
    tottunjjkk = document.getElementById('tottunjjkk' + row).innerHTML;

    komponentunjjkm = document.getElementById('komponentunjjkm' + row).value;
    tottunjjkm = document.getElementById('tottunjjkm' + row).innerHTML;

    komponentunjbpjskes = document.getElementById('komponentunjbpjskes' + row).value;
    tottunjbpjskes = document.getElementById('tottunjbpjskes' + row).innerHTML;

    //=============================================================================================================

    komponenpotbiayajab = document.getElementById('komponenpotbiayajab' + row).value;
    totpotbiayajab = document.getElementById('totpotbiayajab' + row).innerHTML;

    komponenpotjhtkar = document.getElementById('komponenpotjhtkar' + row).value;
    totpotjhtkar = document.getElementById('totpotjhtkar' + row).innerHTML;

    komponenpotjpkar = document.getElementById('komponenpotjpkar' + row).value;
    totpotjpkar = document.getElementById('totpotjpkar' + row).innerHTML;

    komponenpotpph21 = document.getElementById('komponenpotpph21' + row).value;
    totpotpph21 = document.getElementById('totpotpph21' + row).innerHTML;

    komponenpotkoperasi = document.getElementById('komponenpotkoperasi' + row).value;
    totpotkoperasi = document.getElementById('totpotkoperasi' + row).innerHTML;

    komponenpotvop = document.getElementById('komponenpotvop' + row).value;
    totpotvop = document.getElementById('totpotvop' + row).innerHTML;

    komponenpotmotor = document.getElementById('komponenpotmotor' + row).value;
    totpotmotor = document.getElementById('totpotmotor' + row).innerHTML;

    komponenpotlaptop = document.getElementById('komponenpotlaptop' + row).value;
    totpotlaptop = document.getElementById('totpotlaptop' + row).innerHTML;

    komponenpotdenda = document.getElementById('komponenpotdenda' + row).value;
    totpotdenda = document.getElementById('totpotdenda' + row).innerHTML;

    komponenpotdendapanen = document.getElementById('komponenpotdendapanen' + row).value;
    totpotdendapanen = document.getElementById('totpotdendapanen' + row).innerHTML;

    komponenpotbpjskes = document.getElementById('komponenpotbpjskes' + row).value;
    totpotbpjskes = document.getElementById('totpotbpjskes' + row).innerHTML;

    periode = document.getElementById('periode' + row).innerHTML;

    param = 'tottunjtetap=' + remove_comma_var(tottunjtetap) + '&totlembur=' + remove_comma_var(totlembur) + '&totpremi=' + remove_comma_var(totpremi) + '&tottunjkom=' + remove_comma_var(tottunjkom) + '&tottunjlok=' + remove_comma_var(tottunjlok) + '&tottunjprt=' + remove_comma_var(tottunjprt) + '&tottunjbbm=' + remove_comma_var(tottunjbbm) + '&tottunjair=' + remove_comma_var(tottunjair) + '&tottunjspart=' + remove_comma_var(tottunjspart) + '&tottunjharian=' + remove_comma_var(tottunjharian) + '&tottunjdinas=' + remove_comma_var(tottunjdinas) + '&tottunjcuti=' + remove_comma_var(tottunjcuti) + '&tottunjlistrik=' + remove_comma_var(tottunjlistrik) + '&tottunjlain=' + remove_comma_var(tottunjlain) + '&tottunjrapel=' + remove_comma_var(tottunjrapel) + '&tottunjjkk=' + remove_comma_var(tottunjjkk) + '&tottunjjkm=' + remove_comma_var(tottunjjkm) + '&tottunjbpjskes=' + remove_comma_var(tottunjbpjskes) + '&totpotbiayajab=' + remove_comma_var(totpotbiayajab) + '&totpotjhtkar=' + remove_comma_var(totpotjhtkar) + '&totpotjpkar=' + remove_comma_var(totpotjpkar) + '&totpotpph21=' + remove_comma_var(totpotpph21) + '&totpotkoperasi=' + remove_comma_var(totpotkoperasi) + '&totpotvop=' + remove_comma_var(totpotvop) + '&totpotmotor=' + remove_comma_var(totpotmotor) + '&totpotlaptop=' + remove_comma_var(totpotlaptop) + '&totpotdenda=' + remove_comma_var(totpotdenda) + '&totpotdendapanen=' + remove_comma_var(totpotdendapanen) + '&totpotbpjskes=' + remove_comma_var(totpotbpjskes) + '&periode=' + periode + '&komponen=' + komponen + '&komponenlembur=' + komponenlembur + '&komponentunjpremi=' + komponentunjpremi + '&komponentunjkom=' + komponentunjkom + '&komponentunjlok=' + komponentunjlok + '&komponentunjprt=' + komponentunjprt + '&komponentunjbbm=' + komponentunjbbm + '&komponentunjair=' + komponentunjair + '&komponentunjspart=' + komponentunjspart + '&komponentunjharian=' + komponentunjharian + '&komponentunjdinas=' + komponentunjdinas + '&komponentunjcuti=' + komponentunjcuti + '&komponentunjlistrik=' + komponentunjlistrik + '&komponentunjlain=' + komponentunjlain + '&komponentunjrapel=' + komponentunjrapel + '&komponentunjjkk=' + komponentunjjkk + '&komponentunjjkm=' + komponentunjjkm + '&komponentunjbpjskes=' + komponentunjbpjskes + '&row=' + row;
    param += '&komponenpotbiayajab=' + komponenpotbiayajab + '&komponenpotjhtkar=' + komponenpotjhtkar + '&komponenpotjpkar=' + komponenpotjpkar + '&komponenpotpph21=' + komponenpotpph21;
    param += '&komponenpotkoperasi=' + komponenpotkoperasi + '&komponenpotvop=' + komponenpotvop + '&komponenpotmotor=' + komponenpotmotor + '&komponenpotlaptop=' + komponenpotlaptop;
    param += '&komponenpotdenda=' + komponenpotdenda + '&komponenpotdendapanen=' + komponenpotdendapanen + '&komponenpotbpjskes=' + komponenpotbpjskes;
    //alert(param);
    //exit();
    tujuan = 'keu_slave_prosesAlokasiGajiAkhirbulan.php';
    if (row == 1 && confirm('Anda yakin melakukan proses pengalokasian gaji karyawan Unit/Estate ?'))
        post_response_text(tujuan, param, respon);
    else
        post_response_text(tujuan, param, respon);

    document.getElementById('row' + row).style.backgroundColor = 'orange';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    document.getElementById('row' + row).style.backgroundColor = 'red';
                } else {
                    document.getElementById('row' + row).style.display = 'none';
                    try {
                        x = row + 1;
                        if (document.getElementById('row' + x)) {
                            row = x;
                            prosesGaji(row);
                        }
                        else {
                            alert('Done');
                        }
                    }
                    catch (e) {
                        alert('Done');
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function prosesGajiKanwil(row) {
    document.getElementById('btnprosesKanwil').disabled = true;

    komponen = document.getElementById('komponen' + row).value;
    tottunjtetap = document.getElementById('tottunjtetap' + row).innerHTML;

    komponenlembur = document.getElementById('komponenlembur' + row).value;
    totlembur = document.getElementById('totlembur' + row).innerHTML;

    komponentunjpremi = document.getElementById('komponentunjpremi' + row).value;
    totpremi = document.getElementById('totpremi' + row).innerHTML;

    komponentunjkom = document.getElementById('komponentunjkom' + row).value;
    tottunjkom = document.getElementById('tottunjkom' + row).innerHTML;

    komponentunjlok = document.getElementById('komponentunjlok' + row).value;
    tottunjlok = document.getElementById('tottunjlok' + row).innerHTML;

    komponentunjprt = document.getElementById('komponentunjprt' + row).value;
    tottunjprt = document.getElementById('tottunjprt' + row).innerHTML;

    komponentunjbbm = document.getElementById('komponentunjbbm' + row).value;
    tottunjbbm = document.getElementById('tottunjbbm' + row).innerHTML;

    komponentunjair = document.getElementById('komponentunjair' + row).value;
    tottunjair = document.getElementById('tottunjair' + row).innerHTML;

    komponentunjspart = document.getElementById('komponentunjspart' + row).value;
    tottunjspart = document.getElementById('tottunjspart' + row).innerHTML;

    komponentunjharian = document.getElementById('komponentunjharian' + row).value;
    tottunjharian = document.getElementById('tottunjharian' + row).innerHTML;

    komponentunjdinas = document.getElementById('komponentunjdinas' + row).value;
    tottunjdinas = document.getElementById('tottunjdinas' + row).innerHTML;

    komponentunjcuti = document.getElementById('komponentunjcuti' + row).value;
    tottunjcuti = document.getElementById('tottunjcuti' + row).innerHTML;

    komponentunjlistrik = document.getElementById('komponentunjlistrik' + row).value;
    tottunjlistrik = document.getElementById('tottunjlistrik' + row).innerHTML;

    komponentunjlain = document.getElementById('komponentunjlain' + row).value;
    tottunjlain = document.getElementById('tottunjlain' + row).innerHTML;

    komponentunjrapel = document.getElementById('komponentunjrapel' + row).value;
    tottunjrapel = document.getElementById('tottunjrapel' + row).innerHTML;

    komponentunjjkk = document.getElementById('komponentunjjkk' + row).value;
    tottunjjkk = document.getElementById('tottunjjkk' + row).innerHTML;

    komponentunjjkm = document.getElementById('komponentunjjkm' + row).value;
    tottunjjkm = document.getElementById('tottunjjkm' + row).innerHTML;

    komponentunjbpjskes = document.getElementById('komponentunjbpjskes' + row).value;
    tottunjbpjskes = document.getElementById('tottunjbpjskes' + row).innerHTML;

    //=============================================================================================================

    komponenpotbiayajab = document.getElementById('komponenpotbiayajab' + row).value;
    totpotbiayajab = document.getElementById('totpotbiayajab' + row).innerHTML;

    komponenpotjhtkar = document.getElementById('komponenpotjhtkar' + row).value;
    totpotjhtkar = document.getElementById('totpotjhtkar' + row).innerHTML;

    komponenpotjpkar = document.getElementById('komponenpotjpkar' + row).value;
    totpotjpkar = document.getElementById('totpotjpkar' + row).innerHTML;

    komponenpotpph21 = document.getElementById('komponenpotpph21' + row).value;
    totpotpph21 = document.getElementById('totpotpph21' + row).innerHTML;

    komponenpotkoperasi = document.getElementById('komponenpotkoperasi' + row).value;
    totpotkoperasi = document.getElementById('totpotkoperasi' + row).innerHTML;

    komponenpotvop = document.getElementById('komponenpotvop' + row).value;
    totpotvop = document.getElementById('totpotvop' + row).innerHTML;

    komponenpotmotor = document.getElementById('komponenpotmotor' + row).value;
    totpotmotor = document.getElementById('totpotmotor' + row).innerHTML;

    komponenpotlaptop = document.getElementById('komponenpotlaptop' + row).value;
    totpotlaptop = document.getElementById('totpotlaptop' + row).innerHTML;

    komponenpotdenda = document.getElementById('komponenpotdenda' + row).value;
    totpotdenda = document.getElementById('totpotdenda' + row).innerHTML;

    //komponenpotdendapanen = document.getElementById('komponenpotdendapanen' + row).value;
    //totpotdendapanen = document.getElementById('totpotdendapanen' + row).innerHTML;

    komponenpotbpjskes = document.getElementById('komponenpotbpjskes' + row).value;
    totpotbpjskes = document.getElementById('totpotbpjskes' + row).innerHTML;

    periode = document.getElementById('periode' + row).innerHTML;

    param = 'tottunjtetap=' + remove_comma_var(tottunjtetap) + '&totlembur=' + remove_comma_var(totlembur) + '&totpremi=' + remove_comma_var(totpremi) + '&tottunjkom=' + remove_comma_var(tottunjkom) + '&tottunjlok=' + remove_comma_var(tottunjlok) + '&tottunjprt=' + remove_comma_var(tottunjprt) + '&tottunjbbm=' + remove_comma_var(tottunjbbm) + '&tottunjair=' + remove_comma_var(tottunjair) + '&tottunjspart=' + remove_comma_var(tottunjspart) + '&tottunjharian=' + remove_comma_var(tottunjharian) + '&tottunjdinas=' + remove_comma_var(tottunjdinas) + '&tottunjcuti=' + remove_comma_var(tottunjcuti) + '&tottunjlistrik=' + remove_comma_var(tottunjlistrik) + '&tottunjlain=' + remove_comma_var(tottunjlain) + '&tottunjrapel=' + remove_comma_var(tottunjrapel) + '&tottunjjkk=' + remove_comma_var(tottunjjkk) + '&tottunjjkm=' + remove_comma_var(tottunjjkm) + '&tottunjbpjskes=' + remove_comma_var(tottunjbpjskes) + '&totpotbiayajab=' + remove_comma_var(totpotbiayajab) + '&totpotjhtkar=' + remove_comma_var(totpotjhtkar) + '&totpotjpkar=' + remove_comma_var(totpotjpkar) + '&totpotpph21=' + remove_comma_var(totpotpph21) + '&totpotkoperasi=' + remove_comma_var(totpotkoperasi) + '&totpotvop=' + remove_comma_var(totpotvop) + '&totpotmotor=' + remove_comma_var(totpotmotor) + '&totpotlaptop=' + remove_comma_var(totpotlaptop) + '&totpotdenda=' + remove_comma_var(totpotdenda) + '&totpotbpjskes=' + remove_comma_var(totpotbpjskes) + '&periode=' + periode + '&komponen=' + komponen + '&komponenlembur=' + komponenlembur + '&komponentunjpremi=' + komponentunjpremi + '&komponentunjkom=' + komponentunjkom + '&komponentunjlok=' + komponentunjlok + '&komponentunjprt=' + komponentunjprt + '&komponentunjbbm=' + komponentunjbbm + '&komponentunjair=' + komponentunjair + '&komponentunjspart=' + komponentunjspart + '&komponentunjharian=' + komponentunjharian + '&komponentunjdinas=' + komponentunjdinas + '&komponentunjcuti=' + komponentunjcuti + '&komponentunjlistrik=' + komponentunjlistrik + '&komponentunjlain=' + komponentunjlain + '&komponentunjrapel=' + komponentunjrapel + '&komponentunjjkk=' + komponentunjjkk + '&komponentunjjkm=' + komponentunjjkm + '&komponentunjbpjskes=' + komponentunjbpjskes + '&row=' + row;
    param += '&komponenpotbiayajab=' + komponenpotbiayajab + '&komponenpotjhtkar=' + komponenpotjhtkar + '&komponenpotjpkar=' + komponenpotjpkar + '&komponenpotpph21=' + komponenpotpph21;
    param += '&komponenpotkoperasi=' + komponenpotkoperasi + '&komponenpotvop=' + komponenpotvop + '&komponenpotmotor=' + komponenpotmotor + '&komponenpotlaptop=' + komponenpotlaptop;
    param += '&komponenpotdenda=' + komponenpotdenda + '&komponenpotbpjskes=' + komponenpotbpjskes;
    //alert(param);
    //exit();
    tujuan = 'keu_slave_prosesAlokasiGajiAkhirbulan.php';
    if (row == 1 && confirm('Anda yakin melakukan proses pengalokasian gaji karyawan RO Dan Traksi ?'))
        post_response_text(tujuan, param, respon);
    else
        post_response_text(tujuan, param, respon);

    document.getElementById('row' + row).style.backgroundColor = 'orange';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    document.getElementById('row' + row).style.backgroundColor = 'red';
                } else {
                    document.getElementById('row' + row).style.display = 'none';
                    try {
                        x = row + 1;
                        if (document.getElementById('row' + x)) {
                            row = x;
                            prosesGajiKanwil(row);
                        }
                        else {
                            alert('Done');
                        }
                    }
                    catch (e) {
                        alert('Done');
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function prosesGajiPabrik(row) {
    document.getElementById('btnprosesPabrik').disabled = true;

    komponen = document.getElementById('komponen' + row).value;
    tottunjtetap = document.getElementById('tottunjtetap' + row).innerHTML;

    komponenlembur = document.getElementById('komponenlembur' + row).value;
    totlembur = document.getElementById('totlembur' + row).innerHTML;

    komponentunjpremi = document.getElementById('komponentunjpremi' + row).value;
    totpremi = document.getElementById('totpremi' + row).innerHTML;

    komponentunjkom = document.getElementById('komponentunjkom' + row).value;
    tottunjkom = document.getElementById('tottunjkom' + row).innerHTML;

    komponentunjlok = document.getElementById('komponentunjlok' + row).value;
    tottunjlok = document.getElementById('tottunjlok' + row).innerHTML;

    komponentunjprt = document.getElementById('komponentunjprt' + row).value;
    tottunjprt = document.getElementById('tottunjprt' + row).innerHTML;

    komponentunjbbm = document.getElementById('komponentunjbbm' + row).value;
    tottunjbbm = document.getElementById('tottunjbbm' + row).innerHTML;

    komponentunjair = document.getElementById('komponentunjair' + row).value;
    tottunjair = document.getElementById('tottunjair' + row).innerHTML;

    komponentunjspart = document.getElementById('komponentunjspart' + row).value;
    tottunjspart = document.getElementById('tottunjspart' + row).innerHTML;

    komponentunjharian = document.getElementById('komponentunjharian' + row).value;
    tottunjharian = document.getElementById('tottunjharian' + row).innerHTML;

    komponentunjdinas = document.getElementById('komponentunjdinas' + row).value;
    tottunjdinas = document.getElementById('tottunjdinas' + row).innerHTML;

    komponentunjcuti = document.getElementById('komponentunjcuti' + row).value;
    tottunjcuti = document.getElementById('tottunjcuti' + row).innerHTML;

    komponentunjlistrik = document.getElementById('komponentunjlistrik' + row).value;
    tottunjlistrik = document.getElementById('tottunjlistrik' + row).innerHTML;

    komponentunjlain = document.getElementById('komponentunjlain' + row).value;
    tottunjlain = document.getElementById('tottunjlain' + row).innerHTML;

    komponentunjrapel = document.getElementById('komponentunjrapel' + row).value;
    tottunjrapel = document.getElementById('tottunjrapel' + row).innerHTML;

    komponentunjjkk = document.getElementById('komponentunjjkk' + row).value;
    tottunjjkk = document.getElementById('tottunjjkk' + row).innerHTML;

    komponentunjjkm = document.getElementById('komponentunjjkm' + row).value;
    tottunjjkm = document.getElementById('tottunjjkm' + row).innerHTML;

    komponentunjbpjskes = document.getElementById('komponentunjbpjskes' + row).value;
    tottunjbpjskes = document.getElementById('tottunjbpjskes' + row).innerHTML;

    //=============================================================================================================

    komponenpotbiayajab = document.getElementById('komponenpotbiayajab' + row).value;
    totpotbiayajab = document.getElementById('totpotbiayajab' + row).innerHTML;

    komponenpotjhtkar = document.getElementById('komponenpotjhtkar' + row).value;
    totpotjhtkar = document.getElementById('totpotjhtkar' + row).innerHTML;

    komponenpotjpkar = document.getElementById('komponenpotjpkar' + row).value;
    totpotjpkar = document.getElementById('totpotjpkar' + row).innerHTML;

    komponenpotpph21 = document.getElementById('komponenpotpph21' + row).value;
    totpotpph21 = document.getElementById('totpotpph21' + row).innerHTML;

    komponenpotkoperasi = document.getElementById('komponenpotkoperasi' + row).value;
    totpotkoperasi = document.getElementById('totpotkoperasi' + row).innerHTML;

    komponenpotvop = document.getElementById('komponenpotvop' + row).value;
    totpotvop = document.getElementById('totpotvop' + row).innerHTML;

    komponenpotmotor = document.getElementById('komponenpotmotor' + row).value;
    totpotmotor = document.getElementById('totpotmotor' + row).innerHTML;

    komponenpotlaptop = document.getElementById('komponenpotlaptop' + row).value;
    totpotlaptop = document.getElementById('totpotlaptop' + row).innerHTML;

    komponenpotdenda = document.getElementById('komponenpotdenda' + row).value;
    totpotdenda = document.getElementById('totpotdenda' + row).innerHTML;

    //komponenpotdendapanen = document.getElementById('komponenpotdendapanen' + row).value;
    //totpotdendapanen = document.getElementById('totpotdendapanen' + row).innerHTML;

    komponenpotbpjskes = document.getElementById('komponenpotbpjskes' + row).value;
    totpotbpjskes = document.getElementById('totpotbpjskes' + row).innerHTML;

    periode = document.getElementById('periode' + row).innerHTML;

    param = 'tottunjtetap=' + remove_comma_var(tottunjtetap) + '&totlembur=' + remove_comma_var(totlembur) + '&totpremi=' + remove_comma_var(totpremi) + '&tottunjkom=' + remove_comma_var(tottunjkom) + '&tottunjlok=' + remove_comma_var(tottunjlok) + '&tottunjprt=' + remove_comma_var(tottunjprt) + '&tottunjbbm=' + remove_comma_var(tottunjbbm) + '&tottunjair=' + remove_comma_var(tottunjair) + '&tottunjspart=' + remove_comma_var(tottunjspart) + '&tottunjharian=' + remove_comma_var(tottunjharian) + '&tottunjdinas=' + remove_comma_var(tottunjdinas) + '&tottunjcuti=' + remove_comma_var(tottunjcuti) + '&tottunjlistrik=' + remove_comma_var(tottunjlistrik) + '&tottunjlain=' + remove_comma_var(tottunjlain) + '&tottunjrapel=' + remove_comma_var(tottunjrapel) + '&tottunjjkk=' + remove_comma_var(tottunjjkk) + '&tottunjjkm=' + remove_comma_var(tottunjjkm) + '&tottunjbpjskes=' + remove_comma_var(tottunjbpjskes) + '&totpotbiayajab=' + remove_comma_var(totpotbiayajab) + '&totpotjhtkar=' + remove_comma_var(totpotjhtkar) + '&totpotjpkar=' + remove_comma_var(totpotjpkar) + '&totpotpph21=' + remove_comma_var(totpotpph21) + '&totpotkoperasi=' + remove_comma_var(totpotkoperasi) + '&totpotvop=' + remove_comma_var(totpotvop) + '&totpotmotor=' + remove_comma_var(totpotmotor) + '&totpotlaptop=' + remove_comma_var(totpotlaptop) + '&totpotdenda=' + remove_comma_var(totpotdenda) + '&totpotbpjskes=' + remove_comma_var(totpotbpjskes) + '&periode=' + periode + '&komponen=' + komponen + '&komponenlembur=' + komponenlembur + '&komponentunjpremi=' + komponentunjpremi + '&komponentunjkom=' + komponentunjkom + '&komponentunjlok=' + komponentunjlok + '&komponentunjprt=' + komponentunjprt + '&komponentunjbbm=' + komponentunjbbm + '&komponentunjair=' + komponentunjair + '&komponentunjspart=' + komponentunjspart + '&komponentunjharian=' + komponentunjharian + '&komponentunjdinas=' + komponentunjdinas + '&komponentunjcuti=' + komponentunjcuti + '&komponentunjlistrik=' + komponentunjlistrik + '&komponentunjlain=' + komponentunjlain + '&komponentunjrapel=' + komponentunjrapel + '&komponentunjjkk=' + komponentunjjkk + '&komponentunjjkm=' + komponentunjjkm + '&komponentunjbpjskes=' + komponentunjbpjskes + '&row=' + row;
    param += '&komponenpotbiayajab=' + komponenpotbiayajab + '&komponenpotjhtkar=' + komponenpotjhtkar + '&komponenpotjpkar=' + komponenpotjpkar + '&komponenpotpph21=' + komponenpotpph21;
    param += '&komponenpotkoperasi=' + komponenpotkoperasi + '&komponenpotvop=' + komponenpotvop + '&komponenpotmotor=' + komponenpotmotor + '&komponenpotlaptop=' + komponenpotlaptop;
    param += '&komponenpotdenda=' + komponenpotdenda + '&komponenpotbpjskes=' + komponenpotbpjskes;
    //alert(param);
    //exit();
    tujuan = 'keu_slave_prosesAlokasiGajiAkhirbulan.php';
    if (row == 1 && confirm('Anda yakin melakukan proses pengalokasian gaji karyawan Pabrik ?'))
        post_response_text(tujuan, param, respon);
    else
        post_response_text(tujuan, param, respon);

    document.getElementById('row' + row).style.backgroundColor = 'orange';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    document.getElementById('row' + row).style.backgroundColor = 'red';
                } else {
                    document.getElementById('row' + row).style.display = 'none';
                    try {
                        x = row + 1;
                        if (document.getElementById('row' + x)) {
                            row = x;
                            prosesGajiPabrik(row);
                        }
                        else {
                            alert('Done');
                        }
                    }
                    catch (e) {
                        alert('Done');
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function prosesAlokasi(row)
{
    periode  =document.getElementById('periode'+row).innerHTML;
    param='periode='+periode;
    tujuan='vhc_slave_updateFlag.php';
     if(confirm('Anda yakin melakukan proses pengalokasian biaya Kendaraan?'))
     post_response_text(tujuan, param, respon);

        function respon() {
              if (con.readyState == 4) {
                  if (con.status == 200) {
                      busy_off();
                      if (!isSaveResponse(con.responseText)) {
                          alert(' Error:,\n' + con.responseText);
                      } else {
                          doProsesAlokasi(row);
                      }
                  } else {
                      busy_off();
                      error_catch(con.status);
                  }
              }
          }     
}

function doProsesAlokasi(row)
{
    document.getElementById('btnproses').disabled=true;
    periode  =document.getElementById('periode'+row).innerHTML;
    kodevhc  =document.getElementById('kodevhc'+row).innerHTML;
    jumlah   =document.getElementById('jumlah'+row).innerHTML;
    jenis    =document.getElementById('jenis'+row).innerHTML;

    param='periode='+periode+'&kodevhc='+kodevhc+'&jumlah='+jumlah+'&jenis='+jenis;   
    tujuan='keu_slave_prosesAlokasiTraksi.php';
 if(jumlah!='0')
   {   
            post_response_text(tujuan, param, respon);
   }
  else
   {//next
         row++;
         doProsesAlokasi(row);      
   }   
    document.getElementById('row'+row).style.backgroundColor='orange';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    document.getElementById('row'+row).style.backgroundColor='red';
                } else {
                    document.getElementById('row'+row).style.display='none';
                    try{
                        x=row+1;
                        if(document.getElementById('row'+x))
                         {   
                             row=x;
                             doProsesAlokasi(row);
                         }
                         else
                         {
                            alert('Done');//jangan buang ini
                         }
                    }
                    catch(e)
                    {
                        alert('Done');//jangan buang ini
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    } 
}



function prosesPenyusutan(row)
{
   document.getElementById('btnproses').disabled=true;
    kodejurnal  =document.getElementById('kodejurnal'+row).innerHTML;
    periode     =document.getElementById('periode'+row).innerHTML;
    keterangan  =document.getElementById('keterangan'+row).innerHTML;
    jumlah      =document.getElementById('jumlah'+row).innerHTML;


    param='kodejurnal='+kodejurnal+'&periode='+periode+
          '&keterangan='+keterangan+'&jumlah='+jumlah;    
    tujuan='keu_slave_prosesDepresiasiAkhirbulan.php';
 if(row==1 && confirm('Anda yakin melakukan proses penyusutan?'))
        post_response_text(tujuan, param, respon);
 else
        post_response_text(tujuan, param, respon);

    document.getElementById('row'+row).style.backgroundColor='orange';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    document.getElementById('row'+row).style.backgroundColor='red';
                } else {
                    document.getElementById('row'+row).style.display='none';
                    try{
                        x=row+1;
                        if(document.getElementById('row'+x))
                         {   
                             row=x;
                             prosesPenyusutan(row);
                         }
                         else
                         {
                            alert('Done');
                         }
                    }
                    catch(e)
                    {
                        alert('Done');
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }     
}


function prosesGajiLangsung(row)
{
    document.getElementById('btnproses').disabled=true;
    karyawanid     =document.getElementById('karyawanid').value;
    jumlah         =document.getElementById('jumlah').value;
    dari        =document.getElementById('dari').value;
    sampai        =document.getElementById('sampai').value;
    param='karyawanid='+karyawanid+'&jumlah='+jumlah+
          '&dari='+dari+'&sampai='+sampai+'&row='+row;     
    tujuan='keu_slave_prosesAlokasiGajiKetinggalan.php';
 if(confirm('Anda yakin melakukan proses pengalokasian gaji?'))
        post_response_text(tujuan, param, respon);


    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    try{
                        x=row+1;
                        if(document.getElementById('row'+x))
                         {   
                             row=x;
                             prosesGajiLangsung(row);
                         }
                         else
                         {
                            alert('Done');
                         }
                    }
                    catch(e)
                    {
                        alert('Done');
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }    
}

function prosesPotongan(periode){
     param='periode='+periode+'&method=post';
    tujuan='keu_slave_3pengakuanPotongan.php';
     if(confirm('Anda yakin melakukan proses ini?'))
          post_response_text(tujuan, param, respon);

        function respon() {
              if (con.readyState == 4) {
                  if (con.status == 200) {
                      busy_off();
                      if (!isSaveResponse(con.responseText)) {
                          alert(' Error:,\n' + con.responseText);
                      } else {
                          alert('Done');
                      }
                  } else {
                      busy_off();
                      error_catch(con.status);
                  }
              }
          }  
}
