<?php

namespace App\Services;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\Header;
use PhpOffice\PhpWord\TemplateProcessor;

class WordServicecopy
{
    public function create()
    {
        $vars = [
            '${documentType}' => 'INFORME',
            '${documentNumber}' => '0057-2025-TMT/OS',
            '${recipientName}' => 'CPC. ROBERT JESUS CASTILLO SALVADOR',
            '${recipientPosition}' => 'GERENTE DE ADMINISTRACION FINANZAS Y SISTEMAS',
            '${senderName}' => 'ING. CLODOMIRO ALEXANDER CRUZADO CHAVEZ',
            '${senderPosition}' => 'JEFE DE SISTEMAS',
            '${subject}' => 'PAPELETA DE DEPOSITO T-6 POR ENCARGO ECONÓMICO',
            '${documentDate}' => 'Trujillo, 23 de octubre del 2025',
            '${area}' => 'OFICINA DE SISTEMAS',
            '${documentExpNumber}' => '004253-2025'
        ];

        $recipientsMult = [[
            'recipientName' => 'ABOG. JUAN PÉREZ',
            'recipientPosition' => 'GERENTE DE ASESORÍA LEGAL'
        ], [
            'recipientName' => 'CPC. LUIS MÉNDEZ',
            'recipientPosition' => 'RESPONSABLE MESA DE PARTES'
        ]];

        $refs = [
            'REF. 1',
            'REF. 2',
            'REF. 3',
        ];

        $templatePath = 'template.docx';
        $newPath = 'test.docx';
        if (!copy($templatePath, $newPath)) {
            throw new \Exception('No se pudo copiar el archivo template.');
        }

        [$index, $newHeaderContent] = $this->getNewHeader(
            $vars
        );

        $zip = new \ZipArchive;
        if ($zip->open($newPath) === true) {
            $zip->addFromString("word/header$index.xml", $newHeaderContent);

            $zip->close();
        } else {
            throw new \Exception('No se pudo abrir el archivo DOCX.');
        }

        return true;
    }

    private function getHeaderIndex($zip)
    {
        $index = 1;
        $found = false;
        do {
            $headerContent = $zip->getFromName("word/header$index.xml");
            if (str_contains($headerContent, "documentType")) {
                $found = true;
            } else {
                $index++;
            }
        } while (!$found);

        return $index;
    }

    public function edit()
    {
        $documentType = 'MEMO MULT';
        $path = 'test.docx';

        [$index, $newHeaderContent] = $this->getNewHeader($documentType);

        $zip = new \ZipArchive;
        if ($zip->open($path) === true) {
            $zip->addFromString("word/header$index.xml", $newHeaderContent);

            $zip->close();
        } else {
            throw new \Exception('No se pudo abrir el archivo DOCX.');
        }

        return true;
    }

    private function getNewHeader($vars, $recipientsMult = [], $refs = [])
    {
        $templatePath = 'template.docx';
        $index = 1;
        $newHeaderContent = '';

        $zip = new \ZipArchive;
        if (!$zip->open($templatePath)) {
            throw new \Exception('It was not possible to open the template.');
        }

        $index = $this->getHeaderIndex($zip);
        $newHeaderContent = $zip->getFromName("word/header$index.xml");

        if (count($recipientsMult)) {
            $newHeaderContent = $this->replaceRecipientMulti($newHeaderContent, $recipientsMult);
        }

        if (count($refs)) {
            $newHeaderContent = $this->replaceRefs($newHeaderContent, $refs);
        }

        $newHeaderContent = $this->replaceVars($newHeaderContent, $vars);

        $zip->close();

        return [$index, $newHeaderContent];
    }

    private function replaceVars($headerContent, $vars)
    {
        foreach ($vars as $key => $value) {
            $headerContent = str_replace($key, $value, $headerContent);
        }

        return $headerContent;
    }

    private function replaceRecipientMulti($headerContent, $recipientsMult)
    {
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($headerContent);
        $parentP = $this->getParagraphOfVar($dom, '${recipientPosition}');

        foreach ($recipientsMult as $recipient) {
            $recipientName = $recipient['recipientName'];
            $recipientPosition = $recipient['recipientPosition'];
            $paragraphName = $this->getParagraph($dom, $recipientName, true);
            $paragraphPosition = $this->getParagraph($dom, $recipientPosition);

            $next = $parentP->nextSibling;

            if ($next) {
                $parentP->parentNode->insertBefore($paragraphName, $next);
                $parentP->parentNode->insertBefore($paragraphPosition, $next);
            } else {
                $parentP->parentNode->appendChild($paragraphName);
                $parentP->parentNode->appendChild($paragraphPosition);
            }

            $parentP = $parentP->nextSibling->nextSibling;
        }

        return $dom->saveXML();
    }

    private function getParagraph($dom, $textEntity, $isBold = false)
    {
        $newParagraph = $dom->createElementNS(
            'http://schemas.openxmlformats.org/wordprocessingml/2006/main',
            'w:p'
        );

        // $refAux = $this->getRefAux($dom);
        $space = $this->getSpace($dom);
        $text = $this->getText($dom, $textEntity, $isBold);

        // $newParagraph->appendChild($refAux);
        $newParagraph->appendChild($space);
        $newParagraph->appendChild($text);

        return $newParagraph;
    }

    private function getParagraphOfVar($dom, $var)
    {
        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        $searchText = $var;
        $nodes = $xpath->query("//w:t[contains(text(), '$searchText')]");

        if (!$nodes->length) {
            throw new \Exception($var . ' was not found.');
        }

        $targetNode = $nodes->item(0);

        $parentP = $targetNode->parentNode;
        while ($parentP && $parentP->nodeName !== 'w:p') {
            $parentP = $parentP->parentNode;
        }

        if (!$parentP) {
            throw new \Exception('Paragraph was not found.');
        }

        return $parentP;
    }

    private function getSpace($dom)
    {
        $pPr = $dom->createElement('w:pPr');

        $spacing = $dom->createElement('w:spacing');
        $spacing->setAttribute('w:before', 10);

        $ind = $dom->createElement('w:ind');
        $ind->setAttribute('w:left', 1173);

        $pPr->append($spacing, $ind);

        return $pPr;
    }

    private function getText($dom, $text, $isBold = false)
    {
        $run = $dom->createElement('w:r');
        $rPr = $dom->createElement('w:rPr');

        if ($isBold) {
            $b = $dom->createElement('w:b');
            $rPr->appendChild($b);

            $bCs = $dom->createElement('w:bCs');
            $rPr->appendChild($bCs);
        }

        $sz = $dom->createElement('w:sz');
        $sz->setAttribute('w:val', 18);
        $rPr->appendChild($sz);

        $textTag = $dom->createElement('w:t', $text);

        $run->appendChild($rPr);
        $run->appendChild($textTag);

        return $run;
    }

    private function replaceRefs($headerContent, $refs)
    {
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($headerContent);
        $parentP = $this->getParagraphOfVar($dom, '${subject}');
        $emptyParagraph = $this->getEmptyParagraph($dom);

        $next = $parentP->nextSibling;

        if ($next) {
            $parentP->parentNode->insertBefore($emptyParagraph, $next);
        } else {
            $parentP->parentNode->appendChild($emptyParagraph);
        }

        $parentP = $parentP->nextSibling;

        foreach ($refs as $key => $ref) {
            if ($key === 0) {
                $paragraphRef = $this->getParagraphRefWithLabel($dom, $ref);
            } else {
                $paragraphRef = $this->getParagraphRef($dom, $ref);
            }

            $next = $parentP->nextSibling;

            if ($next) {
                $parentP->parentNode->insertBefore($paragraphRef, $next);
            } else {
                $parentP->parentNode->appendChild($paragraphRef);
            }

            $parentP = $parentP->nextSibling;
        }

        return $dom->saveXML();
    }

    private function getEmptyParagraph($dom)
    {
        $newParagraph = $dom->createElementNS(
            'http://schemas.openxmlformats.org/wordprocessingml/2006/main',
            'w:p'
        );

        return $newParagraph;
    }

    private function getParagraphRefWithLabel($dom, $textEntity)
    {
        $newParagraph = $dom->createElementNS(
            'http://schemas.openxmlformats.org/wordprocessingml/2006/main',
            'w:p'
        );

        $refAux = $this->getRefAux($dom);
        $refLabel = $this->getRefLabel($dom);
        $text = $this->getTextWithTab($dom, $textEntity);

        $newParagraph->appendChild($refAux);
        $newParagraph->appendChild($refLabel);
        $newParagraph->appendChild($text);

        return $newParagraph;
    }

    private function getParagraphRef($dom, $textEntity)
    {
        $newParagraph = $dom->createElementNS(
            'http://schemas.openxmlformats.org/wordprocessingml/2006/main',
            'w:p'
        );

        $refAux = $this->getRefAux($dom);
        $text = $this->getTextWithTab($dom, $textEntity);

        $newParagraph->appendChild($refAux);
        $newParagraph->appendChild($text);

        return $newParagraph;
    }

    private function getTextWithTab($dom, $text)
    {
        $run = $dom->createElement('w:r');
        $rPr = $dom->createElement('w:rPr');

        $sz = $dom->createElement('w:sz');
        $sz->setAttribute('w:val', 18);
        $rPr->appendChild($sz);

        $tab = $dom->createElement('w:tab');
        $textTag = $dom->createElement('w:t', $text);

        $run->append($rPr, $tab, $textTag);

        return $run;
    }

    private function getRefLabel($dom)
    {
        $run = $dom->createElement('w:r');
        $rPr = $dom->createElement('w:rPr');

        $sz = $dom->createElement('w:sz');
        $sz->setAttribute('w:val', 18);
        $rPr->appendChild($sz);

        $textTag = $dom->createElement('w:t', 'REF.');

        $run->append($rPr, $textTag);

        return $run;
    }

    private function getRefAux($dom)
    {
        $pPr = $dom->createElement('w:pPr');
        $tabs = $dom->createElement('w:tabs');
        $tab = $dom->createElement('w:tab');
        $tab->setAttribute('w:val', 'left');
        $tab->setAttribute('w:pos', '1220');
        $tabs->appendChild($tab);
        $ind = $dom->createElement('w:ind');
        $ind->setAttribute('w:left', 3);
        $rPr = $dom->createElement('w:rPr');
        $sz = $dom->createElement('w:sz');
        $sz->setAttribute('w:val', 18);
        $rPr->appendChild($sz);
        $pPr->append($tabs, $ind, $rPr);

        return $pPr;
    }
}
