<?php
/** Shared interface translations. Missing entries deliberately fall back to English. */
function lanai_languages()
{
    return array(
        'english'=>array('label'=>'English', 'locale'=>'en'),
        'thai'=>array('label'=>'ไทย (Thai)', 'locale'=>'th'),
        'spanish'=>array('label'=>'Español (Spanish)', 'locale'=>'es'),
        'german'=>array('label'=>'Deutsch (German)', 'locale'=>'de'),
        'french'=>array('label'=>'Français (French)', 'locale'=>'fr'),
        'portuguese_brazil'=>array('label'=>'Português do Brasil', 'locale'=>'pt-BR'),
        'japanese'=>array('label'=>'日本語 (Japanese)', 'locale'=>'ja'),
    );
}

function lanai_language_locale($language)
{
    return lanai_languages()[$language]['locale'] ?? 'en';
}

function lanai_interface_labels()
{
    $labels = array();
    $handle = fopen(__DIR__.'/../../language/translations.tsv', 'rb');
    if (!$handle) return $labels;
    fgetcsv($handle, 0, "\t", '"', '');
    while (($row = fgetcsv($handle, 0, "\t", '"', '')) !== false) {
        $key = str_replace('\\n', "\n", $row[0]);
        $labels[$key] = htmlspecialchars(html_entity_decode(lanai_translate($key), ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
    }
    fclose($handle);
    return $labels;
}

function lanai_translate($english, $language = null, $thai = null)
{
    global $cfg;
    $language = $language ?? ($cfg['lang'] ?? 'english');
    if ($language === 'thai') return $thai ?? $english;
    static $catalog = null;
    if ($catalog === null) {
        $catalog = array();
        $handle = fopen(__DIR__.'/../../language/translations.tsv', 'rb');
        if ($handle) {
            $languages = fgetcsv($handle, 0, "\t", '"', '');
            while (($row = fgetcsv($handle, 0, "\t", '"', '')) !== false) {
                if (count($row) !== count($languages)) continue;
                $row = array_map(static function ($value) { return str_replace('\\n', "\n", $value); }, $row);
                foreach (array_slice($languages, 1, null, true) as $column=>$name) {
                    if ($row[$column] !== '') $catalog[$name][$row[0]] = $row[$column];
                }
            }
            fclose($handle);
        }
    }
    return $catalog[$language][trim($english)] ?? $english;
}
