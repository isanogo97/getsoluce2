<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use RuntimeException;
use ZipArchive;

class SpreadsheetImporter
{
    public function fromUploadedFile(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension === 'csv') {
            return $this->fromCsv($file->getPathname());
        }

        if ($extension === 'xlsx') {
            return $this->fromXlsx($file->getPathname());
        }

        throw new RuntimeException('Format de fichier non supporté. Utilisez CSV ou XLSX.');
    }

    private function fromCsv(string $path): array
    {
        $handle = fopen($path, 'r');
        if (!$handle) {
            throw new RuntimeException('Impossible de lire le fichier CSV.');
        }

        $rows = [];
        $delimiter = $this->detectDelimiter($handle);

        $header = null;
        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            if ($header === null) {
                $header = $this->normalizeHeaderRow($data);
                continue;
            }

            if (count(array_filter($data, fn ($value) => $value !== null && $value !== '')) === 0) {
                continue;
            }

            $rows[] = $this->mapRow($header, $data);
        }

        fclose($handle);

        return $rows;
    }

    private function fromXlsx(string $path): array
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new RuntimeException('Impossible de lire le fichier XLSX.');
        }

        $sharedStrings = $this->loadSharedStrings($zip);
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if (!$sheetXml) {
            throw new RuntimeException('Feuille XLSX introuvable.');
        }

        $xml = simplexml_load_string($sheetXml);
        if (!$xml || !isset($xml->sheetData)) {
            throw new RuntimeException('Format XLSX invalide.');
        }

        $rows = [];
        $header = null;

        foreach ($xml->sheetData->row as $row) {
            $rowData = [];
            foreach ($row->c as $cell) {
                $reference = (string) $cell['r'];
                $columnIndex = $this->columnIndexFromReference($reference);
                $value = (string) $cell->v;
                if ((string) $cell['t'] === 's') {
                    $value = $sharedStrings[(int) $value] ?? '';
                }
                $rowData[$columnIndex] = $value;
            }

            ksort($rowData);
            $rowValues = array_values($rowData);

            if ($header === null) {
                $header = $this->normalizeHeaderRow($rowValues);
                continue;
            }

            if (count(array_filter($rowValues, fn ($value) => $value !== null && $value !== '')) === 0) {
                continue;
            }

            $rows[] = $this->mapRow($header, $rowValues);
        }

        return $rows;
    }

    private function loadSharedStrings(ZipArchive $zip): array
    {
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
        if (!$sharedStringsXml) {
            return [];
        }

        $xml = simplexml_load_string($sharedStringsXml);
        if (!$xml) {
            return [];
        }

        $strings = [];
        foreach ($xml->si as $entry) {
            $text = '';
            if (isset($entry->t)) {
                $text = (string) $entry->t;
            } elseif (isset($entry->r)) {
                foreach ($entry->r as $run) {
                    $text .= (string) $run->t;
                }
            }
            $strings[] = $text;
        }

        return $strings;
    }

    private function detectDelimiter($handle): string
    {
        $position = ftell($handle);
        $line = fgets($handle);
        fseek($handle, $position);

        $delimiters = [',', ';', "\t"];
        $best = ',';
        $maxCount = 0;

        foreach ($delimiters as $delimiter) {
            $count = count(str_getcsv($line ?? '', $delimiter));
            if ($count > $maxCount) {
                $maxCount = $count;
                $best = $delimiter;
            }
        }

        return $best;
    }

    private function normalizeHeaderRow(array $header): array
    {
        return array_map(function ($value) {
            $value = strtolower(trim((string) $value));
            $value = str_replace([' ', '-'], '_', $value);
            $value = str_replace(['é', 'è', 'ê', 'ë'], 'e', $value);
            $value = str_replace(['à', 'â'], 'a', $value);
            $value = str_replace(['ù', 'û'], 'u', $value);
            $value = str_replace(['î', 'ï'], 'i', $value);
            return $value;
        }, $header);
    }

    private function mapRow(array $header, array $data): array
    {
        $row = [];
        foreach ($header as $index => $column) {
            $row[$column] = $data[$index] ?? null;
        }

        return $row;
    }

    private function columnIndexFromReference(string $reference): int
    {
        preg_match('/([A-Z]+)/', $reference, $matches);
        $letters = $matches[1] ?? 'A';

        $index = 0;
        foreach (str_split($letters) as $letter) {
            $index = $index * 26 + (ord($letter) - 64);
        }

        return $index - 1;
    }
}
