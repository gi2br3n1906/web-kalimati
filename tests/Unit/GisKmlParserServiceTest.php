<?php

declare(strict_types=1);

use App\Services\GisKmlParserService;

it('parses Google Earth point and polygon placemarks into GeoJSON geometry', function (): void {
    $placemarks = (new GisKmlParserService)->parse(sampleGisKml());

    expect($placemarks)->toHaveCount(2)
        ->and($placemarks[0]->name)->toBe('Balai Desa Kalimati')
        ->and($placemarks[0]->description)->toBe('Pusat pelayanan warga')
        ->and($placemarks[0]->longitude)->toBe(110.8234)
        ->and($placemarks[0]->latitude)->toBe(-7.2145)
        ->and($placemarks[0]->geometry)->toBe([
            'type' => 'Point',
            'coordinates' => [110.8234, -7.2145],
        ])
        ->and($placemarks[1]->geometry['type'])->toBe('Polygon')
        ->and($placemarks[1]->geometry['coordinates'][0])->toHaveCount(5)
        ->and($placemarks[1]->geometry['coordinates'][0][0])
        ->toBe($placemarks[1]->geometry['coordinates'][0][4])
        ->and($placemarks[1]->longitude)->toEqualWithDelta(110.822, 0.0000001)
        ->and($placemarks[1]->latitude)->toEqualWithDelta(-7.212, 0.0000001);
});

it('reads the primary KML document from a KMZ archive', function (): void {
    $filePath = tempnam(sys_get_temp_dir(), 'kalimati-kmz-');

    expect($filePath)->not->toBeFalse();

    $archive = new ZipArchive;
    expect($archive->open($filePath, ZipArchive::OVERWRITE))->toBeTrue();
    $archive->addFromString('layers/secondary.kml', '<kml xmlns="http://www.opengis.net/kml/2.2"/>');
    $archive->addFromString('doc.kml', sampleGisKml());
    $archive->close();

    try {
        $placemarks = (new GisKmlParserService)->parseFile($filePath, 'peta-desa.kmz');

        expect($placemarks)->toHaveCount(2)
            ->and($placemarks[0]->name)->toBe('Balai Desa Kalimati');
    } finally {
        @unlink($filePath);
    }
});

it('rejects KML documents containing external entity declarations', function (): void {
    $maliciousKml = <<<'KML'
        <?xml version="1.0"?>
        <!DOCTYPE kml [<!ENTITY xxe SYSTEM "file:///etc/passwd">]>
        <kml xmlns="http://www.opengis.net/kml/2.2">
            <Placemark><name>&xxe;</name><Point><coordinates>110,-7,0</coordinates></Point></Placemark>
        </kml>
        KML;

    expect(fn (): array => (new GisKmlParserService)->parse($maliciousKml))
        ->toThrow(InvalidArgumentException::class, 'DOCTYPE dan ENTITY tidak diizinkan');
});
