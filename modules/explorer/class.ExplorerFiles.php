<?php

/** Filesystem operations confined to explicitly configured content roots. */
class ExplorerFiles
{
    public $roots = array();
    public $trashDir;
    private $media;
    private $siteRoot;
    private $webRoot;
    private const BLOCKED = array('cache', 'package', 'media', 'backup', 'log');
    public const UPLOAD_TYPES = array('jpg','jpeg','png','gif','webp','avif','pdf','txt','csv','md','json','xml','doc','docx','xls','xlsx','ppt','pptx','odt','ods','zip','mp3','wav','ogg','mp4','webm');

    function __construct($cfg, $media = null)
    {
        $this->siteRoot = realpath($cfg['dir']);
        $this->webRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
        $configured = $cfg['explorer_roots'] ?? array('data' => array('label' => 'Files', 'path' => $cfg['datadir']), 'images' => array('label' => 'Images', 'path' => $cfg['dir'] . '/images'));
        foreach ($configured as $id => $root) {
            $path = realpath($root['path']);
            if (preg_match('/^[a-zA-Z0-9_-]+$/D', (string) $id) && $path && is_dir($path)) $this->roots[$id] = array('label' => $root['label'], 'path' => rtrim($path, '/\\'));
        }
        if (!$this->roots) throw new RuntimeException('No Explorer content folders are available.');
        $this->trashDir = $cfg['explorer_trash'] ?? dirname($this->webRoot ?: $cfg['dir']) . '/.lanai-explorer-trash-' . substr(hash('sha256', $cfg['dir']), 0, 12);
        $this->media = $media;
    }

    function normal($path)
    {
        if (!is_string($path) || strlen($path) > 2000 || preg_match('/[\\\\:\x00-\x1f\x7f]/', $path) || str_starts_with($path, '/')) throw new RuntimeException('Invalid relative path.');
        $path = rtrim($path, '/');
        if ($path === '') return '';
        foreach (explode('/', $path) as $part) $this->name($part);
        return $path;
    }

    function name($name)
    {
        if (!is_string($name) || $name === '' || strlen($name) > 240 || preg_match('/[<>:"\/\\\\|?*\x00-\x1f\x7f]/', $name) || $name[0] === '.' || preg_match('/[. ]$/D', $name) || preg_match('/^(con|prn|aux|nul|com[0-9]|lpt[0-9])(?:\.|$)/i', $name)) throw new RuntimeException('Use a filename without reserved names, leading dots, or path characters.');
        return $name;
    }

    function path($root, $relative = '', $mustExist = true)
    {
        if (!isset($this->roots[$root])) throw new RuntimeException('Unknown content folder.');
        $relative = $this->normal($relative);
        $path = $this->roots[$root]['path'];
        $parts = $relative === '' ? array() : explode('/', $relative);
        foreach ($parts as $i => $part) {
            if (in_array(strtolower($part), self::BLOCKED, true)) throw new RuntimeException('This folder is managed internally and is not available in Explorer.');
            if (preg_match('/\.(php[0-9]*|phtml|phar|cgi|pl|asp[x]?|shtml|html?|svg|m?js)(\.|$)/i', $part)) throw new RuntimeException('Executable code and active web files are not managed by Explorer.');
            $path .= DIRECTORY_SEPARATOR . $part;
            if (is_link($path)) throw new RuntimeException('Symbolic links are not managed by Explorer.');
            if (!file_exists($path) && ($mustExist || $i < count($parts) - 1)) throw new RuntimeException('File or folder no longer exists.');
            if ($i < count($parts) - 1 && !is_dir($path)) throw new RuntimeException('Parent is not a folder.');
        }
        if (file_exists($path)) {
            $real = realpath($path);
            $base = $this->roots[$root]['path'];
            if (!$real || ($real !== $base && !str_starts_with($real, $base . DIRECTORY_SEPARATOR))) throw new RuntimeException('Path is outside the content folder.');
        }
        return $path;
    }

    function locked($callback)
    {
        if (!is_dir($this->trashDir) && !@mkdir($this->trashDir, 0700, true)) throw new RuntimeException('Explorer needs a writable trash folder outside the website. Configure explorer_trash.');
        if (is_link($this->trashDir)) throw new RuntimeException('Trash cannot be a symbolic link.');
        $trashReal = realpath($this->trashDir);
        if ($this->siteRoot && ($trashReal === $this->siteRoot || str_starts_with($trashReal, $this->siteRoot . DIRECTORY_SEPARATOR))) throw new RuntimeException('Configure a trash folder outside the website.');
        if ($this->webRoot && ($trashReal === $this->webRoot || str_starts_with($trashReal, $this->webRoot . DIRECTORY_SEPARATOR))) throw new RuntimeException('Configure a trash folder outside the public document root.');
        foreach ($this->roots as $root) {
            if ($trashReal === $root['path'] || str_starts_with($trashReal, $root['path'] . DIRECTORY_SEPARATOR)) throw new RuntimeException('Configure a trash folder outside the content folders.');
        }
        $handle = fopen($this->trashDir . '/.lock', 'c');
        if (!$handle || !flock($handle, LOCK_EX)) throw new RuntimeException('Unable to lock file operations.');
        try { return $callback(); } finally { flock($handle, LOCK_UN); fclose($handle); }
    }

    function kind($path)
    {
        if (is_dir($path)) return 'folder';
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($ext, array('jpg','jpeg','png','gif','webp','avif'))) return 'image';
        if ($ext === 'pdf') return 'pdf';
        if (in_array($ext, array('txt','csv','md','json','xml','log'))) return 'text';
        if (in_array($ext, array('mp3','wav','ogg','mp4','webm'))) return 'audio/video';
        if (in_array($ext, array('zip','gz','tar','7z'))) return 'archive';
        return 'document';
    }

    function entries($root, $dir, $filters = array())
    {
        $base = $this->path($root, $dir);
        if (!is_dir($base)) throw new RuntimeException('Select a folder.');
        $rows = array(); $visited = 0;
        $walk = function ($relative, $depth = 0) use (&$walk, &$rows, &$visited, $root, $filters) {
            if ($depth > 30) throw new RuntimeException('Search is too deep. Choose a smaller folder.');
            $items = scandir($this->path($root, $relative));
            if ($items === false) throw new RuntimeException('Unable to read folder.');
            foreach ($items as $name) {
                if ($name[0] === '.') continue;
                if (++$visited > 10000) throw new RuntimeException('More than 10,000 items. Search a smaller folder.');
                $rel = ($relative === '' ? '' : $relative . '/') . $name;
                try { $path = $this->path($root, $rel); } catch (RuntimeException $e) { continue; }
                $type = $this->kind($path); $modified = filemtime($path);
                $match = empty($filters['q']) || mb_stripos($name, $filters['q']) !== false;
                if (!empty($filters['type']) && $filters['type'] !== $type) $match = false;
                foreach (array('from', 'to') as $key) {
                    $value = $filters[$key] ?? '';
                    if ($value === '') continue;
                    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/D', $value)) throw new RuntimeException('Choose a valid date.');
                    $date = DateTime::createFromFormat('!Y-m-d', $value);
                    if (!$date || $date->format('Y-m-d') !== $value) throw new RuntimeException('Choose a valid date.');
                    if ($key === 'from' && $modified < $date->getTimestamp()) $match = false;
                    if ($key === 'to' && $modified >= $date->modify('+1 day')->getTimestamp()) $match = false;
                }
                if ($match) $rows[] = array('name' => $name, 'path' => $rel, 'type' => $type, 'size' => is_dir($path) ? 0 : filesize($path), 'modified' => $modified);
                if (!empty($filters['recursive']) && is_dir($path)) $walk($rel, $depth + 1);
            }
        };
        $walk($this->normal($dir));
        $sort = in_array($filters['sort'] ?? '', array('name','type','size','modified')) ? $filters['sort'] : 'name';
        $direction = ($filters['order'] ?? '') === 'desc' ? -1 : 1;
        usort($rows, function ($a, $b) use ($sort, $direction) {
            if (($a['type'] === 'folder') !== ($b['type'] === 'folder')) return $a['type'] === 'folder' ? -1 : 1;
            $comparison = in_array($sort, array('size','modified')) ? $a[$sort] <=> $b[$sort] : strnatcasecmp($a[$sort], $b[$sort]);
            return $direction * ($comparison ?: strcmp($a['path'], $b['path']));
        });
        $total = count($rows); $page = min(max(1, (int)($filters['page'] ?? 1)), max(1, (int)ceil($total / 100)));
        return array('items' => array_slice($rows, ($page - 1) * 100, 100), 'total' => $total, 'page' => $page, 'pages' => max(1, (int)ceil($total / 100)));
    }

    function createFolder($root, $dir, $name)
    {
        $relative = ($dir === '' ? '' : $this->normal($dir) . '/') . $this->name($name);
        $path = $this->path($root, $relative, false);
        if (file_exists($path) || !mkdir($path, 0755)) throw new RuntimeException('Folder exists or could not be created.');
        return $relative;
    }

    function destination($root, $relative, $collision)
    {
        if (!in_array($collision, array('skip','keep','replace'), true)) throw new RuntimeException('Choose a collision policy.');
        $path = $this->path($root, $relative, false);
        if (!file_exists($path)) return $relative;
        if ($collision === 'skip') throw new RuntimeException('Skipped: a file with this name already exists.');
        if ($collision === 'replace') {
            if (is_dir($path)) throw new RuntimeException('Folders cannot be replaced. Use another name.');
            return $relative;
        }
        $parent = dirname($relative); $parent = $parent === '.' ? '' : $parent . '/';
        $name = pathinfo($relative, PATHINFO_FILENAME); $ext = pathinfo($relative, PATHINFO_EXTENSION);
        for ($n = 2; $n < 10000; $n++) {
            $candidate = $parent . $name . ' (' . $n . ')' . ($ext === '' ? '' : '.' . $ext);
            if (!file_exists($this->path($root, $candidate, false))) return $candidate;
        }
        throw new RuntimeException('Unable to find a free filename.');
    }

    function checkTree($root, $relative)
    {
        $path = $this->path($root, $relative);
        if (is_dir($path)) {
            $count = 0;
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
            foreach ($iterator as $item) {
                if (++$count > 10000) throw new RuntimeException('Folder has too many items for one operation.');
                $rel = $relative . '/' . str_replace(DIRECTORY_SEPARATOR, '/', substr($item->getPathname(), strlen($path) + 1));
                $this->path($root, $rel);
            }
        }
    }

    function relocate($root, $source, $targetRoot, $target, $collision = 'skip')
    {
        $source = $this->normal($source); $target = $this->normal($target);
        if ($source === '' || $target === '') throw new RuntimeException('Content roots cannot be moved.');
        $old = $this->path($root, $source); $this->checkTree($root, $source);
        $new = $this->path($targetRoot, $target, false);
        if ($old === $new || str_starts_with($new . DIRECTORY_SEPARATOR, $old . DIRECTORY_SEPARATOR)) throw new RuntimeException('Choose a different destination outside the source folder.');
        $target = $this->destination($targetRoot, $target, $collision); $new = $this->path($targetRoot, $target, false);
        $backup = file_exists($new) ? $this->trash($targetRoot, $target) : null;
        if (!@rename($old, $new)) {
            if ($backup) $this->restore($backup, 'skip');
            throw new RuntimeException('Move failed. Check permissions and keep moves on the same filesystem.');
        }
        try { if ($this->media) $this->media->relocate($root, $source, $targetRoot, $target, $backup, $new); }
        catch (Throwable $e) { rename($new, $old); if ($backup) $this->restore($backup, 'skip'); throw $e; }
        return $target;
    }

    function writeManifest($id, $entry)
    {
        $file = $this->trashDir . '/' . $id . '.json';
        if (file_put_contents($file . '.tmp', json_encode($entry, JSON_THROW_ON_ERROR), LOCK_EX) === false || !rename($file . '.tmp', $file)) throw new RuntimeException('Unable to record trash entry.');
    }

    function trash($root, $relative)
    {
        $relative = $this->normal($relative);
        if ($relative === '') throw new RuntimeException('Content roots cannot be deleted.');
        $path = $this->path($root, $relative); $this->checkTree($root, $relative);
        $id = bin2hex(random_bytes(16));
        $entry = array('id' => $id, 'root' => $root, 'path' => $relative, 'deleted' => time(), 'type' => $this->kind($path));
        $this->writeManifest($id, $entry);
        if (!rename($path, $this->trashDir . '/' . $id . '.data')) { unlink($this->trashDir . '/' . $id . '.json'); throw new RuntimeException('Unable to move item to trash.'); }
        try { if ($this->media) $this->media->markTrash($root, $relative, $id); }
        catch (Throwable $e) { rename($this->trashDir . '/' . $id . '.data', $path); unlink($this->trashDir . '/' . $id . '.json'); throw $e; }
        return $id;
    }

    function trashEntry($id)
    {
        if (!is_string($id) || !preg_match('/^[a-f0-9]{32}$/D', $id)) throw new RuntimeException('Invalid trash item.');
        $file = $this->trashDir . '/' . $id . '.json';
        if (!is_file($file) || is_link($file)) throw new RuntimeException('Trash item not found.');
        return json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
    }

    function trashList()
    {
        $items = array();
        foreach (glob($this->trashDir . '/*.json') ?: array() as $file) {
            $id = basename($file, '.json');
            $items[] = $this->trashEntry($id);
        }
        usort($items, function ($a, $b) { return $b['deleted'] <=> $a['deleted']; });
        return $items;
    }

    function restore($id, $collision = 'skip')
    {
        $entry = $this->trashEntry($id);
        if ($collision === 'replace') throw new RuntimeException('Restore supports Skip or Keep both.');
        $target = $this->destination($entry['root'], $entry['path'], $collision);
        $path = $this->path($entry['root'], $target, false); $payload = $this->trashDir . '/' . $id . '.data';
        if (is_link($payload) || !@rename($payload, $path)) throw new RuntimeException('Unable to restore. Restore its parent folder first.');
        try { if ($this->media) $this->media->restore($id, $entry['root'], $entry['path'], $target); }
        catch (Throwable $e) { rename($path, $payload); throw $e; }
        unlink($this->trashDir . '/' . $id . '.json');
        return $target;
    }

    function purge($id)
    {
        $this->trashEntry($id);
        $path = $this->trashDir . '/' . $id . '.data';
        $remove = function ($file) use (&$remove) {
            if (is_link($file) || is_file($file)) { if (!unlink($file)) throw new RuntimeException('Unable to remove trash file.'); }
            elseif (is_dir($file)) { foreach (scandir($file) as $name) if ($name !== '.' && $name !== '..') $remove($file . '/' . $name); if (!rmdir($file)) throw new RuntimeException('Unable to remove trash folder.'); }
        };
        $remove($path);
        if ($this->media) $this->media->purge($id);
        unlink($this->trashDir . '/' . $id . '.json');
    }

    function upload($root, $dir, $file, $collision)
    {
        if (!isset($file['error'], $file['tmp_name'], $file['name']) || $file['error'] !== UPLOAD_ERR_OK || !is_string($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) throw new RuntimeException('Upload failed or exceeded the server limit.');
        $name = $this->name($file['name']); $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($ext, self::UPLOAD_TYPES, true) || preg_match('/\.(php[0-9]*|phtml|phar|cgi|pl|asp[x]?|shtml|htaccess)(\.|$)/i', $name)) throw new RuntimeException('This file type cannot be uploaded.');
        if (in_array($ext, array('jpg','jpeg','png','gif','webp','avif')) && !@getimagesize($file['tmp_name'])) throw new RuntimeException('The uploaded image is invalid.');
        $relative = ($dir === '' ? '' : $this->normal($dir) . '/') . $name;
        $target = $this->destination($root, $relative, $collision); $path = $this->path($root, $target, false);
        $backup = file_exists($path) ? $this->trash($root, $target) : null;
        if (!move_uploaded_file($file['tmp_name'], $path)) { if ($backup) $this->restore($backup); throw new RuntimeException('Unable to save uploaded file.'); }
        try { if ($backup && $this->media) $this->media->replace($backup, $path); }
        catch (Throwable $e) { unlink($path); $this->restore($backup); throw $e; }
        return $target;
    }
}
