<?php



echo "<div id=masterfooter style='border-top:#9D9D9D solid 1px; width:100%;background-color:#08088A;color:#ADADAD;text-align:center;position:fixed;bottom:0px'>\r\n<script type=\"text/javascript\">\r\nvar currenttime = '";
date('F d, Y H:i:s', time());
echo date('F d, Y H:i:s', time());
echo "' // Where php says the current time, then javascript keeps it counting.\r\nvar montharray=new Array(\"January\",\"February\",\"March\",\"April\",\"May\",\"June\",\"July\",\"August\",\"September\",\"October\",\"November\",\"December\")\r\nvar serverdate=new Date(currenttime)\r\nfunction padlength(what){\r\nvar output=(what.toString().length==1)? \"0\"+what : what\r\nreturn output\r\n}\r\nfunction displaytime(){\r\nserverdate.setSeconds(serverdate.getSeconds()+1)\r\nvar datestring=montharray[serverdate.getMonth()]+\" \"+padlength(serverdate.getDate())+\", \"+serverdate.getFullYear()\r\nvar timestring=padlength(serverdate.getHours())+\":\"+padlength(serverdate.getMinutes())+\":\"+padlength(serverdate.getSeconds())\r\ndocument.getElementById(\"servertime\").innerHTML=\"<font color=black><b><i>\"+datestring+\" \"+timestring+\"</i></b></font>\"\r\n}\r\nwindow.onload=function(){\r\nsetInterval(\"displaytime()\", 1000)\r\n}\r\n</script>\r\n\r\n<table border=0 cellspacing=1 width=100% height=5% class=tableWrSystem>\r\n<tr>\r\n<td id=warningContainer width=33% class=WrContainer align=left><span id=\"servertime\"><!-- Server time will automatically be placed here --></span></td>\t\t\r\n<td align=center><font color=black ><b>&copy2017 e-Agro Plantation Management Software</b></font></td>\r\n<td id=chatContainer width=33% class=ChContainer style=cursor:pointer; align=Right><span onclick=logout() title='Click to Logout from system'><font color=black><b>LOG OUT</b></font></span></td>\r\n</tr>\r\n</table>\r\n</div>";

?>