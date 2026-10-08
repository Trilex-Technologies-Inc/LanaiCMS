<?php
require_once __DIR__ . '/../qrcode/qrcode.php';

/**
 * Render text as an inline SVG QR code (error correction M, or L for long text; 4-module quiet zone).
 * Byte mode is forced so UTF-8 text is never mistaken for Kanji. Returns '' if the text does not fit.
 */
function lanai_qr_svg($text, $label = '', $pixels = 240)
{
    $text = (string)$text;
    // The bundled encoder's capacity table covers versions 1-10; prefer level M and fall back to L for long text.
    $typeNumber = 0;
    foreach (array(QR_ERROR_CORRECT_LEVEL_M, QR_ERROR_CORRECT_LEVEL_L) as $level) {
        for ($candidate = 1; $candidate <= count(QRUtil::$QR_MAX_LENGTH); $candidate++) {
            if (strlen($text) <= QRUtil::getMaxLength($candidate, QR_MODE_8BIT_BYTE, $level)) { $typeNumber = $candidate; break 2; }
        }
    }
    if ($typeNumber === 0) return '';
    $qr = new QRCode();
    $qr->setErrorCorrectLevel($level);
    $qr->addData($text, QR_MODE_8BIT_BYTE);
    $qr->setTypeNumber($typeNumber);
    $qr->make();
    $quiet = 4;
    $count = $qr->getModuleCount();
    $size = $count + 2 * $quiet;
    $path = '';
    for ($row = 0; $row < $count; $row++) {
        for ($col = 0; $col < $count; $col++) {
            if ($qr->isDark($row, $col)) $path .= 'M' . ($col + $quiet) . ' ' . ($row + $quiet) . 'h1v1h-1z';
        }
    }
    $pixels = max(64, (int)$pixels);
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $size . ' ' . $size . '" width="' . $pixels . '" height="' . $pixels
        . '" style="max-width:100%;height:auto" shape-rendering="crispEdges" role="img" aria-label="' . htmlspecialchars((string)$label, ENT_QUOTES, 'UTF-8') . '">'
        . '<rect width="' . $size . '" height="' . $size . '" fill="#fff"/><path d="' . $path . '" fill="#000"/></svg>';
}