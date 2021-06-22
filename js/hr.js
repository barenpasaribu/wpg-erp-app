/*
 * @uthor:anthesis-team@mitraagroservindo.com
 * Indonesia 2009
 */
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//title/jabatan
nForm = '';   //to store original tab caption
cForm = '';   //to store input original Employee input form when the form changed
function saveTitle(){
    id = document.getElementById('idx').value;
    newTitle = document.getElementById('newTitle').value;
    code = document.getElementById('code').value;
    if (trim(newTitle) != '' && trim(code) != '') {
        if (confirm('Are you sure..?')) {
            param = 'id=' + id + '&newtitle=' + newTitle + '&code=' + code;
            post_response_text('hr_slave_saveTitle.php', param, respon);
            
        }
    }
    else {
        alert('Text is empty');
    }
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (con.responseText.lastIndexOf('Gagal') > -1 || con.responseText.lastIndexOf('ror') > -1 || con.responseText.lastIndexOf('rning') > -1) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('result').innerHTML = con.responseText;
                    clearFormTitle();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function clearFormTitle(){
    document.getElementById('idx').value = 'new';
    document.getElementById('newTitle').value = '';
    document.getElementById('note').innerHTML = 'New ';
    document.getElementById('code').value = '';
}

function changeSTitle(id, code, name){
    document.getElementById('idx').value = id;
    document.getElementById('newTitle').value = name;
    document.getElementById('note').innerHTML = 'Edit ';
    document.getElementById('code').value = code;
}

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//Departement
function saveTitleDept(){
    id = document.getElementById('idx').value;
    code = document.getElementById('newTitle').value;
    name = document.getElementById('name').value;
    if (trim(code) != '' && trim(name) != '') {
        if (confirm('Are you sure..?')) {
            param = 'id=' + id + '&code=' + code + '&name=' + name;
            //alert(param);
            post_response_text('hr_slave_saveDepartement.php', param, respon);
        }
    }
    else {
        alert('Dept.Code and/or Dept.Name  is empty');
    }
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('result').innerHTML = con.responseText;
                    clearFormDept();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function clearFormDept(){
    document.getElementById('idx').value = 'new';
    document.getElementById('newTitle').value = '';
    document.getElementById('name').value = '';
    document.getElementById('note').innerHTML = 'New ';
}

function changeSDept(id, code, name){
    document.getElementById('idx').value = id;
    document.getElementById('newTitle').value = code;
    document.getElementById('name').value = name;
    document.getElementById('note').innerHTML = 'Edit ';
}

//family relation
function saveTitleRelation(){
    id = document.getElementById('idx').value;
    relname = document.getElementById('relname').value;
    if (trim(relname) != '') {
        if (confirm('Are you sure..?')) {
            param = 'id=' + id + '&relname=' + relname;
            post_response_text('hr_slave_saveFamilyRelation.php', param, respon);
        }
    }
    else {
        alert('Text is empty');
    }
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('result').innerHTML = con.responseText;
                    clearFormRel();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function clearFormRel(){
    document.getElementById('idx').value = 'new';
    document.getElementById('relname').value = '';
    document.getElementById('note').innerHTML = 'New ';
}

function changeSRel(id, name){
    document.getElementById('idx').value = id;
    document.getElementById('relname').value = name;
    document.getElementById('note').innerHTML = 'Edit ';
}

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//===========new Employee
function showList(id){
    hideById('EList');
    hideById('newForm');
    //show the last	
    showById(id);
    
}

function isSaveAble(textbox, btn){
    if (trim(textbox.value) != '') {
        document.getElementById(btn).disabled = false;
    }
    else {
        document.getElementById(btn).disabled = true;
    }
}

function saveEmployee(){
    userid = trim(document.getElementById('userid').value);
    eName  = trim(document.getElementById('eName').value);
    ePoh   = trim(document.getElementById('ePoh').value);
    eSigndate = trim(document.getElementById('eSigndate').value);
    eBirthday = trim(document.getElementById('eBirthday').value);
    ePlaceofbirth = trim(document.getElementById('ePlaceofbirth').value);
    eIdnum = trim(document.getElementById('eIdnum').value);
    eMail  = trim(document.getElementById('eMail').value);
    eNationality  = trim(document.getElementById('eNationality').value);
    ePhone = trim(document.getElementById('ePhone').value);
    eReligion = trim(document.getElementById('eReligion').value);
    eEthnic   = trim(document.getElementById('eEthnic').value);
    eUsercode = trim(document.getElementById('eUsercode').value);
    eLevel = trim(document.getElementById('eLevel').value);
    eLoc   = trim(document.getElementById('eLoc').value);
    eAdd   = trim(document.getElementById('eAdd').value);
    eHome  = trim(document.getElementById('eHome').value);
	ePystart = trim(document.getElementById('payrollstart').value);
	
	eTax     =trim(document.getElementById('eTax').value);
	eCity   = trim(document.getElementById('eCity').value);
	eProvince = trim(document.getElementById('eProvince').value);
	eZipcode = trim(document.getElementById('eZipcode').value);
	
	eAdd1   = trim(document.getElementById('eAdd1').value);
	eCity1   = trim(document.getElementById('eCity1').value);
	eProvince1 = trim(document.getElementById('eProvince1').value);
	eZipcode1 = trim(document.getElementById('eZipcode1').value);
	eNicname  =	trim(document.getElementById('eNicname').value);
	eHobby  =	trim(document.getElementById('eHobby').value);
	eHeight  =	trim(document.getElementById('eHeight').value);
	
    eSex     = trim(document.getElementById('eSex').options[document.getElementById('eSex').selectedIndex].value);
    ePayroll = trim(document.getElementById('ePayroll').options[document.getElementById('ePayroll').selectedIndex].value);
    eIdtype  = trim(document.getElementById('eIdtype').options[document.getElementById('eIdtype').selectedIndex].text);
    eEmpltype= trim(document.getElementById('eEmpltype').options[document.getElementById('eEmpltype').selectedIndex].text);
    eMstatus = trim(document.getElementById('eMstatus').options[document.getElementById('eMstatus').selectedIndex].value);
    edept    = trim(document.getElementById('eDept').options[document.getElementById('eDept').selectedIndex].value);
    eTitle   = trim(document.getElementById('eTitle').options[document.getElementById('eTitle').selectedIndex].value);
    eOrg     = trim(document.getElementById('eOrg').options[document.getElementById('eOrg').selectedIndex].value);
    
	tipestaff= trim(document.getElementById('tipestaff').options[document.getElementById('tipestaff').selectedIndex].value);
	
	if(ePystart=='')
	   ePystart = eSigndate;
	   
	if (eName == '' || eSex == '' || eBirthday == '' || ePlaceofbirth == '' || eNationality == '' || eMstatus == '' || eTitle == '' || eOrg == '' || ePoh == '' || eSigndate == '' || eEmpltype == '' || eUsercode == '' || eLevel == '' || eLoc == '') {
        alert('Each obligatory field must satisfied');
    }
    else {
        if (confirm('Are you sure ?')) {
            param = 'userid=' + userid + '&name=' + eName;
            param += '&poh=' + ePoh + '&signdate=' + eSigndate;
            param += '&bday=' + eBirthday + '&bloc=' + ePlaceofbirth;
            param += '&idnum=' + eIdnum + '&mail=' + eMail;
            param += '&national=' + eNationality + '&phone=' + ePhone;
            param += '&religion=' + eReligion + '&ethnic=' + eEthnic;
            param += '&usercode=' + eUsercode + '&level=' + eLevel;
            param += '&emplloc=' + eLoc + '&addr=' + eAdd;
            param += '&sex=' + eSex + '&payroll=' + ePayroll;
            param += '&idtype=' + eIdtype + '&empltype=' + eEmpltype;
            param += '&mstatus=' + eMstatus + '&dept=' + edept;
            param += '&title=' + eTitle + '&org=' + eOrg;
			param += '&payrollstart='+ePystart+'&home='+eHome;
			param += '&city='+eCity+'&province='+eProvince;
			param += '&zipcode='+eZipcode+'&addr1='+eAdd1;
			param += '&city1='+eCity1+'&province1='+eProvince1;
			param += '&zipcode1='+eZipcode1+'&etax='+eTax;
			param += '&nicname='+eNicname+'&hobby='+eHobby;
			param += '&height='+eHeight+'&tipestaff='+tipestaff;
 			post_response_text('hr_slave_saveNewEmployee.php', param, respon);
        }
        
    }
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    ////FRM is tge name of the tab
                    document.getElementById('userid').value = trim(con.responseText);
                    alert('Saved');
					if(nForm=='')
					   nForm = document.getElementById('tabFRM0').innerHTML;
                    document.getElementById('tabFRM0').innerHTML = eName.substr(0,15);
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function clearFrm(){

    document.getElementById('tabFRM0').innerHTML = 'NewEmployee';	
    if (cForm != '') {
        //FRM is the name of tab
        document.getElementById('contentFRM0').innerHTML = cForm;
        //tabFRM0 is tab caption
    }

        document.frm1.reset();
        document.getElementById('userid').value = '';//makesure userid is reset
        document.getElementById('saveBtn1').disabled = true;
        document.frm2.reset();
        document.getElementById('saveBtn2').disabled = true;
        document.getElementById('experiencelist').innerHTML = '';
        document.frm3.reset();
        document.getElementById('saveBtn3').disabled = true;
        document.getElementById('schoolslist').innerHTML = '';
        document.frm4.reset();
        document.getElementById('saveBtn4').disabled = true;
        document.getElementById('courselist').innerHTML = '';
        document.frm5.reset();
        document.getElementById('saveBtn5').disabled = true;
        document.getElementById('famlist').innerHTML = '';
        document.frm6.reset();
        document.getElementById('frameF').src = '';
   
}

function saveExperience(){
    userid = trim(document.getElementById('userid').value);
    if (userid == '') {
        alert('Please choose Employee');
    }
    else {
        lastcom = trim(document.getElementById('excom').value);
        lastitle = trim(document.getElementById('extitle').value);
        startm = trim(document.getElementById('startm').options[document.getElementById('startm').selectedIndex].value);
        starty = trim(document.getElementById('starty').options[document.getElementById('starty').selectedIndex].value);
        endm = trim(document.getElementById('endm').options[document.getElementById('endm').selectedIndex].value);
        endy = trim(document.getElementById('endy').options[document.getElementById('endy').selectedIndex].value);
        exnote = trim(document.getElementById('exnote').value);
        exphone = trim(document.getElementById('exphone').value);
		startdate = starty + '-' + startm + '-01';
        enddate = endy + '-' + endm + '-28';
        if (enddate <= startdate) {
            alert('Periode is incorrect');
        }
        else {
            if (confirm('Are you sure...?')) {
                param = 'company=' + lastcom + '&title=' + lastitle;
                param += '&startdate=' + startdate + '&endate=' + enddate;
                param += '&note=' + exnote + '&userid=' + userid;
				param += '&phone='+exphone;
                post_response_text('hr_slave_saveNewExperince.php', param, respon);
            }
        }
    }
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('experiencelist').innerHTML = document.getElementById('eName').value + ' Experience(s): <hr>' + con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function saveSchools(){
    userid = trim(document.getElementById('userid').value);
    if (userid == '') {
        alert('Please choose Employee');
    }
    else {
        school = trim(document.getElementById('school').value);
        address = trim(document.getElementById('address').value);
        startm = trim(document.getElementById('scstartm').options[document.getElementById('scstartm').selectedIndex].value);
        starty = trim(document.getElementById('scstarty').options[document.getElementById('scstarty').selectedIndex].value);
        endm = trim(document.getElementById('scendm').options[document.getElementById('scendm').selectedIndex].value);
        endy = trim(document.getElementById('scendy').options[document.getElementById('scendy').selectedIndex].value);
        faculty = trim(document.getElementById('faculty').options[document.getElementById('faculty').selectedIndex].value);
        grade = trim(document.getElementById('grade').options[document.getElementById('grade').selectedIndex].value);
        major = trim(document.getElementById('major').options[document.getElementById('major').selectedIndex].value);
		scorr = trim(document.getElementById('scorr').value);
		
        startdate = starty + '-' + startm + '-01';
        enddate = endy + '-' + endm + '-28';
        if (enddate <= startdate) {
            alert('Period is incorrect');
        }
        else 
            if (school == '' || address == '' || grade=='') {
                alert('Obligatory fields must setisfied');
            }
            else {
                if (confirm('Are you sure...?')) {
                    param = 'school=' + school + '&add=' + address;
                    param += '&startdate=' + startdate + '&endate=' + enddate;
                    param += '&faculty=' + faculty + '&userid=' + userid;
					param += '&grade='+grade+'&major='+major+'&scorr='+scorr;
                    post_response_text('hr_slave_saveNewSchool.php', param, respon);
                }
            }
    }
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('schoolslist').innerHTML = document.getElementById('eName').value + ' School(s): <hr>' + con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function saveCourses(){
    userid = trim(document.getElementById('userid').value);
    if (userid == '') {
        alert('Please choose Employee');
    }
    else {
        provider = trim(document.getElementById('provider').value);
        _location = trim(document.getElementById('location').value);
        subject = trim(document.getElementById('subject').value);
        sponsor = trim(document.getElementById('sponsor').value);
        startm = trim(document.getElementById('curstartm').options[document.getElementById('curstartm').selectedIndex].value);
        starty = trim(document.getElementById('curstarty').options[document.getElementById('curstarty').selectedIndex].value);
        endm = trim(document.getElementById('curendm').options[document.getElementById('curendm').selectedIndex].value);
        endy = trim(document.getElementById('curendy').options[document.getElementById('curendy').selectedIndex].value);
        certified = trim(document.getElementById('certified').options[document.getElementById('certified').selectedIndex].value);
        startdate = starty + '-' + startm + '-01';
        enddate = endy + '-' + endm + '-28';
        if (enddate <= startdate) {
            alert('Period is incorrect');
        }
        else 
            if (provider == ''  || subject == '') {
                alert('Obligatory fields must setisfied');
            }
            else {
                if (confirm('Are you sure...?')) {
                    param = 'provider=' + provider + '&location=' + _location;
                    param += '&subject=' + subject + '&sponsor=' + sponsor;
                    param += '&startdate=' + startdate + '&endate=' + enddate;
                    param += '&certified=' + certified + '&userid=' + userid;
                    post_response_text('hr_slave_saveNewCourse.php', param, respon);
                }
            }
    }
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('courselist').innerHTML = document.getElementById('eName').value + ' Course(s): <hr>' + con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

//family
function saveFamily(){
    userid = trim(document.getElementById('userid').value);
    if (userid == '') {
        alert('Please choose Employee');
    }
    else {
        famname = trim(document.getElementById('famname').value);
        famrelation = trim(document.getElementById('famrelation').options[document.getElementById('famrelation').selectedIndex].value);
        faminsurance = trim(document.getElementById('faminsurance').options[document.getElementById('faminsurance').selectedIndex].value);
        famsex = trim(document.getElementById('famsex').options[document.getElementById('famsex').selectedIndex].value);
        fambirthdate = trim(document.getElementById('fambirthdate').value);
        famadd = trim(document.getElementById('famadd').value);
        famphone = trim(document.getElementById('famphone').value);
        currentjob = trim(document.getElementById('currentjob').value);
        if (famname == '' || famrelation == '' || faminsurance == '' || fambirthdate == '') {
            alert('Obligatory fields must setisfied');
        }
        else {
            if (confirm('Are you sure...?')) {
                param = 'famname=' + famname + '&famrelation=' + famrelation;
                param += '&faminsurance=' + faminsurance + '&famsex=' + famsex;
                param += '&fambirthdate=' + fambirthdate + '&famadd=' + famadd;
                param += '&famphone=' + famphone + '&userid=' + userid+'&currentjob='+currentjob;
                post_response_text('hr_slave_saveFamily.php', param, respon);
            }
        }
    }
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('famlist').innerHTML = document.getElementById('eName').value + ' Families: <hr>' + con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function savePhoto(){
    obj = document.getElementById('fileField1');
    if (obj.value == '') {
        alert('Please pick up a photo');
    }
    else 
        if (obj.value.toLowerCase().lastIndexOf('.jp') == -1) {
            alert('Filetype does not support');
        }
        else {
            userid = trim(document.getElementById('userid').value);
            document.getElementById('puserid').value = userid;
            document.getElementById('frm6').submit();
        }
}

function uploadSelesai(){
    document.getElementById('frameF').style.height = '150px';
    alert('Photo Changed');
}

function uploadGagal(x){
    alert('Upload Failed' + x);
    document.getElementById('frm6').reset();
}

function previewData(userid, evt){
    param = 'userid=' + userid;
    post_response_text('hr_slave_showEmployeeDetail.php', param, respon);
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    pos = new Array();
                    pos = getMouseP(evt);
                    document.getElementById('dynamic').innerHTML = con.responseText;
                    document.getElementById('dynamic').style.top = pos[1] + 'px';
                    document.getElementById('dynamic').style.left = '75px';
                    document.getElementById('dynamic').style.display = '';
                    //displayDetail(con.responseText,evt);   
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}


function editDetail(userid, name){
    param = 'userid=' + userid;
	//FRM is tge name of the tab
	if(cForm=='')
	   cForm = document.getElementById('contentFRM0').innerHTML;

    post_response_text('hr_slave_getEmployeeForEdit.php', param, respon);
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {					
                    document.getElementById('contentFRM0').innerHTML = con.responseText;
                    document.getElementById('tabFRM0').innerHTML = name;
                    showList('newForm');
                    loadFam(userid);
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function loadFam(userid){
    param = 'userid=' + userid;
    post_response_text('hr_slave_saveFamily.php', param, respon);
    
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('famlist').innerHTML = document.getElementById('eName').value + ' Families: <hr>' + con.responseText;
                    loadExper(userid);
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function loadExper(userid){
    param = 'userid=' + userid;
    post_response_text('hr_slave_saveNewExperince.php', param, respon);
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('experiencelist').innerHTML = document.getElementById('eName').value + ' Experience(s): <hr>' + con.responseText;
                    loadEducation(userid);
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function loadEducation(userid){
    param = 'userid=' + userid;
    post_response_text('hr_slave_saveNewSchool.php', param, respon);
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('schoolslist').innerHTML = document.getElementById('eName').value + ' School(s): <hr>' + con.responseText;
                    loadCourse(userid);
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function loadCourse(userid){
    param = 'userid=' + userid;
    post_response_text('hr_slave_saveNewCourse.php', param, respon);
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('courselist').innerHTML = document.getElementById('eName').value + ' Course(s): <hr>' + con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function tryFind(event){
    key = getKey(event);
    if (key == 13) 
        searchEmployee();
}

function searchEmployee(){
    findtext = document.getElementById('texttosearch').value;
    if (findtext.lastIndexOf('%') > -1) 
        alert('% is invalid character');
    else 
        if (findtext.lastIndexOf(';') > -1) 
            alert('; is invalid character');
        else {
            switch (findtext) {
                case '':
                    alert('No text to find');
                    break;
                case '?':
                    alert('? is invalid character');
                    break;
                default:
                    param = 'texttofind=' + findtext;
                    post_response_text('hr_slave_searchEmployee.php', param, respon);
            }
        }
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('searchresult').innerHTML = con.responseText;
                    document.getElementById('searchresult').style.display = '';
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function closeSearch(){
    document.getElementById('searchresult').innerHTML = '';
    document.getElementById('searchresult').style.display = 'none';
}

function deleteExpr(candid,corp,startx)
{
	param='userid='+candid+'&company='+corp+'&startdate='+startx+'&del=true';
    if(confirm('Are you sure..?'))
	   post_response_text('hr_slave_saveNewExperince.php', param, respon);
   function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('experiencelist').innerHTML = document.getElementById('eName').value + ' Experience(s): <hr>' + con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	
}

function deleteSchool(candid,school,startx,grade)
{
	param='userid='+candid+'&school='+school+'&startdate='+startx+'&grade='+grade+'&del=true';
    if(confirm('Are you sure..?'))
	   post_response_text('hr_slave_saveNewSchool.php', param, respon);
       function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('schoolslist').innerHTML = document.getElementById('eName').value + ' School(s): <hr>' + con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	
}

function deleteCourse(candid,provider,subject)
{
	param='userid='+candid+'&provider='+provider+'&subject='+subject+'&del=true';
    if(confirm('Are you sure..?'))
	   post_response_text('hr_slave_saveNewCourse.php', param, respon);
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('courselist').innerHTML = document.getElementById('eName').value + ' Course(s): <hr>' + con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	
}
function deleteFamily(candid,relation,name)
{
	param='userid='+candid+'&famrelation='+relation+'&famname='+name+'&del=true';
	if(confirm('Are you sure..?'))
	   post_response_text('hr_slave_saveFamily.php', param, respon);
       function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('famlist').innerHTML = document.getElementById('eName').value + ' School(s): <hr>' + con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	
}
function saveFacultyOpt(){
    id = document.getElementById('group').options[document.getElementById('group').selectedIndex].value;
    newTitle = document.getElementById('newValue').value;
    code = document.getElementById('code').value;
    num  = document.getElementById('number').value;
	if (trim(newTitle) != '' && trim(code) != '' && trim(id) != '') {
        if (confirm('Are you sure..?')) {
            param = 'group=' + id + '&newtitle=' + newTitle + '&code=' + code;
			param+= '&num='+num;
            post_response_text('hr_slave_saveFacultyOpt.php', param, respon);
        }
    }
    else {
        alert('Alll field  obligatory');
    }
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (con.responseText.lastIndexOf('Gagal') > -1 || con.responseText.lastIndexOf('ror') > -1 || con.responseText.lastIndexOf('rning') > -1) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('result').innerHTML = con.responseText;
                    clearFormFaculty();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function clearFormFaculty(){
    document.getElementById('note').innerHTML = 'New ';
    document.getElementById('code').value = '';
	document.getElementById('newValue').value = '';
	document.getElementById('number').value='0';
	document.getElementById('group').disabled=false;
	document.getElementById('code').disabled=false;
}

function changeSFacultyOpt(id, code, name,urut){
    x=document.getElementById('group');
	for(z=0;z<x.length;z++)
	{
		if(x.options[z].value==id)
		{
			x.options[z].selected =true;
		}
	}
    document.getElementById('note').innerHTML = 'Edit ';
    document.getElementById('code').value = code;
	document.getElementById('newValue').value = name;
	document.getElementById('number').value=urut;
	document.getElementById('group').disabled=true;
	document.getElementById('code').disabled=true;
}

function deleteFaculty(group,code)
{
	param='group='+group+'&code='+code+'&del=true';
	if(confirm('Are you sure..?'))
	   post_response_text('hr_slave_saveFacultyOpt.php', param, respon);
       function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('result').innerHTML = con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	
}

function changeToTabView(x,y)
{
	var tipest=document.getElementById('tipest').options[document.getElementById('tipest').selectedIndex].value;
	var jab=document.getElementById('jab').options[document.getElementById('jab').selectedIndex].value;
	param='row='+x+'&page='+y+'&tipe='+tipest+'&jab='+jab;;
	 if (confirm('This require high speed connection, use only on LAN, are you sure..?')) {
	 	post_response_text('hr_slaveEmployeeDataTabbing.php', param, respon);
	 }
       function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('EList').innerHTML = con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		 
	
}
function changeToListView()
{
	var tipest=document.getElementById('tipest').options[document.getElementById('tipest').selectedIndex].value;
	var jab=document.getElementById('jab').options[document.getElementById('jab').selectedIndex].value;
	param='tipe='+tipest+'&jab='+jab;
	 	post_response_text('hr_slaveEmployeeDataList.php', param, respon);

       function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('EList').innerHTML = con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		 
	
}
