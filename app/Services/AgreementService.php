<?php
namespace App\Services;

use PDF;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;

class AgreementService
{
    public function pdf($client)
    {
        return PDF::loadView('agreement', compact('client'));
    }

    public function word($client)
    {
        $html = view('agreement', compact('client'))->render();

        // Strip doctype and wrapper tags to make HTML XML-friendly for PhpWord
        $html = preg_replace('/<!DOCTYPE.*?>/is', '', $html);
        $html = preg_replace('/<\/?(?:html|head|body)[^>]*>/i', '', $html);
        // Replace non-breaking spaces
        $html = str_replace('&nbsp;', ' ', $html);

        // Ensure void elements are XML self-closed for DOMDocument
        $html = preg_replace('#<(img|br|hr|meta|link)([^>/]*?)>#i', '<$1$2 />', $html);

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        Html::addHtml($section, $html, false, false);

        $path = storage_path("agreement_{$client->id}.docx");
        $phpWord->save($path, 'Word2007');

        return $path;
    }
}
