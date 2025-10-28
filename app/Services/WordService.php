<?php

namespace App\Services;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\Header;
use PhpOffice\PhpWord\TemplateProcessor;

class WordService
{
    public function create()
    {
        $vars = [
            '${documentType}' => 'INFORME',
            '${documentNumber}' => '0057-2025-TMT/OS',
        ];

        $templatePath = 'template.docx';
        $newPath = 'test.docx';
        if (!copy($templatePath, $newPath)) {
            throw new \Exception('No se pudo copiar el archivo template.');
        }

        $zip = new \ZipArchive;
        if ($zip->open($newPath) === true) {
            $index = $this->getHeaderIndex($zip);
            $headerContent = $zip->getFromName("word/header$index.xml");

            $newHeaderContent = $headerContent;
            foreach ($vars as $key => $value) {
                $newHeaderContent = str_replace($key, $value, $newHeaderContent);
            }

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

    public function getNewHeader($documentType)
    {
        $templatePath = 'template.docx';
        $index = 1;
        $newHeaderContent = '';

        $zip = new \ZipArchive;
        if ($zip->open($templatePath) === true) {
            $index = $this->getHeaderIndex($zip);
            $headerContent = $zip->getFromName("word/header$index.xml");
            $newHeaderContent = str_replace('${documentType}', $documentType, $headerContent);
            $zip->close();
        }

        return [$index, $newHeaderContent];
    }
}
