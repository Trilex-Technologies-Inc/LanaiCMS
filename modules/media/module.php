<?php

include_once("class.MediaPager.php");

/**
 * Media — a simple media library: upload, browse, and delete images/files
 * used across content (ctype image/file fields, articles, etc.).
 */
class Media
{
    var $uid;
    var $db;
    var $cfg;
    var $_sql;

    static $imageExtensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');
    static $fileExtensions = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'txt');

    function __construct()
    {
        global $db, $cfg;
        $this->db = $db;
        $this->cfg = $cfg;
        if (!empty($_SESSION['uid']))
            $this->uid = $_SESSION['uid'];
    }

    function getMedia()
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "media ORDER BY createdAt DESC";
        $this->_sql = $sql;
        $rs = $this->db->execute($sql);
        return $rs;
    }

    function canManage()
    {
        if (empty($_SESSION['uid'])) return false;
        $user = new User();
        return $user->getUserPrivilege($_SESSION['uid']) === 'a';
    }

    // Additive upgrade for existing installations; called only by admin screens.
    function ensureLibraryFields()
    {
        $table = $this->cfg['tablepre'] . 'media';
        $columns = $this->db->MetaColumns($table);
        if (!$columns) return false;
        $names = array_map('strtolower', array_keys($columns));
        foreach (array('title' => "varchar(255) NOT NULL DEFAULT ''", 'caption' => 'text NULL', 'explorerRoot' => 'varchar(64) NULL', 'explorerPath' => 'text NULL', 'explorerTrashId' => 'varchar(32) NULL') as $name => $definition) {
            if (!in_array(strtolower($name), $names, true) && !$this->db->Execute("ALTER TABLE " . $table . " ADD COLUMN " . $name . " " . $definition)) return false;
        }
        return true;
    }

    function libraryWhere($filters)
    {
        $conditions = array('explorerTrashId IS NULL');
        if ($filters['q'] !== '') {
            $needle = $this->db->qstr('%' . str_replace(array('!', '%', '_'), array('!!', '!%', '!_'), $filters['q']) . '%');
            $conditions[] = "(origName LIKE $needle ESCAPE '!' OR title LIKE $needle ESCAPE '!' OR caption LIKE $needle ESCAPE '!' OR altText LIKE $needle ESCAPE '!')";
        }
        if ($filters['type'] === 'image') $conditions[] = "mediaType='image'";
        if ($filters['type'] === 'file') $conditions[] = "mediaType='file'";
        if ($filters['type'] === 'pdf') $conditions[] = "mimeType='application/pdf'";
        foreach (array('from', 'to') as $key) {
            $date = $filters[$key];
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/D', $date)) continue;
            $parsed = DateTime::createFromFormat('!Y-m-d', $date);
            if ($parsed && $parsed->format('Y-m-d') === $date) {
                $conditions[] = 'createdAt ' . ($key === 'from' ? '>= ' : '< ') . $this->db->qstr($key === 'from' ? $date : $parsed->modify('+1 day')->format('Y-m-d'));
            }
        }
        return implode(' AND ', $conditions);
    }

    function updateDetails($id, $title, $caption, $altText)
    {
        return $this->db->Execute('UPDATE ' . $this->cfg['tablepre'] . 'media SET title=' . $this->db->qstr($title) . ', caption=' . $this->db->qstr($caption) . ', altText=' . $this->db->qstr($altText) . ' WHERE mediaId=' . (int) $id);
    }

    function getMediaList($rows = 24)
    {
        $this->getMedia();
        $pager = new MediaPager($this->db, $this->_sql, true);
        $pager->Render($rows);
    }

    function getMediaById($mediaId)
    {
        $sql = "SELECT * FROM " . $this->cfg['tablepre'] . "media WHERE mediaId=" . intval($mediaId);
        $rs = $this->db->execute($sql);
        return $rs;
    }

    /**
     * True if uploads can be stored (either the media folder already exists
     * and is writable, or its parent is writable so it can be created).
     */
    function isStorageWritable()
    {
        $base = $this->cfg['datadir'] . DIRECTORY_SEPARATOR . 'media';
        if (is_dir($base)) {
            return is_writable($base);
        }
        return is_dir(dirname($base)) && is_writable(dirname($base));
    }

    /**
     * Validate + store an uploaded file, generating a thumbnail for images
     * when the GD extension is available. Returns the new mediaId, or false.
     */
    function saveUpload($fileArr, $altText = '')
    {
        if (!isset($fileArr['error'], $fileArr['name'], $fileArr['tmp_name'], $fileArr['size']) || $fileArr['error'] !== UPLOAD_ERR_OK || !is_string($fileArr['name']) || !is_string($fileArr['tmp_name']) || !is_numeric($fileArr['size'])) return false;
        if (!class_exists('finfo') || mb_strlen(basename($fileArr['name'])) > 255) return false;
        if (empty($fileArr['tmp_name']) || !is_uploaded_file($fileArr['tmp_name'])) {
            return false;
        }

        $ext = strtolower(pathinfo(basename($fileArr['name']), PATHINFO_EXTENSION));
        $isImage = in_array($ext, self::$imageExtensions, true);
        $isFile = in_array($ext, self::$fileExtensions, true);
        if (!$isImage && !$isFile) {
            return false;
        }

        $imgInfo = false;
        if ($isImage) {
            $imgInfo = @getimagesize($fileArr['tmp_name']);
            if ($imgInfo === false) {
                return false;
            }
        }

        $subdir = $isImage ? 'image' : 'file';
        $destDir = $this->cfg['datadir'] . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . $subdir;
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        $filename = bin2hex(random_bytes(12)) . '.' . $ext;
        $destPath = $destDir . DIRECTORY_SEPARATOR . $filename;
        if (!move_uploaded_file($fileArr['tmp_name'], $destPath)) {
            return false;
        }

        $webPath = 'datacenter/media/' . $subdir . '/' . $filename;
        $thumbWebPath = null;
        if ($isImage) {
            $thumbWebPath = $this->generateThumbnail($destPath, $destDir, $filename, $ext);
        }

        // Derive MIME type server-side from file contents (more reliable than client-provided type)
        $fileInfo = new finfo(FILEINFO_MIME_TYPE);
        $serverMimeType = $fileInfo->file($destPath);

        $sql = "INSERT INTO " . $this->cfg['tablepre'] . "media
                (fileName, origName, filePath, thumbPath, mediaType, mimeType, fileSize, width, height, altText, userId, createdAt)
                VALUES (" . $this->db->qstr($filename) . ", " . $this->db->qstr(basename($fileArr['name'])) . ",
                        " . $this->db->qstr($webPath) . ", " . $this->db->qstr($thumbWebPath) . ",
<<<<<<< HEAD
                        " . $this->db->qstr($isImage ? 'image' : 'file') . ", " . $this->db->qstr($serverMimeType) . ",
=======
                        " . $this->db->qstr($isImage ? 'image' : 'file') . ", " . $this->db->qstr(function_exists('mime_content_type') ? @mime_content_type($destPath) : (isset($fileArr['type']) ? $fileArr['type'] : '')) . ",
>>>>>>> 471332ec9ca38ac3c0ce5e2519e3b9116636b7c1
                        " . intval($fileArr['size']) . ",
                        " . ($imgInfo ? intval($imgInfo[0]) : "NULL") . ", " . ($imgInfo ? intval($imgInfo[1]) : "NULL") . ",
                        " . $this->db->qstr($altText) . ", " . (int) $this->uid . ", NOW())";
        if (!$this->db->Execute($sql)) {
            @unlink($destPath);
            if ($thumbWebPath) @unlink($destDir . DIRECTORY_SEPARATOR . 'thumb_' . $filename);
            return false;
        }
        return $this->db->Insert_ID();
    }

    /**
     * Best-effort thumbnail (max 300px) using GD; returns web path or null
     * if GD isn't available for this image type.
     */
    function generateThumbnail($srcPath, $destDir, $filename, $ext)
    {
        if (!function_exists('imagecreatetruecolor')) {
            return null;
        }

        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                $src = function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($srcPath) : false;
                break;
            case 'png':
                $src = function_exists('imagecreatefrompng') ? @imagecreatefrompng($srcPath) : false;
                break;
            case 'gif':
                $src = function_exists('imagecreatefromgif') ? @imagecreatefromgif($srcPath) : false;
                break;
            case 'webp':
                $src = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($srcPath) : false;
                break;
            default:
                $src = false;
        }
        if (!$src) {
            return null;
        }

        $srcW = imagesx($src);
        $srcH = imagesy($src);
        $maxDim = 300;
        if ($srcW <= $maxDim && $srcH <= $maxDim) {
            imagedestroy($src);
            return null; // already small enough, no separate thumb needed
        }

        $ratio = min($maxDim / $srcW, $maxDim / $srcH);
        $thumbW = max(1, (int) round($srcW * $ratio));
        $thumbH = max(1, (int) round($srcH * $ratio));

        $thumb = imagecreatetruecolor($thumbW, $thumbH);
        if ($ext === 'png' || $ext === 'gif' || $ext === 'webp') {
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
        }
        imagecopyresampled($thumb, $src, 0, 0, 0, 0, $thumbW, $thumbH, $srcW, $srcH);

        $thumbFilename = 'thumb_' . $filename;
        $thumbPath = $destDir . DIRECTORY_SEPARATOR . $thumbFilename;
        $ok = false;
        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                $ok = imagejpeg($thumb, $thumbPath, 82);
                break;
            case 'png':
                $ok = imagepng($thumb, $thumbPath);
                break;
            case 'gif':
                $ok = imagegif($thumb, $thumbPath);
                break;
            case 'webp':
                $ok = function_exists('imagewebp') ? imagewebp($thumb, $thumbPath) : false;
                break;
        }
        imagedestroy($src);
        imagedestroy($thumb);

        return $ok ? ('datacenter/media/image/' . $thumbFilename) : null;
    }

    function setDeleteMedia($mediaId)
    {
        $rs = $this->getMediaById($mediaId);
        if ($rs->recordcount() < 1) {
            return false;
        }
        if (!empty($rs->fields['explorerRoot'])) {
            require_once dirname(__DIR__) . '/explorer/class.ExplorerFiles.php';
            require_once dirname(__DIR__) . '/explorer/class.ExplorerMedia.php';
            $row = $rs->fields;
            if (!empty($row['explorerTrashId'])) return false;
            $files = new ExplorerFiles($this->cfg, new ExplorerMedia($this->db, $this->cfg));
            $files->locked(function () use ($files, $row) { $files->trash($row['explorerRoot'], $row['explorerPath']); });
            return true;
        }
        $base = dirname(dirname(__DIR__)); // web root (modules/media -> modules -> root)
        foreach (array($rs->fields['filePath'], $rs->fields['thumbPath']) as $relPath) {
            if (!empty($relPath)) {
                $abs = $base . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relPath);
                if (is_file($abs)) {
                    @unlink($abs);
                }
            }
        }
        $sql = "DELETE FROM " . $this->cfg['tablepre'] . "media WHERE mediaId=" . intval($mediaId);
        return $this->db->Execute($sql);
    }
}

?>
