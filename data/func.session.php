<?php
/**
 * User: loveyu
 * Date: 2015/1/5
 * Time: 0:08
 */

function logout(){
	setcookie("loggedon", 0, 0, "/");
	login_form();
}

/**
 *
 */
function login(){
	global $pass, $auth_pass, $path_info;
	if(md5(trim($pass)) == $auth_pass){
		setcookie("loggedon", $auth_pass, 0, "/");
		header("Location: " . $path_info["basename"] . "");
	} else{
		header("Location: " . $path_info["basename"] . "?erro=1");
	}
}

/**
 *
 */
function login_form(){
	global $erro, $auth_pass, $path_info;
	html_header();
	echo "<body onLoad=\"if(parent.location.href != self.location.href){ parent.location.href = self.location.href } return true;\">\n";
	if($auth_pass != md5("")){
		echo "
        <table border=0 cellspacing=0 cellpadding=5>
            <form name=\"login_form\" action=\"" . $path_info["basename"] . "\" method=\"post\">
            <tr>
            <td><b>" . et('FileMan') . "</b>
            </tr>
            <tr>
            <td align=left><font size=4>" . et('TypePass') . ".</font>
            </tr>
            <tr>
            <td><input name=pass type=password size=10> <input type=submit value=\"" . et('Send') . "\">
            </tr>
        ";
		if(strlen($erro)){
			echo "
            <tr>
            <td align=left><font color=red size=4>" . et('InvPass') . ".</font>
            </tr>
        ";
		}
		echo "
            </form>
        </table>
             <script language=\"Javascript\" type=\"text/javascript\">
             <!--
             document.login_form.pass.focus();
             //-->
             </script>
        ";
	} else{
		echo "
        <table border=0 cellspacing=0 cellpadding=5>
            <form name=\"login_form\" action=\"" . $path_info["basename"] . "\" method=\"post\">
            <input type=hidden name=frame value=3>
            <input type=hidden name=pass value=\"\">
            <tr>
            <td><b>" . et('FileMan') . "</b>
            </tr>
            <tr>
            <td><input type=submit value=\"" . et('Enter') . "\">
            </tr>
            </form>
        </table>
        ";
	}
	echo "</body>\n</html>";
}