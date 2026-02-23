<?php

namespace App\Services;

use DOMDocument;
use DOMXPath;
use ZipArchive;

class DocxYellowReplaceService
{
    public function generate(string $templatePath, array $replacementsByGroupIndex, string $outputPath): string
    {
        if (!is_file($templatePath)) {
            throw new \RuntimeException("Template not found: {$templatePath}");
        }

        if (!copy($templatePath, $outputPath)) {
            throw new \RuntimeException("Failed to copy template.");
        }

        $zip = new ZipArchive();
        if ($zip->open($outputPath) !== true) {
            throw new \RuntimeException("Failed to open docx zip.");
        }

        $xmlPath = 'word/document.xml';
        $xml = $zip->getFromName($xmlPath);
        
        if ($xml === false) {
            $zip->close();
            throw new \RuntimeException("Missing document.xml");
        }

        // Process replacements
        $updatedXml = $this->replaceInDocumentXml($xml, $replacementsByGroupIndex);
        
        // Clean up all highlights
        $updatedXml = $this->removeAllHighlights($updatedXml);

        $zip->addFromString($xmlPath, $updatedXml);
        $zip->close();

        return $outputPath;
    }

    private function replaceInDocumentXml(string $xml, array $replacementsByGroupIndex): string
    {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($xml);

        $xp = new DOMXPath($dom);
        $xp->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        $paras = $xp->query('//w:p');
        $groupIndex = 0;

        foreach ($paras as $p) {
            $runs = $xp->query('.//w:r', $p);
            $inGroup = false;
            $groupTextNodes = [];

            foreach ($runs as $r) {
                // Check for yellow highlight
                $isYellow = $xp->query('w:rPr/w:highlight[@w:val="yellow"]', $r)->length > 0;
                $textNodes = $xp->query('w:t', $r);

                if ($isYellow) {
                    $inGroup = true;
                    foreach ($textNodes as $tn) {
                        $groupTextNodes[] = $tn;
                    }
                    continue;
                }

                if ($inGroup) {
                    $this->applyReplacement($groupIndex, $groupTextNodes, $replacementsByGroupIndex);
                    $groupIndex++;
                    $inGroup = false;
                    $groupTextNodes = [];
                }
            }

            if ($inGroup) {
                $this->applyReplacement($groupIndex, $groupTextNodes, $replacementsByGroupIndex);
                $groupIndex++;
            }
        }

        return $dom->saveXML();
    }

    private function applyReplacement(int $groupIndex, array $groupTextNodes, array $replacementsByGroupIndex): void
    {
        if (!array_key_exists($groupIndex, $replacementsByGroupIndex) || empty($groupTextNodes)) {
            return;
        }

        $replacement = (string) $replacementsByGroupIndex[$groupIndex];

        // Fill first node with full text, clear others in the same highlight group
        $groupTextNodes[0]->nodeValue = $replacement;
        for ($i = 1; $i < count($groupTextNodes); $i++) {
            $groupTextNodes[$i]->nodeValue = '';
        }
    }

    private function removeAllHighlights(string $xml): string
    {
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        $xp = new DOMXPath($dom);
        $xp->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        $highlights = $xp->query('//w:highlight');
        foreach ($highlights as $h) {
            $h->parentNode->removeChild($h);
        }

        return $dom->saveXML();
    }
}