<?php
class ExplorerResponse
{
    static function stream($path, $name, $inline = false)
    {
        if (!is_file($path) || !is_readable($path)) throw new RuntimeException('File is not available.');
        $mime = class_exists('finfo') ? (new finfo(FILEINFO_MIME_TYPE))->file($path) : 'application/octet-stream';
        $safeInline = in_array($mime, array('image/jpeg','image/png','image/gif','image/webp','image/avif','application/pdf'), true);
        $handle = fopen($path, 'rb');
        if (!$handle) throw new RuntimeException('Unable to open file.');
        header('Content-Type: ' . ($inline && $safeInline ? $mime : 'application/octet-stream'));
        header('X-Content-Type-Options: nosniff');
        header("Content-Security-Policy: sandbox; default-src 'none'");
        header('Cache-Control: private, no-store');
        header('Content-Length: ' . filesize($path));
        header('Content-Disposition: ' . ($inline && $safeInline ? 'inline' : 'attachment') . "; filename=\"download\"; filename*=UTF-8''" . rawurlencode($name));
        if (session_status() === PHP_SESSION_ACTIVE) session_write_close();
        fpassthru($handle); fclose($handle);
    }

    static function archive($files, $root, $paths)
    {
        if (!class_exists('ZipArchive')) throw new RuntimeException('ZIP downloads require the PHP zip extension.');
        $temp = tempnam(sys_get_temp_dir(), 'lanai-zip-');
        $zip = new ZipArchive();
        if (!$temp || $zip->open($temp, ZipArchive::OVERWRITE) !== true) throw new RuntimeException('Unable to create download archive.');
        $count = 0; $bytes = 0; $seen = array();
        $add = function ($relative) use (&$add, $files, $root, $zip, &$count, &$bytes, &$seen) {
            if (isset($seen[$relative])) return;
            $seen[$relative] = true;
            $path = $files->path($root, $relative);
            if (++$count > 10000) throw new RuntimeException('Select fewer files for this download.');
            if (is_dir($path)) {
                if (!$zip->addEmptyDir($relative)) throw new RuntimeException('Unable to archive folder.');
                foreach (scandir($path) as $name) if ($name !== '.' && $name !== '..') $add($relative . '/' . $name);
            } else {
                $bytes += filesize($path);
                if ($bytes > 512 * 1024 * 1024) throw new RuntimeException('ZIP downloads are limited to 512 MB of source files. Download large files individually.');
                if (!$zip->addFile($path, $relative)) throw new RuntimeException('Unable to archive file.');
            }
        };
        try {
            foreach ($paths as $path) { if ($files->normal($path) === '') throw new RuntimeException('Select files or subfolders.'); $add($path); }
            if (!$zip->close()) throw new RuntimeException('Unable to finish archive.');
            return $temp;
        } catch (Throwable $e) { @$zip->close(); @unlink($temp); throw $e; }
    }
}
