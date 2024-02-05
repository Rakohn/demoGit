<?php

require_once '../vendor/autoload.php';

function removeAccents($str) {
    $accents = array(
        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'AE', 'Ç'=>'C',
        'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I',
        'Ð'=>'D', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O',
        'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
        'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'ae', 'ç'=>'c',
        'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i',
        'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o',
        'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'Ŕ'=>'R',
        'ŕ'=>'r',
    );

    return strtr($str, $accents);
}


function createAccentInsensitiveRegex($string) {
    $accentMapping = [
        'A' => '[AÀÁÂÃÄÅaàáâãäå]',
        'E' => '[EÈÉÊËeèéêë]',
        'I' => '[IÌÍÎÏiìíîï]',
        'O' => '[OÒÓÔÕÖØoòóôõöø]',
        'U' => '[UÙÚÛÜuùúûü]',
        'C' => '[CÇcç]',
        'N' => '[NÑnñ]'
    ];

    $normalizedString = removeAccents($string);
    $pattern = '';
    foreach (str_split($normalizedString) as $char) {
        $upperChar = strtoupper($char);
        $pattern .= array_key_exists($upperChar, $accentMapping) ? $accentMapping[$upperChar] : preg_quote($char, '/');
    }

    return '/' . $pattern . '/iu';
}

$lastnamePattern = createAccentInsensitiveRegex('humbert');
$firstnamePattern = createAccentInsensitiveRegex('béatrice');
$addressPattern = "/(\d+[\sA-zÀ-ÿ'-]*)(?:\r?\n|\r)?(\d{5})[\s,]*([A-zÀ-ÿ'-]+)/iu";
$phonePattern = '/(?:\+33|0)[1-9](?:[\s.-]?[0-9]{2}){4}/';
$emailPattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';

$patterns = [
    $emailPattern,
    $firstnamePattern,
    $lastnamePattern,
    $addressPattern,
    $phonePattern,
];

$source = 'CV/HUMBERT BEATRICE CV.docx';
$parser = new \Application\Services\Doc2Text($source);

//in somme situation, regexp pattern used by componant can throw an error/warning. So we suppress it explicitly
$text = preg_replace($patterns, '', $parser->convertToText());
echo '<pre>' . $text . '</pre>';