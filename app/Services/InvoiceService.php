<?php
namespace App\Services;

use PDF;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;

class InvoiceService
{
    public function pdf($client)
    {
        return PDF::loadView('invoice', compact('client'));
    }

    public function word($client)
    {
        $html = view('invoice', compact('client'))->render();

        // Strip doctype and wrapper tags for PhpWord compatibility
        $html = preg_replace('/<!DOCTYPE.*?>/is', '', $html);
        $html = preg_replace('/<\/?(?:html|head|body)[^>]*>/i', '', $html);
        $html = str_replace('&nbsp;', ' ', $html);

        // Ensure void elements are XML self-closed for DOMDocument
        $html = preg_replace('#<(img|br|hr|meta|link)([^>/]*?)>#i', '<$1$2 />', $html);

        // Replace logo src with a safe JPEG path for PhpWord image handling
        try {
            $safeLogo = ImageSafe::toJpeg(public_path('logo.jpg'));
            $safeLogo = str_replace('\\', '/', $safeLogo);
            // Replace the entire src attribute that points to the logo with the safe path
                $html = preg_replace("/src=(\"|').*?logo\.jpg\\1/i", 'src="' . $safeLogo . '"', $html);
        } catch (\Throwable $e) {
            // If logo conversion fails, continue without replacing
        }

        $phpWord = new PhpWord();
        // Replace unordered lists with paragraphs (PhpWord HTML importer has limited list support)
        $html = str_replace(['<ul>', '</ul>', '<li>', '</li>'], ['', '', '<p>- ', '</p>'], $html);
        $section = $phpWord->addSection();
        Html::addHtml($section, $html, false, false);

        $path = storage_path("invoice_{$client->id}.docx");
        $phpWord->save($path, 'Word2007');

        return $path;
    }
}
