<?php
/**
 * List contiguous yellow-highlight text groups from a WordprocessingML document.xml.
 *
 * Usage:
 *   php scripts/list_docx_yellow.php path/to/document.xml
 */

if ($argc < 2) {
    fwrite(STDERR, "Usage: php scripts/list_docx_yellow.php path/to/document.xml\n");
    exit(1);
}

$path = $argv[1];
if (!is_file($path)) {
    fwrite(STDERR, "File not found: {$path}\n");
    exit(1);
}

$xml = file_get_contents($path);
if ($xml === false) {
    fwrite(STDERR, "Failed to read: {$path}\n");
    exit(1);
}

$dom = new DOMDocument();
$dom->preserveWhiteSpace = false;
$dom->loadXML($xml);

$xp = new DOMXPath($dom);
$xp->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

$paras = $xp->query('//w:p');
$groups = [];

foreach ($paras as $p) {
    $runs = $xp->query('.//w:r', $p);
    $cur = '';

    foreach ($runs as $r) {
        $isYellow = $xp->query('w:rPr/w:highlight[@w:val="yellow"]', $r)->length > 0;
        $texts = $xp->query('w:t', $r);
        $t = '';
        foreach ($texts as $tn) {
            $t .= $tn->nodeValue;
        }

        if ($isYellow) {
            $cur .= $t;
        } else {
            if ($cur !== '') {
                $groups[] = $cur;
                $cur = '';
            }
        }
    }

    if ($cur !== '') {
        $groups[] = $cur;
    }
}

foreach ($groups as $i => $g) {
    $g = str_replace(["\r", "\n", "\t"], ['\\r', '\\n', '\\t'], $g);
    echo $i . "::" . $g . PHP_EOL;
}

echo "TOTAL::" . count($groups) . PHP_EOL;

