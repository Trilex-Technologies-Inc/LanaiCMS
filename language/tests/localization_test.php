<?php
require_once __DIR__.'/../../include/lanai/localization.php';
function languageCheck($condition, $message) {
    if (!$condition) throw new RuntimeException($message);
}
$handle = fopen(__DIR__.'/../translations.tsv', 'rb');
$headers = fgetcsv($handle, 0, "\t", '"', '');
$seen = array();
while (($row = fgetcsv($handle, 0, "\t", '"', '')) !== false) {
    languageCheck(count($row) === 6, 'Malformed catalog row: '.$row[0]);
    $row = array_map(static function ($value) { return str_replace('\\n', "\n", $value); }, $row);
    languageCheck(!isset($seen[$row[0]]), 'Duplicate translation: '.$row[0]);
    $seen[$row[0]] = true;
    foreach ($row as $value) languageCheck(preg_match('//u', $value) === 1, 'Invalid UTF-8: '.$row[0]);
    foreach (array_slice($row, 1) as $value) {
        languageCheck($value !== '', 'Empty translation: '.$row[0]);
        preg_match_all('/\{[a-z]+\}/', $row[0], $sourcePlaceholders);
        preg_match_all('/\{[a-z]+\}/', $value, $translatedPlaceholders);
        languageCheck($sourcePlaceholders[0] === $translatedPlaceholders[0], 'Changed placeholders: '.$row[0]);
    }
}
fclose($handle);
$sources = array_merge(array(__DIR__.'/../lang-english.php'), glob(__DIR__.'/../../modules/*/language/lang-english.php'), array(__DIR__.'/../../install/language/lang-english.php'));
$missing = array();
foreach ($sources as $source) {
    preg_match_all('/define\(\s*"([A-Z_0-9]+)"\s*,\s*"((?:\\\\.|[^"\\\\])*)"\s*\)\s*;/', file_get_contents($source), $constants, PREG_SET_ORDER);
    foreach (array_slice($headers, 1) as $language) {
        $pack = str_replace('lang-english.php', 'lang-'.$language.'.php', $source);
        languageCheck(is_file($pack), 'Missing pack: '.$pack);
        $contents = file_get_contents($pack);
        token_get_all($contents, TOKEN_PARSE);
        foreach ($constants as $constant) {
            languageCheck(strpos($contents, "define('".$constant[1]."',") !== false, 'Missing constant: '.$constant[1]);
            $english = trim(stripcslashes($constant[2]));
            if ($english !== '' && !isset($seen[$english]) && !in_array($english, array('UTF-8','RSS','&raquo;','&laquo;'))) $missing[$english] = true;
        }
    }
}
languageCheck(lanai_translate('Save','spanish') === 'Guardar', 'Spanish lookup failed');
languageCheck(lanai_translate('Save','japanese') === '保存', 'Japanese lookup failed');
languageCheck(lanai_translate('Unknown phrase','spanish') === 'Unknown phrase', 'English fallback failed');
languageCheck(lanai_translate('Save','../../unknown') === 'Save', 'Unknown locale fallback failed');
languageCheck(lanai_translate('Save','thai','บันทึก') === 'บันทึก', 'Thai fallback failed');
languageCheck(lanai_language_locale('portuguese_brazil') === 'pt-BR', 'Brazil locale failed');
languageCheck(!$missing, 'Untranslated legacy phrases: '.implode(', ', array_keys($missing)));
require_once __DIR__.'/../../modules/language/module.php';
$class = new ReflectionClass('Language');
$languages = $class->newInstanceWithoutConstructor();
languageCheck(count($languages->getLanguage()) === 7, 'Selector must contain seven language packs only');
try { $languages->setUpdateLanguage('../../config'); throw new RuntimeException('Invalid language accepted'); }
catch (InvalidArgumentException $expected) {}
foreach ($sources as $source) {
    foreach (array_slice($headers, 1) as $language) {
        require str_replace('lang-english.php', 'lang-'.$language.'.php', $source);
        // Every pack is safe to load even when shared constants were already defined.
    }
}
echo 'PASS: catalog integrity, placeholders, constant coverage, UTF-8, fallbacks, locale codes and selector validation.'.PHP_EOL;
if (in_array('--missing', $argv, true)) foreach (array_keys($missing) as $phrase) echo $phrase.PHP_EOL;
