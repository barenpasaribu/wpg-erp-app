<?php

    require_once 'master_validation.php';
    include 'lib/eagrolib.php';

    echo open_body();
    include 'master_mainMenu.php';
    OPEN_BOX('', 'Produksi Harian');
    $str = 'select kodeorganisasi,namaorganisasi 
            from '.$dbname.".organisasi 
            where 
            kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' 
            and 
            tipe='PABRIK'";
    $res = mysql_query($str);

    $optorg = '';
    while ($bar = mysql_fetch_object($res)) {
        $optorg .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
    }
    ?>
        <input type="hidden" id="CPOPengirimanKemarin" value="0">
        <input type="hidden" id="CPOSoundingHariIni" value="0">

        <input type="hidden" id="KERNELPengirimanKemarin" value="0">
        <input type="hidden" id="KERNELSoundingHariIni" value="0">

        <input type="hidden" id="pengirimanHariIni" value="0">

        <input type="hidden" id="nogenset1" value="0">
        <input type="hidden" id="nogenset2" value="0">
        <input type="hidden" id="nogenset3" value="0">

        <input type="hidden" id="noloader1" value="0">
        <input type="hidden" id="noloader2" value="0">
        <input type="hidden" id="noloader3" value="0">
    <?php
    echo "  <fieldset>
                <legend>".$_SESSION['lang']['form']."</legend>
                    <table>
                        <tr>
                            <td valign=top>
                                <table>
                                    <tr>
                                        <td>
                                            ".$_SESSION['lang']['kodeorganisasi']."
                                        </td>
                                        <td colspan=2>
                                            <select id=kodeorg style='width:100%;'>".$optorg."</select>
                                        </td> 
                                    </tr> 
                                    <tr>  
                                        <td>".$_SESSION['lang']['tanggal']."</td> 
                                        <td colspan=2>
                                            <input autocomplete=off type=text class=myinputtext id=tanggal onmousemove=setCalendar(this.id) onchange=getDataPengolahan() maxlength=10 onkeypress=\"return false;\"> 
                                        </td>
                                    </tr> 
                                    <tr>  
                                        <td></td> 
                                        <td colspan=2>
                                            <button onclick='lihatDataTarikan()'>Lihat Data</button>
                                        </td>
                                    </tr> 
                                    <tr>
                                        <td>Informasi Cuaca </td>   
                                       
                                        <td colspan=2>
                                            <select id=informasi_cuaca style='width:100%;'>
                                                <option value='CERAH'>CERAH</option>
                                                <option value='HUJAN'>HUJAN </option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>   
                                        <td>
                                            <h3>TBS</h3>
                                        </td>
                                        <td> </td>
                                    </tr>
                                    <tr>   
                                        <td>
                                            TBS Sisa Kemarin
                                        </td>   
                                        <td>    
                                            <input type=text onchange=\"calcSisa();\" title=GenerateData_Press_Anykey value=0 id=tbs_sisa_kemarin class=myinputtextnumber maxlength=10 size=10 onblur=\"return angka_doang(event);\">
                                        </td>
                                        <td>Kg.</td> 
                                    </tr> 
                                    <tr>    
                                        <td>    
                                            ".$_SESSION['lang']['tbsmasuk']." (Bruto)
                                        </td> 
                                        <td>
                                            <input type=text id=tbs_masuk_bruto onchange=\"calcSisa();\" title=GenerateData_Press_Anykey value=0 class=myinputtextnumber maxlength=10 size=10 onkeypress=\"return angka_doang(event);\">
                                        </td>
                                        <td>Kg.</td> 
                                    </tr>
                                    <tr>    
                                        <td>    
                                            TBS (Potongan)
                                        </td> 
                                        <td>
                                            <input type=text id=tbs_potongan onchange=\"calcSisa();\" title=GenerateData_Press_Anykey value=0 class=myinputtextnumber maxlength=10 size=10 onkeypress=\"return angka_doang(event);\">
                                        </td>
                                        <td>Kg.</td> 
                                    </tr>
                                    <tr>    
                                        <td>    
                                            ".$_SESSION['lang']['tbsmasuk']." (Netto)
                                        </td> 
                                        <td>
                                            <input type=text id=tbs_masuk_netto onchange=\"calcSisa();\" title=GenerateData_Press_Anykey value=0 class=myinputtextnumber maxlength=10 size=10 onkeypress=\"return angka_doang(event);\">
                                        </td>
                                        <td>Kg.</td> 
                                    </tr>
                                    
                                    <tr>
                                        <td>
                                            TBS diolah
                                        </td>   
                                        <td>   
                                            <input type=text id=tbs_diolah value=0 onchange=\"calcSisa();\" class=myinputtextnumber maxlength=10 size=10 onkeypress=\"return angka_doang(event);\">
                                        </td> 
                                        <td>Kg.</td> 
                                    </tr>
                                    <tr>
                                        <td>
                                            TBS diolah After Grading
                                        </td>   
                                        <td>   
                                            <input type=text id=tbs_after_grading value=0 onchange=\"calcSisa();\" class=myinputtextnumber maxlength=10 size=10 onkeypress=\"return angka_doang(event);\">
                                        </td> 
                                        <td>Kg.</td> 
                                    </tr>
                                    <tr>
                                        <td>
                                            TBS (".$_SESSION['lang']['sisa'].")
                                        </td>
                                        <td>
                                            <input type=text id=tbs_sisa title=PressAnyKey value=0 class=myinputtextnumber  maxlength=10 size=10 readonly>
                                        </td>
                                        <td>Kg.</td> 
                                    </tr>
                                    <tr>   
                                        <td>
                                            <h3>Lori</h3>
                                        </td>
                                        <td> </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Lori olah
                                        </td>
                                        <td>
                                            <input type=text id=lori_olah disabled title=PressAnyKey value=0 class=myinputtextnumber  maxlength=10 size=10 readonly>
                                        </td>
                                        <td> Unit.</td> 
                                    </tr>
                                    <tr>
                                        <td>
                                            Lori Dalam Rebusan
                                        </td>
                                        <td>
                                            <input type=text id=lori_dalam_rebusan disabled title=PressAnyKey value=0 class=myinputtextnumber  maxlength=10 size=10 readonly>
                                        </td>
                                        <td> Unit.</td> 
                                    </tr>
                                    <tr>
                                        <td>
                                            Restan Depan Rebusan
                                        </td>
                                        <td>
                                            <input type=text id=restan_depan_rebusan disabled title=PressAnyKey value=0 class=myinputtextnumber  maxlength=10 size=10 readonly>
                                        </td>
                                        <td> Unit.</td> 
                                    </tr>
                                    <tr>
                                        <td>
                                            Restan dibelakang Rebusan
                                        </td>
                                        <td>
                                            <input type=text id=restan_dibelakang_rebusan disabled title=PressAnyKey value=0 class=myinputtextnumber  maxlength=10 size=10 readonly>
                                        </td>
                                        <td> Unit.</td> 
                                    </tr>
                                    <tr>
                                        <td>
                                            Estimasi di Peron
                                        </td>
                                        <td>
                                            <input type=text id=estimasi_di_peron disabled title=PressAnyKey value=0 class=myinputtextnumber  maxlength=10 size=10 readonly>
                                        </td>
                                        <td> Unit.</td> 
                                    </tr>
                                    <tr>
                                        <td>
                                            Total Lori
                                        </td>
                                        <td>
                                            <input type=text id=total_lori disabled title=PressAnyKey value=0 class=myinputtextnumber  maxlength=10 size=10 readonly>
                                        </td>
                                        <td> Unit.</td> 
                                    </tr>
                                    <tr>
                                        <td>
                                            Kapasitas Lori Rata-rata
                                        </td>
                                        <td>
                                            <input type=text id=lori_rata_rata disabled title=PressAnyKey value=0 class=myinputtextnumber  maxlength=10 size=10 readonly>
                                        </td>
                                        <td> Kg.</td> 
                                    </tr>
                                ";
    echo "                          <tr>   
                                        <td>
                                            <h3>Rendemen</h3>
                                        </td>
                                        <td> </td>
                                    </tr>
                                    
                                    
                                    
                                    
                                    
                                    <tr>
                                        <td>Rendemen CPO Before Grading </td>   
                                        <td>
                                            <input type=text id=rendemen_cpo_before value=0 class=myinputtextnumber  maxlength=10 size=10 > 
                                        </td> 
                                        <td> %</td> 
                                    </tr>
                                    <tr>
                                        <td>Rendemen CPO After Grading </td>   
                                        <td>
                                            <input type=text id=rendemen_cpo_after value=0 class=myinputtextnumber  maxlength=10 size=10 >
                                        </td> 
                                        <td> %</td> 
                                    </tr>
                                    <tr>
                                        <td>Rendemen PK Before</td>   
                                        <td>
                                            <input type=text id=rendemen_pk_before value=0 class=myinputtextnumber  maxlength=10 size=10 >
                                        </td> 
                                        <td> %</td> 
                                    </tr>
                                    <tr>
                                        <td>Rendemen PK After</td>   
                                        <td>
                                            <input type=text id=rendemen_pk_after value=0 class=myinputtextnumber  maxlength=10 size=10 >
                                        </td> 
                                        <td> %</td> 
                                    </tr>
                                ";
    /*
    <tr>
                                        <td>
                                            <fieldset>
                                                <legend>Oil Loses to FFB</legend>
                                                <table>
                                                    <tr>  
                                                        <td>".$_SESSION['lang']['tanggal']."</td> 
                                                        <td colspan=2>
                                                            <input autocomplete=off type=text onchange='getCPOLosesByTanggal()' class=myinputtext id=cpo_loses_tanggal onmousemove=setCalendar(this.id) maxlength=10 onkeypress=\"return false;\"> 
                                                        </td>
                                                    </tr> 
                                                    <tr>
                                                        <td>USB </td> 
                                                        <td>    
                                                            <input type=text id=cpo_usb value=0 disabled class=myinputtextnumber maxlength=7  size=10 onkeypress=\"return angka_doang(event);\">
                                                        </td>
                                                        <td>%.</td> 
                                                    </tr>
                                                    <tr>
                                                        <td>Empty Bunch  </td>
                                                        <td>    
                                                            <input type=text id=cpo_empty_bunch value=0 disabled class=myinputtextnumber maxlength=7 size=10 onkeypress=\"return angka_doang(event);\">
                                                        </td>
                                                        <td>%.</td> 
                                                    </tr>
                                                    <tr>   
                                                        <td> Fibre Cyclone </td> 
                                                        <td>    
                                                            <input type=text id=cpo_fibre_cyclone value=0 disabled class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\">
                                                        </td>
                                                        <td>%.</td> 
                                                    </tr>
                                                    <tr>
                                                        <td>Nut from Polishingdrum</td>   
                                                        <td>    
                                                            <input type=text id=cpo_nut_from_polishingdrum value=0 disabled class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\">
                                                        </td>
                                                        <td>%.</td> 
                                                    </tr>
                                                    <tr>   
                                                        <td>Effluent </td>   
                                                        <td>    
                                                            <input type=text id=cpo_effluent value=0 disabled class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\"> 
                                                        </td>
                                                        <td>%.</td> 
                                                    </tr>
                                                </table>
                                            </fieldset>
                                        </td>
                                    </tr>

    */
    
    /*
    <tr>
                                        <td> 
                                            <fieldset>
                                                <legend>".$_SESSION['lang']['kernel']." Loses to FFB</legend>
                                                <table>
                                                    <tr>  
                                                        <td>".$_SESSION['lang']['tanggal']."</td> 
                                                        <td colspan=2>
                                                            <input autocomplete=off onchange='getPKLosesByTanggal()' type=text class=myinputtext id=kernel_loses_tanggal onmousemove=setCalendar(this.id) maxlength=10 onkeypress=\"return false;\"> 
                                                        </td>
                                                    </tr> 
                                                    <tr>
                                                        <td>
                                                            USB
                                                        </td> 
                                                        <td>    
                                                            <input type=text id=kernel_loses_usb disabled value=0 class=myinputtextnumber maxlength=7 size=10 onkeypress=\"return angka_doang(event);\">
                                                        </td>
                                                        <td>%.</td> 
                                                    </tr>
                                                    <tr>   
                                                        <td>Fibre Cyclone </td>   
                                                        <td>    
                                                            <input type=text id=kernel_loses_fibre_cyclone disabled value=0 class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\">
                                                        </td>
                                                        <td>%.</td> 
                                                    </tr>
                                                    <tr>   
                                                        <td>LTDS 1 </td> 
                                                        <td>    
                                                            <input type=text id=kernel_loses_ltds_1 disabled value=0 class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\">
                                                        </td>
                                                        <td>%.</td> 
                                                    </tr>
                                                    <tr>   
                                                        <td>LTDS 2 </td> 
                                                        <td>    
                                                            <input type=text id=kernel_loses_ltds_2 disabled value=0 class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\">
                                                        </td>
                                                        <td>%.</td> 
                                                    </tr>
                                                    <tr>   
                                                        <td>Claybath </td>   
                                                        <td>    
                                                            <input type=text id=kernel_loses_claybath disabled value=0  class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\">
                                                        </td>
                                                        <td>%.</td> 
                                                    </tr>
                                                </table>
                                            </fieldset>
                                        </td>
                                    </tr>

    */
    echo "                      </table>
                            </td>
                            <td valign=top>  
                                <table>
                                    <tr>
                                        <td>
                                            <fieldset>
                                            <legend>
                                                ".$_SESSION['lang']['cpo']."
                                            </legend>
                                            <table>
                                                <tr>
                                                    <td>
                                                        Opening Stock (CPO)
                                                    </td>
                                                    <td>
                                                        <input type=text id=cpo_opening_stock onchange=\"calcCPOKERNELKG();\" value=0 class=myinputtextnumber maxlength=7 size=10 onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>Kg.</td> 
                                                </tr>
                                                <tr>
                                                    <td>
                                                        Produksi ".$_SESSION['lang']['cpo']." (Kg) 
                                                    </td>
                                                    <td>
                                                        <input type=text id=cpo_produksi  value=0 class=myinputtextnumber maxlength=7 size=10 onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>Kg.</td> 
                                                </tr>
                                                
                                                <tr>
                                                    <td>
                                                        Closing Stock (CPO)
                                                    </td>
                                                    <td>
                                                        <input type=text id=cpo_closing_stock onchange=\"calcCPOKERNELKG();\" value=0 class=myinputtextnumber maxlength=7 size=10 onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>Kg.</td> 
                                                </tr>
                                                <tr>
                                                    <td>
                                                        ".$_SESSION['lang']['kotoran']." 
                                                    </td>
                                                    <td>
                                                        <input type=text id=cpo_kotoran value=0 onblur=periksaCPO(this)   class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>%.</td> 
                                                </tr>
                                                <tr>
                                                    <td>
                                                        ".$_SESSION['lang']['kadarair']." 
                                                    </td>
                                                    <td>
                                                        <input type=text id=cpo_kadar_air value=0 onblur=periksaCPO(this)   class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>%.</td> 
                                                </tr>
                                                <tr>
                                                    <td>    
                                                        FFa 
                                                    </td>
                                                    <td>    
                                                        <input type=text id=cpo_ffa value=0 onblur=periksaCPO(this)   class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>%.</td> 
                                                </tr>
                                                <tr>
                                                    <td>    Dobi </td>   
                                                    <td>    
                                                        <input type=text id=cpo_dobi value=0   class=myinputtextnumber maxlength=5 size=10 onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>%.</td> 
                                                </tr>
                                            </table>
                                            </fieldset>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td>
                                            <fieldset>
                                                <legend>Pengiriman</legend>
                                                <table>
                                                    <tr>
                                                        <td>Despatch (CPO)</td> 
                                                        <td>    
                                                            <input type=text id=pengiriman_despatch_cpo onchange='hitungProduksiCPO()' value=0 class=myinputtextnumber size=10 onkeypress=\"return angka_doang(event);\">
                                                        </td>
                                                        <td>Kg.</td> 
                                                    </tr>
                                                    <tr>
                                                        <td>Return CPO</td> 
                                                        <td>
                                                            <input type=text id=pengiriman_return_cpo onchange='hitungProduksiCPO()' value=0 class=myinputtextnumber size=10 onkeypress=\"return angka_doang(event);\">
                                                        </td>
                                                        <td>Kg.</td> 
                                                    </tr>
                                                    <tr>
                                                        <td>Despatch (PK)</td> 
                                                        <td>
                                                            <input type=text id=pengiriman_despatch_pk onchange='hitungProduksiPK()' value=0 class=myinputtextnumber size=10 onkeypress=\"return angka_doang(event);\">
                                                        </td>
                                                        <td>Kg.</td> 
                                                    </tr>
                                                    <tr>
                                                        <td>Return PK</td> 
                                                        <td>
                                                            <input type=text id=pengiriman_return_pk onchange='hitungProduksiPK()' value=0 class=myinputtextnumber size=10 onkeypress=\"return angka_doang(event);\">
                                                        </td>
                                                        <td>Kg.</td> 
                                                    </tr>
                                                    <tr>
                                                        <td>Janjang Kosong (EFB)</td> 
                                                        <td>
                                                            <input type=text id=pengiriman_janjang_kosong value=0 class=myinputtextnumber size=10 onkeypress=\"return angka_doang(event);\">
                                                        </td>
                                                        <td>Kg.</td> 
                                                    </tr>
                                                    <tr>
                                                        <td>Limbah Cair (POME)</td> 
                                                        <td>
                                                            <input type=text id=pengiriman_limbah_kosong value=0 class=myinputtextnumber size=10 onkeypress=\"return angka_doang(event);\">
                                                        </td>
                                                        <td>Kg.</td> 
                                                    </tr>
                                                    <tr>
                                                        <td>Solid Decnter</td> 
                                                        <td>
                                                            <input type=text id=pengiriman_solid_decnter value=0 class=myinputtextnumber size=10 onkeypress=\"return angka_doang(event);\">
                                                        </td>
                                                        <td>Kg.</td> 
                                                    </tr>
                                                    <tr>
                                                        <td>Abu Janjang (Bunch Ash)</td> 
                                                        <td>
                                                            <input type=text id=pengiriman_abu_janjang value=0 class=myinputtextnumber size=10 onkeypress=\"return angka_doang(event);\">
                                                        </td>
                                                        <td>Kg.</td> 
                                                    </tr>
                                                    <tr>
                                                        <td>Cangkang ( Shell)</td> 
                                                        <td>
                                                            <input type=text id=pengiriman_cangkang value=0 class=myinputtextnumber size=10 onkeypress=\"return angka_doang(event);\">
                                                        </td>
                                                        <td>Kg.</td> 
                                                    </tr>
                                                    <tr>
                                                        <td>Fibre</td> 
                                                        <td>
                                                            <input type=text id=pengiriman_fibre value=0 class=myinputtextnumber size=10 onkeypress=\"return angka_doang(event);\">
                                                        </td>
                                                        <td>Kg.</td> 
                                                    </tr>

                                                </table>
                                            </fieldset>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td valign=top>
                                <table>
                                    <tr>
                                        <td>
                                            <fieldset>
                                                <legend>
                                                    ".$_SESSION['lang']['kernel']."
                                                </legend>
                                                <table>
                                                    
                                                    <tr>
                                                        <td>
                                                            Opening Stok (PK)
                                                        </td>
                                                        <td>
                                                            <input type=text id=kernel_opening_stock onchange=\"calcCPOKERNELKG();\" value=0 class=myinputtextnumber size=10 onkeypress=\"return angka_doang(event);\">
                                                        </td>
                                                        <td>Kg.</td> 
                                                    </tr>
                                                    <tr>
                                                        <td>    
                                                            Produksi ".$_SESSION['lang']['kernel']." (Kg) 
                                                        </td> 
                                                        <td>
                                                            <input type=text id=kernel_produksi  value=0 onblur=periksaOERPK(this)  class=myinputtextnumber size=10 onkeypress=\"return angka_doang(event);\">
                                                        </td>
                                                        <td>Kg.</td> 
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            Closing Stock (PK)
                                                        </td>
                                                        <td>
                                                            <input type=text id=kernel_closing_stock onchange=\"calcCPOKERNELKG();\" value=0 class=myinputtextnumber size=10 onkeypress=\"return angka_doang(event);\">
                                                        </td>
                                                        <td>Kg.</td> 
                                                    </tr>
                                                    <tr>   
                                                        <td>    
                                                            ".$_SESSION['lang']['kotoran']." 
                                                        </td>   
                                                        <td>    
                                                            <input type=text id=kernel_kotoran value=0 onblur=periksaPK(this) class=myinputtextnumber size=10 onkeypress=\"return angka_doang(event);\">
                                                        </td>
                                                        <td>%.</td> 
                                                    </tr>
                                                    <tr>   
                                                        <td>    
                                                            ".$_SESSION['lang']['kadarair']." 
                                                        </td> 
                                                        <td>    
                                                            <input type=text id=kernel_kadar_air value=0 onblur=periksaPK(this)  class=myinputtextnumber size=10 onkeypress=\"return angka_doang(event);\">
                                                        </td>
                                                        <td>%.</td> 
                                                    </tr>
                                                    <tr>
                                                        <td> Inti Pecah </td>   
                                                        <td>    
                                                            <input type=text id=kernel_inti_pecah value=0  class=myinputtextnumber size=10 onkeypress=\"return angka_doang(event);\">
                                                        </td>
                                                        <td>%.</td> 
                                                    </tr>
                                                </table>
                                            </fieldset>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td> 
                                            <fieldset>
                                                <legend>Utilisasi Pabrik</legend>
                                                <table>
                                                    <tr>
                                                        <td>
                                                            Jumlah hari olah
                                                        </td> 
                                                        <td>    
                                                            <input type=text id=jumlah_hari_olah  value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                        </td>
                                                        <td>Hari.</td> 
                                                    </tr>
                                                    
                                                    <tr>
                                                        <td>
                                                            Kapasitas olah
                                                        </td> 
                                                        <td>    
                                                            <input type=text id=kapasitas_olah  value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                        </td>
                                                        <td>Kg/Jam.</td> 
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            Utilitas Kapasitas
                                                        </td> 
                                                        <td>    
                                                            <input type=text id=utilitas_kapasitas  value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                        </td>
                                                        <td>%.</td> 
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            Utilitas Factor Commercial
                                                        </td> 
                                                        <td>    
                                                            <input type=text id=utility_factor_commercial  value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\"> 
                                                        </td>
                                                        <td></td> 
                                                    </tr>                                                    
                                                </table>
                                            </fieldset>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td  valign=top>
                                <table>
                                    
                                    <tr>
                                        <td>
                                        <fieldset>
                                            <legend>Pemakaian Kalsium</legend>
                                            <table>
                                                <tr>
                                                    <td>CaCO3 </td>   
                                                    <td>
                                                        <input type=text id=caco3 onchange=\"calcKalsium();\" value=0 class=myinputtextnumber>
                                                    </td> 
                                                    <td>Kg.</td> 
                                                </tr>
                                                <tr>
                                                    <td>Rasio Kalsium terhadap TBS</td> 
                                                    <td>    
                                                        <input type=text disabled id=rasio_kalsium_tbs value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td></td> 
                                                </tr>
                                                
                                                <tr>
                                                    <td>Rasio Kalsium terhadap PK</td> 
                                                    <td>    
                                                        <input type=text disabled id=rasio_kalsium_pk value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td></td> 
                                                </tr>
                                                
                                            </table>
                                        </fieldset>
                                
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                        <fieldset>
                                            <legend>Press</legend>
                                            <table>
                                                <tr>
                                                    <td>Total Jam Press</td> 
                                                    <td>    
                                                        <input disabled type=text id=total_jam_press value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>Jam.</td> 
                                                </tr>
                                                <tr>
                                                    <td>Total Jam Operasi</td> 
                                                    <td>    
                                                        <input disabled type=text id=total_jam_operasi value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>Jam.</td> 
                                                </tr>
                                                <tr>
                                                    <td>Kapasitas Press</td> 
                                                    <td>    
                                                        <input type=text id=kapasitas_press disabled value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>Kg/Jam.</td> 
                                                </tr>                                                
                                            </table>
                                        </fieldset>
                                
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                        <fieldset>
                                            <legend>Stock by Products</legend>
                                            <table>
                                                <tr>
                                                    <td>Janjang Kosong</td> 
                                                    <td>    
                                                        <input type=text id=stock_product_janjang_kosong value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>Kg.</td> 
                                                </tr>
                                                
                                                <tr>
                                                    <td>Limbah Cair (POME)</td> 
                                                    <td>    
                                                        <input type=text id=stock_product_limbar_cair value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>Kg.</td> 
                                                </tr>                                                
                                                <tr>
                                                    <td>Cangkang (Shell)</td> 
                                                    <td>    
                                                        <input type=text id=stock_product_cangkang value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>Kg.</td> 
                                                </tr>                                                
                                                <tr>
                                                    <td>Fibre</td> 
                                                    <td>    
                                                        <input type=text id=stock_product_fibre value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>Kg.</td> 
                                                </tr>                                                
                                                <tr>
                                                    <td>Abu Incenerator</td> 
                                                    <td>    
                                                        <input type=text id=stock_product_abu_incenerator value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>Kg.</td> 
                                                </tr>                                                
                                            </table>
                                        </fieldset>
                                
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                        <fieldset>
                                            <legend>Solar</legend>
                                            <table>
                                                <tr>
                                                    <td>Solar Genset 1</td> 
                                                    <td>    
                                                        <input onchange=hitungTotalSolarGenset() disabled type=text id=solar_genset_1 size=10 value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\"> 
                                                    </td>
                                                    <td>Liter.</td> 
                                                </tr>
                                                
                                                <tr>
                                                    <td>Solar Genset 2</td> 
                                                    <td>    
                                                        <input type=text onchange=hitungTotalSolarGenset() disabled id=solar_genset_2 size=10 value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\"> 
                                                    </td>
                                                    <td>Liter.</td> 
                                                </tr>                                                
                                                <tr>
                                                    <td>Solar Genset 3</td> 
                                                    <td>    
                                                        <input type=text onchange=hitungTotalSolarGenset() disabled id=solar_genset_3 size=10 value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>Liter.</td> 
                                                </tr>                                                
                                                <tr>
                                                    <td>Total Solar Genset</td> 
                                                    <td>    
                                                        <input type=text disabled id=total_solar_genset size=10 value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>Liter.</td> 
                                                </tr>                                                
                                                
                                                <tr>
                                                    <td>HM Genset 1</td> 
                                                    <td>    
                                                        <input type=text disabled onchange=hitungTotalHMGenset() id=hm_genset_1 size=10 value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>HM.</td> 
                                                </tr>                                                
                                                <tr>
                                                    <td>HM Genset 2</td> 
                                                    <td>    
                                                        <input type=text disabled onchange=hitungTotalHMGenset() id=hm_genset_2 size=10 value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>HM.</td> 
                                                </tr>                                                
                                                <tr>
                                                    <td>HM Genset 3</td> 
                                                    <td>    
                                                        <input type=text disabled onchange=hitungTotalHMGenset() id=hm_genset_3 size=10 value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>HM.</td> 
                                                </tr>                                                  
                                                <tr>
                                                    <td>Total HM Genset</td> 
                                                    <td>    
                                                        <input type=text disabled id=total_hm_genset size=10 value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>HM.</td> 
                                                </tr>
                                                <tr>
                                                    <td>Solar Loader 1</td> 
                                                    <td>    
                                                        <input type=text disabled onchange=hitungTotalSolarLoader() id=solar_loader_1 size=10 value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>Liter.</td> 
                                                </tr>                                                
                                                <tr>
                                                    <td>Solar Loader 2</td> 
                                                    <td>    
                                                        <input type=text disabled onchange=hitungTotalSolarLoader() id=solar_loader_2 size=10 value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>Liter.</td> 
                                                </tr>                                                
                                                <tr>
                                                    <td>Solar Loader 3</td> 
                                                    <td>    
                                                        <input type=text disabled onchange=hitungTotalSolarLoader() id=solar_loader_3 size=10 value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>Liter.</td> 
                                                </tr>                                                
                                                <tr>
                                                    <td>Loader Rental</td> 
                                                    <td>    
                                                        <input type=text onchange=hitungTotalSolarLoader() id=solar_loader_rental size=10 value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>Liter.</td> 
                                                </tr>                                                
                                                <tr>
                                                    <td>Total Solar Loader</td> 
                                                    <td>    
                                                        <input type=text disabled id=total_solar_loader size=10 value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>Liter.</td> 
                                                </tr> 
                                                <tr>
                                                    <td>HM Loader 1</td> 
                                                    <td>    
                                                        <input type=text disabled onchange=hitungTotalHMLoader() id=hm_loader_1  size=10 value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>HM.</td> 
                                                </tr>                                                
                                                <tr>
                                                    <td>HM Loader 2</td> 
                                                    <td>    
                                                        <input type=text disabled onchange=hitungTotalHMLoader() id=hm_loader_2 size=10 value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>HM.</td> 
                                                </tr>                                                
                                                <tr>
                                                    <td>HM Loader 3</td> 
                                                    <td>    
                                                        <input type=text disabled onchange=hitungTotalHMLoader() id=hm_loader_3 size=10 value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>HM.</td> 
                                                </tr>                                                  
                                                <tr>
                                                    <td>HM Loader Rental</td> 
                                                    <td>    
                                                        <input type=text onchange=hitungTotalHMLoader() id=hm_loader_rental size=10 value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>HM.</td> 
                                                </tr>                                                  
                                                <tr>
                                                    <td>Total HM Loader</td> 
                                                    <td>    
                                                        <input type=text disabled id=total_hm_loader size=10 value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>HM.</td> 
                                                </tr>
                                                <tr>
                                                    <td>Rasio Total Solar Genset to HM Total Genset</td> 
                                                    <td>    
                                                        <input type=text disabled id=rasio_total_solar_genset_hm_total_genset size=10 value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>Liter/HM.</td> 
                                                </tr>
                                                <tr>
                                                    <td>Rasio Total Solar Loader to HM Total Loader</td> 
                                                    <td>    
                                                        <input type=text disabled id=rasio_total_solar_loader_hm_total_loader size=10 value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\">
                                                    </td>
                                                    <td>Liter/HM.</td> 
                                                </tr>
                                            </table>
                                        </fieldset>
                                
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>";
    ?>
                    <table style="width:100%">
                        <tr>
                            <td>
                                Catatan 1
                            </td>
                            <td>
                                Catatan 2
                            </td>
                            <td>
                                Catatan 3
                            </td>
                            <td>
                                Catatan 4
                            </td>
                        </tr>
                        <tr>
                            <td>    
                                <textarea id="catatan1" rows="4" cols="30%"></textarea>
                            </td>
                            <td>    
                                <textarea id="catatan2" rows="4" cols="30%"></textarea>
                            </td>
                            <td>    
                                <textarea id="catatan3" rows="4" cols="30%"></textarea>
                            </td>
                            <td>    
                                <textarea id="catatan4" rows="4" cols="30%"></textarea>
                            </td>
                        </tr>
                    </table>
                    <br>
    <?php

    echo "          <center>
                        <button class=mybutton onclick=simpanProduksi()>".$_SESSION['lang']['save']."</button>
                    </center>
                    </fieldset>
                    <input type=hidden id=statSounding value=0 />
                    ";
    echo '              <fieldset>
                            <legend>'.$_SESSION['lang']['list']."</legend>
                            <div id='table_pabrik_produksi'></div>
                            <i>*Max 1.000 Data</i>
                        </fieldset>";
    CLOSE_BOX();
?>
    <script type="text/javascript" src="/anthesis-erp/lib/awan/jQuery-3.3.1/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="/anthesis-erp/lib/awan/DataTables-1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="/anthesis-erp/lib/awan/sweetalert2/dist/sweetalert2.min.js"></script>
	<!-- <script type="text/javascript" src="js/zTools.js"></script> -->
	<script type="text/javascript" src="js/pabrik_produksi.js"></script>
	<script type="text/javascript">
		$( document ).ready(function() {
			$('head').append($('<link rel="stylesheet" type="text/css" />').attr('href', '/anthesis-erp//lib/awan/DataTables-1.10.21/css/jquery.dataTables.min.css'));
			$('head').append($('<link rel="stylesheet" type="text/css" />').attr('href', '/anthesis-erp//lib/awan/DataTables-1.10.21/css/dataTables.bootstrap4.min.css'));
			$('head').append($('<link rel="stylesheet" type="text/css" />').attr('href', '/anthesis-erp//lib/awan/DataTables-1.10.21/css/dataTables.jqueryui.min.css'));
			$('head').append($('<link rel="stylesheet" type="text/css" />').attr('href', '/anthesis-erp//lib/awan/sweetalert2/dist/sweetalert2.min.css'));
            loadData();
            // $('#dataKelengkapanLoses').DataTable();
		});
	</script>
	
	</body>
</html>