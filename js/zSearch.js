/* Function getInv
 * Fungsi untuk pop up window, untuk memilih barang
 * I : id target
 * O : Pop up window untuk pencarian barang
 */
function getInv(event,id) {
    var cont = "<fieldset><legend><b>Search</b></legend>";
    cont += "<input id='invSearch' type='text' onkeypress=\"if(getKey(event)==13){searchInv('invSearch','"+id+"');} else {return tanpa_kutip(event)}\" />";
    cont += "<img src='images/search.png' onclick=\"searchInv('invSearch','"+id+"')\" style='cursor:pointer'>";
    cont += "</fieldset>";
    
    cont += "<fieldset><legend><b>Result</b></legend><div id='sResult' style='height:315px;overflow:auto'>";
    cont += "</div></fieldset>";
    showDialog2('Search Inventory',cont,'500','400',event);
}

/* Function getInvName
 * Fungsi untuk pop up window, untuk memilih barang
 * I : id target
 * O : Pop up window untuk pencarian barang
 */
function getInvName(event,id,targetSatuan,targetHarga) {
    var cont = "<fieldset><legend><b>Search</b></legend>";
    cont += "<input id='invSearch' type='text' onkeypress=\"if(getKey(event)==13){searchInvName('invSearch','"+id+"','"+targetSatuan+"','"+targetHarga+"');} else {return tanpa_kutip(event)}\" />";
    cont += "<img src='images/search.png' onclick=\"searchInvName('invSearch','"+id+"','"+targetSatuan+"','"+targetHarga+"')\" style='cursor:pointer'>";
    cont += "</fieldset>";
    
    cont += "<fieldset><legend><b>Result</b></legend><div id='sResult' style='height:315px;overflow:auto'>";
    cont += "</div></fieldset>";
    showDialog2('Search Inventory',cont,'500','400',event);
}

/* Function getSearch
 * Fungsi untuk pop up window, untuk memilih pencarian sesuai mode
 * I : id target
 * O : Pop up window untuk pencarian barang
 */
function getSearch(event,id,mode) {
    var cont = "<fieldset><legend><b>Search</b></legend>";
    cont += "<input id='invSearch' type='text' onkeypress=\"if(getKey(event)==13){searchSpec('invSearch','"+id+"','"+mode+"');} else {return tanpa_kutip(event)}\" />";
    cont += "<img src='images/search.png' onclick=\"searchSpec('invSearch','"+id+"','"+mode+"')\" style='cursor:pointer'>";
    cont += "</fieldset>";
    
    cont += "<fieldset><legend><b>Result</b></legend><div id='sResult' style='height:315px;overflow:auto'>";
    cont += "</div></fieldset>";
    showDialog2('Search',cont,'500','400',event);
}

/* Function searchInv
 * Fungsi untuk mencari barang
 * I : id search text, id target
 * O : Tampilkan hasil pencarian
 */
function searchInv(id,targetId) {
    var sText = document.getElementById(id);
    
    if(sText.value=='' || sText.value.length<3) {
        alert('Kata yang dicari minimal 3 karakter');
        exit;
    }
    
    var param = "keyword="+sText.value;
    param += "&target="+targetId;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    var res = document.getElementById('sResult');
                    res.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('slave_search_barang.php', param, respon);
}

/* Function searchInvName
 * Fungsi untuk mencari barang
 * I : id search text, id target
 * O : Tampilkan hasil pencarian
 */
function searchInvName(id,targetId,targetSatuan,targetHarga) {
    var sText = document.getElementById(id);
    
    if(sText.value=='' || sText.value.length<3) {
        alert('Kata yang dicari minimal 3 karakter');
        exit;
    }
    
    var param = "keyword="+sText.value;
    param += "&target="+targetId;
    param += "&targetSatuan="+targetSatuan;
    param += "&targetHarga="+targetHarga;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    var res = document.getElementById('sResult');
                    res.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('slave_search.php?mode=barang', param, respon);
}

/* Function searchSpec
 * Fungsi untuk mencari barang
 * I : id search text, id target
 * O : Tampilkan hasil pencarian
 */
function searchSpec(id,targetId,mode) {
    var sText = document.getElementById(id);
    
    if(sText.value=='' || sText.value.length<3) {
        alert('Kata yang dicari minimal 3 karakter');
        exit;
    }
    
    var param = "keyword="+sText.value;
    param += "&target="+targetId;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    var res = document.getElementById('sResult');
                    res.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('slave_search.php?mode='+mode, param, respon);
}

/* Function passValue
 * Fungsi untuk mengirim nilai ke element tertentu
 * I : nilai, id target
 * O : nilai terupdate
 */
function passValue(val,target) {
    var tTarget = document.getElementById(target);
    if(tTarget)
        tTarget.value = val;
    closeDialog2();
}

function getCleared(id,nmbrg)
{
    var kdbrg = document.getElementById(id);
    var nmbrg2 = document.getElementById(nmbrg);
    if (kdbrg.value == '' || kdbrg.value.length < 1) {
        alert('Kolom Kode Barang Masih Kosong.');
        //exit;
    }
    else {
        document.getElementById(id).value = '';
        document.getElementById(id).text = '';
        document.getElementById(nmbrg).value = '';
        document.getElementById(nmbrg).text = '';
    }
}