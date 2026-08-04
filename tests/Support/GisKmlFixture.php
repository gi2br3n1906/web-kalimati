<?php

declare(strict_types=1);

function sampleGisKml(): string
{
    return <<<'KML'
        <?xml version="1.0" encoding="UTF-8"?>
        <kml xmlns="http://www.opengis.net/kml/2.2">
            <Document>
                <Placemark>
                    <name>Balai Desa Kalimati</name>
                    <description><![CDATA[Pusat <strong>pelayanan</strong> warga]]></description>
                    <Point><coordinates>110.82340000,-7.21450000,125</coordinates></Point>
                </Placemark>
                <Placemark>
                    <name>Area Pertanian Dampit</name>
                    <description>Area sensor IoT</description>
                    <Polygon><outerBoundaryIs><LinearRing><coordinates>
                        110.8200,-7.2100,0
                        110.8240,-7.2100,0
                        110.8240,-7.2140,0
                        110.8200,-7.2140,0
                    </coordinates></LinearRing></outerBoundaryIs></Polygon>
                </Placemark>
            </Document>
        </kml>
        KML;
}
