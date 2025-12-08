<?

class Explorer
{
    var $cfg;
    var $syslanai;
    var $mime = array(
        "txt" => "txt.png",
        "doc" => "document.png",
        "pdf" => "pdf.png",
        "php" => "php.png",
        "gif" => "image.png",
        "png" => "image.png",
        "jpg" => "image.png",
        "zip" => "tar.png",
        "tgz" => "tar.png",
        "tar.gz" => "tar.png",
        "tar" => "tar.png",
        "gz" => "tar.png",
        "rar" => "tar.png",
        "dir" => "folder.png",
        "html" => "html.png",
        "htm" => "html.png",
        "xml" => "netscape_doc.png"
    );

    function Explorer()
    {
        global $cfg, $sys_lanai;
        $this->cfg = $cfg;
        $this->syslanai = $sys_lanai;
    }

    function loadDir($dir)
    {
        $arFile = array();
        if ($handle = opendir($dir)) {
            $i = 0;
            while (false !== ($file = readdir($handle))) {

                if (is_dir($dir . $file)) {

                    $arFile[$i]['name'] = $file;
                    $arFile[$i]['size'] = $this->getFileSize(filesize($dir . $file));
                    $arFile[$i]['perms'] = substr(sprintf('%o', fileperms($dir . $file)), -4);
                    $path_parts = pathinfo($dir . $file);
                    if (!empty($path_parts['extension']))
                        $arFile[$i]['mime'] = $path_parts['extension'];
                    $i++;
                }
            }
            closedir($handle);
        }
        sort($arFile);
        return $arFile;
    }

    function loadFile($dir)
    {
        global $vardir;
        $arFile = array();
        if ($handle = opendir($dir)) {
            $i = 0;
            while (false !== ($file = readdir($handle))) {
                if (!is_dir($dir . $file)) {
                    $arFile[$i]['name'] = $file;
                    $arFile[$i]['size'] = $this->getFileSize(filesize($dir . $file));
                    $arFile[$i]['perms'] = substr(sprintf('%o', fileperms($dir . $file)), -4);
                    $path_parts = pathinfo($dir . $file);
                    if (!empty($path_parts['extension']))
                        $arFile[$i]['mime'] = $path_parts['extension'];
                    $i++;
                }
            }
            closedir($handle);
        }
        sort($arFile);
        return $arFile;
    }

    function getMimeIcon($file, $isdir)
    {
        $path_parts = pathinfo($file);
        if ($isdir) {
            $icon = "modules/explorer/images/" . $this->mime["dir"];
        } else {
            if (isset($path_parts['extension']) && !empty($this->mime[$path_parts['extension']])) {
                $icon = "modules/explorer/images/" . $this->mime[$path_parts['extension']];
            } else {
                $icon = "modules/explorer/images/unknown.png";
            }
        }
        ?><img src="<?= $icon; ?>" border="0" align="absmiddle"><?
    }

    function getFileSize($file_size)
    {
        if ($file_size >= 1073741824) {
            $file_size = round($file_size / 1073741824 * 100) / 100 . "g";
        } elseif ($file_size >= 1048576) {
            $file_size = round($file_size / 1048576 * 100) / 100 . "m";
        } elseif ($file_size >= 1024) {
            $file_size = round($file_size / 1024 * 100) / 100 . "k";
        } else {
            $file_size = $file_size . "b";
        }
        return $file_size;
    }

    function _dir($path)
    {
        return ($this->cfg['dir'] . $this->syslanai->getPath() . $path);
    }

}

?>
