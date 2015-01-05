<?php
/**
 * User: loveyu
 * Date: 2015/1/5
 * Time: 0:12
 */
/**
 * 输出文件列表
 */
function dir_list_form(){
	global $fm_current_root, $current_dir, $quota_mb, $resolveIDs, $order_dir_list_by, $islinux, $cmd_name, $ip, $path_info, $fm_color;
	$ti = getmicrotime();
	clearstatcache();
	$out = "<table border=0 cellspacing=1 cellpadding=4 width=\"100%\" bgcolor=\"#eeeeee\">\n";
	$current_dir = preg_replace("/[\\/]{2,}/", "/", $current_dir . "/");
	$current_sys_dir = nameToSys($current_dir);
	if($opdir = @opendir($current_sys_dir)){
		$has_files = false;
		$entry_count = 0;
		$total_size = 0;
		$entry_list = array();
		while($file = readdir($opdir)){
			if(($file != ".") && ($file != "..")){
				$entry_list[$entry_count]["size"] = 0;
				$entry_list[$entry_count]["sizet"] = 0;
				$entry_list[$entry_count]["type"] = "none";
				if(is_file($current_sys_dir . $file)){
					$ext = lowercase(strrchr($file, "."));
					$entry_list[$entry_count]["type"] = "file";
					// Função filetype() returns only "file"...
					$entry_list[$entry_count]["size"] = filesize($current_sys_dir . $file);
					$entry_list[$entry_count]["sizet"] = format_size($entry_list[$entry_count]["size"]);
					if(strstr($ext, ".")){
						$entry_list[$entry_count]["ext"] = $ext;
						$entry_list[$entry_count]["extt"] = $ext;
					} else{
						$entry_list[$entry_count]["ext"] = "";
						$entry_list[$entry_count]["extt"] = "&nbsp;";
					}
					$has_files = true;
				} elseif(is_dir($current_sys_dir . $file)){
					// Recursive directory size disabled
					// $entry_list[$entry_count]["size"] = total_size($current_dir.$file);
					$entry_list[$entry_count]["size"] = 0;
					$entry_list[$entry_count]["sizet"] = "&nbsp;";
					$entry_list[$entry_count]["type"] = "dir";
				}
				$entry_list[$entry_count]["name"] = $file;
				$entry_list[$entry_count]["display_name"] = nameToPage($file);
				$entry_list[$entry_count]["date"] = date("Ymd", @filemtime($current_sys_dir . $file));
				$entry_list[$entry_count]["time"] = date("his", @filemtime($current_sys_dir . $file));
				$entry_list[$entry_count]["datet"] = date("Y-m-d H:i:s", @filemtime($current_sys_dir . $file));
				if($islinux && $resolveIDs){
					$entry_list[$entry_count]["p"] = show_perms(@fileperms($current_sys_dir . $file));
					$entry_list[$entry_count]["u"] = get_user(@fileowner($current_sys_dir . $file));
					$entry_list[$entry_count]["g"] = get_group(@filegroup($current_sys_dir . $file));
				} else{
					$entry_list[$entry_count]["p"] = base_convert(@fileperms($current_sys_dir . $file), 10, 8);
					$entry_list[$entry_count]["p"] = substr($entry_list[$entry_count]["p"], strlen($entry_list[$entry_count]["p"]) - 3);
					$entry_list[$entry_count]["u"] = @fileowner($current_sys_dir . $file);
					$entry_list[$entry_count]["g"] = @filegroup($current_sys_dir . $file);
				}
				$total_size += $entry_list[$entry_count]["size"];
				$entry_count++;
			}
		}
		@closedir($opdir);

		if($entry_count){
			$or1 = "1A";
			$or2 = "2D";
			$or3 = "3A";
			$or4 = "4A";
			$or5 = "5A";
			$or6 = "6D";
			$or7 = "7D";
			switch($order_dir_list_by){
				case "1A":
					$entry_list = array_csort($entry_list, "type", SORT_STRING, SORT_ASC, "name", SORT_STRING, SORT_ASC);
					$or1 = "1D";
					break;
				case "1D":
					$entry_list = array_csort($entry_list, "type", SORT_STRING, SORT_ASC, "name", SORT_STRING, SORT_DESC);
					$or1 = "1A";
					break;
				case "2A":
					$entry_list = array_csort($entry_list, "type", SORT_STRING, SORT_ASC, "p", SORT_STRING, SORT_ASC, "g", SORT_STRING, SORT_ASC, "u", SORT_STRING, SORT_ASC);
					$or2 = "2D";
					break;
				case "2D":
					$entry_list = array_csort($entry_list, "type", SORT_STRING, SORT_ASC, "p", SORT_STRING, SORT_DESC, "g", SORT_STRING, SORT_ASC, "u", SORT_STRING, SORT_ASC);
					$or2 = "2A";
					break;
				case "3A":
					$entry_list = array_csort($entry_list, "type", SORT_STRING, SORT_ASC, "u", SORT_STRING, SORT_ASC, "g", SORT_STRING, SORT_ASC);
					$or3 = "3D";
					break;
				case "3D":
					$entry_list = array_csort($entry_list, "type", SORT_STRING, SORT_ASC, "u", SORT_STRING, SORT_DESC, "g", SORT_STRING, SORT_ASC);
					$or3 = "3A";
					break;
				case "4A":
					$entry_list = array_csort($entry_list, "type", SORT_STRING, SORT_ASC, "g", SORT_STRING, SORT_ASC, "u", SORT_STRING, SORT_DESC);
					$or4 = "4D";
					break;
				case "4D":
					$entry_list = array_csort($entry_list, "type", SORT_STRING, SORT_ASC, "g", SORT_STRING, SORT_DESC, "u", SORT_STRING, SORT_DESC);
					$or4 = "4A";
					break;
				case "5A":
					$entry_list = array_csort($entry_list, "type", SORT_STRING, SORT_ASC, "size", SORT_NUMERIC, SORT_ASC);
					$or5 = "5D";
					break;
				case "5D":
					$entry_list = array_csort($entry_list, "type", SORT_STRING, SORT_ASC, "size", SORT_NUMERIC, SORT_DESC);
					$or5 = "5A";
					break;
				case "6A":
					$entry_list = array_csort($entry_list, "type", SORT_STRING, SORT_ASC, "date", SORT_STRING, SORT_ASC, "time", SORT_STRING, SORT_ASC, "name", SORT_STRING, SORT_ASC);
					$or6 = "6D";
					break;
				case "6D":
					$entry_list = array_csort($entry_list, "type", SORT_STRING, SORT_ASC, "date", SORT_STRING, SORT_DESC, "time", SORT_STRING, SORT_DESC, "name", SORT_STRING, SORT_ASC);
					$or6 = "6A";
					break;
				case "7A":
					$entry_list = array_csort($entry_list, "type", SORT_STRING, SORT_ASC, "ext", SORT_STRING, SORT_ASC, "name", SORT_STRING, SORT_ASC);
					$or7 = "7D";
					break;
				case "7D":
					$entry_list = array_csort($entry_list, "type", SORT_STRING, SORT_ASC, "ext", SORT_STRING, SORT_DESC, "name", SORT_STRING, SORT_ASC);
					$or7 = "7A";
					break;
			}
		}
		$out .= "
        <script language=\"Javascript\" type=\"text/javascript\">
        function go(arg) {
            document.location.href='" . addslashes($path_info["basename"]) . "?frame=3&current_dir=" . addslashes($current_dir) . "'+arg+'/';
        }
        function resolveIDs() {
            document.location.href='" . addslashes($path_info["basename"]) . "?frame=3&set_resolveIDs=1&current_dir=" . addslashes($current_dir) . "';
        }
        var entry_list = new Array();
        // Custom object constructor
        function entry(name, type, size, selected){
            this.name = name;
            this.type = type;
            this.size = size;
            this.selected = false;
        }
        // Declare entry_list for selection procedures";
		foreach($entry_list as $i => $data){
			$out .= "\nentry_list['entry$i'] = new entry('" . addslashes($data["display_name"]) . "', '" . $data["type"] . "', " . $data["size"] . ", false);";
		}
		$out .= "
        // Select/Unselect Rows OnClick/OnMouseOver
        var lastRows = new Array(null,null);
        function selectEntry(Row, Action){
            if (multipleSelection){
                // Avoid repeated onmouseover events from same Row ( cell transition )
                if (Row != lastRows[0]){
                    if (Action == 'over') {
                        if (entry_list[Row.id].selected){
                            if (unselect(entry_list[Row.id])) {
                                Row.className = 'entryUnselected';
                            }
                            // Change the last Row when you change the movement orientation
                            if (lastRows[0] != null && lastRows[1] != null){
                                var LastRowID = lastRows[0].id;
                                if (Row.id == lastRows[1].id){
                                    if (unselect(entry_list[LastRowID])) {
                                        lastRows[0].className = 'entryUnselected';
                                    }
                                }
                            }
                        } else {
                            if (select(entry_list[Row.id])){
                                Row.className = 'entrySelected';
                            }
                            // Change the last Row when you change the movement orientation
                            if (lastRows[0] != null && lastRows[1] != null){
                                var LastRowID = lastRows[0].id;
                                if (Row.id == lastRows[1].id){
                                    if (select(entry_list[LastRowID])) {
                                        lastRows[0].className = 'entrySelected';
                                    }
                                }
                            }
                        }
                        lastRows[1] = lastRows[0];
                        lastRows[0] = Row;
                    }
                }
            } else {
                if (Action == 'click') {
                    var newClassName = null;
                    if (entry_list[Row.id].selected){
                        if (unselect(entry_list[Row.id])) newClassName = 'entryUnselected';
                    } else {
                        if (select(entry_list[Row.id])) newClassName = 'entrySelected';
                    }
                    if (newClassName) {
                        lastRows[0] = lastRows[1] = Row;
                        Row.className = newClassName;
                    }
                }
            }
            return true;
        }
        // Disable text selection and bind multiple selection flag
        var multipleSelection = false;
        if (is.ie) {
            document.onselectstart=new Function('return false');
            document.onmousedown=switch_flag_on;
            document.onmouseup=switch_flag_off;
            // Event mouseup is not generated over scrollbar.. curiously, mousedown is.. go figure.
            window.onscroll=new Function('multipleSelection=false');
            window.onresize=new Function('multipleSelection=false');
        } else {
            if (document.layers) window.captureEvents(Event.MOUSEDOWN);
            if (document.layers) window.captureEvents(Event.MOUSEUP);
            window.onmousedown=switch_flag_on;
            window.onmouseup=switch_flag_off;
        }
        // Using same function and a ternary operator couses bug on double click
        function switch_flag_on(e) {
            if (is.ie){
                multipleSelection = (event.button == 1);
            } else {
                multipleSelection = (e.which == 1);
            }
			var type = String(e.target.type);
			return (type.indexOf('select') != -1 || type.indexOf('button') != -1 || type.indexOf('input') != -1 || type.indexOf('radio') != -1);
        }
        function switch_flag_off(e) {
            if (is.ie){
                multipleSelection = (event.button != 1);
            } else {
                multipleSelection = (e.which != 1);
            }
            lastRows[0] = lastRows[1] = null;
            update_sel_status();
            return false;
        }
        var total_dirs_selected = 0;
        var total_files_selected = 0;
        function unselect(Entry){
            if (!Entry.selected) return false;
            Entry.selected = false;
            sel_totalsize -= Entry.size;
            if (Entry.type == 'dir') total_dirs_selected--;
            else total_files_selected--;
            return true;
        }
        function select(Entry){
            if(Entry.selected) return false;
            Entry.selected = true;
            sel_totalsize += Entry.size;
            if(Entry.type == 'dir') total_dirs_selected++;
            else total_files_selected++;
            return true;
        }
        function is_anything_selected(){
            var selected_dir_list = new Array();
            var selected_file_list = new Array();
            for(var x=0;x<" . (integer)count($entry_list) . ";x++){
                if(entry_list['entry'+x].selected){
                    if(entry_list['entry'+x].type == 'dir') selected_dir_list.push(entry_list['entry'+x].name);
                    else selected_file_list.push(entry_list['entry'+x].name);
                }
            }
            document.form_action.selected_dir_list.value = selected_dir_list.join('<|*|>');
            document.form_action.selected_file_list.value = selected_file_list.join('<|*|>');
            return (total_dirs_selected>0 || total_files_selected>0);
        }
        function format_size (arg) {
            var resul = '';
            if (arg>0){
                var j = 0;
                var ext = new Array(' bytes',' Kb',' Mb',' Gb',' Tb');
                while (arg >= Math.pow(1024,j)) ++j;
                resul = (Math.round(arg/Math.pow(1024,j-1)*100)/100) + ext[j-1];
            } else resul = 0;
            return resul;
        }
        var sel_totalsize = 0;
        function update_sel_status(){
            var t = total_dirs_selected+' " . et('Dir_s') . " " . et('And') . " '+total_files_selected+' " . et('File_s') . " " . et('Selected_s') . " = '+format_size(sel_totalsize);
            //document.getElementById(\"sel_status\").innerHTML = t;
            window.status = t;
        }
        // Select all/none/inverse
        function selectANI(Butt){
        	cancel_copy_move();
            for(var x=0;x<" . (integer)count($entry_list) . ";x++){
                var Row = document.getElementById('entry'+x);
                var newClassName = null;
                switch (Butt.value){
                    case '" . et('SelAll') . "':
                        if (select(entry_list[Row.id])) newClassName = 'entrySelected';
                    break;
                    case '" . et('SelNone') . "':
                        if (unselect(entry_list[Row.id])) newClassName = 'entryUnselected';
                    break;
                    case '" . et('SelInverse') . "':
                        if (entry_list[Row.id].selected){
                            if (unselect(entry_list[Row.id])) newClassName = 'entryUnselected';
                        } else {
                            if (select(entry_list[Row.id])) newClassName = 'entrySelected';
                        }
                    break;
                }
                if (newClassName) {
                    Row.className = newClassName;
                }
            }
            if (Butt.value == '" . et('SelAll') . "'){
                for(var i=0;i<2;i++){
                    document.getElementById('ANI'+i).value='" . et('SelNone') . "';
                }
            } else if (Butt.value == '" . et('SelNone') . "'){
                for(var i=0;i<2;i++){
                    document.getElementById('ANI'+i).value='" . et('SelAll') . "';
                }
            }
            update_sel_status();
            return true;
        }
        function download(arg){
            parent.frame1.location.href='" . addslashes($path_info["basename"]) . "?action=3&current_dir=" . addslashes($current_dir) . "&filename='+encodeURIComponent(arg);
        }
        function upload(){
            var w = 600;
            var h = 450;
            window.open('" . addslashes($path_info["basename"]) . "?action=10&current_dir=" . addslashes($current_dir) . "', '', 'width='+w+',height='+h+',fullscreen=no,scrollbars=no,resizable=yes,status=no,toolbar=no,menubar=no,location=no');
        }
        function execute_cmd(){
            var arg = prompt('" . et('TypeCmd') . ".');
            if(arg && arg.length>0){
                if(confirm('" . et('ConfExec') . " \\' '+arg+' \\' ?')) {
                    var w = 800;
                    var h = 600;
                    window.open('" . addslashes($path_info["basename"]) . "?action=6&current_dir=" . addslashes($current_dir) . "&cmd='+encodeURIComponent(arg), '', 'width='+w+',height='+h+',fullscreen=no,scrollbars=yes,resizable=yes,status=no,toolbar=no,menubar=no,location=no');
                }
            }
        }
        function decompress(arg){
            if(confirm('" . uppercase(et('Decompress')) . " \\' '+arg+' \\' ?')) {
                document.form_action.action.value = 72;
                document.form_action.cmd_arg.value = arg;
                document.form_action.submit();
            }
        }
        function execute_file(arg){
            if(arg.length>0){
                if(confirm('" . et('ConfExec') . " \\' '+arg+' \\' ?')) {
                    var w = 800;
                    var h = 600;
                    window.open('" . addslashes($path_info["basename"]) . "?action=11&current_dir=" . addslashes($current_dir) . "&filename='+encodeURIComponent(arg), '', 'width='+w+',height='+h+',fullscreen=no,scrollbars=yes,resizable=yes,status=no,toolbar=no,menubar=no,location=no');
                }
            }
        }
        function edit_file(arg){
            var w = 1024;
            var h = 768;
            // if(confirm('" . uppercase(et('Edit')) . " \\' '+arg+' \\' ?'))
            window.open('" . addslashes($path_info["basename"]) . "?action=7&current_dir=" . addslashes($current_dir) . "&filename='+encodeURIComponent(arg), '', 'width='+w+',height='+h+',fullscreen=no,scrollbars=no,resizable=yes,status=no,toolbar=no,menubar=no,location=no');
        }
        function config(){
            var w = 650;
            var h = 400;
            window.open('" . addslashes($path_info["basename"]) . "?action=2', 'win_config', 'width='+w+',height='+h+',fullscreen=no,scrollbars=yes,resizable=yes,status=no,toolbar=no,menubar=no,location=no');
        }
        function server_info(arg){
            var w = 800;
            var h = 600;
            window.open('" . addslashes($path_info["basename"]) . "?action=5', 'win_serverinfo', 'width='+w+',height='+h+',fullscreen=no,scrollbars=yes,resizable=yes,status=no,toolbar=no,menubar=no,location=no');
        }
        function shell(){
            var w = 800;
            var h = 600;
            window.open('" . addslashes($path_info["basename"]) . "?action=9', '', 'width='+w+',height='+h+',fullscreen=no,scrollbars=yes,resizable=yes,status=no,toolbar=no,menubar=no,location=no');
        }
        function view(arg){
            var w = 800;
            var h = 600;
            if(confirm('" . uppercase(et('View')) . " \\' '+arg+' \\' ?')) window.open('" . addslashes($path_info["basename"]) . "?action=4&current_dir=" . addslashes($current_dir) . "&filename='+encodeURIComponent(arg), '', 'width='+w+',height='+h+',fullscreen=no,scrollbars=yes,resizable=yes,status=yes,toolbar=no,menubar=no,location=yes');
        }
        function rename(arg){
            var nome = '';
            if (nome = prompt('" . uppercase(et('Ren')) . " \\' '+arg+' \\' " . et('To') . " ...')) document.location.href='" . addslashes($path_info["basename"]) . "?frame=3&action=3&current_dir=" . addslashes($current_dir) . "&old_name='+encodeURIComponent(arg)+'&new_name='+encodeURIComponent(nome);
        }
        function set_dir_dest(arg){
            document.form_action.dir_dest.value=arg;
            if (document.form_action.action.value.length>0) test(document.form_action.action.value);
            else alert('" . et('JSError') . ".');
        }
        function sel_dir(arg){
            document.form_action.action.value = arg;
            document.form_action.dir_dest.value='';
            if (!is_anything_selected()) alert('" . et('NoSel') . ".');
            else {
                if (!getCookie('sel_dir_warn')) {
                    //alert('" . et('SelDir') . ".');
                    document.cookie='sel_dir_warn'+'='+escape('true')+';';
                }
                set_sel_dir_warn(true);
                parent.frame2.set_flag(true);
            }
        }
		function set_sel_dir_warn(b){
        	document.getElementById(\"sel_dir_warn\").style.display=(b?'':'none');
		}
		function cancel_copy_move(){
           	set_sel_dir_warn(false);
           	parent.frame2.set_flag(false);
		}
        function chmod_form(){
            cancel_copy_move();
            document.form_action.dir_dest.value='';
            document.form_action.chmod_arg.value='';
            if (!is_anything_selected()) alert('" . et('NoSel') . ".');
            else {
                var w = 280;
                var h = 180;
                window.open('" . addslashes($path_info["basename"]) . "?action=8', '', 'width='+w+',height='+h+',fullscreen=no,scrollbars=no,resizable=yes,status=no,toolbar=no,menubar=no,location=no');
            }
        }
        function set_chmod_arg(arg){
            cancel_copy_move();
            if (!is_anything_selected()) alert('" . et('NoSel') . ".');
            else {
            	document.form_action.dir_dest.value='';
            	document.form_action.chmod_arg.value=arg;
            	test(9);
			}
        }
        function test_action(){
            if (document.form_action.action.value != 0) return true;
            else return false;
        }
        function test_prompt(arg){
        	cancel_copy_move();
			var erro='';
            var conf='';
            if (arg == 1){
                document.form_action.cmd_arg.value = prompt('" . et('TypeDir') . ".');
            } else if (arg == 2){
                document.form_action.cmd_arg.value = prompt('" . et('TypeArq') . ".');
            } else if (arg == 71){
                if (!is_anything_selected()) erro = '" . et('NoSel') . ".';
                else document.form_action.cmd_arg.value = prompt('" . et('TypeArqComp') . "');
            }
            if (erro!=''){
                document.form_action.cmd_arg.focus();
                alert(erro);
            } else if(document.form_action.cmd_arg.value.length>0) {
                document.form_action.action.value = arg;
                document.form_action.submit();
            }
        }
        function strstr(haystack,needle){
            var index = haystack.indexOf(needle);
            return (index==-1)?false:index;
        }
        function valid_dest(dest,orig){
            return (strstr(dest,orig)==false)?true:false;
        }
        // ArrayAlert - Selection debug only
        function aa(){
            var str = 'selected_dir_list:\\n';
            for (x=0;x<selected_dir_list.length;x++){
                str += selected_dir_list[x]+'\\n';
            }
            str += '\\nselected_file_list:\\n';
            for (x=0;x<selected_file_list.length;x++){
                str += selected_file_list[x]+'\\n';
            }
            alert(str);
        }
        function test(arg){
        	cancel_copy_move();
            var erro='';
            var conf='';
            if (arg == 4){
                if (!is_anything_selected()) erro = '" . et('NoSel') . ".\\n';
                conf = '" . et('RemSel') . " ?\\n';
            } else if (arg == 5){
                if (!is_anything_selected()) erro = '" . et('NoSel') . ".\\n';
                else if(document.form_action.dir_dest.value.length == 0) erro = '" . et('NoDestDir') . ".';
                else if(document.form_action.dir_dest.value == document.form_action.current_dir.value) erro = '" . et('DestEqOrig') . ".';
                else if(!valid_dest(document.form_action.dir_dest.value,document.form_action.current_dir.value)) erro = '" . et('InvalidDest') . ".';
                conf = '" . et('CopyTo') . " \\' '+document.form_action.dir_dest.value+' \\' ?\\n';
            } else if (arg == 6){
                if (!is_anything_selected()) erro = '" . et('NoSel') . ".';
                else if(document.form_action.dir_dest.value.length == 0) erro = '" . et('NoDestDir') . ".';
                else if(document.form_action.dir_dest.value == document.form_action.current_dir.value) erro = '" . et('DestEqOrig') . ".';
                else if(!valid_dest(document.form_action.dir_dest.value,document.form_action.current_dir.value)) erro = '" . et('InvalidDest') . ".';
                conf = '" . et('MoveTo') . " \\' '+document.form_action.dir_dest.value+' \\' ?\\n';
            } else if (arg == 9){
                if (!is_anything_selected()) erro = '" . et('NoSel') . ".';
                else if(document.form_action.chmod_arg.value.length == 0) erro = '" . et('NoNewPerm') . ".';
                //conf = '" . et('AlterPermTo') . " \\' '+document.form_action.chmod_arg.value+' \\' ?\\n';
            }
            if (erro!=''){
                document.form_action.cmd_arg.focus();
                alert(erro);
            } else if(conf!='') {
                if(confirm(conf)) {
                    document.form_action.action.value = arg;
                    document.form_action.submit();
                } else {
                    set_sel_dir_warn(false);
				}
            } else {
                document.form_action.action.value = arg;
                document.form_action.submit();
            }
        }
        //-->
        </script>";
		if(!isset($dir_before)){
			$dir_before = NULL;
		}
		$out .= "
        <form name=\"form_action\" action=\"" . $path_info["basename"] . "\" method=\"post\" onsubmit=\"return test_action();\">
            <input type=hidden name=\"frame\" value=3>
            <input type=hidden name=\"action\" value=0>
            <input type=hidden name=\"dir_dest\" value=\"\">
            <input type=hidden name=\"chmod_arg\" value=\"\">
            <input type=hidden name=\"cmd_arg\" value=\"\">
            <input type=hidden name=\"current_dir\" value=\"$current_dir\">
            <input type=hidden name=\"dir_before\" value=\"$dir_before\">
            <input type=hidden name=\"selected_dir_list\" value=\"\">
            <input type=hidden name=\"selected_file_list\" value=\"\">";
		$out .= "
            <tr>
            <td bgcolor=\"#DDDDDD\" colspan=50><nobr>
            <input type=button onclick=\"config()\" value=\"" . et('Config') . "\">
            <input type=button onclick=\"server_info()\" value=\"" . et('ServerInfo') . "\">
            <input type=button onclick=\"test_prompt(1)\" value=\"" . et('CreateDir') . "\">
            <input type=button onclick=\"test_prompt(2)\" value=\"" . et('CreateArq') . "\">
            <input type=button onclick=\"execute_cmd()\" value=\"" . et('ExecCmd') . "\">
            <input type=button onclick=\"upload()\" value=\"" . et('Upload') . "\">
            <input type=button onclick=\"shell()\" value=\"" . et('Shell') . "\">
            <input type=button onclick=\"location.reload()\" value=\"" . et('Refresh') . "\">
            <b>$ip</b>
            </nobr>";
		$uplink = "";
		if($current_dir != $fm_current_root){
			$mat = explode("/", $current_dir);
			$dir_before = "";
			for($x = 0; $x < (count($mat) - 2); $x++){
				$dir_before .= $mat[$x] . "/";
			}
			$uplink = "<a href=\"" . $path_info["basename"] . "?frame=3&current_dir=$dir_before\"><<</a> ";
		}
		if($entry_count){
			$out .= "
                <tr bgcolor=\"#DDDDDD\"><td colspan=50><nobr>$uplink <a href=\"" . $path_info["basename"] . "?frame=3&current_dir=$current_dir\">$current_dir</a></nobr>
                <tr>
                <td bgcolor=\"#DDDDDD\" colspan=50><nobr>
                    <input type=\"button\" style=\"width:80\" onclick=\"selectANI(this)\" id=\"ANI0\" value=\"" . et('SelAll') . "\">
                    <input type=\"button\" style=\"width:80\" onclick=\"selectANI(this)\" value=\"" . et('SelInverse') . "\">
                    <input type=\"button\" style=\"width:80\" onclick=\"test(4)\" value=\"" . et('Rem') . "\">
                    <input type=\"button\" style=\"width:80\" onclick=\"sel_dir(5)\" value=\"" . et('Copy') . "\">
                    <input type=\"button\" style=\"width:80\" onclick=\"sel_dir(6)\" value=\"" . et('Move') . "\">
                    <input type=\"button\" style=\"width:100\" onclick=\"test_prompt(71)\" value=\"" . et('Compress') . "\">";
			if($islinux){
				$out .= "
                    <input type=\"button\" style=\"width:100\" onclick=\"resolveIDs()\" value=\"" . et('ResolveIDs') . "\">";
			}
			$out .= "
                    <input type=\"button\" style=\"width:100\" onclick=\"chmod_form()\" value=\"" . et('Perms') . "\">";
			$out .= "
                </nobr></td>
                </tr>
				<tr>
                <td bgcolor=\"#DDDDDD\" colspan=50 id=\"sel_dir_warn\" style=\"display:none\"><nobr><font color=\"red\">" . et('SelDir') . "...</font></nobr></td>
                </tr>";
			$file_count = 0;
			$dir_count = 0;
			$dir_out = array();
			$file_out = array();
			$max_opt = 0;
			foreach($entry_list as $ind => $dir_entry){
				$file = $dir_entry["name"];
				$display = $dir_entry['display_name'];
				if($dir_entry["type"] == "dir"){
					$dir_out[$dir_count] = array();
					$dir_out[$dir_count][] = "
                        <tr ID=\"entry$ind\" class=\"entryUnselected\" onmouseover=\"selectEntry(this, 'over');\" onmousedown=\"selectEntry(this, 'click');\">
                        <td><nobr><a href=\"JavaScript:go('" . addslashes($display) . "')\">$display</a></nobr></td>";
					$dir_out[$dir_count][] = "<td>" . $dir_entry["p"] . "</td>";
					if($islinux){
						$dir_out[$dir_count][] = "<td><nobr>" . $dir_entry["u"] . "</nobr></td>";
						$dir_out[$dir_count][] = "<td><nobr>" . $dir_entry["g"] . "</nobr></td>";
					}
					$dir_out[$dir_count][] = "<td><nobr>" . $dir_entry["sizet"] . "</nobr></td>";
					$dir_out[$dir_count][] = "<td><nobr>" . $dir_entry["datet"] . "</nobr></td>";
					if($has_files){
						$dir_out[$dir_count][] = "<td>&nbsp;</td>";
					}
					// Opções de diretório
					if(is_writable($current_dir . $file)){
						$dir_out[$dir_count][] = "
                        <td align=center><a href=\"JavaScript:if(confirm('" . et('ConfRem') . " \\'" . addslashes($display) . "\\' ?')) document.location.href='" . addslashes($path_info["basename"]) . "?frame=3&action=8&cmd_arg=" . addslashes($display) . "&current_dir=" . addslashes($current_dir) . "'\">" . et('Rem') . "</a>";
					}
					if(is_writable($current_dir . $file)){
						$dir_out[$dir_count][] = "
                        <td align=center><a href=\"JavaScript:rename('" . addslashes($display) . "')\">" . et('Ren') . "</a>";
					}
					if(count($dir_out[$dir_count]) > $max_opt){
						$max_opt = count($dir_out[$dir_count]);
					}
					$dir_count++;
				} else{
					$file_out[$file_count] = array();
					$file_out[$file_count][] = "
                        <tr ID=\"entry$ind\" class=\"entryUnselected\" onmouseover=\"selectEntry(this, 'over');\" onmousedown=\"selectEntry(this, 'click');\">
                        <td><nobr><a href=\"JavaScript:download('" . addslashes($display) . "')\">$display</a></nobr></td>";
					$file_out[$file_count][] = "<td>" . $dir_entry["p"] . "</td>";
					if($islinux){
						$file_out[$file_count][] = "<td><nobr>" . $dir_entry["u"] . "</nobr></td>";
						$file_out[$file_count][] = "<td><nobr>" . $dir_entry["g"] . "</nobr></td>";
					}
					$file_out[$file_count][] = "<td><nobr>" . $dir_entry["sizet"] . "</nobr></td>";
					$file_out[$file_count][] = "<td><nobr>" . $dir_entry["datet"] . "</nobr></td>";
					$file_out[$file_count][] = "<td>" . (isset($dir_entry["extt"]) ? $dir_entry["extt"] : "无") . "</td>";
					if(!isset($dir_entry["ext"])){
						$dir_entry["ext"] = "";
					}
					if(is_writable($current_dir . $file)){
						$file_out[$file_count][] = "
                                <td align=center><a href=\"javascript:if(confirm('" . uppercase(et('Rem')) . " \\'" . addslashes($display) . "\\' ?')) document.location.href='" . addslashes($path_info["basename"]) . "?frame=3&action=8&cmd_arg=" . addslashes($display) . "&current_dir=" . addslashes($current_dir) . "'\">" . et('Rem') . "</a>";
					} else{
						$file_out[$file_count][] = "<td>&nbsp;</td>";
					}
					if(is_writable($current_dir . $file)){
						$file_out[$file_count][] = "
                                <td align=center><a href=\"javascript:rename('" . addslashes($display) . "')\">" . et('Ren') . "</a>";
					} else{
						$file_out[$file_count][] = "<td>&nbsp;</td>";
					}
					if(is_readable($current_dir . $file) && (strpos(".wav#.mp3#.mid#.avi#.mov#.mpeg#.mpg#.rm#.iso#.bin#.img#.dll#.psd#.fla#.swf#.class#.ppt#.tif#.tiff#.pcx#.jpg#.gif#.png#.wmf#.eps#.bmp#.msi#.exe#.com#.rar#.tar#.zip#.bz2#.tbz2#.bz#.tbz#.bzip#.gzip#.gz#.tgz#", $dir_entry["ext"] . "#") === false)){
						$file_out[$file_count][] = "
                                <td align=center><a href=\"javascript:edit_file('" . addslashes($display) . "')\">" . et('Edit') . "</a>";
					} else{
						$file_out[$file_count][] = "<td>&nbsp;</td>";
					}
					if(is_readable($current_dir . $file) && (strpos(".txt#.sys#.bat#.ini#.conf#.swf#.php#.php3#.asp#.html#.htm#.jpg#.gif#.png#.bmp#", $dir_entry["ext"] . "#") !== false)){
						$file_out[$file_count][] = "
                                <td align=center><a href=\"javascript:view('" . addslashes($display) . "');\">" . et('View') . "</a>";
					} else{
						$file_out[$file_count][] = "<td>&nbsp;</td>";
					}
					if(is_readable($current_dir . $file) && strlen($dir_entry["ext"]) && (strpos(".tar#.zip#.bz2#.tbz2#.bz#.tbz#.bzip#.gzip#.gz#.tgz#", $dir_entry["ext"] . "#") !== false)){
						$file_out[$file_count][] = "
                                <td align=center><a href=\"javascript:decompress('" . addslashes($display) . "')\">" . et('Decompress') . "</a>";
					} else{
						$file_out[$file_count][] = "<td>&nbsp;</td>";
					}
					if(is_readable($current_dir . $file) && strlen($dir_entry["ext"]) && (strpos(".exe#.com#.sh#.bat#", $dir_entry["ext"] . "#") !== false)){
						$file_out[$file_count][] = "
                                <td align=center><a href=\"javascript:execute_file('" . addslashes($display) . "')\">" . et('Exec') . "</a>";
					} else{
						$file_out[$file_count][] = "<td>&nbsp;</td>";
					}
					if(count($file_out[$file_count]) > $max_opt){
						$max_opt = count($file_out[$file_count]);
					}
					$file_count++;
				}
			}
			if($dir_count){
				$out .= "
                <tr>
                      <td bgcolor=\"#DDDDDD\"><nobr><a href=\"" . $path_info["basename"] . "?frame=3&or_by=$or1&current_dir=$current_dir\">" . et('Name') . "</a></nobr></td>
                      <td bgcolor=\"#DDDDDD\"><nobr><a href=\"" . $path_info["basename"] . "?frame=3&or_by=$or2&current_dir=$current_dir\">" . et('Perm') . "</a></nobr></td>";
				if($islinux){
					$out .= "
                      <td bgcolor=\"#DDDDDD\"><nobr><a href=\"" . $path_info["basename"] . "?frame=3&or_by=$or3&current_dir=$current_dir\">" . et('Owner') . "</a></td>
                      <td bgcolor=\"#DDDDDD\"><nobr><a href=\"" . $path_info["basename"] . "?frame=3&or_by=$or4&current_dir=$current_dir\">" . et('Group') . "</a></nobr></td>";
				}
				$out .= "
                      <td bgcolor=\"#DDDDDD\"><nobr><a href=\"" . $path_info["basename"] . "?frame=3&or_by=$or5&current_dir=$current_dir\">" . et('Size') . "</a></nobr></td>
                      <td bgcolor=\"#DDDDDD\"><nobr><a href=\"" . $path_info["basename"] . "?frame=3&or_by=$or6&current_dir=$current_dir\">" . et('Date') . "</a></nobr></td>";
				if($file_count){
					$out .= "
                      <td bgcolor=\"#DDDDDD\"><nobr><a href=\"" . $path_info["basename"] . "?frame=3&or_by=$or7&current_dir=$current_dir\">" . et('Type') . "</a></nobr></td>";
				}
				$out .= "
                      <td bgcolor=\"#DDDDDD\" colspan=50>&nbsp;</td>
                </tr>";

			}
			foreach($dir_out as $k => $v){
				while(count($dir_out[$k]) < $max_opt){
					$dir_out[$k][] = "<td>&nbsp;</td>";
				}
				$out .= implode($dir_out[$k]);
				$out .= "</tr>";
			}
			if($file_count){
				$out .= "
                <tr>
                      <td bgcolor=\"#DDDDDD\"><nobr><a href=\"" . $path_info["basename"] . "?frame=3&or_by=$or1&current_dir=$current_dir\">" . et('Name') . "</a></nobr></td>
                      <td bgcolor=\"#DDDDDD\"><nobr><a href=\"" . $path_info["basename"] . "?frame=3&or_by=$or2&current_dir=$current_dir\">" . et('Perm') . "</a></nobr></td>";
				if($islinux){
					$out .= "
                      <td bgcolor=\"#DDDDDD\"><nobr><a href=\"" . $path_info["basename"] . "?frame=3&or_by=$or3&current_dir=$current_dir\">" . et('Owner') . "</a></td>
                      <td bgcolor=\"#DDDDDD\"><nobr><a href=\"" . $path_info["basename"] . "?frame=3&or_by=$or4&current_dir=$current_dir\">" . et('Group') . "</a></nobr></td>";
				}
				$out .= "
                      <td bgcolor=\"#DDDDDD\"><nobr><a href=\"" . $path_info["basename"] . "?frame=3&or_by=$or5&current_dir=$current_dir\">" . et('Size') . "</a></nobr></td>
                      <td bgcolor=\"#DDDDDD\"><nobr><a href=\"" . $path_info["basename"] . "?frame=3&or_by=$or6&current_dir=$current_dir\">" . et('Date') . "</a></nobr></td>
                      <td bgcolor=\"#DDDDDD\"><nobr><a href=\"" . $path_info["basename"] . "?frame=3&or_by=$or7&current_dir=$current_dir\">" . et('Type') . "</a></nobr></td>
                      <td bgcolor=\"#DDDDDD\" colspan=50>&nbsp;</td>
                </tr>";

			}
			foreach($file_out as $k => $v){
				while(count($file_out[$k]) < $max_opt){
					$file_out[$k][] = "<td>&nbsp;</td>";
				}
				$out .= implode($file_out[$k]);
				$out .= "</tr>";
			}
			$out .= "
                <tr>
                <td bgcolor=\"#DDDDDD\" colspan=50><nobr>
                      <input type=\"button\" style=\"width:80\" onclick=\"selectANI(this)\" id=\"ANI1\" value=\"" . et('SelAll') . "\">
                      <input type=\"button\" style=\"width:80\" onclick=\"selectANI(this)\" value=\"" . et('SelInverse') . "\">
                      <input type=\"button\" style=\"width:80\" onclick=\"test(4)\" value=\"" . et('Rem') . "\">
                      <input type=\"button\" style=\"width:80\" onclick=\"sel_dir(5)\" value=\"" . et('Copy') . "\">
                      <input type=\"button\" style=\"width:80\" onclick=\"sel_dir(6)\" value=\"" . et('Move') . "\">
                      <input type=\"button\" style=\"width:100\" onclick=\"test_prompt(71)\" value=\"" . et('Compress') . "\">";
			if($islinux){
				$out .= "
                      <input type=\"button\" style=\"width:100\" onclick=\"resolveIDs()\" value=\"" . et('ResolveIDs') . "\">";
			}
			$out .= "
                      <input type=\"button\" style=\"width:100\" onclick=\"chmod_form()\" value=\"" . et('Perms') . "\">";
			$out .= "
                </nobr></td>
                </tr>";
			$out .= "
            </form>";
			$out .= "
                <tr><td bgcolor=\"#DDDDDD\" colspan=50><b>$dir_count " . et('Dir_s') . " " . et('And') . " $file_count " . et('File_s') . " = " . format_size($total_size) . "</td></tr>";
			if($quota_mb){
				$out .= "
                <tr><td bgcolor=\"#DDDDDD\" colspan=50><b>" . et('Partition') . ": " . format_size(($quota_mb * 1024 * 1024)) . " " . et('Total') . " - " . format_size(($quota_mb * 1024 * 1024) - total_size($fm_current_root)) . " " . et('Free') . "</td></tr>";
			} else{
				$out .= "
                <tr><td bgcolor=\"#DDDDDD\" colspan=50><b>" . et('Partition') . ": " . format_size(disk_total_space($current_sys_dir)) . " " . et('Total') . " - " . format_size(disk_free_space($current_sys_dir)) . " " . et('Free') . "</td></tr>";
			}
			$tf = getmicrotime();
			$tt = ($tf - $ti);
			$out .= "
                <tr><td bgcolor=\"#DDDDDD\" colspan=50><b>" . et('RenderTime') . ": " . substr($tt, 0, strrpos($tt, ".") + 5) . " " . et('Seconds') . "</td></tr>";
			$out .= "
            <script language=\"Javascript\" type=\"text/javascript\">
            <!--
                update_sel_status();
            //-->
            </script>";
		} else{
			$out .= "
            <tr>
            <td bgcolor=\"#DDDDDD\" width=\"1%\">$uplink<td bgcolor=\"#DDDDDD\" colspan=50><nobr><a href=\"" . $path_info["basename"] . "?frame=3&current_dir=$current_dir\">$current_dir</a></nobr>
            <tr><td bgcolor=\"#DDDDDD\" colspan=50>" . et('EmptyDir') . ".</tr>";
		}
	} else{
		$out .= "<tr><td><font color=red>" . et('IOError') . ".<br>$current_dir</font>";
	}
	$out .= "</table>";
	echo $out;
}

/**
 *
 */
function upload_form(){
	global $_FILES, $current_dir, $dir_dest, $fechar, $quota_mb, $path_info;
	$num_uploads = 5;
	html_header();
	echo "<body marginwidth=\"0\" marginheight=\"0\">";
	if(count($_FILES) == 0){
		echo "
        <table height=\"100%\" border=0 cellspacing=0 cellpadding=2 align=center>
        <form name=\"upload_form\" action=\"" . $path_info["basename"] . "\" method=\"post\" ENCTYPE=\"multipart/form-data\">
        <input type=hidden name=dir_dest value=\"$current_dir\">
        <input type=hidden name=action value=10>
        <tr><th colspan=2>" . et('Upload') . "</th></tr>
        <tr><td align=right><b>" . et('Destination') . ":<td><b><nobr>$current_dir</nobr>";
		$test_js = "";
		for($x = 0; $x < $num_uploads; $x++){
			echo "<tr><td width=1 style=\"width:50px;\" align=right><b>" . et('File') . ":<td><nobr><input type=\"file\" name=\"file$x\"></nobr>";
			$test_js .= "(document.upload_form.file$x.value.length>0)||";
		}
		echo "
        <input type=button value=\"" . et('Send') . "\" onclick=\"test_upload_form()\"></nobr>
        <tr><td> <td><input type=checkbox name=fechar value=\"1\"> <a href=\"JavaScript:troca();\">" . et('AutoClose') . "</a>
        <tr><td colspan=2> </td></tr>
        </form>
        </table>
        <script language=\"Javascript\" type=\"text/javascript\">
        <!--
            function troca(){
                if(document.upload_form.fechar.checked){document.upload_form.fechar.checked=false;}else{document.upload_form.fechar.checked=true;}
            }
            foi = false;
            function test_upload_form(){
                if(" . substr($test_js, 0, strlen($test_js) - 2) . "){
                    if (foi) alert('" . et('SendingForm') . "...');
                    else {
                        foi = true;
                        document.upload_form.submit();
                    }
                } else alert('" . et('NoFileSel') . ".');
            }
            window.moveTo((window.screen.width-400)/2,((window.screen.height-200)/2)-20);
        //-->
        </script>";
	} else{
		$out = "<tr><th colspan=2>" . et('UploadEnd') . "</th></tr>
                <tr><th colspan=2><nobr>" . et('Destination') . ": $dir_dest</nobr>";
		for($x = 0; $x < $num_uploads; $x++){
			$temp_file = $_FILES["file" . $x]["tmp_name"];
			$filename = $_FILES["file" . $x]["name"];
			if(strlen($filename)){
				$resul = save_upload($temp_file, $filename, $dir_dest);
			} else{
				$resul = 7;
			}
			switch($resul){
				case 1:
					$out .= "<tr><td><b>" . str_zero($x + 1, 3) . ".<font color=green><b> " . et('FileSent') . ":</font><td>" . $filename . "</td></tr>\n";
					break;
				case 2:
					$out .= "<tr><td><b>" . str_zero($x + 1, 3) . ".<font color=red><b> " . et('IOError') . ":</font><td>" . $filename . "</td></tr>\n";
					break;
				case 3:
					$out .= "<tr><td><b>" . str_zero($x + 1, 3) . ".<font color=red><b>" . et('SpaceLimReached') . " ($quota_mb Mb)</font><td>" . $filename . "</td></tr>\n";
					break;
				case 4:
					$out .= "<tr><td><b>" . str_zero($x + 1, 3) . ".<font color=red><b> " . et('InvExt') . ":</font><td>" . $filename . "</td></tr>\n";
					break;
				case 5:
					$out .= "<tr><td><b>" . str_zero($x + 1, 3) . ".<font color=red><b> " . et('FileNoOverw') . "</font><td>" . $filename . "</td></tr>\n";
					break;
				case 6:
					$out .= "<tr><td><b>" . str_zero($x + 1, 3) . ".<font color=green><b> " . et('FileOverw') . ":</font><td>" . $filename . "</td></tr>\n";
					break;
				case 7:
					$out .= "<tr><td colspan=2><b>" . str_zero($x + 1, 3) . ".<font color=red><b> " . et('FileIgnored') . "</font></td></tr>\n";
			}
		}
		if($fechar){
			echo "
            <script language=\"Javascript\" type=\"text/javascript\">
            <!--
                window.close();
            //-->
            </script>
            ";
		} else{
			echo "
            <table height=\"100%\" border=0 cellspacing=0 cellpadding=2 align=center>
            $out
            <tr><td colspan=2> </td></tr>
            </table>
            <script language=\"Javascript\" type=\"text/javascript\">
            <!--
                window.focus();
            //-->
            </script>
            ";
		}
	}
	echo "</body>\n</html>";
}

/**
 * 权限修改表单
 */
function chmod_form(){
	html_header("
    <script language=\"Javascript\" type=\"text/javascript\">
    <!--
    function octalchange()
    {
        var val = document.chmod_form.t_total.value;
        var stickybin = parseInt(val.charAt(0)).toString(2);
        var ownerbin = parseInt(val.charAt(1)).toString(2);
        while (ownerbin.length<3) { ownerbin=\"0\"+ownerbin; };
        var groupbin = parseInt(val.charAt(2)).toString(2);
        while (groupbin.length<3) { groupbin=\"0\"+groupbin; };
        var otherbin = parseInt(val.charAt(3)).toString(2);
        while (otherbin.length<3) { otherbin=\"0\"+otherbin; };
        document.chmod_form.sticky.checked = parseInt(stickybin.charAt(0));
        document.chmod_form.owner4.checked = parseInt(ownerbin.charAt(0));
        document.chmod_form.owner2.checked = parseInt(ownerbin.charAt(1));
        document.chmod_form.owner1.checked = parseInt(ownerbin.charAt(2));
        document.chmod_form.group4.checked = parseInt(groupbin.charAt(0));
        document.chmod_form.group2.checked = parseInt(groupbin.charAt(1));
        document.chmod_form.group1.checked = parseInt(groupbin.charAt(2));
        document.chmod_form.other4.checked = parseInt(otherbin.charAt(0));
        document.chmod_form.other2.checked = parseInt(otherbin.charAt(1));
        document.chmod_form.other1.checked = parseInt(otherbin.charAt(2));
        calc_chmod(1);
    }

    function calc_chmod(nototals)
    {
      var users = new Array(\"owner\", \"group\", \"other\");
      var totals = new Array(\"\",\"\",\"\");
      var syms = new Array(\"\",\"\",\"\");

        for (var i=0; i<users.length; i++)
        {
            var user=users[i];
            var field4 = user + \"4\";
            var field2 = user + \"2\";
            var field1 = user + \"1\";
            var symbolic = \"sym_\" + user;
            var number = 0;
            var sym_string = \"\";
            var sticky = \"0\";
            var sticky_sym = \" \";
            if (document.chmod_form.sticky.checked){
                sticky = \"1\";
                sticky_sym = \"t\";
            }
            if (document.chmod_form[field4].checked == true) { number += 4; }
            if (document.chmod_form[field2].checked == true) { number += 2; }
            if (document.chmod_form[field1].checked == true) { number += 1; }

            if (document.chmod_form[field4].checked == true) {
                sym_string += \"r\";
            } else {
                sym_string += \"-\";
            }
            if (document.chmod_form[field2].checked == true) {
                sym_string += \"w\";
            } else {
                sym_string += \"-\";
            }
            if (document.chmod_form[field1].checked == true) {
                sym_string += \"x\";
            } else {
                sym_string += \"-\";
            }

            totals[i] = totals[i]+number;
            syms[i] =  syms[i]+sym_string;

      }
        if (!nototals) document.chmod_form.t_total.value = sticky + totals[0] + totals[1] + totals[2];
        document.chmod_form.sym_total.value = syms[0] + syms[1] + syms[2] + sticky_sym;
    }
    function sticky_change(){
        document.chmod_form.sticky.checked = !(document.chmod_form.sticky.checked);
    }
	function apply_chmod(){
        if (confirm('" . et('AlterPermTo') . " \\' '+document.chmod_form.t_total.value+' \\' ?\\n')){
            window.opener.set_chmod_arg(document.chmod_form.t_total.value);
			window.close();
		}
	}

    window.onload=octalchange;
    window.moveTo((window.screen.width-400)/2,((window.screen.height-200)/2)-20);
    //-->
    </script>");
	echo "<body marginwidth=\"0\" marginheight=\"0\">
    <form name=\"chmod_form\">
    <TABLE BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"4\" ALIGN=CENTER>
    <tr><th colspan=4>" . et('Perms') . "</th></tr>
    <TR ALIGN=\"LEFT\" VALIGN=\"MIDDLE\">
    <TD><input type=\"text\" name=\"t_total\" value=\"0755\" size=\"4\" onKeyUp=\"octalchange()\"> </TD>
    <TD><input type=\"text\" name=\"sym_total\" value=\"\" size=\"12\" READONLY=\"1\"></TD>
    </TR>
    </TABLE>
    <table cellpadding=\"2\" cellspacing=\"0\" border=\"0\" ALIGN=CENTER>
    <tr bgcolor=\"#333333\">
    <td WIDTH=\"60\" align=\"left\"> </td>
    <td WIDTH=\"55\" align=\"center\" style=\"color:#FFFFFF\"><b>" . et('Owner') . "
    </b></td>
    <td WIDTH=\"55\" align=\"center\" style=\"color:#FFFFFF\"><b>" . et('Group') . "
    </b></td>
    <td WIDTH=\"55\" align=\"center\" style=\"color:#FFFFFF\"><b>" . et('Other') . "
    <b></td>
    </tr>
    <tr bgcolor=\"#DDDDDD\">
    <td WIDTH=\"60\" align=\"left\" nowrap BGCOLOR=\"#FFFFFF\">" . et('Read') . "</td>
    <td WIDTH=\"55\" align=\"center\" bgcolor=\"#EEEEEE\">
    <input type=\"checkbox\" name=\"owner4\" value=\"4\" onclick=\"calc_chmod()\">
    </td>
    <td WIDTH=\"55\" align=\"center\" bgcolor=\"#FFFFFF\"><input type=\"checkbox\" name=\"group4\" value=\"4\" onclick=\"calc_chmod()\">
    </td>
    <td WIDTH=\"55\" align=\"center\" bgcolor=\"#EEEEEE\">
    <input type=\"checkbox\" name=\"other4\" value=\"4\" onclick=\"calc_chmod()\">
    </td>
    </tr>
    <tr bgcolor=\"#DDDDDD\">
    <td WIDTH=\"60\" align=\"left\" nowrap BGCOLOR=\"#FFFFFF\">" . et('Write') . "</td>
    <td WIDTH=\"55\" align=\"center\" bgcolor=\"#EEEEEE\">
    <input type=\"checkbox\" name=\"owner2\" value=\"2\" onclick=\"calc_chmod()\"></td>
    <td WIDTH=\"55\" align=\"center\" bgcolor=\"#FFFFFF\"><input type=\"checkbox\" name=\"group2\" value=\"2\" onclick=\"calc_chmod()\">
    </td>
    <td WIDTH=\"55\" align=\"center\" bgcolor=\"#EEEEEE\">
    <input type=\"checkbox\" name=\"other2\" value=\"2\" onclick=\"calc_chmod()\">
    </td>
    </tr>
    <tr bgcolor=\"#DDDDDD\">
    <td WIDTH=\"60\" align=\"left\" nowrap BGCOLOR=\"#FFFFFF\">" . et('Exec') . "</td>
    <td WIDTH=\"55\" align=\"center\" bgcolor=\"#EEEEEE\">
    <input type=\"checkbox\" name=\"owner1\" value=\"1\" onclick=\"calc_chmod()\">
    </td>
    <td WIDTH=\"55\" align=\"center\" bgcolor=\"#FFFFFF\"><input type=\"checkbox\" name=\"group1\" value=\"1\" onclick=\"calc_chmod()\">
    </td>
    <td WIDTH=\"55\" align=\"center\" bgcolor=\"#EEEEEE\">
    <input type=\"checkbox\" name=\"other1\" value=\"1\" onclick=\"calc_chmod()\">
    </td>
    </tr>
    </TABLE>
    <TABLE BORDER=\"0\" CELLSPACING=\"0\" CELLPADDING=\"4\" ALIGN=CENTER>
    <tr><td colspan=2><input type=checkbox name=sticky value=\"1\" onclick=\"calc_chmod()\"> <a href=\"JavaScript:sticky_change();\">" . et('StickyBit') . "</a><td colspan=2 align=right><input type=button value=\"" . et('Apply') . "\" onClick=\"apply_chmod()\"></tr>
    </table>
    </form>
    </body>\n</html>";
}

/**
 * 编辑文件操作
 */
function edit_file_form(){
	global $current_dir, $filename, $file_data, $save_file, $path_info;
	$file = $current_dir . $filename;
	$sys_file = nameToSys($file);
	if($save_file){
		$fh = fopen($sys_file, "w");
		fputs($fh, $file_data, strlen($file_data));
		fclose($fh);
	}
	$fh = fopen($sys_file, "r");
	$file_data = fread($fh, filesize($sys_file));
	fclose($fh);
	html_header();
	echo "
<style>
table{width: 100%}
</style>
<body marginwidth=\"0\" marginheight=\"0\">
    <table border=0 cellspacing=0 cellpadding=5 align=center>
    <form name=\"edit_form\" action=\"" . $path_info["basename"] . "\" method=\"post\">
    <input type=hidden name=action value=\"7\">
    <input type=hidden name=save_file value=\"1\">
    <input type=hidden name=current_dir value=\"$current_dir\">
    <input type=hidden name=filename value=\"$filename\">
    <tr><th colspan=2>" . $file . "</th></tr>
    <tr><td colspan=2><textarea name=file_data style='width:98%;height:480px;'>" . html_encode($file_data) . "</textarea></td></tr>
    <tr><td><input type=button value=\"" . et('Refresh') . "\" onclick=\"document.edit_form_refresh.submit()\"></td><td align=right><input type=button value=\"" . et('SaveFile') . "\" onclick=\"go_save()\"></td></tr>
    </form>
    <form name=\"edit_form_refresh\" action=\"" . $path_info["basename"] . "\" method=\"post\">
    <input type=hidden name=action value=\"7\">
    <input type=hidden name=current_dir value=\"$current_dir\">
    <input type=hidden name=filename value=\"$filename\">
    </form>
    </table>
    <script language=\"Javascript\" type=\"text/javascript\">
        window.moveTo((window.screen.width-1024)/2,((window.screen.height-728)/2)-20);
        function go_save(){";
	if(is_writable($sys_file)){
		echo "
        document.edit_form.submit();";
	} else{
		echo "
        if(confirm('" . et('ConfTrySave') . " ?')) document.edit_form.submit();";
	}
	echo "
        }
    </script>
    </body>\n</html>";
}

/**
 *
 */
function config_form(){
	global $cfg;
	global $current_dir, $fm_self, $doc_root, $path_info, $fm_current_root, $lang, $error_reporting, $version;
	global $config_action, $newpass, $newlang, $newerror, $newfm_root;
	$Warning = "";
	switch($config_action){
		case 1:
			$ChkVerWarning = "";
			if($fh = fopen("http://phpfm.sf.net/latest.php", "r")){
				$data = "";
				while(!feof($fh)){
					$data .= fread($fh, 1024);
				}
				fclose($fh);
				$data = unserialize($data);
				$ChkVerWarning = "<tr><td align=right> ";
				if(is_array($data) && count($data)){
					$ChkVerWarning .= "<a href=\"JavaScript:open_win('http://sourceforge.net')\">
                    <img src=\"http://sourceforge.net/sflogo.php?group_id=114392&type=1\" width=\"88\" height=\"31\" style=\"border: 1px solid #AAAAAA\" alt=\"SourceForge.net Logo\" />
					</a>";
					if(str_replace(".", "", $data['version']) > str_replace(".", "", $cfg->data['version'])){
						$ChkVerWarning .= "<td><a href=\"JavaScript:open_win('http://prdownloads.sourceforge.net/phpfm/phpFileManager-" . $data['version'] . ".zip?download')\"><font color=green>" . et('ChkVerAvailable') . "</font></a>";
					} else{
						$ChkVerWarning .= "<td><font color=red>" . et('ChkVerNotAvailable') . "</font>";
					}
				} else{
					$ChkVerWarning .= "<td><font color=red>" . et('ChkVerError') . "</font>";
				}
			} else{
				$ChkVerWarning .= "<td><font color=red>" . et('ChkVerError') . "</font>";
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
    <tr><td align=right>" . et('Website') . ":<td><a href=\"JavaScript:open_win('http://phpfm.sf.net')\">http://phpfm.sf.net</a>&nbsp;&nbsp;&nbsp;<input type=button value=\"" . et('ChkVer') . "\" onclick=\"test_config_form(1)\"></td></tr>
	</form>";
	if(strlen($ChkVerWarning)){
		echo $ChkVerWarning . $data['warnings'];
	}
	echo "
 	<style type=\"text/css\">
		.buymeabeer {
		    background: url('http://phpfm.sf.net/img/buymeabeer.png') 0 0 no-repeat;
		    text-indent: -9999px;
		    width: 128px;
		    height: 31px;
            border: none;
   			cursor: hand;
   			cursor: pointer;
		}
		.buymeabeer:hover {
		    background: url('http://phpfm.sf.net/img/buymeabeer.png') 0 -31px no-repeat;
		}
	</style>
	<tr><td align=right>Like this project?</td><td>
	<form name=\"buymeabeer_form\" action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\">
		<input type=\"hidden\" name=\"cmd\" value=\"_xclick\">
		<input type=\"hidden\" name=\"business\" value=\"dulldusk@gmail.com\">
		<input type=\"hidden\" name=\"lc\" value=\"BR\">
		<input type=\"hidden\" name=\"item_name\" value=\"A Beer\">
		<input type=\"hidden\" name=\"button_subtype\" value=\"services\">
		<input type=\"hidden\" name=\"currency_code\" value=\"USD\">
		<input type=\"hidden\" name=\"tax_rate\" value=\"0.000\">
		<input type=\"hidden\" name=\"shipping\" value=\"0.00\">
		<input type=\"hidden\" name=\"bn\" value=\"PP-BuyNowBF:btn_buynowCC_LG.gif:NonHostedGuest\">
        <input type=\"submit\" class=\"buymeabeer\" value=\"buy me a beer\">
	        <input type=\"hidden\" name=\"buyer_credit_promo_code\" value=\"\">
	        <input type=\"hidden\" name=\"buyer_credit_product_category\" value=\"\">
	        <input type=\"hidden\" name=\"buyer_credit_shipping_method\" value=\"\">
	        <input type=\"hidden\" name=\"buyer_credit_user_address_change\" value=\"\">
	        <input type=\"hidden\" name=\"tax\" value=\"0\">
			<input type=\"hidden\" name=\"no_shipping\" value=\"1\">
	        <input type=\"hidden\" name=\"return\" value=\"http://phpfm.sf.net\">
	        <input type=\"hidden\" name=\"cancel_return\" value=\"http://phpfm.sf.net\">
	</form>
	</td></tr>
    <form name=\"config_form\" action=\"" . $path_info["basename"] . "\" method=\"post\">
    <input type=hidden name=action value=2>
    <input type=hidden name=config_action value=0>
    <tr><td align=right width=1><nobr>" . et('DocRoot') . ":</nobr><td>" . $doc_root . "</td></tr>
    <tr><td align=right><nobr>" . et('FLRoot') . ":</nobr><td><input type=text size=60 name=newfm_root value=\"" . $cfg->data['fm_root'] . "\" onkeypress=\"enterSubmit(event,'test_config_form(2)')\"></td></tr>
    <tr><td align=right>" . et('Lang') . ":<td>
	<select name=newlang>
    	<option value=cat>Catalan - by Pere Borràs AKA @Norl
        <option value=nl>Dutch - by Leon Buijs
		<option value=en>English - by Fabricio Seger Kolling
		<option value=fr1>French - by Jean Bilwes
        <option value=fr2>French - by Sharky
        <option value=fr3>French - by Michel Lainey
		<option value=de1>German - by Guido Ogrzal
        <option value=de2>German - by AXL
        <option value=de3>German - by Mathias Rothe
        <option value=it1>Italian - by Valerio Capello
        <option value=it2>Italian - by Federico Corrà
        <option value=it3>Italian - by Luca Zorzi
        <option value=it4>Italian - by Gianni
		<option value=kr>Korean - by Airplanez
		<option value=pt>Portuguese - by Fabricio Seger Kolling
		<option value=es>Spanish - by Sh Studios
        <option value=ru>Russian - by Евгений Рашев
        <option value=tr>Turkish - by Necdet Yazilimlari
	</select></td></tr>
    <tr><td align=right>" . et('ErrorReport') . ":<td><select name=newerror>
	<option value=\"0\">Disabled
	<option value=\"1\">Show Errors
	<option value=\"2\">Show Errors, Warnings and Notices
	</select></td></tr>
    <tr><td> <td><input type=button value=\"" . et('SaveConfig') . "\" onclick=\"test_config_form(2)\">";
	if(strlen($Warning1)){
		echo " <font color=red>$Warning1</font>";
	}
	echo "
    <tr><td align=right>" . et('Pass') . ":<td><input type=text size=30 name=newpass value=\"\" onkeypress=\"enterSubmit(event,'test_config_form(3)')\"></td></tr>
    <tr><td> <td><input type=button value=\"" . et('SavePass') . "\" onclick=\"test_config_form(3)\">";
	if(strlen($Warning2)){
		echo " <font color=red>$Warning2</font>";
	}
	echo "</td></tr>";
	echo "
    </form>
    </table>
    <script language=\"Javascript\" type=\"text/javascript\">
    <!--
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
    //-->
    </script>
    ";
	echo "</body>\n</html>";
}

/**
 *
 */
function shell_form(){
	global $current_dir, $shell_form, $cmd_arg, $path_info;
	$data_out = "";
	if(strlen($cmd_arg)){
		exec($cmd_arg, $mat);
		if(count($mat)){
			$data_out = trim(implode("\n", $mat));
		}
	}
	switch($shell_form){
		case 1:
			html_header();
			echo "
            <body>
            <style>*{margin: 0;padding: 0}</style>
            <form name=\"data_form\">
            <textarea name=data_out rows=36 READONLY=\"1\" style='width:99%;margin: 10px auto'></textarea>
            </form>
            </body></html>";
			break;
		case 2:
			html_header();
			echo "
            <body marginwidth=\"0\" marginheight=\"0\">
            <form name=\"shell_form\" action=\"" . $path_info["basename"] . "\" method=\"post\" style=\"width:70%;margin: 10px auto\">
            <input type=hidden name=current_dir value=\"$current_dir\">
            <input type=hidden name=action value=\"9\">
            <input type=hidden name=shell_form value=\"2\">
            <input type=text name=cmd_arg style=\"width:100%\">
            </form>";
			echo "
            <script language=\"Javascript\" type=\"text/javascript\">
            <!--";
			if(strlen($data_out)){
				echo "
                var val = '# " . html_encode($cmd_arg) . "\\n" . html_encode(str_replace("<", "[", str_replace(">", "]", str_replace("\n", "\\n", str_replace("'", "\'", str_replace("\\", "\\\\", $data_out)))))) . "\\n';
                parent.frame1.document.data_form.data_out.value += val;
				parent.frame1.document.data_form.data_out.scrollTop = parent.frame1.document.data_form.data_out.scrollHeight;";
			}
			echo "
                document.shell_form.cmd_arg.focus();
            //-->
            </script>
            ";
			echo "
            </body></html>";
			break;
		default:
			html_header("
            <script language=\"Javascript\" type=\"text/javascript\">
            <!--
                window.moveTo((window.screen.width-800)/2,((window.screen.height-600)/2)-20);
            //-->
            </script>");
			echo "
            <frameset rows=\"90%,10%\" framespacing=\"0\" frameborder=no>
                <frame src=\"" . $path_info["basename"] . "?action=9&shell_form=1\" name=frame1 border=\"0\" marginwidth=\"0\" marginheight=\"0\">
                <frame src=\"" . $path_info["basename"] . "?action=9&shell_form=2\" name=frame2 border=\"0\" marginwidth=\"0\" marginheight=\"0\">
            </frameset>
            </html>";
	}
}