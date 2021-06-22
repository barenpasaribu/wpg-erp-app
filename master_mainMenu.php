<?php
require_once 'config/connection.php';
include_once 'lib/zLib.php';

require_once 'lib/devLibrary.php';
$nmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
echo "<div> ".
"<div style='float:left; margin-left:10px;'>Welcome, " . $_SESSION['standard']['username'] . " @ ". $_SESSION['empl']['namalokasitugas']."</div>".
"<div style='float:right; margin-right:10px; cursor:pointer' onclick=logout() title='Logout'> ".
"<b>Logout</b> ".
"</div> ".
"</div> ".
"<br/><br/>";
echo "\r\n<div  style=\"background-image:url(images/menu/center_blue3.gif)\">\r\n<table border=0 cellpadding=0 cellspacing=0 style=\"width:100%;\">\r\n<tr><td>\r\n\t\t<!--div style=\"font-size:1px;width:6px;height:34px;background-image:url(images/menu/left_cap_blue.gif);\"-->\r\n        <div style=\"font-size:1px;width:6px;height:34px;\">\r\n\t\t</div></td><td style=\"width:100%;\">\r\n<ul id=\"qm0\" class=\"qmmc qm-horizontal-c\">";
if ($_SESSION['security'] == 'off') {
    $ssq = '';
} else {
    if ($_SESSION['access_type'] == 'detail') {
        $ssq = ' and id in ('.$_SESSION['allpriv'].')';
    } else {
        $ssq = ' and access_level >='.$_SESSION['standard']['access_level'];
    }
}

if ($_SESSION['language'] == 'EN') {
    $cell = 'id, type, class, caption2 as caption, action, access_level, parent, urut, hide, lastupdate, lastuser';
} else {
    if ($_SESSION['language'] == 'MY') {
        $cell = 'id, type, class, caption3 as caption, action, access_level, parent, urut, hide, lastupdate, lastuser';
    } else {
        $cell = 'id, type, class, caption as caption, action, access_level, parent, urut, hide, lastupdate, lastuser';
    }
}

$str_m1 = 'select '.$cell.' from '.$dbname.".menu\r\n         where type='master' ".$ssq."\r\n\t\t and hide=0 order by urut";
$res_m1 = mysql_query($str_m1);
while ($bar_m1 = mysql_fetch_object($res_m1)) {
    $master_id = $bar_m1->id;
    echo '<li><a class="qmitem-m qmparent" href="javascript:void(0)">'.strtoupper($bar_m1->caption).'</a>';
    if ($_SESSION['standard']['access_level'] == 1) {
        $str_m2 = 'select '.$cell.' from '.$dbname.'.menu where parent='.$master_id.'  '.$ssq.' and hide=0 order by urut';
        $res_m2 = mysql_query($str_m2);
        if (mysql_num_rows($res_m2) > 0) {
            echo '<ul class="qmsub">';
            while ($bar_m2 = mysql_fetch_object($res_m2)) {
                $master_m2 = $bar_m2->id;
                if ($bar_m2->class == 'devider') {
                    echo '<li><span class="qmdivider qmdividerx" ></span></li>';
                } else {
                    if ($bar_m2->class == 'title') {
                        echo '<li><span class="qmtitle" >'.$bar_m2->caption.'</span></li>';
                    } else {
                        if ($bar_m2->type == 'parent') {
                            echo '<li><a class="qmitem-s" href="javascript:void(0);">'.$bar_m2->caption.'</a>';
                            $str_m3 = 'select '.$cell.' from '.$dbname.".menu\r\n                                                                 where parent=".$master_m2.'  '.$ssq."\r\n                                                                          and hide=0 order by urut";
                            $res_m3 = mysql_query($str_m3);
                            if (mysql_num_rows($res_m3) > 0) {
                                echo '<ul class="qmsub">';
                                while ($bar_m3 = mysql_fetch_object($res_m3)) {
                                    $master_m3 = $bar_m3->id;
                                    if ($bar_m3->class == 'devider') {
                                        echo '<li><span class="qmdivider qmdividerx" ></span></li>';
                                    } else {
                                        if ($bar_m3->class == 'title') {
                                            echo '<li><span class="qmtitle" >'.$bar_m3->caption.'</span></li>';
                                        } else {
                                            if ($bar_m3->type == 'parent') {
                                                echo '<li><a class="qmitem-s" href="javascript:void(0);">'.$bar_m3->caption.'</a>';
                                                $str_m4 = 'select '.$cell.' from '.$dbname.".menu\r\n                                                                                                         where parent=".$master_m3.'  '.$ssq."\r\n                                                                                                                  and hide=0 order by urut";
                                                $res_m4 = mysql_query($str_m4);
                                                if (mysql_num_rows($res_m4) > 0) {
                                                    echo '<ul class="qmsub">';
                                                    while ($bar_m4 = mysql_fetch_object($res_m4)) {
                                                        $master_m4 = $bar_m4->id;
                                                        if ($bar_m4->class == 'devider') {
                                                            echo '<li><span class="qmdivider qmdividerx" ></span></li>';
                                                        } else {
                                                            if ($bar_m4->class == 'title') {
                                                                echo '<li><span class="qmtitle" >'.$bar_m4->caption.'</span></li>';
                                                            } else {
                                                                if ($bar_m4->type == 'parent') {
                                                                    echo '<li><a class="qmitem-s" href="javascript:void(0);">'.$bar_m4->caption.'</a>';
                                                                    $str_m5 = 'select '.$cell.' from '.$dbname.".menu\r\n                                                                                                                                         where parent=".$master_m4.'  '.$ssq."\r\n                                                                                                                                                  and hide=0 order by urut";
                                                                    $res_m5 = mysql_query($str_m5);
                                                                    if (mysql_num_rows($res_m5) > 0) {
                                                                        echo '<ul class="qmsub">';
                                                                        while ($bar_m5 = mysql_fetch_object($res_m5)) {
                                                                            $master_m5 = $bar_m5->id;
                                                                            if ($bar_m5->class == 'devider') {
                                                                                echo '<li><span class="qmdivider qmdividerx" ></span></li>';
                                                                            } else {
                                                                                if ($bar_m5->class == 'title') {
                                                                                    echo '<li><span class="qmtitle" >'.$bar_m5->caption.'</span></li>';
                                                                                } else {
                                                                                    if ($bar_m5->type == 'parent') {
                                                                                        echo '<li><a class="qmitem-s" href="javascript:void(0);">'.$bar_m5->caption.'</a>';
                                                                                        $str_m6 = 'select '.$cell.' from '.$dbname.".menu\r\n                                                                                                                                                                                 where parent=".$master_m5.'   '.$ssq."\r\n                                                                                                                                                                                          and hide=0 order by urut";
                                                                                        $res_m6 = mysql_query($str_m6);
                                                                                        if (mysql_num_rows($res_m6) > 0) {
                                                                                            echo '<ul class="qmsub">';
                                                                                            while ($bar_m6 = mysql_fetch_object($res_m6)) {
                                                                                                $master_m6 = $bar_m6->id;
                                                                                                if ($bar_m6->class == 'devider') {
                                                                                                    echo '<li><span class="qmdivider qmdividerx" ></span></li>';
                                                                                                } else {
                                                                                                    if ($bar_m6->class == 'title') {
                                                                                                        echo '<li><span class="qmtitle" >'.$bar_m6->caption.'</span></li>';
                                                                                                    } else {
                                                                                                        if ($bar_m6->type == 'parent') {
                                                                                                            echo '<li><a class="qmitem-s" href="javascript:void(0);">'.$bar_m6->caption.'</a>';
                                                                                                            $str_m7 = 'select '.$cell.' from '.$dbname.".menu\r\n                                                                                                                                                                                                                 where parent=".$master_m6.'  '.$ssq."\r\n                                                                                                                                                                                                                          and hide=0 order by urut";
                                                                                                            $res_m7 = mysql_query($str_m7);
                                                                                                            if (mysql_num_rows($res_m7) > 0) {
                                                                                                                echo '<ul class="qmsub">';
                                                                                                                while ($bar_m7 = mysql_fetch_object($res_m7)) {
                                                                                                                    $master_m7 = $bar_m7->id;
                                                                                                                    if ($bar_m7->class == 'devider') {
                                                                                                                        echo '<li><span class="qmdivider qmdividerx" ></span></li>';
                                                                                                                    } else {
                                                                                                                        if ($bar_m7->class == 'title') {
                                                                                                                            echo '<li><span class="qmtitle" >'.$bar_m7->caption.'</span></li>';
                                                                                                                        } else {
                                                                                                                            echo "<li><a class=\"qmitem-s\" href=\"javascript:do_load('".$bar_m7->action."')\">".$bar_m7->caption.'</a></li>';
                                                                                                                        }
                                                                                                                    }
                                                                                                                }
                                                                                                                echo '</ul>';
                                                                                                            }

                                                                                                            echo '</li>';
                                                                                                        } else {
                                                                                                            echo "<li><a class=\"qmitem-s\" href=\"javascript:do_load('".$bar_m6->action."')\">".$bar_m6->caption.'</a></li>';
                                                                                                        }
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                            echo '</ul>';
                                                                                        }

                                                                                        echo '</li>';
                                                                                    } else {
                                                                                        echo "<li><a class=\"qmitem-s\" href=\"javascript:do_load('".$bar_m5->action."')\">".$bar_m5->caption.'</a></li>';
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                        echo '</ul>';
                                                                    }

                                                                    echo '</li>';
                                                                } else {
                                                                    echo "<li><a class=\"qmitem-s\" href=\"javascript:do_load('".$bar_m4->action."')\">".$bar_m4->caption.'</a></li>';
                                                                }
                                                            }
                                                        }
                                                    }
                                                    echo '</ul>';
                                                }

                                                echo '</li>';
                                            } else {
                                                echo "<li><a class=\"qmitem-s\" href=\"javascript:do_load('".$bar_m3->action."')\">".$bar_m3->caption.'</a></li>';
                                            }
                                        }
                                    }
                                }
                                echo '</ul>';
                            }

                            echo '</li>';
                        } else {
                            echo "<li><a class=\"qmitem-s\" href=\"javascript:do_load('".$bar_m2->action."')\">".$bar_m2->caption.'</a></li>';
                        }
                    }
                }
            }
            echo '</ul>';
        }
    } else {
        $str_m2 = 'select '.$cell.' from '.$dbname.'.menu where parent='.$master_id.'  '.$ssq.' and hide=0 and id NOT IN (13,949) order by urut';
        $res_m2 = mysql_query($str_m2);
        if (mysql_num_rows($res_m2) > 0) {
            echo '<ul class="qmsub">';
            while ($bar_m2 = mysql_fetch_object($res_m2)) {
                $master_m2 = $bar_m2->id;
                if ($bar_m2->class == 'devider') {
                    echo '<li><span class="qmdivider qmdividerx" ></span></li>';
                } else {
                    if ($bar_m2->class == 'title') {
                        echo '<li><span class="qmtitle" >'.$bar_m2->caption.'</span></li>';
                    } else {
                        if ($bar_m2->type == 'parent') {
                            echo '<li><a class="qmitem-s" href="javascript:void(0);">'.$bar_m2->caption.'</a>';
                            $str_m3 = 'select '.$cell.' from '.$dbname.".menu\r\n                                                                 where parent=".$master_m2.'  '.$ssq."\r\n                                                                          and hide=0 order by urut";
                            $res_m3 = mysql_query($str_m3);
                            if (mysql_num_rows($res_m3) > 0) {
                                echo '<ul class="qmsub">';
                                while ($bar_m3 = mysql_fetch_object($res_m3)) {
                                    $master_m3 = $bar_m3->id;
                                    if ($bar_m3->class == 'devider') {
                                        echo '<li><span class="qmdivider qmdividerx" ></span></li>';
                                    } else {
                                        if ($bar_m3->class == 'title') {
                                            echo '<li><span class="qmtitle" >'.$bar_m3->caption.'</span></li>';
                                        } else {
                                            if ($bar_m3->type == 'parent') {
                                                echo '<li><a class="qmitem-s" href="javascript:void(0);">'.$bar_m3->caption.'</a>';
                                                $str_m4 = 'select '.$cell.' from '.$dbname.".menu\r\n                                                                                                         where parent=".$master_m3.'  '.$ssq."\r\n                                                                                                                  and hide=0 order by urut";
                                                $res_m4 = mysql_query($str_m4);
                                                if (mysql_num_rows($res_m4) > 0) {
                                                    echo '<ul class="qmsub">';
                                                    while ($bar_m4 = mysql_fetch_object($res_m4)) {
                                                        $master_m4 = $bar_m4->id;
                                                        if ($bar_m4->class == 'devider') {
                                                            echo '<li><span class="qmdivider qmdividerx" ></span></li>';
                                                        } else {
                                                            if ($bar_m4->class == 'title') {
                                                                echo '<li><span class="qmtitle" >'.$bar_m4->caption.'</span></li>';
                                                            } else {
                                                                if ($bar_m4->type == 'parent') {
                                                                    echo '<li><a class="qmitem-s" href="javascript:void(0);">'.$bar_m4->caption.'</a>';
                                                                    $str_m5 = 'select '.$cell.' from '.$dbname.".menu\r\n                                                                                                                                         where parent=".$master_m4.'  '.$ssq."\r\n                                                                                                                                                  and hide=0 order by urut";
                                                                    $res_m5 = mysql_query($str_m5);
                                                                    if (mysql_num_rows($res_m5) > 0) {
                                                                        echo '<ul class="qmsub">';
                                                                        while ($bar_m5 = mysql_fetch_object($res_m5)) {
                                                                            $master_m5 = $bar_m5->id;
                                                                            if ($bar_m5->class == 'devider') {
                                                                                echo '<li><span class="qmdivider qmdividerx" ></span></li>';
                                                                            } else {
                                                                                if ($bar_m5->class == 'title') {
                                                                                    echo '<li><span class="qmtitle" >'.$bar_m5->caption.'</span></li>';
                                                                                } else {
                                                                                    if ($bar_m5->type == 'parent') {
                                                                                        echo '<li><a class="qmitem-s" href="javascript:void(0);">'.$bar_m5->caption.'</a>';
                                                                                        $str_m6 = 'select '.$cell.' from '.$dbname.".menu\r\n                                                                                                                                                                                 where parent=".$master_m5.'   '.$ssq."\r\n                                                                                                                                                                                          and hide=0 order by urut";
                                                                                        $res_m6 = mysql_query($str_m6);
                                                                                        if (mysql_num_rows($res_m6) > 0) {
                                                                                            echo '<ul class="qmsub">';
                                                                                            while ($bar_m6 = mysql_fetch_object($res_m6)) {
                                                                                                $master_m6 = $bar_m6->id;
                                                                                                if ($bar_m6->class == 'devider') {
                                                                                                    echo '<li><span class="qmdivider qmdividerx" ></span></li>';
                                                                                                } else {
                                                                                                    if ($bar_m6->class == 'title') {
                                                                                                        echo '<li><span class="qmtitle" >'.$bar_m6->caption.'</span></li>';
                                                                                                    } else {
                                                                                                        if ($bar_m6->type == 'parent') {
                                                                                                            echo '<li><a class="qmitem-s" href="javascript:void(0);">'.$bar_m6->caption.'</a>';
                                                                                                            $str_m7 = 'select '.$cell.' from '.$dbname.".menu\r\n                                                                                                                                                                                                                 where parent=".$master_m6.'  '.$ssq."\r\n                                                                                                                                                                                                                          and hide=0 order by urut";
                                                                                                            $res_m7 = mysql_query($str_m7);
                                                                                                            if (mysql_num_rows($res_m7) > 0) {
                                                                                                                echo '<ul class="qmsub">';
                                                                                                                while ($bar_m7 = mysql_fetch_object($res_m7)) {
                                                                                                                    $master_m7 = $bar_m7->id;
                                                                                                                    if ($bar_m7->class == 'devider') {
                                                                                                                        echo '<li><span class="qmdivider qmdividerx" ></span></li>';
                                                                                                                    } else {
                                                                                                                        if ($bar_m7->class == 'title') {
                                                                                                                            echo '<li><span class="qmtitle" >'.$bar_m7->caption.'</span></li>';
                                                                                                                        } else {
                                                                                                                            echo "<li><a class=\"qmitem-s\" href=\"javascript:do_load('".$bar_m7->action."')\">".$bar_m7->caption.'</a></li>';
                                                                                                                        }
                                                                                                                    }
                                                                                                                }
                                                                                                                echo '</ul>';
                                                                                                            }

                                                                                                            echo '</li>';
                                                                                                        } else {
                                                                                                            echo "<li><a class=\"qmitem-s\" href=\"javascript:do_load('".$bar_m6->action."')\">".$bar_m6->caption.'</a></li>';
                                                                                                        }
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                            echo '</ul>';
                                                                                        }

                                                                                        echo '</li>';
                                                                                    } else {
                                                                                        echo "<li><a class=\"qmitem-s\" href=\"javascript:do_load('".$bar_m5->action."')\">".$bar_m5->caption.'</a></li>';
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                        echo '</ul>';
                                                                    }

                                                                    echo '</li>';
                                                                } else {
                                                                    echo "<li><a class=\"qmitem-s\" href=\"javascript:do_load('".$bar_m4->action."')\">".$bar_m4->caption.'</a></li>';
                                                                }
                                                            }
                                                        }
                                                    }
                                                    echo '</ul>';
                                                }

                                                echo '</li>';
                                            } else {
                                                echo "<li><a class=\"qmitem-s\" href=\"javascript:do_load('".$bar_m3->action."')\">".$bar_m3->caption.'</a></li>';
                                            }
                                        }
                                    }
                                }
                                echo '</ul>';
                            }

                            echo '</li>';
                        } else {
                            echo "<li><a class=\"qmitem-s\" href=\"javascript:do_load('".$bar_m2->action."')\">".$bar_m2->caption.'</a></li>';
                        }
                    }
                }
            }
            echo '</ul>';
        }
    }
}
echo "\r\n<!--li><a class=\"qmitem-m qmparent\">".$_SESSION['standard']['username']."</a></li></ul-->\r\n<!-- Ending Page Content [menu nests within] -->\r\n</td>\r\n<td>\r\n<!--span onclick=logout() title='Logout'>LOGOUT</span-->\r\n</td>\r\n\t<td>\r\n\t\t<!--div style=\"font-size:1px;width:6px;height:34px;background-image:url(images/menu/right_cap_blue.gif);\"-->\r\n        <div style=\"font-size:1px;width:6px;height:34px;\">\r\n\t\t</div>\r\n\t</td>\r\n</tr>\r\n</table>\r\n</div>\r\n<!-- Create Menu Settings: (Int Menu ID, Object Literal Settings - {showDelay:int, hideDelay:int, interaction:string}) [interaction options... 'hover', 'all-always-open', 'click-all', 'all', 'all-always-open', 'main')] -->\r\n<script type=\"text/javascript\">qm_create(0,{showDelay:50,hideDelay:50,interaction:'hover',autoResize:false});</script><!--[END-QM0]-->\r\n";
echo "\r\n<!--div id='progress' style='display:none;border:grey solid 1px;width:150px;position:fixed;right:3px;top:75px;color:#000000;font-family:Tahoma;font-size:15px;font-weight:bolder;text-align:center;background-color:grey;z-index:10000;'>\r\nLoading...<br>\r\n<img src='images/progress.gif'>\r\n</div-->\r\n<div id='progress' style='display:none;border:grey solid 1px;width:100%;height:120%;top:0px;right:0px;position:absolute;opacity: 0.8;color:#ffffff;font-family:Tahoma;font-size:25px;font-weight:bolder;text-align:center;background-color:grey;'>\r\n<font>Loading...</font><br><br>\r\n<img style='position: absolute;left: 600px;top: 250px;z-index: -1;' src='images/progress.gif'>\r\n</div>\r\n<!--div id='progress' class=\"cssload-loader\">e-agro</div-->\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n                                                                                                             \r\n\r\n";
/*if ('2dc2175441295bdd60a0133cf7c502b9899dd5bd' != sha1($_SESSION['org']['holding'])) {
    echo '<script type="text/javascript">alert("You Are Using A Different Serial Number. Please Contact e-Agro Team For A New License Or Demo.");</script>';
    session_destroy();
    exit();
}

if ('SSP' == $_SESSION['org']['kodeorganisasi'] && '9d1bfa25b7c231af0d691b748232fe775829bf37' != sha1($_SESSION['org']['namaorganisasi'])) {
    echo '<script type="text/javascript">alert("You Are Using A Different Serial Number. Please Contact e-Agro Team For A New Serial Number Or Demo.");</script>';
    session_destroy();
    exit();
}

if ('MJR' == $_SESSION['org']['kodeorganisasi'] && 'a1bc34098f04e9e0fd7a3f7ace59eeb898fbb6bc' != sha1($_SESSION['org']['namaorganisasi'])) {
    echo '<script type="text/javascript">alert("You Are Using A Different Serial Number. Please Contact e-Agro Team For A New Serial Number Or Demo.");</script>';
    session_destroy();
    exit();
}

if ('HSS' == $_SESSION['org']['kodeorganisasi'] && 'adb86a13c4973b92d75fccb2223b77272f237f0e' != sha1($_SESSION['org']['namaorganisasi'])) {
    echo '<script type="text/javascript">alert("You Are Using A Different Serial Number. Please Contact e-Agro Team For A New Serial Number Or Demo.");</script>';
    session_destroy();
    exit();
}

if ('BNM' == $_SESSION['org']['kodeorganisasi'] && '405544042c2e5259398c06e9c9cd7974572ec64e' != sha1($_SESSION['org']['namaorganisasi'])) {
    echo '<script type="text/javascript">alert("You Are Using A Different Serial Number. Please Contact e-Agro Team For A New Serial Number Or Demo.");</script>';
    session_destroy();
    exit();
}*/

echo "                                                                                                                                                                                                                                                                                                                                                                                                                                                                             \r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n";

?>