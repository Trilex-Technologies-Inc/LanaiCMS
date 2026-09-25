<?php

/** In-place Media registrations keep stable public URLs across Explorer moves. */
class ExplorerMedia
{
    private $db;
    private $table;
    function __construct($db, $cfg)
    {
        $this->db = $db;
        $this->table = $cfg['tablepre'] . 'media';
    }

    function rows()
    {
        $result = $this->db->Execute('SELECT * FROM ' . $this->table . ' WHERE explorerRoot IS NOT NULL');
        if (!$result) throw new RuntimeException('Unable to read Media references.');
        $rows = array();
        while (!$result->EOF) { $rows[] = $result->fields; $result->MoveNext(); }
        return $rows;
    }

    function matched($row, $root, $path)
    {
        return $row['explorerRoot'] === $root && ($row['explorerPath'] === $path || str_starts_with($row['explorerPath'], $path . '/'));
    }

    private function changes($changes)
    {
        $done = array();
        foreach ($changes as $pair) {
            try { $this->update($pair[1]); $done[] = $pair[0]; }
            catch (Throwable $e) {
                foreach (array_reverse($done) as $original) $this->update($original);
                throw $e;
            }
        }
    }

    private function update($row)
    {
        $fields = array();
        foreach (array('explorerRoot','explorerPath','explorerTrashId','origName','mimeType','fileSize','width','height') as $key) $fields[] = $key . '=' . ($row[$key] === null ? 'NULL' : $this->db->qstr($row[$key]));
        if (!$this->db->Execute('UPDATE ' . $this->table . ' SET ' . implode(',', $fields) . ' WHERE mediaId=' . (int)$row['mediaId'])) throw new RuntimeException('Unable to update Media references.');
    }

    function relocate($root, $path, $newRoot, $newPath, $replacementId = null, $physical = null)
    {
        $changes = array();
        foreach ($this->rows() as $row) {
            if ($replacementId && $row['explorerTrashId'] === $replacementId) {
                $new = $this->replacementRow($row, $physical); $changes[] = array($row, $new); continue;
            }
            if ($row['explorerTrashId'] !== null || !$this->matched($row, $root, $path)) continue;
            $new = $row; $new['explorerRoot'] = $newRoot; $new['explorerPath'] = $newPath . substr($row['explorerPath'], strlen($path)); $new['origName'] = basename($new['explorerPath']);
            $changes[] = array($row, $new);
        }
        $this->changes($changes);
    }

    private function replacementRow($row, $path)
    {
        $row['explorerTrashId'] = null;
        $row['fileSize'] = filesize($path);
        $row['mimeType'] = (new finfo(FILEINFO_MIME_TYPE))->file($path);
        $dimensions = @getimagesize($path);
        $row['width'] = $dimensions ? $dimensions[0] : null;
        $row['height'] = $dimensions ? $dimensions[1] : null;
        return $row;
    }

    function replace($id, $path)
    {
        $changes = array();
        foreach ($this->rows() as $row) if ($row['explorerTrashId'] === $id) $changes[] = array($row, $this->replacementRow($row, $path));
        $this->changes($changes);
    }

    function markTrash($root, $path, $id)
    {
        $changes = array();
        foreach ($this->rows() as $row) if ($row['explorerTrashId'] === null && $this->matched($row, $root, $path)) { $new = $row; $new['explorerTrashId'] = $id; $changes[] = array($row, $new); }
        $this->changes($changes);
    }

    function restore($id, $root, $path, $newPath)
    {
        $changes = array();
        foreach ($this->rows() as $row) if ($row['explorerTrashId'] === $id) {
            $new = $row; $new['explorerTrashId'] = null; $new['explorerRoot'] = $root; $new['explorerPath'] = $newPath . substr($row['explorerPath'], strlen($path)); $new['origName'] = basename($new['explorerPath']); $changes[] = array($row, $new);
        }
        $this->changes($changes);
    }

    function purge($id)
    {
        if (!$this->db->Execute('DELETE FROM ' . $this->table . ' WHERE explorerTrashId=' . $this->db->qstr($id))) throw new RuntimeException('Files removed, but Media cleanup failed. Retry this trash item.');
    }

    function register($files, $root, $relative, $userId)
    {
        $path = $files->path($root, $relative);
        if (!is_file($path)) throw new RuntimeException('Select individual files to register.');
        foreach ($this->rows() as $row) if ($row['explorerRoot'] === $root && $row['explorerPath'] === $relative && $row['explorerTrashId'] === null) return (int)$row['mediaId'];
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($ext, array_merge(Media::$imageExtensions, Media::$fileExtensions), true)) throw new RuntimeException('This type is not supported by Media.');
        $image = in_array($ext, Media::$imageExtensions, true);
        $dimensions = $image ? @getimagesize($path) : false;
        if ($image && !$dimensions) throw new RuntimeException('Invalid image.');
        if (!class_exists('finfo')) throw new RuntimeException('The fileinfo extension is required.');
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($path);
        $values = array('fileName' => basename($path), 'origName' => basename($path), 'filePath' => '', 'mediaType' => $image ? 'image' : 'file', 'mimeType' => $mime, 'explorerRoot' => $root, 'explorerPath' => $relative);
        $columns = array_keys($values); $quoted = array_map(array($this->db, 'qstr'), array_values($values));
        $sql = 'INSERT INTO ' . $this->table . ' (' . implode(',', $columns) . ',fileSize,width,height,userId,createdAt) VALUES (' . implode(',', $quoted) . ',' . (int)filesize($path) . ',' . ($dimensions ? (int)$dimensions[0] : 'NULL') . ',' . ($dimensions ? (int)$dimensions[1] : 'NULL') . ',' . (int)$userId . ',NOW())';
        if (!$this->db->Execute($sql)) throw new RuntimeException('Unable to register file in Media.');
        $id = (int)$this->db->Insert_ID();
        $url = 'modules/explorer/media.php?id=' . $id;
        if (!$this->db->Execute('UPDATE ' . $this->table . ' SET filePath=' . $this->db->qstr($url) . ' WHERE mediaId=' . $id)) {
            $this->db->Execute('DELETE FROM ' . $this->table . ' WHERE mediaId=' . $id);
            throw new RuntimeException('Unable to create Media URL.');
        }
        return $id;
    }
}
