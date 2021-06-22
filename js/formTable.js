/* Class formTable
 * Kelas standard untuk manajemen table dalam bentuk form dan table

 *
 *  */

function formTable() {

    var ftMain = this;
	
	this.id = null;
    this.els = null;
    this.addEls = null;
    this.afterCrud = null;
	
    /* Initialisasi */
    this.prep = function(id,els,addEls) {
        this.id = id;
        this.els = els;
        this.addEls = addEls;
    }
    
    /* getParam
     * Fungsi untuk mendapatkan parameter yang dibutuhkan dari form
     */
    this.getParam = function(id,els,addEls) {
        var tmpEls = els.split('##');
        var tmpAddEls = addEls.split('##');
        var param = "";
        for(i=1;i<tmpEls.length;i++) {
            var tmp = document.getElementById(id+"_"+tmpEls[i]);
            var tmpSel = tmp.getElementsByTagName('select');
			if(i>1) {
				param += '&';
			}
			
			if(tmp.firstChild.options) {
				if(tmp.childNodes.length>1) {
					if(tmp.firstChild.options[0].value=='D') {
						if(tmp.firstChild.options[tmp.firstChild.selectedIndex].value=='K') {
							param += tmpEls[i]+"="+parseFloat(remove_comma(tmp.childNodes[1]))*(-1);
						} else {
							param += tmpEls[i]+"="+tmp.childNodes[1].value;
						}
					} else if(tmp.childNodes[1].tagName=='IMG'){
						if(tmp.firstChild.selectedIndex==-1) {
							param += tmpEls[i]+"=";
						} else {
							param += tmpEls[i]+"="+tmp.firstChild.options[tmp.firstChild.selectedIndex].value;
						}
					} else {
						param += tmpEls[i]+"="+tmp.childNodes[0].value+":"+tmp.childNodes[2].value+":00";
					}
				} else {
					if(tmp.firstChild.selectedIndex==-1) {
						param += tmpEls[i]+"=";
					} else {
						param += tmpEls[i]+"="+tmp.firstChild.options[tmp.firstChild.selectedIndex].value;
					}
				}
			} else {
				if(typeof z != 'undefined') {
					if(z.hasClass(tmp.firstChild,'myinputtextnumber')) {
                        if (tmp.firstChild.value=='') {
                            tmp.firstChild.value = 0;
                        }
						param += tmpEls[i]+"="+parseFloat(remove_comma_var(tmp.firstChild.value));
					} else {
						param += tmpEls[i]+"="+tmp.firstChild.value;
					}
				} else {
					var element = tmp.firstChild;
					if(element.className && new RegExp("(^|\\s)" + "myinputtextnumber" + "(\\s|$)").test(element.className)) {
                        if (tmp.firstChild.value=='') {
                            tmp.firstChild.value = 0;
                        }
						param += tmpEls[i]+"="+parseFloat(remove_comma_var(tmp.firstChild.value));
					} else {
						param += tmpEls[i]+"="+tmp.firstChild.value;
					}
				}
				// param += tmpEls[i]+"="+tmp.firstChild.value;
			}
        }
        for(i=1;i<tmpAddEls.length;i++) {
            if(document.getElementById(tmpAddEls[i]).getAttribute('type')=='text') {
                param += "&"+tmpAddEls[i]+"="+document.getElementById(tmpAddEls[i]).value;
            } else if(document.getElementById(tmpAddEls[i]).hasAttribute('value')) {
                param += "&"+tmpAddEls[i]+"="+document.getElementById(tmpAddEls[i]).getAttribute('value');
            } else {
                param += "&"+tmpAddEls[i]+"="+document.getElementById(tmpAddEls[i]).value;
            }
        }
        return param;
    }
    
    /* getParamRow
     * Fungsi untuk mendapatkan parameter yang dibutuhkan dari table
     */
    this.getParamRow = function(numRow,id,els,addEls) {
        var tmpEls = els.split('##');
        var tmpAddEls = addEls.split('##');
        var param = "";
        for(i=1;i<tmpEls.length;i++) {
            var tmp = document.getElementById(id+"_"+tmpEls[i]+"_"+numRow);
            if(i==1) {
                param += tmpEls[i]+"="+tmp.getAttribute('value');
            } else {
                param += "&"+tmpEls[i]+"="+tmp.getAttribute('value');
            }
        }
        for(i=1;i<tmpAddEls.length;i++) {
            if(document.getElementById(tmpAddEls[i]).getAttribute('type')=='text') {
                param += "&"+tmpAddEls[i]+"="+document.getElementById(tmpAddEls[i]).value;
            } else if(document.getElementById(tmpAddEls[i]).hasAttribute('value')) {
                param += "&"+tmpAddEls[i]+"="+document.getElementById(tmpAddEls[i]).getAttribute('value');
            } else {
                param += "&"+tmpAddEls[i]+"="+document.getElementById(tmpAddEls[i]).value;
            }
        }
        return param;
    }
    
    /* addFT
     * Fungsi untuk menambah data
     * id : Id dari formtable
     * els : element pada form
     * addEls : element tambahan yang diextract
     * target : target file
     */
    this.addFT = function(id,els,addEls,target,align,mode,noaction,noClear,noEnable,defValue,addAction,freeze,numFormat) {
        this.prep(id,els,addEls);
        
        // Find available numRow
        var numRow = 0;
        while(document.getElementById('tr_'+id+"_"+numRow)) {
            numRow++;
        }
        var alignArr = align.split('##');
        var param = this.getParam(id,els,addEls)+"&numRow="+numRow;
        var body = document.getElementById('tbody_'+id);
        
        if(addAction!='') {
            tmpAction = addAction.replace(/##/g,'"');
            eval('addAction = '+tmpAction);
        }
        
        function respon() {
            if (con.readyState == 4) {
                if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                        alert('ERROR TRANSACTION,\n' + con.responseText);
                    } else {
                        
                        // Success Response
                        eval("var tmpRes = "+con.responseText);
                        var res = tmpRes.res;
                        var theme = tmpRes.theme;
                        var tmpEls = els.split('##');
                        var tmpTd = res.split('##');
                        var tmpRes = "";
                        tmpRes += "<tr id='tr_"+id+"_"+numRow+"' class='rowcontent'>";
                        if(noaction==false) {
                            tmpRes += "<td><img id='editmodeFTBtn' class='zImgBtn' ";
                            tmpRes += "src='images/"+theme+"/edit.png' ";
                            tmpRes += "onclick=\"theFT.editmodeFT("+numRow+",'"+id+"','"+els+"','"+addEls+"','"+mode+"','"+freeze+"','"+numFormat+"')\"></td>";
                            tmpRes += "<td><img id='delFTBtn' class='zImgBtn' ";
                            tmpRes += "src='images/"+theme+"/delete.png' ";
                            tmpRes += "onclick=\"theFT.delFT("+numRow+",'"+id+"','"+els+"','"+addEls+"','"+target+"','"+mode+"','"+noClear+"','"+noEnable+"','"+defValue+"')\"></td>";
                            if(typeof(addAction)!='undefined') {
                                for(i in addAction) {
                                    tmpRes += "<td><img id='"+i+"' class='zImgBtn' ";
                                    tmpRes += "src='images/"+theme+"/"+addAction[i]['img']+"' ";
                                    tmpRes += "onclick=\""+addAction[i]['onclick']+"("+numRow+",event)\"></td>";
                                }
                            }
                        }
						
                        for(i=1;i<tmpTd.length;i++) {
                            var tmp = document.getElementById(id+"_"+tmpEls[i]).firstChild,
								tmpCont = document.getElementById(id+"_"+tmpEls[i]);
                            if(!tmp) {
                                alert('DOM Definition Error : '+tmpEls[i]);
                            }
                            if(tmp.options) {
                                if(tmpCont.childNodes.length>1){
                                    if(tmpCont.childNodes[0].options[0].value=='D') {
                                        if(tmpCont.childNodes[0].options[document.getElementById(id+"_"+tmpEls[i]).childNodes[0].selectedIndex].value=='K') {
                                            tmpRes += "<td id='"+id+"_"+tmpEls[i]+"_"+numRow+"' align='"+alignArr[i]+"' value='"+tmpTd[i]+"'>"+
                                                '-'+tmpCont.childNodes[1].value+"</td>";
                                        } else {
                                            tmpRes += "<td id='"+id+"_"+tmpEls[i]+"_"+numRow+"' align='"+alignArr[i]+"' value='"+tmpTd[i]+"'>"+
                                                tmpCont.childNodes[1].value+"</td>";
                                        }
									} else if(tmpCont.childNodes[1].tagName=='IMG'){
										if(tmp.selectedIndex==-1) {
											tmpRes += "<td id='"+id+"_"+tmpEls[i]+"_"+numRow+"' align='"+alignArr[i]+"' value='"+tmpTd[i]+"'>"+"</td>";
										} else {
											tmpRes += "<td id='"+id+"_"+tmpEls[i]+"_"+numRow+"' align='"+alignArr[i]+"' value='"+tmpTd[i]+"'>"+
												tmp.options[tmp.selectedIndex].text+"</td>";
										}
                                    } else {
                                        tmpRes += "<td id='"+id+"_"+tmpEls[i]+"_"+numRow+"' align='"+alignArr[i]+"' value='"+tmpTd[i]+"'>"+
                                            tmpCont.childNodes[0].value+":"+
                                            tmpCont.childNodes[2].value+":00</td>";
                                    }
                                } else {
									if(tmp.selectedIndex==-1) {
										tmpRes += "<td id='"+id+"_"+tmpEls[i]+"_"+numRow+"' align='"+alignArr[i]+"' value='"+tmpTd[i]+"'>"+"</td>";
									} else {
										tmpRes += "<td id='"+id+"_"+tmpEls[i]+"_"+numRow+"' align='"+alignArr[i]+"' value='"+tmpTd[i]+"'>"+
											tmp.options[tmp.selectedIndex].text+"</td>";
									}
                                }
                            } else if(document.getElementById(id+"_"+tmpEls[i]).childNodes.length>3){
                                tmpRes += "<td id='"+id+"_"+tmpEls[i]+"_"+numRow+"' align='"+alignArr[i]+"' value='"+tmpTd[i]+"'>"+
                                    document.getElementById(id+"_"+tmpEls[i]).childNodes[2].value+"</td>";
                            } else if(document.getElementById(id+"_"+tmpEls[i]).childNodes.length>1){
                                tmpRes += "<td id='"+id+"_"+tmpEls[i]+"_"+numRow+"' align='"+alignArr[i]+"' value='"+tmpTd[i]+"'>"+
                                    document.getElementById(id+"_"+tmpEls[i]).childNodes[0].value+"</td>";
                            } else {
                                if(tmpTd[i]==parseFloat(tmpTd[i]) && tmpEls[i]!='tahun' && tmpEls[i]!='tahuntanam' && tmpEls[i]!='nourut') {
                                    var tmpNF = document.getElementById(id+"_"+tmpEls[i]).firstChild;
                                    change_number(tmpNF);
                                    tmpRes += "<td id='"+id+"_"+tmpEls[i]+"_"+numRow+"' align='"+alignArr[i]+"' value='"+remove_comma_var(tmpTd[i])+"'>"+
                                        tmpNF.value+"</td>";
                                    var tmp2=remove_comma(tmpNF);
                                    tmp3 = tmp2.split('.');
									if(tmp3[1]>0) {
										tmpNF.value=tmp2;
									} else {
										tmpNF.value=tmp3[0];
									}
                                } else {
                                    tmpRes += "<td id='"+id+"_"+tmpEls[i]+"_"+numRow+"' align='"+alignArr[i]+"' value='"+tmpTd[i]+"'>"+
                                        tmpTd[i]+"</td>";
                                }
                            }
                        }
                        tmpRes += "</tr>";
                        body.innerHTML += tmpRes;
                        
                        if(noaction==false) {
                            // Get Default Value
                            var arrDef = defValue.split('##');
                            for(i=1;i<arrDef.length;i++) {
                                arrDef[i] = arrDef[i].split('=');
                            }
                            var theValue = {};
                            for(i=1;i<arrDef.length;i++) {
                                theValue[arrDef[i][0]] = arrDef[i][1];
                            }
                            
                            // Clear Value and Enable
                            tmpEls = els.split("##");
                            var noClr = noClear.split('##');
                            var noEnableArr = noEnable.split('##');
                            for(i=1;i<tmpEls.length;i++) {
                                
                                var tmpTarget = document.getElementById(id+"_"+tmpEls[i]).firstChild;
                                var tmpNoClr = false;
                                // If Don't Clear Value
                                for(j=1;j<noClr.length;j++) {
                                    if(tmpEls[i]==noClr[j]) {
                                        tmpNoClr = true;
                                    }
                                }
                                
                                // Clear Value
                                if(tmpNoClr==false) {
                                    if(tmpTarget.options) {
                                       //alert('masuk');
                                        if(theValue[tmpEls[i]]) {
                                            for(val in tmpTarget.options) {
                                                if(tmpTarget.options[val]==theValue[tmpEls[i]]) {
                                                    tmpTarget.selectedIndex=val;
                                                    break;
                                                }
                                            }
                                        } else {
                                           //============== edit by louis / sudah dibuka by adi
                                            if(tmpTarget.selectedIndex>-1) tmpTarget.selectedIndex=0;
                                           //================================== 
                                        }
                                    } else {
                                        if(theValue[tmpEls[i]]) {
                                            tmpTarget.value=theValue[tmpEls[i]];
                                        } else {
                                            tmpTarget.value=0;//ini dia
                                        }
                                    }
                                }
                                
                                var tmpNoEnable = false;
                                // If Don't Enable
                                for(j=1;j<noEnableArr.length;j++) {
                                    if(tmpEls[i]==noEnableArr[j]) {
                                        tmpNoEnable = true;
                                    }
                                }
                                
                                // Enable
                                if(tmpNoEnable==false) {
                                    tmpTarget.removeAttribute('disabled');
                                }
                            }
                        } else {
                            for(i=1;i<tmpEls.length;i++) {
                              
                              try{//=================try ini tambahan louis  
                                // Clear Value
                                if(tmpTarget.options) {
               
                                    if(theValue[tmpEls[i]]) {
                                        for(val in tmpTarget.options) {
                                            if(tmpTarget.options[val]==theValue[tmpEls[i]]) {
                                                tmpTarget.selectedIndex=val;
                                                break;
                                            }
                                        }
                                    } else {
                                        if(tmpTarget.selectedIndex>-1) tmpTarget.selectedIndex=0;
                                    }
                                } else {
                                    if(theValue[tmpEls[i]]) {
                                        tmpTarget.value=theValue[tmpEls[i]];
                                    } else {
                                        tmpTarget.value=0;
                                    }
                                }
                              }
                              catch(err)
                              {}
                            }
                            document.getElementById('form_'+id).style.display = 'none';
                        }
						
						// After CRUD Custom function
						if(ftMain.afterCrud!=null) {
							eval(ftMain.afterCrud+'()');
						}
                    }
                } else {
                    busy_off();
                    error_catch(con.status);
                }
            }
        }
        cek_kegiatan(param);
        function cek_kegiatan(kegiatan)
        {
//            var param = "kegiatan="+kegiatan;
        //    var param = "notransaksi="+notrans+"&kodeorg="+kodeorg+"&kegiatan="+kegiatan;
            param=kegiatan;
//            alert(param);
            function responcek() {
                if (con.readyState == 4) {
                    if (con.status == 200) {
                        busy_off();
                        if (!isSaveResponse(con.responseText)) {
                            alert('ERROR TRANSACTION,\n' + con.responseText);
                        } else {
                            //== Success Response
        //                    detailField.innerHTML = con.responseText;
        //                    initUmrIns();
//        alert('done');
                            //if(con.responseText=='SS')sebabSisip(param); //else // kalo bukan ss, lanjut di sini...
                            //if (con.responseText == 'SS') { sebabSisip(param); }
                                post_response_text(target+'.php?proses=add', param, respon);
                        }
                    } else {
                        busy_off();
                        error_catch(con.status);
                    }
                }
            }

            post_response_text('kebun_slave_operasional_detail.php?proses=cekSisip', param, responcek);
        }
        
        function sebabSisip(parah)
        {
//            alert(parah);
            param=parah;
//            var notrans = document.getElementById('notransaksi').value;
//            var kodeorg = document.getElementById('kodeorg').value;
//            alert(kodeorg);
//           param='notrans='+notrans+'&kodeorg='+kodeorg;
        //   param+='&tglAkhir='+tglAkhr+'&tglAwal='+tglAwl;
           tujuan='kebun_slave_operasional_sisip.php?proses=inputSisip&'+param;  
        //   tujuan='vhc_slave_2biayaalokasiperkendaraandetail.php'+"?"+param;  
           width='500';
           height='200';
         //alert(param);
           content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>";
           ev="";
           showDialog1('Sebab Sisip ',content,width,height,ev); 

        }        
        
//        post_response_text(target+'.php?proses=add', param, respon);
    }
    
    /* clearFT
     * Fungsi untuk mengubah form menjadi mode tambah
     */
    this.clearFT = function(id,els,addEls,mode,noClear,noEnable,defValue) {
        this.prep(id,els,addEls);
        
        // Get Default Value
        var arrDef = defValue.split('##');
        for(i=1;i<arrDef.length;i++) {
            arrDef[i] = arrDef[i].split('=');
        }
        var theValue = {};
        for(i=1;i<arrDef.length;i++) {
            theValue[arrDef[i][0]] = arrDef[i][1];
        }
        
        // Change Mode
        var modeSpan = document.getElementById('form_'+id+'_mode');
        modeSpan.innerHTML = mode;
        
        // Button
        var addBtn = document.getElementById('addFTBtn_'+id);
        var editBtn = document.getElementById('editFTBtn_'+id);
        var clearBtn = document.getElementById('clearFTBtn_'+id);
        
        // Clear Value and Enable
        var tmpEls = els.split("##");
        var noClr = noClear.split('##');
        var noEnableArr = noEnable.split('##');
        for(i=1;i<tmpEls.length;i++) {
            var tmpTarget = document.getElementById(id+"_"+tmpEls[i]).firstChild;
            var tmpNoClr = false;
            // If Don't Clear Value
            for(j=1;j<noClr.length;j++) {
                if(tmpEls[i]==noClr[j]) {
                    tmpNoClr = true;
                }
            }
            
            // Clear Value
            if(tmpNoClr==false) {
                if(tmpTarget.options) {
                    if(theValue[tmpEls[i]]) {
                        for(val in tmpTarget.options) {
                            if(tmpTarget.options[val]==theValue[tmpEls[i]]) {
                                tmpTarget.selectedIndex=val;
                                break;
                            }
                        }
                    } else {
						if(tmpTarget.selectedIndex>-1) {
							tmpTarget.selectedIndex=0;
						}
                       //=========================removed by louis
                       // tmpTarget.selectedIndex=0;
                       //======================================
                    }
                } else {
                    if(theValue[tmpEls[i]]) {
                        tmpTarget.value=theValue[tmpEls[i]];
                    } else {
                        tmpTarget.value=0;
                    }
                }
            }
            
            var tmpNoEnable = false;
            // If Don't Enable
            for(j=1;j<noEnableArr.length;j++) {
                if(tmpEls[i]==noEnableArr[j]) {
                    tmpNoEnable = true;
                }
            }
            
            // Enable
            if(tmpNoEnable==false) {
                tmpTarget.removeAttribute('disabled');
            }
			
			// After CRUD Custom function
			if(this.afterCrud!=null) {
				eval(this.afterCrud+'()');
			}
        }
        
        // Hide & Show Button
        addBtn.style.display='';
        editBtn.style.display='none';
        clearBtn.style.display='none';
    }
    
    /* editmodeFT
     * Fungsi untuk mengubah form menjadi mode edit sesuai row yang dipilih
     */
    this.editmodeFT = function(numRow,id,els,addEls,mode,freeze,numFormat) {
        this.prep(id,els,addEls);
        
        // Get Freeze Els
        if(typeof freeze != 'undefined') {
            var freezeArr = freeze.split("##");
        }
        
        // Number Formatted
        var numFormatArr = numFormat.split('##');
        
        // Update numRow
        document.getElementById(id+'_numRow').value=numRow;
        
        // Change Mode
        var modeSpan = document.getElementById('form_'+id+'_mode');
        modeSpan.innerHTML = mode;
        
        // Button
        var addBtn = document.getElementById('addFTBtn_'+id);
        var editBtn = document.getElementById('editFTBtn_'+id);
        var clearBtn = document.getElementById('clearFTBtn_'+id);
        
        // Pass Value
        var tmpEls = els.split("##");
        for(i=1;i<tmpEls.length;i++) {
            var tmpSrc = document.getElementById(id+"_"+tmpEls[i]+"_"+numRow);
            var tmpTargetInduk = document.getElementById(id+"_"+tmpEls[i]);
            var tmpTarget = document.getElementById(id+"_"+tmpEls[i]).firstChild;
            var srcVal = tmpSrc.getAttribute('value');
            // isNumberFormat
            var isNF = false;
            for(j=1;j<numFormatArr.length;j++){
                if(numFormatArr[j]==tmpEls[i]) {
                    isNF = true;
                }
            }
            if(tmpTarget.options) {
                if(tmpTargetInduk.childNodes.length>1) {
                    if(tmpTarget.options[0].value=='D') {
                        if(parseFloat(srcVal)<0) {
                            tmpTarget.selectedIndex = 1;
                            tmpTargetInduk.childNodes[1].value = parseFloat(srcVal)*(-1);
                        } else {
                            tmpTarget.selectedIndex = 0;
                            tmpTargetInduk.childNodes[1].value = srcVal;
                        }
                        if(isNF==true) {
                            tmpTargetInduk.childNodes[1].value = _formatted(tmpTargetInduk.childNodes[1]);
                        }
					} else if(tmpTargetInduk.childNodes[1].tagName=='IMG') {
						for(j=0;j<tmpTarget.options.length;j++) {
							if(tmpTarget.options[j].value==srcVal)
								tmpTarget.options[j].selected=true;
						}
                    } else {
                        var tmpSrcVal = srcVal.split(':');
                        tmpTargetInduk.childNodes[0].value=tmpSrcVal[0];
                        tmpTargetInduk.childNodes[2].value=tmpSrcVal[1];
                    }
                } else {
                    for(j=0;j<tmpTarget.options.length;j++) {
                        if(tmpTarget.options[j].value==srcVal)
                            tmpTarget.options[j].selected=true;
                    }
                }
            } else if(document.getElementById(id+"_"+tmpEls[i]).childNodes.length>1){
                tmpTarget.value = srcVal;
                document.getElementById(id+"_"+tmpEls[i]).childNodes[2].value=tmpSrc.innerHTML;
            } else {
                tmpTarget.value = srcVal;
                if(isNF==true) {
                    tmpTarget.value = _formatted(tmpTarget);
                }
            }
            if(typeof freeze != 'undefined') {
                for(j=1;j<freezeArr.length;j++) {
                    if(freezeArr[j]==tmpEls[i]) {
                        tmpTarget.setAttribute('disabled','disabled');
                    }
                }
            }
        }
        
        // Hide & Show Button
        addBtn.style.display='none';
        editBtn.style.display='';
        clearBtn.style.display='';
    }
    
    /* editFT
     * Fungsi untuk mengubah data
     */
    this.editFT = function(id,els,addEls,target,numFormat,mode,noClear,noEnable,defValue) {
        this.prep(id,els,addEls);
        
        var numRow = document.getElementById(id+'_numRow').value;
        var param = this.getParam(id,els,addEls);
        var tmpCond = els.split('##');
        for(i=1;i<tmpCond.length;i++) {
            var tmp = document.getElementById(id+"_"+tmpCond[i]+"_"+numRow);
            param += "&cond_"+tmpCond[i]+"="+tmp.getAttribute('value');
        }
        var body = document.getElementById('tbody_'+id);
        
        // Number Formatted
        var numFormatArr = numFormat.split('##');
        
        function respon() {
            if (con.readyState == 4) {
                if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                        alert('ERROR TRANSACTION,\n' + con.responseText);
                    } else {
                        // Success Response
                        eval("var tmpRes = "+con.responseText);
                        var tmpEls = els.split('##');
                        for(i=1;i<tmpEls.length;i++) {
                            var tmpSrc = document.getElementById(id+'_'+tmpEls[i]+'_'+numRow),
								tmpTarget = document.getElementById(id+'_'+tmpEls[i]).firstChild,
								tmpTargetCont = document.getElementById(id+'_'+tmpEls[i]),
								tmpAnak = document.getElementById(id+'_'+tmpEls[i]).childNodes;
                            tmpSrc.setAttribute('value',tmpRes[tmpEls[i]]);
                            
                            // isNumberFormat
                            var isNF = false;
                            for(j=1;j<numFormatArr.length;j++){
                                if(numFormatArr[j]==tmpEls[i]) {
                                    isNF = true;
                                }
                            }
                            
                            if(tmpTarget.options) {
                                if(tmpTargetCont.childNodes.length>1){
                                    if(tmpAnak[0].options[0].value=='D') {
                                        if(tmpAnak[0].selectedIndex==1) {
                                            if(isNF==true) {
                                                tmpSrc.innerHTML = '-'+tmpAnak[1].value;
                                            } else {
                                                tmpSrc.innerHTML = parseFloat(tmpAnak[1].value)*(-1);
                                            }
                                        } else {
                                            if(isNF==true) {
                                                tmpSrc.innerHTML = tmpAnak[1].value;
                                            } else {
                                                tmpSrc.innerHTML = parseFloat(tmpAnak[1].value);
                                            }
                                        }
									} else if(tmpTargetCont.childNodes[1].tagName=='IMG'){
										if(tmpTarget.selectedIndex>-1) {
											tmpSrc.innerHTML = tmpTarget.options[tmpTarget.selectedIndex].text;
										}
                                    } else {
                                        tmpSrc.innerHTML = document.getElementById(id+"_"+tmpEls[i]).childNodes[0].value+":"+
                                            tmpTargetCont.childNodes[2].value+":00";
                                    }
                                } else {
									if(tmpTarget.selectedIndex>-1) {
										tmpSrc.innerHTML = tmpTarget.options[tmpTarget.selectedIndex].text;
									}
                                }
                            } else if(document.getElementById(id+"_"+tmpEls[i]).childNodes.length>3){
                                tmpSrc.innerHTML = tmpTargetCont.childNodes[2].value;
                            } else if(document.getElementById(id+"_"+tmpEls[i]).childNodes.length>1){
                                tmpSrc.innerHTML = tmpTargetCont.childNodes[0].value;
                            } else {
                                tmpSrc.innerHTML = tmpRes[tmpEls[i]];
                            }
                        }
                        //test jamhari start
                        // Get Default Value
                        var arrDef = defValue.split('##');
                        for(i=1;i<arrDef.length;i++) {
                            arrDef[i] = arrDef[i].split('=');
                        }
                        var theValue = {};
                        for(i=1;i<arrDef.length;i++) {
                            theValue[arrDef[i][0]] = arrDef[i][1];
                        }
                        // Change Mode
                        var modeSpan = document.getElementById('form_'+id+'_mode');
                        modeSpan.innerHTML = mode;

                        // ButtonclearFTBtn_ftMesin
                        var addBtn = document.getElementById('addFTBtn_'+id);
                        var editBtn = document.getElementById('editFTBtn_'+id);
                        var clearBtn = document.getElementById('clearFTBtn_'+id);

                        // Clear Value and Enableid,els,addEls,mode,noClear,noEnable,defValue
                        var tmpEls = els.split("##");
                        var noClr = noClear.split('##');
                        var noEnableArr = noEnable.split('##');
                        for(i=1;i<tmpEls.length;i++) {
                            var tmpTarget = document.getElementById(id+"_"+tmpEls[i]).firstChild;
                            var tmpNoClr = false;
                             //If Don't Clear Value
                            for(j=1;j<noClr.length;j++) {
                                if(tmpEls[i]==noClr[j]) {
                                    tmpNoClr = true;
                                }
                            }

//                            // Clear Value
                            if(tmpNoClr==false) {
                                if(tmpTarget.options) {
                                    if(theValue[tmpEls[i]]) {
                                        for(val in tmpTarget.options) {
                                            if(tmpTarget.options[val]==theValue[tmpEls[i]]) {
                                                tmpTarget.selectedIndex=val;
                                                break;
                                            }
                                        }
                                    } else {
                                       //=========================removed by louis, open by adi
										if(tmpTarget.selectedIndex>-1) {
											tmpTarget.selectedIndex=0;
										}
                                       //======================================
                                    }
                                } else {
                                    if(theValue[tmpEls[i]]) {
                                        tmpTarget.value=theValue[tmpEls[i]];
                                    } else {
                                        tmpTarget.value=0;
                                    }
                                }
                            }

                            var tmpNoEnable = false;
                            // If Don't Enable
                            for(j=1;j<noEnableArr.length;j++) {
                                if(tmpEls[i]==noEnableArr[j]) {
                                    tmpNoEnable = true;
                                }
                            }

                            // Enable
                            if(tmpNoEnable==false) {
                                tmpTarget.removeAttribute('disabled');
                            }
                        }

                        // Hide & Show Button
                        addBtn.style.display='';
                        editBtn.style.display='none';
                        clearBtn.style.display='none';
                        //end test jamhari
						
						// After CRUD Custom function
						if(ftMain.afterCrud!=null) {
							eval(ftMain.afterCrud+'()');
						}
                    }
                } else {
                    busy_off();
                    error_catch(con.status);
                }
            }
        }
        
        post_response_text(target+'.php?proses=edit', param, respon);
    }
    
    /* delFT
     * Fungsi untuk menghapus data
     */
    this.delFT = function(numRow,id,els,addEls,target,mode,noClear,noEnable,defValue) {
        this.prep(id,els,addEls);
        this.clearFT(id,els,addEls,mode,noClear,noEnable,defValue);
        var param = this.getParamRow(numRow,id,els,addEls);
        var body = document.getElementById('tbody_'+id);
        
        function respon() {
            if (con.readyState == 4) {
                if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                        alert('ERROR TRANSACTION,\n' + con.responseText);
                    } else {
                        // Success Response
                        var body = document.getElementById('tbody_'+id);
                        var bodyTr = body.getElementsByTagName('tr');
                        for(i in bodyTr) {
                            if(bodyTr[i].id=='tr_'+id+'_'+numRow)
                                var tmpTr = bodyTr[i];
                        }
                        tmpTr.parentNode.removeChild(tmpTr);
						
						// After CRUD Custom function
						if(ftMain.afterCrud!=null) {
							eval(ftMain.afterCrud+'()');
						}
                    }
                } else {
                    busy_off();
                    error_catch(con.status);
                }
            }
        }
        if(confirm("Anda akan menghapus data detail baris "+(parseInt(numRow)+1)+"\nAnda yakin?"))
			post_response_text(target+'.php?proses=delete', param, respon);
    }
}

function saveSisip(){ // digunakan untuk menyempili save sisip setelah cek sisip di input prestasi (kalo kegiatannya SS)
    notrans=document.getElementById('notrans').getAttribute('value');    
    kodeorg=document.getElementById('kodeorg').getAttribute('value');    
    jumlah=document.getElementById('jumlah').value;
    penyebab=document.getElementById('penyebab').value;
//     alert('......'+notrans+'......'+kodeorg+'......'+jumlah+'......'+penyebab);
    var param = "notrans="+notrans+"&kodeorg="+kodeorg+"&jumlah="+jumlah+"&penyebab="+penyebab;
    if(jumlah==''||penyebab==''){
     alert('please fill all fields');
     return;
    }
    function responsave() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    alert('Done.');
    parent.closeDialog();
                    //== Success Response
//                    closeDialog2();
//                    document.getElementById(idkomponen).innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }   
post_response_text('kebun_slave_operasional_detail.php?proses=saveSisip', param, responsave);  
}


theFT = new formTable();