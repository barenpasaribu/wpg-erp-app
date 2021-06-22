// JavaScript Document

function getTgl()
{
    periode=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
    kdUnit=document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;
    param='periode='+periode+'&proses=getTgl'+'&kdUnit='+kdUnit;
    tujuan='sdm_slave_2rekapabsen';
    post_response_text(tujuan+'.php?proses=getTgl', param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    ar=con.responseText.split("###");
                    document.getElementById('tgl1').value=ar[0];
                    document.getElementById('tgl2').value=ar[1];
                    document.getElementById('tgl1').disabled=true;
                    //document.getElementById('tgl2').disabled=true;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
    //  alert(fileTarget+'.php?proses=preview', param, respon);
}
function getTgl2()
{
    periode=document.getElementById('period').options[document.getElementById('period').selectedIndex].value;
    kdUnit=document.getElementById('kdeOrg').options[document.getElementById('kdeOrg').selectedIndex].value;
    param='periode='+periode+'&proses=getTgl'+'&kdUnit='+kdUnit;
    tujuan='sdm_slave_2rekapabsen';
    post_response_text(tujuan+'.php?proses=getTgl', param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    ar=con.responseText.split("###");
                    document.getElementById('tgl_1').value=ar[0];
                    document.getElementById('tgl_2').value=ar[1];
                    document.getElementById('tgl_1').disabled=true;
                    //document.getElementById('tgl_2').disabled=true;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
    //  alert(fileTarget+'.php?proses=preview', param, respon);
}

// function getBagian()
// {
//     kdUnit=document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;
//     param='kdUnit='+kdUnit;
//     tujuan='sdm_slave_2rekapabsen';
//
//     post_response_text(tujuan+'.php?proses=getBagian', param, respon);
//     function respon() {
//         if (con.readyState == 4) {
//             if (con.status == 200) {
//                 busy_off();
//                 if (!isSaveResponse(con.responseText)) {
//                     alert('ERROR TRANSACTION,\n' + con.responseText);
//                 } else {
//                     // Success Response
//                     ar=con.responseText.split("####");
//                     kdBag
//                     document.getElementById('kdBag').innerHTML=kdBag;
//                     // document.getElementById('afdId').innerHTML=ar[0];
//                     // document.getElementById('periode').innerHTML=ar[1];
//
//                 }
//             } else {
//                 busy_off();
//                 error_catch(con.status);
//             }
//         }
//     }
// }

function getPeriode()
{
    kdUnit=document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;
    param='kdUnit='+kdUnit;
    tujuan='sdm_slave_2rekapabsen';

    post_response_text(tujuan+'.php?proses=getPeriode', param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    // console.log(con.responseText);
                    var ar=con.responseText.split("####");
                    // document.getElementById('afdId').innerHTML=ar[0];
                    document.getElementById('periode').innerHTML=ar[1];
                    document.getElementById('kdBag').innerHTML=ar[2];

                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function getPeriodeGaji()
{
    kdUnit=document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;
    periode=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
    sistemGaji=document.getElementById('sistemGaji').options[document.getElementById('sistemGaji').selectedIndex].value;
    param='kdUnit='+kdUnit+'&periode='+periode+'&sistemGaji='+sistemGaji;
    tujuan='sdm_slave_2rekapabsen';
    post_response_text(tujuan+'.php?proses=getTgl', param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    //ar=con.responseText.split("###");
                    ar=con.responseText.split("###");
                    document.getElementById('tgl1').value=ar[0];
                    document.getElementById('tgl2').value=ar[1];
                    document.getElementById('tgl1').disabled=true;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function getPeriodeGaji2()
{
    kdUnit=document.getElementById('kdeOrg').options[document.getElementById('kdeOrg').selectedIndex].value;
    period=document.getElementById('period').options[document.getElementById('period').selectedIndex].value;
    sistemGaji=document.getElementById('sistemGaji2').options[document.getElementById('sistemGaji2').selectedIndex].value;
    param='kdUnit='+kdUnit+'&periode='+period+'&sistemGaji='+sistemGaji;
    tujuan='sdm_slave_2rekapabsen';
    post_response_text(tujuan+'.php?proses=getTgl', param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    //ar=con.responseText.split("###");
                    ar=con.responseText.split("###");
                    document.getElementById('tgl_1').value=ar[0];
                    document.getElementById('tgl_2').value=ar[1];
                    document.getElementById('tgl_1').disabled=true;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function getKry()
{
    kdeOrg=document.getElementById('kdeOrg').options[document.getElementById('kdeOrg').selectedIndex].value;
    param='kdeOrg='+kdeOrg;
    tujuan='sdm_slave_2rekapabsen';
    post_response_text(tujuan+'.php?proses=getKry', param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    ar=con.responseText.split("###");
                    document.getElementById('idKry').innerHTML=ar[0];
                    // document.getElementById('period').innerHTML=ar[1];
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function Clear1()
{
    document.getElementById('tgl1').value='';
    document.getElementById('tgl2').value='';
    document.getElementById('tgl1').disabled=false;
    document.getElementById('tgl2').disabled=false;
    document.getElementById('kdOrg').value='';
    document.getElementById('periode').value='';
    document.getElementById('printContainer').innerHTML='';
}
function Clear2()
{
    document.getElementById('tgl_1').value='';
    document.getElementById('tgl_2').value='';
    document.getElementById('tgl_1').disabled=false;
    document.getElementById('tgl_2').disabled=false;
    document.getElementById('kdeOrg').value='';
    document.getElementById('period').value='';
    document.getElementById('idKry').innerHTML="<option value''></option>";
    document.getElementById('printContainer').innerHTML='';
}
function Clear3()
{
    document.getElementById('kdeOrg2').value='';
    document.getElementById('periodThn').value='';
    document.getElementById('tipeKary2').value='';
    document.getElementById('periodThnSmp').value='';
    document.getElementById('sistemGaji3').value='';
    document.getElementById('printContainer').innerHTML='';
}

function showpopup(karyawanid,namakaryawan,tanggal,notransaksi,ev)
{
    param='karyawanid='+karyawanid+'&namakaryawan='+namakaryawan+'&tanggal='+tanggal+'&notransaksi='+notransaksi;
    tujuan='sdm_slave_2rekapabsen_showpopup.php'+"?"+param;
    width='450';
    height='150';

    content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
    showDialog1('Absence transaction: '+karyawanid+' '+tanggal,content,width,height,ev);

}
function getPeriodeGaji5()
{
    kdUnit=document.getElementById('kdeOrg2').options[document.getElementById('kdeOrg2').selectedIndex].value;
    param='kdUnit='+kdUnit;
    tujuan='sdm_slave_2rekapabsen';
    post_response_text(tujuan+'.php?proses=getPeriodeGaji5', param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    der=con.responseText.split("####");
                    document.getElementById('periodThn').innerHTML=der[0];
                    document.getElementById('periodThnSmp').innerHTML=der[1];

                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}