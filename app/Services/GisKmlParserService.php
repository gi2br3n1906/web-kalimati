<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\Gis\ParsedKmlPlacemark;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use InvalidArgumentException;
use RuntimeException;
use ZipArchive;

final class GisKmlParserService
{
    public const MAX_FILE_SIZE_BYTES = 10 * 1024 * 1024;

    private const MAX_PLACEMARKS = 5000;

    /**
     * @return array<int, ParsedKmlPlacemark>
     */
    public function parseFile(string $filePath, ?string $sourceName = null): array
    {
        if (! is_file($filePath) || ! is_readable($filePath)) {
            throw new InvalidArgumentException('File KML/KMZ tidak dapat dibaca.');
        }

        $size = filesize($filePath);

        if ($size === false || $size > self::MAX_FILE_SIZE_BYTES) {
            throw new InvalidArgumentException('Ukuran file melebihi batas 10 MB.');
        }

        $extension = strtolower((string) pathinfo($sourceName ?? $filePath, PATHINFO_EXTENSION));

        return match ($extension) {
            'kml', 'xml' => $this->parse($this->readFile($filePath)),
            'kmz' => $this->parse($this->readKmlFromKmz($filePath)),
            default => throw new InvalidArgumentException('Format file harus KML, KMZ, atau XML.'),
        };
    }

    /**
     * @return array<int, ParsedKmlPlacemark>
     */
    public function parse(string $contents): array
    {
        $this->assertSafeXml($contents);

        $document = new DOMDocument;
        $document->resolveExternals = false;
        $document->substituteEntities = false;

        $previousErrorHandling = libxml_use_internal_errors(true);

        try {
            $loaded = $document->loadXML(
                $contents,
                LIBXML_NONET | LIBXML_NOBLANKS | LIBXML_NOCDATA | LIBXML_COMPACT,
            );

            if (! $loaded) {
                $error = libxml_get_last_error();
                $message = $error === false ? 'XML tidak valid.' : trim($error->message);

                throw new InvalidArgumentException("KML tidak valid: {$message}");
            }
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousErrorHandling);
        }

        $xpath = new DOMXPath($document);
        $nodes = $xpath->query('//*[local-name()="Placemark"]');

        if (! $nodes instanceof DOMNodeList) {
            throw new InvalidArgumentException('Elemen Placemark tidak dapat dibaca.');
        }

        if ($nodes->length > self::MAX_PLACEMARKS) {
            throw new InvalidArgumentException('File berisi lebih dari 5.000 Placemark.');
        }

        $placemarks = [];
        $ordinal = 0;

        foreach ($nodes as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            $ordinal++;
            $placemark = $this->parsePlacemark($xpath, $node, $ordinal);

            if ($placemark !== null) {
                $placemarks[] = $placemark;
            }
        }

        if ($placemarks === []) {
            throw new InvalidArgumentException('Tidak ditemukan Placemark Point atau Polygon yang valid.');
        }

        return $placemarks;
    }

    private function assertSafeXml(string $contents): void
    {
        if (trim($contents) === '') {
            throw new InvalidArgumentException('File KML kosong.');
        }

        if (strlen($contents) > self::MAX_FILE_SIZE_BYTES) {
            throw new InvalidArgumentException('Ukuran KML melebihi batas 10 MB.');
        }

        if (stripos($contents, '<!DOCTYPE') !== false || stripos($contents, '<!ENTITY') !== false) {
            throw new InvalidArgumentException('DOCTYPE dan ENTITY tidak diizinkan pada file KML.');
        }
    }

    private function readFile(string $filePath): string
    {
        $size = filesize($filePath);

        if ($size === false || $size > self::MAX_FILE_SIZE_BYTES) {
            throw new InvalidArgumentException('Ukuran file melebihi batas 10 MB.');
        }

        $contents = file_get_contents($filePath);

        if ($contents === false) {
            throw new RuntimeException('File KML tidak dapat dibaca.');
        }

        return $contents;
    }

    private function readKmlFromKmz(string $filePath): string
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('Ekstensi PHP ZIP diperlukan untuk membaca KMZ.');
        }

        $archive = new ZipArchive;
        $opened = $archive->open($filePath);

        if ($opened !== true) {
            throw new InvalidArgumentException('Arsip KMZ tidak valid atau tidak dapat dibuka.');
        }

        try {
            if ($archive->numFiles > 1000) {
                throw new InvalidArgumentException('Arsip KMZ berisi terlalu banyak file.');
            }

            $candidates = [];

            for ($index = 0; $index < $archive->numFiles; $index++) {
                $stat = $archive->statIndex($index);

                if (! is_array($stat) || ! isset($stat['name'], $stat['size'])) {
                    continue;
                }

                $name = (string) $stat['name'];

                if (! str_ends_with(strtolower($name), '.kml')) {
                    continue;
                }

                if ((int) $stat['size'] > self::MAX_FILE_SIZE_BYTES) {
                    throw new InvalidArgumentException('KML di dalam KMZ melebihi batas 10 MB.');
                }

                $candidates[] = [
                    'index' => $index,
                    'name' => $name,
                ];
            }

            if ($candidates === []) {
                throw new InvalidArgumentException('Arsip KMZ tidak berisi file KML.');
            }

            usort($candidates, static function (array $left, array $right): int {
                $leftName = (string) $left['name'];
                $rightName = (string) $right['name'];
                $leftRank = strtolower(basename($leftName)) === 'doc.kml' ? 0 : 1;
                $rightRank = strtolower(basename($rightName)) === 'doc.kml' ? 0 : 1;

                return ($leftRank <=> $rightRank) ?: strcmp($leftName, $rightName);
            });

            $contents = $archive->getFromIndex((int) $candidates[0]['index']);

            if ($contents === false) {
                throw new RuntimeException('KML di dalam arsip KMZ tidak dapat dibaca.');
            }

            return $contents;
        } finally {
            $archive->close();
        }
    }

    private function parsePlacemark(
        DOMXPath $xpath,
        DOMElement $placemark,
        int $ordinal,
    ): ?ParsedKmlPlacemark {
        $name = $this->normalizeText(
            $this->directChildText($xpath, $placemark, 'name'),
            255,
        ) ?? "Placemark {$ordinal}";
        $description = $this->normalizeText(
            $this->directChildText($xpath, $placemark, 'description'),
            2000,
        );

        $polygonNode = $this->firstElement(
            $xpath->query('.//*[local-name()="Polygon"]', $placemark),
        );

        if ($polygonNode !== null) {
            return $this->parsePolygon($xpath, $polygonNode, $name, $description);
        }

        $pointNode = $this->firstElement(
            $xpath->query('.//*[local-name()="Point"]', $placemark),
        );

        if ($pointNode !== null) {
            return $this->parsePoint($xpath, $pointNode, $name, $description);
        }

        return null;
    }

    private function parsePoint(
        DOMXPath $xpath,
        DOMElement $point,
        string $name,
        ?string $description,
    ): ParsedKmlPlacemark {
        $coordinatesNode = $this->firstElement(
            $xpath->query('.//*[local-name()="coordinates"]', $point),
        );

        if ($coordinatesNode === null) {
            throw new InvalidArgumentException("Point '{$name}' tidak memiliki coordinates.");
        }

        $coordinates = $this->parseCoordinateSequence($coordinatesNode->textContent, $name);
        [$longitude, $latitude] = $coordinates[0];

        return new ParsedKmlPlacemark(
            name: $name,
            description: $description,
            latitude: $latitude,
            longitude: $longitude,
            geometry: [
                'type' => 'Point',
                'coordinates' => [$longitude, $latitude],
            ],
        );
    }

    private function parsePolygon(
        DOMXPath $xpath,
        DOMElement $polygon,
        string $name,
        ?string $description,
    ): ParsedKmlPlacemark {
        $outerNode = $this->firstElement(
            $xpath->query(
                './*[local-name()="outerBoundaryIs"]//*[local-name()="coordinates"]',
                $polygon,
            ),
        );

        if ($outerNode === null) {
            $outerNode = $this->firstElement(
                $xpath->query('.//*[local-name()="coordinates"]', $polygon),
            );
        }

        if ($outerNode === null) {
            throw new InvalidArgumentException("Polygon '{$name}' tidak memiliki coordinates.");
        }

        $outerRing = $this->closeRing(
            $this->parseCoordinateSequence($outerNode->textContent, $name),
            $name,
        );
        $rings = [$outerRing];
        $innerNodes = $xpath->query(
            './*[local-name()="innerBoundaryIs"]//*[local-name()="coordinates"]',
            $polygon,
        );

        if ($innerNodes instanceof DOMNodeList) {
            foreach ($innerNodes as $innerNode) {
                if (! $innerNode instanceof DOMNode) {
                    continue;
                }

                $rings[] = $this->closeRing(
                    $this->parseCoordinateSequence($innerNode->textContent, $name),
                    $name,
                );
            }
        }

        [$longitude, $latitude] = $this->polygonCentroid($outerRing);

        return new ParsedKmlPlacemark(
            name: $name,
            description: $description,
            latitude: $latitude,
            longitude: $longitude,
            geometry: [
                'type' => 'Polygon',
                'coordinates' => $rings,
            ],
        );
    }

    /**
     * @return array<int, array{0: float, 1: float}>
     */
    private function parseCoordinateSequence(string $value, string $name): array
    {
        $tuples = preg_split('/\s+/', trim($value)) ?: [];
        $coordinates = [];

        foreach ($tuples as $tuple) {
            if ($tuple === '') {
                continue;
            }

            $parts = explode(',', $tuple);

            if (count($parts) < 2 || ! is_numeric($parts[0]) || ! is_numeric($parts[1])) {
                throw new InvalidArgumentException("Koordinat pada '{$name}' tidak valid.");
            }

            $longitude = (float) $parts[0];
            $latitude = (float) $parts[1];

            if (! is_finite($longitude) || ! is_finite($latitude)) {
                throw new InvalidArgumentException("Koordinat pada '{$name}' tidak valid.");
            }

            if ($longitude < -180 || $longitude > 180 || $latitude < -90 || $latitude > 90) {
                throw new InvalidArgumentException("Koordinat pada '{$name}' berada di luar rentang bumi.");
            }

            $coordinates[] = [$longitude, $latitude];
        }

        if ($coordinates === []) {
            throw new InvalidArgumentException("Koordinat pada '{$name}' kosong.");
        }

        return $coordinates;
    }

    /**
     * @param  array<int, array{0: float, 1: float}>  $ring
     * @return array<int, array{0: float, 1: float}>
     */
    private function closeRing(array $ring, string $name): array
    {
        $uniqueCoordinates = array_unique(
            array_map(
                static fn (array $coordinate): string => $coordinate[0].','.$coordinate[1],
                $ring,
            ),
        );

        if (count($uniqueCoordinates) < 3) {
            throw new InvalidArgumentException("Polygon '{$name}' harus memiliki minimal tiga titik unik.");
        }

        $first = $ring[0];
        $last = $ring[array_key_last($ring)];

        if ($first[0] !== $last[0] || $first[1] !== $last[1]) {
            $ring[] = $first;
        }

        return $ring;
    }

    /**
     * @param  array<int, array{0: float, 1: float}>  $ring
     * @return array{0: float, 1: float}
     */
    private function polygonCentroid(array $ring): array
    {
        $twiceArea = 0.0;
        $longitudeSum = 0.0;
        $latitudeSum = 0.0;

        for ($index = 0, $last = count($ring) - 1; $index < $last; $index++) {
            [$longitudeA, $latitudeA] = $ring[$index];
            [$longitudeB, $latitudeB] = $ring[$index + 1];
            $cross = ($longitudeA * $latitudeB) - ($longitudeB * $latitudeA);
            $twiceArea += $cross;
            $longitudeSum += ($longitudeA + $longitudeB) * $cross;
            $latitudeSum += ($latitudeA + $latitudeB) * $cross;
        }

        if (abs($twiceArea) > 0.000000000001) {
            return [
                $longitudeSum / (3 * $twiceArea),
                $latitudeSum / (3 * $twiceArea),
            ];
        }

        $vertices = array_slice($ring, 0, -1);
        $count = count($vertices);

        return [
            array_sum(array_column($vertices, 0)) / $count,
            array_sum(array_column($vertices, 1)) / $count,
        ];
    }

    private function directChildText(
        DOMXPath $xpath,
        DOMElement $parent,
        string $localName,
    ): string {
        $node = $this->firstElement(
            $xpath->query('./*[local-name()="'.$localName.'"]', $parent),
        );

        return $node?->textContent ?? '';
    }

    private function normalizeText(string $value, int $maxLength): ?string
    {
        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = strip_tags($value);
        $value = preg_replace('/\s+/u', ' ', trim($value)) ?? trim($value);

        if ($value === '') {
            return null;
        }

        return mb_substr($value, 0, $maxLength);
    }

    private function firstElement(DOMNodeList|false $nodes): ?DOMElement
    {
        if (! $nodes instanceof DOMNodeList) {
            return null;
        }

        $node = $nodes->item(0);

        return $node instanceof DOMElement ? $node : null;
    }
}
