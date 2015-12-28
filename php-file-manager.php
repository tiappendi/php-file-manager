<?php
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$action = filter_input(INPUT_GET, "action");
if (!isset($action) || is_null($action) || $action == "") {
    $action = "list";
    $folder = filter_input(INPUT_GET, "folder");
    if (!isset($folder) || is_null($folder) || $folder == "") {
        $folder = ".";
    }
}

switch ($action) {
    case "expandFolder":
        $path = filter_input(INPUT_GET, "path");
        $level = filter_input(INPUT_GET, "level");
        echo expandFolder($path, $level+1);
        exit();
        break;
}

//action and folder variables are set. Now print the header stuff
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 

<html>
<head>
<meta http-equiv="Content-Language" content="cs">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
<title>PHP File manager</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

</head>
<body>
    <script language="Javascript">
    function showPrmDlg(src){
        var path = $(src).parents("TR").find("TD#name").html();
        $("DIV#changePermissionsModal DIV.modal-header H4#heading SPAN").html(path);
        $("DIV#changePermissionsModal").modal("show");
    }
    
    function showUnpDlg(src){
        var path = $(src).parents("TR").find("TD#name").html();
        $("DIV#unpackModal DIV.modal-header H4#heading SPAN").html(path);
        $("DIV#unpackModal").modal("show");
    }
    
    function showPckDlg(src){
        var path = $(src).parents("TR").find("TD#name").html();
        $("DIV#packModal DIV.modal-header H4#heading SPAN").html(path);
        $("DIV#packModal").modal("show");
    }
    
    function showMkdDlg(src){
        var path = $(src).parents("TR").find("TD#name").html();
        $("DIV#mkDirModal DIV.modal-header H4#heading SPAN").html(path);
        $("DIV#mkDirModal").modal("show");
    }
    
    function showDltDlg(src){
        var tr = $(src).parents("TR");
        var parent = tr.attr("data-parent");
        var path = "";
        if(typeof parent != "undefined"){
            path = parent + "/" + tr.find("TD#name").html();
        } else {
            path = tr.find("TD#name").html();
        }
        $("DIV#deleteModal DIV.modal-header H4#heading SPAN").html(path);
        $("DIV#deleteModal").modal("show");
    }
    
    function deleteDir(){
        var path = $("DIV#deleteModal DIV.modal-header H4#heading SPAN").html();
        $.ajax({
            url: "php-file-manager.php?action=deleteDir&path="+path,
        }).done(function( data ) {
            $("DIV#showLog PRE").html(data);
            $("DIV#showLog").modal("show");
        });
    }
    
    function makeDir(){
        var path = $("DIV#mkDirModal DIV.modal-header H4#heading SPAN").html();
        var name = $("DIV#mkDirModal TR#dirPath INPUT[type='text']").val();
        $.ajax({
            url: "php-file-manager.php?action=makeDir&path="+path+"&name="+name,
        }).done(function( data ) {
            location.reload();
        });
    }
    
    function folderPermCheck(){
        if($("DIV#changePermissionsModal TR#folderPerm INPUT[type='checkbox']").prop('checked') == true){
            $("DIV#changePermissionsModal TR#folderPerm INPUT[type='text']").prop('disabled', false);
        } else {
            $("DIV#changePermissionsModal TR#folderPerm INPUT[type='text']").prop('disabled', true);
        }
    }
    
    function filePermCheck(){
        if($("DIV#changePermissionsModal TR#filePerm INPUT[type='checkbox']").prop('checked') == true){
            $("DIV#changePermissionsModal TR#filePerm INPUT[type='text']").prop('disabled', false);
        } else {
            $("DIV#changePermissionsModal TR#filePerm INPUT[type='text']").prop('disabled', true);
        }
    }
    
    function changePerm(){
        var path = $("DIV#changePermissionsModal DIV.modal-header H4#heading SPAN").html();
        var recursively = $("DIV#changePermissionsModal TR#recursively INPUT[type='checkbox']").prop('checked');
        var filesChange = $("DIV#changePermissionsModal TR#filePerm INPUT[type='checkbox']").prop('checked');
        var foldersChange = $("DIV#changePermissionsModal TR#folderPerm INPUT[type='checkbox']").prop('checked');
        var filesPerm = $("DIV#changePermissionsModal TR#filePerm INPUT[type='text']").val();
        var foldersPerm = $("DIV#changePermissionsModal TR#folderPerm INPUT[type='text']").val();
        $.ajax({
            url: "php-file-manager.php?path="+path+"&action=changeChmod&recursively="+recursively+"&filesChange="+filesChange+"&foldersChange="+foldersChange+"&filesPerm="+filesPerm+"&foldersPerm="+foldersPerm,
        }).done(function( data ) {
            $("DIV#showLog PRE").html(data);
            $("DIV#showLog").modal("show");
        });
    }
    
    function unPack(){
        var file = $("DIV#unpackModal DIV.modal-header H4#heading SPAN").html();
        var path = $("DIV#unpackModal TR#unpackPath INPUT[type='text']").val();
        $.ajax({
            url: "php-file-manager.php?action=unpack&file="+file+"&path="+path,
        }).done(function( data ) {
            $("DIV#showLog PRE").html(data);
            $("DIV#showLog").modal("show");
        });
    }
    
    function pack(){
        var path = $("DIV#packModal DIV.modal-header H4#heading SPAN").html();
        var file = $("DIV#packModal TR#filename INPUT[type='text']").val();
        $.ajax({
            url: "php-file-manager.php?action=pack&file="+file+"&path="+path,
        }).done(function( data ) {
            location.reload();
        });
    }
            
    
    function expandFolder(src){
        var tr = $(src).parents("TR");
        var level = tr.attr("data-level");
        var parent = tr.attr("data-parent");
        var path = "";
        if(typeof parent != "undefined"){
            path = parent + "/" + tr.find("TD#name").html();
        } else {
            path = tr.find("TD#name").html();
        }
        $.ajax({
            url: "php-file-manager.php?action=expandFolder&path="+path+"&level="+level,
        }).done(function( data ) {
            var arr = tr.find("TD#type SPAN");
            arr.removeClass("glyphicon-folder-close");
            arr.attr("onclick","collapseFolder(this)");
            arr.addClass("glyphicon-folder-open");
            tr.after(data);
        });
    }
    
    function collapseFolder(src){
        var tr = $(src).parents("TR");
        var level = tr.attr("data-level");
        tr.nextAll("TR").each(function(){
            var lvl = $(this).attr("data-level");
            if(lvl>level){
                $(this).remove();
            } else {
                return;
            }
        });
        $(src).removeClass("glyphicon-folder-open");
        $(src).addClass("glyphicon-folder-close");
        $(src).attr("onclick","expandFolder(this)");
    }
</script>
<?php

switch ($action) {
    case "list":
        printFolderTable($folder);
        break;
    case "deleteDir":
        $path = filter_input(INPUT_GET, "path");
        delete_directory($path);
        exit();
        break;
    case "makeDir":
        $path = filter_input(INPUT_GET, "path")==""?"":filter_input(INPUT_GET, "path") . "/";
        $name = filter_input(INPUT_GET, "name");
        mkdir(__DIR__ . "/" . $path . $name, 0755);
        exit();
        break;
    case "unpack":
        $path = filter_input(INPUT_GET, "path");
        $file = filter_input(INPUT_GET, "file");
        unpackFile($file, $path);
        exit();
        break;    
    case "pack":
        $path = filter_input(INPUT_GET, "path");
        $file = filter_input(INPUT_GET, "file") . ".zip";
        packDir($path, $file);
        exit();
        break; 
    
    case "changeChmod":
        $path = filter_input(INPUT_GET, "path");
        $recursively = filter_input(INPUT_GET, "recursively") == "true";
        $filesChange = filter_input(INPUT_GET, "filesChange") == "true";
        $foldersChange = filter_input(INPUT_GET, "foldersChange") == "true";
        $filesPerm = (filter_input(INPUT_GET, "filesPerm"));
        $foldersPerm = (filter_input(INPUT_GET, "foldersPerm"));
        recursiveChmod($path, $recursively, $filesChange, $foldersChange, $filesPerm, $foldersPerm);
        exit();
        break;
    
}

?>    

<!-- Modal - change permissions -->
<div class="modal fade" id="changePermissionsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="heading">Change permissions of <span></span></h4>
      </div>
      <div class="modal-body">
          <table>
              <tr id="recursively"><td>Including files and subfolders</td><td style="padding-left: 15px"><input type="checkbox" checked></td></tr>
              <tr id="folderPerm"><td>Folder permissions:</td><td style="padding-left: 15px"><input type="checkbox" checked onclick="folderPermCheck()"><input type="text" value="0755" style="margin-left: 5px"></td></tr>
              <tr id="filePerm"><td>File permissions:</td><td style="padding-left: 15px"><input type="checkbox" checked onclick="filePermCheck()"><input type="text" value="0644" style="margin-left: 5px"></td></tr>
           </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="changePerm()">Change permissions</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal - unpack -->
<div class="modal fade" id="unpackModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="heading">Unpack file <span></span></h4>
      </div>
      <div class="modal-body">
          <table>
              <tr id="unpackPath"><td>Path where to unpack:</td><td style="padding-left: 15px"><input type="text" value="." style="margin-left: 5px"></td></tr>
           </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="unPack()">Unpack</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal - pack -->
<div class="modal fade" id="packModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="heading">Pack <span></span></h4>
      </div>
      <div class="modal-body">
          <table>
              <tr id="filename"><td>File name:</td><td style="padding-left: 15px"><input type="text" value="archive" style="margin-left: 5px">.zip</td></tr>
           </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="pack()">Pack</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal - Make Directory -->
<div class="modal fade" id="mkDirModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="heading">Create directory in <span></span></h4>
      </div>
      <div class="modal-body">
          <table>
              <tr id="dirPath"><td>Name:</td><td style="padding-left: 15px"><input type="text" value="New" style="margin-left: 5px"></td></tr>
           </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="makeDir()">Create</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal - delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="heading">Delete: <span></span></h4>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn" onclick="deleteDir()">Yes</button>
        <button type="button" class="btn" data-dismiss="modal">No</button>
      </div>
    </div>
  </div>
</div>

<!-- Log -->
<div class="modal fade" id="showLog" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
          <pre>
          </pre>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal" onclick="location.reload()">Close & Refresh</button>
      </div>
    </div>
  </div>
</div>
    
</body>
</html>

<?php

//functions
function eraseFile($file) {
    $handle = fopen($file, "w+");
    if ($handle == false)
        return false;
    if (!fclose($handle))
        return false;
    return true;
}

function unpackFile($file, $path){
    echo "Unpacking file $file to directory $path";
    if(!is_dir($path)){
        echo "$path does not exist or is not a directory";
        return false;
    }
    if(!is_file($file)){
        echo "$file does not exist";
        return false;
    }
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    if (strtoupper($ext) == "ZIP") {
        $zip = new ZipArchive;
        $res = $zip->open($file);
        if ($res === TRUE) {
            $zip->extractTo($path);
            $zip->close();
            echo "File $file unzipped succesfully to path $path";
        } else {
            echo "Unpacking failed";
        }
    }
}

function packDir($source, $destination){
    if (!extension_loaded('zip') || !file_exists($source)) return false;
    $zip = new ZipArchive();
    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) return false;
    $source = str_replace('\\', '/', realpath($source));
    if (is_dir($source) === true){
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
        foreach ($files as $file){
            $file = str_replace('\\', '/', $file);
            // Ignore "." and ".." folders
            if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
                continue;
            $file = realpath($file);
            if (is_dir($file) === true){
                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
            } else if (is_file($file) === true){
                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
            }
        }
    } else if (is_file($source) === true){
        $zip->addFromString(basename($source), file_get_contents($source));
    }
    return $zip->close();
}


function delete_directory($path) {
    echo "Deleting Contents Of: $path<br /><br />";
    if(is_file($path)){
        unlink($path);
        echo "Deleted File: $path<br />";
        return true;
    }
    $files = array_diff(scandir($path), array('.', '..'));
    foreach ($files as $file) {
        if(is_dir("$path/$file")){
            delete_directory("$path/$file");
        } else {
           if(unlink("$path/$file")) 
                echo "Deleted File: $path/$file<br />";
        }
    }
    if (rmdir($path)) 
        echo "Deleted directory: $path<br />";
    return true;
}

function recurse_copy($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== ( $file = readdir($dir))) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if (is_dir($src . '/' . $file)) {
                recurse_copy($src . '/' . $file, $dst . '/' . $file);
            } else {
                echo "Copying $src/$file -> $dst/$file ... ";
                if(copy($src . '/' . $file, $dst . '/' . $file)){
                    echo "Done.\n";
                } else {
                    return false;
                }
            }
        }
    }
    closedir($dir);
    return true;
}

function recursiveChmod($path, $recursively, $filesChange, $foldersChange, $filesPerm, $foldersPerm) {
    echo "Changing $path with folder perms: $foldersPerm and file perms: $filesPerm\n";
    if (!file_exists($path)) {
        echo "File $path does not exists!";
        return(false);
    }
    if (is_file($path)) {
        if ($filesChange) {
            chmod($path, octdec($filesPerm));
            echo "Changed file permissions to ".($filesPerm)." on file $path.\n";
        }
    } elseif (is_dir($path)) {
        if ($recursively) {
            $foldersAndFiles = scandir($path);
            $entries = array_slice($foldersAndFiles, 2);
            foreach ($entries as $entry) {
                recursiveChmod($path . "/" . $entry, $recursively, $filesChange, $foldersChange, $filesPerm, $foldersPerm);
            }
        }
        // When we are done with the contents of the directory, we chmod the directory itself
        if ($foldersChange) {
            chmod($path, octdec($foldersPerm));
            echo "Changed directory permissions to ".($foldersPerm)." on directory $path.\n";
        }
    }
    return(true);
}

function printFolderTable($folder){
    echo "<h3>List directory " . realpath($folder) . "</h3>";
    $newDir = "<span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\" style=\"cursor:pointer; top: 0px\" onclick=\"showMkdDlg(this)\" title=\"Create new directory...\"></span>";
    echo "<button class=\"btn btn-default\" style=\"margin: 0% 5%;\">$newDir</button>";
    echo "<table class=\"table table-bordered table-hover table-striped\" style=\"margin: 5px 5%; width: 90%\">";
    $contents = scandir($folder);
    echo "<tr>"
                . "<th>Type</th>"
                . "<th>Name</th>"
                . "<th>Size [b]</th>"
                . "<th>Files</th>"
                . "<th>Permissions</th>"
                . "<th>Actions</th>"
                . "</tr>";
    foreach ($contents as $line) {
        if($line == "." || $line == ".." ) continue;
        $dir = is_dir($line);
        $icon = "glyphicon glyphicon-file";
        $cursor = "cursor: auto;";
        $title = "";
        $click = "";
        $iconColor = "";
        if($dir){
            $icon = "glyphicon glyphicon-folder-close";
            $cursor = "cursor: pointer;";
            $title = "Expand";
            $click = "expandFolder(this)";
            $iconColor = "color: #F0AD4E";
        }
        $perm = substr(sprintf('%o', fileperms($line)), -4);
        $data = dirsize($line);
        $size = $data["size"];
        $count = $data["count"];
        if($size > 1048576){
            $size = round($size/1048576,2) . " MB";
        } elseif ($size > 1024){
            $size = round($size/1024,2) . " KB";
        } else {
            $size = $size . " B";
        }
        $changePerm = "<span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\" style=\"cursor:pointer; margin: 0px 2px\" onclick=\"showPrmDlg(this)\" title=\"Change permissions...\"></span>";
        $deleteIcon = "<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\" style=\"cursor:pointer; margin: 0px 2px\" onclick=\"showDltDlg(this)\" title=\"Delete...\"></span>";
        $ext = pathinfo($line, PATHINFO_EXTENSION);
        
        $packIcon = "<span class=\"glyphicon glyphicon glyphicon-compressed\" aria-hidden=\"true\" style=\"cursor:pointer; margin: 0px 2px\" onclick=\"showPckDlg(this)\" title=\"Pack to zip...\"></span>";
        $unpackIcon = strtoupper($ext) == "ZIP"?"<span class=\"glyphicon glyphicon-open-file\" aria-hidden=\"true\" style=\"cursor:pointer; margin: 0px 2px\" onclick=\"showUnpDlg(this)\" title=\"Unpack...\"></span>":"";
        echo "<tr data-level=\"0\" title=\"".print_r($dirData, TRUE)."\">"
                . "<td id=\"type\" style=\"width: 70px\"><span class=\"$icon\" style=\"$cursor $iconColor\" title=\"$title\" onclick=\"$click\" aria-hidden=\"true\"></span></td>"
                . "<td id=\"name\">$line</td>"
                . "<td id=\"size\" style=\"width: 120px\">$size</td>"
                . "<td id=\"fileCount\" style=\"width: 120px\">$count</td>"
                . "<td id=\"perm\" style=\"width: 120px\">$perm</td>"
                . "<td style=\"width: 120px\">$changePerm $packIcon $unpackIcon $deleteIcon</td>"
                . "</tr>";
    }
    echo "</table>";
}

function expandFolder($path, $level) {
    $contents = scandir($path);
    $margin = "margin-left: " . ($level*10) . "px;";
    foreach ($contents as $line) {
        if ($line == "." || $line == "..")
            continue;
        $dir = is_dir($path . "/" . $line);
        $icon = "glyphicon glyphicon-file";
        $cursor = "cursor: auto;";
        $title = "";
        $click = "";
        $iconColor = "";
        if($dir){
            $icon = "glyphicon glyphicon-folder-close";
            $cursor = "cursor: pointer;";
            $title = "Expand";
            $click = "expandFolder(this)";
            $iconColor = "color: #F0AD4E";
        }
        
        $perm = substr(sprintf('%o', fileperms($path . "/" . $line)), -4);
        $data = dirsize($path . "/" . $line);
        $size = $data["size"];
        $count = $data["count"];
        if($size > 1048576){
            $size = round($size/1048576,2) . " MB";
        } elseif ($size > 1024){
            $size = round($size/1024,2) . " KB";
        } else {
            $size = $size . " B";
        }
        $changePerm = "<span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\" style=\"cursor:pointer; margin: 0px 5px\" onclick=\"showPrmDlg(this)\" title=\"Change permissions...\"></span>";
        $deleteIcon = "<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\" style=\"cursor:pointer; margin: 0px 5px\" onclick=\"showDltDlg(this)\" title=\"Delete...\"></span>";
        $unpackIcon = "<span class=\"glyphicon glyphicon-open-file\" aria-hidden=\"true\" style=\"cursor:pointer; margin: 0px 5px\" onclick=\"showUnpDlg(this)\" title=\"Unpack...\"></span>";
        echo "<tr data-level=\"$level\" data-parent=\"$path\" title=\"".print_r($dirData, TRUE)."\">"
        . "<td id=\"type\" style=\"width: 70px\"><span class=\"$icon\" style=\"$cursor $margin $iconColor\" title=\"$title\" onclick=\"$click\" aria-hidden=\"true\"></span></td>"
        . "<td id=\"name\">$line</td>"
        . "<td id=\"size\">$size</td>"
        . "<td id=\"fileCount\" style=\"width: 120px\">$count</td>"
        . "<td id=\"perm\">$perm</td>"
        . "<td>$changePerm $deleteIcon</td>"
        . "</tr>";
    }
}

function dirsize($dir) {
    if(is_file($dir)) return array("size" => filesize ($dir), "count"=>1);
    $total_size = 0;
    $files = scandir($dir);
    $total_count = count($files) - 2; //do not count . and ..
    foreach ($files as $t) {
        if (is_dir(rtrim($dir, '/') . '/' . $t)) {
            if ($t <> "." && $t <> "..") {
                $data = dirsize(rtrim($dir, '/') . '/' . $t);
                $size = $data["size"];
                $count = $data["count"];
                $total_size += $size;
                $total_count += $count;
            }
        } else {
            $size = filesize(rtrim($dir, '/') . '/' . $t);
            $total_size += $size;
        }
    }
    return array("size" => $total_size, "count"=>$total_count);
}
