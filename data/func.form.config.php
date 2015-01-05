<?php
/**
 * 配置表单
 */
function config_form(){
	global $cfg;
	global $current_dir, $fm_self, $doc_root, $path_info, $fm_current_root, $lang, $error_reporting, $version;
	global $config_action, $newpass, $newlang, $newerror, $newfm_root;
	$data = array();
	$Warning1 = "";
	$Warning2 = "";
	switch($config_action){
		case 1:
			$update_msg = file_get_contents("http://www.loveyu.net/Update/slfm.php?version=".$version);
			$update_msg = json_decode($update_msg,true);
			if(!is_array($update_msg) || !isset($update_msg['top_version'])){
				unset($update_msg);
			}
			break;
		case 2:
			$reload = false;
			if($cfg->data['lang'] != $newlang){
				$cfg->data['lang'] = $newlang;
				$lang = $newlang;
				$reload = true;
			}
			if($cfg->data['error_reporting'] != $newerror){
				$cfg->data['error_reporting'] = $newerror;
				$error_reporting = $newerror;
				$reload = true;
			}
			$newfm_root = format_path($newfm_root);
			if($cfg->data['fm_root'] != $newfm_root){
				$cfg->data['fm_root'] = $newfm_root;
				if(strlen($newfm_root)){
					$current_dir = $newfm_root;
				} else{
					$current_dir = $path_info["dirname"] . "/";
				}
				setcookie("fm_current_root", $newfm_root, 0, "/");
				$reload = true;
			}
			$cfg->save();
			if($reload){
				reloadframe("window.opener.parent", 2);
				reloadframe("window.opener.parent", 3);
			}
			$Warning1 = et('ConfSaved') . "...";
			break;
		case 3:
			if($cfg->data['auth_pass'] != md5($newpass)){
				$cfg->data['auth_pass'] = md5($newpass);
				setcookie("loggedon", md5($newpass), 0, "/");
			}
			$cfg->save();
			$Warning2 = et('PassSaved') . "...";
			break;
	}
	html_header();
	echo "<body marginwidth=\"0\" marginheight=\"0\">\n";
	echo "
    <table border=0 cellspacing=0 cellpadding=5 align=center width=\"100%\">
    <tr><td colspan=2 align=center><b>" . uppercase(et('Configurations')) . "</b></td></tr>
    </table>
    <table border=0 cellspacing=0 cellpadding=5 align=center width=\"100%\">
	<form>
    <tr><td align=right width=\"1%\">" . et('Version') . ":<td>$version (" . get_size($fm_self) . ")</td></tr>
    <tr><td align=right>" . et('Website') . ":<td><a href=\"JavaScript:open_win('http://www.loveyu.net/slfm')\">http://www.loveyu.net/slfm</a>&nbsp;&nbsp;&nbsp;<input type=button value=\"" . et('ChkVer') . "\" onclick=\"test_config_form(1)\"></td></tr>
	</form>";
	if(isset($update_msg)){
		$flag = version_compare($version,$update_msg['top_version'],"<");
		if($flag){
			echo "<tr><td align=right>" . et('UpdateResult') . ":</td><td style=\"color: #f51\"><strong>(".$update_msg['top_version'].")</strong>,<a href=\"".$update_msg['top_download']."\">".et("ChkVerAvailable")."</a></a></a></td></tr>";
		}else{
			echo "<tr><td align=right>" . et('UpdateResult') . ":</td><td>".et("ChkVerNotAvailable")."</td></tr>";
		}
	}
	echo "
	</td></tr>
    <form name=\"config_form\" action=\"" . $path_info["basename"] . "\" method=\"post\">
    <input type=hidden name=action value=2>
    <input type=hidden name=config_action value=0>
    <tr><td align=right width=1><nobr>" . et('DocRoot') . ":</nobr><td>" . $doc_root . "</td></tr>
    <tr><td align=right><nobr>" . et('FLRoot') . ":</nobr><td><input type=text size=60 name=newfm_root value=\"" . $cfg->data['fm_root'] . "\" onkeypress=\"enterSubmit(event,'test_config_form(2)')\"></td></tr>
    <tr><td align=right>" . et('Lang') . ":<td>
	<select name=newlang>
		<option value=en>English - by Fabricio Seger Kolling</option>
    	<option value=zh>Chinese - by @loveyu</option>
    	<option value=cat>Catalan - by Pere Borràs AKA @Norl</option>
        <option value=nl>Dutch - by Leon Buijs</option>
		<option value=fr1>French - by Jean Bilwes</option>
        <option value=fr2>French - by Sharky</option>
        <option value=fr3>French - by Michel Lainey</option>
		<option value=de1>German - by Guido Ogrzal</option>
        <option value=de2>German - by AXL</option>
        <option value=de3>German - by Mathias Rothe</option>
        <option value=it1>Italian - by Valerio Capello</option>
        <option value=it2>Italian - by Federico Corrà</option>
        <option value=it3>Italian - by Luca Zorzi</option>
        <option value=it4>Italian - by Gianni</option>
		<option value=kr>Korean - by Airplanez</option>
		<option value=pt>Portuguese - by Fabricio Seger Kolling</option>
		<option value=es>Spanish - by Sh Studios</option>
        <option value=ru>Russian - by Евгений Рашев</option>
        <option value=tr>Turkish - by Necdet Yazilimlari</option>
	</select></td></tr>
    <tr><td align=right>" . et('ErrorReport') . ":<td><select name=newerror>
	<option value=\"0\">" . et("Disabled Errors") . "</option>
	<option value=\"1\">" . et("Show Errors") . "</option>
	<option value=\"2\">" . et("Show All Errors") . "</option>
	</select></td></tr>
    <tr><td> <td><input type=button value=\"" . et('SaveConfig') . "\" onclick=\"test_config_form(2)\">";
	if(strlen($Warning1)){
		echo " <font color=red>$Warning1</font>";
	}
	echo "
    <tr><td align=right>" . et('Pass') . ":<td><input type=password size=30 name=newpass value=\"\" onkeypress=\"enterSubmit(event,'test_config_form(3)')\"></td></tr>
    <tr><td> <td><input type=button value=\"" . et('SavePass') . "\" onclick=\"test_config_form(3)\">";
	if(strlen($Warning2)){
		echo " <font color=red>$Warning2</font>";
	}
	echo "</td></tr>";
	echo "
    </form>
    </table>
    <script language=\"Javascript\" type=\"text/javascript\">
        function set_select(sel,val){
            for(var x=0;x<sel.length;x++){
                if(sel.options[x].value==val){
                    sel.options[x].selected=true;
                    break;
                }
            }
        }
        set_select(document.config_form.newlang,'" . $cfg->data['lang'] . "');
        set_select(document.config_form.newerror,'" . $cfg->data['error_reporting'] . "');
        function test_config_form(arg){
            document.config_form.config_action.value = arg;
            document.config_form.submit();
        }
        function open_win(url){
            var w = 800;
            var h = 600;
            window.open(url, '', 'width='+w+',height='+h+',fullscreen=no,scrollbars=yes,resizable=yes,status=yes,toolbar=yes,menubar=yes,location=yes');
        }
        window.moveTo((window.screen.width-600)/2,((window.screen.height-400)/2)-20);
        window.focus();
    </script>
    ";
	echo "</body>\n</html>";
}
