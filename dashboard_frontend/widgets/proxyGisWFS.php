<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

function isJson($string) {
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}

function parseCoordinatesFromPosList($posListText) {
    $coords = preg_split('/\s+/', trim($posListText));
    $points = [];

    for ($i = 0; $i < count($coords); $i += 2) {
        $x = floatval($coords[$i]);
        $y = floatval($coords[$i + 1]);
        $points[] = [$x, $y, 0]; // Z = 0 fisso
    }

    return $points;
}

function parseCoordinatesFromPos($posText) {
    $coords = preg_split('/\s+/', trim($posText));
    return [floatval($coords[0]), floatval($coords[1]), 0]; // aggiunge z = 0
}

function parseCoordinatesFromCoordinates($text) {
    $coords = preg_split('/\s+/', trim($text));
    $points = [];

    foreach ($coords as $pair) {
        $parts = explode(',', $pair);
        if (count($parts) >= 2) {
            $points[] = [floatval($parts[0]), floatval($parts[1]), 0];
        }
    }

    return $points;
}



function extractGeometry($node) {
    $namespaces = $node->getNamespaces(true);

    // 1. MultiPolygon / MultiSurface (con posList o coordinates)
    $multiSurface = $node->xpath('.//gml:MultiSurface | .//gml:MultiPolygon');
    if ($multiSurface) {
        $polygons = [];

        // Cerchiamo Polygon anche in polygonMember e compatibilità vecchia
        foreach ($node->xpath('.//gml:Polygon') as $polygon) {
            $rings = [];

            // -- A) con posList
            $exterior = $polygon->xpath('.//gml:exterior//gml:posList | .//gml:outerBoundaryIs//gml:posList');
            if (!empty($exterior)) {
                $outer = parseCoordinatesFromPosList((string)$exterior[0]);
                $rings[] = $outer;
            } else {
                // -- B) con coordinates
                $coordinates = $polygon->xpath('.//gml:outerBoundaryIs//gml:coordinates | .//gml:exterior//gml:coordinates');
                if (!empty($coordinates)) {
                    $outer = parseCoordinatesFromCoordinates((string)$coordinates[0]);
                    $rings[] = $outer;
                }
            }

            // Interiore: posList o coordinates
            $interiors = $polygon->xpath('.//gml:interior//gml:posList | .//gml:innerBoundaryIs//gml:posList');
            foreach ($interiors as $inner) {
                $rings[] = parseCoordinatesFromPosList((string)$inner);
            }

            $intCoords = $polygon->xpath('.//gml:interior//gml:coordinates | .//gml:innerBoundaryIs//gml:coordinates');
            foreach ($intCoords as $inner) {
                $rings[] = parseCoordinatesFromCoordinates((string)$inner);
            }

            $polygons[] = $rings;
        }

        return [
            'type' => 'MultiPolygon',
            'coordinates' => $polygons
        ];
    }

    // 2. Point con gml:pos
    $points = $node->xpath('.//gml:Point/gml:pos');
    if (!empty($points)) {
        return [
            'type' => 'Point',
            'coordinates' => parseCoordinatesFromPos((string)$points[0])
        ];
    }

    // 3. Point con gml:coordinates
    $coordPoints = $node->xpath('.//gml:Point/gml:coordinates');
    if (!empty($coordPoints)) {
        return [
            'type' => 'Point',
            'coordinates' => parseCoordinatesFromCoordinates((string)$coordPoints[0])
        ];
    }

    // 4. LineString (fallback)
    $posLists = $node->xpath('.//gml:posList');
    if (!empty($posLists)) {
        $coords = parseCoordinatesFromPosList((string)$posLists[0]);
        return count($coords) === 1
            ? ['type' => 'Point', 'coordinates' => $coords[0]]
            : ['type' => 'LineString', 'coordinates' => $coords];
    }

    return null;
}




$url = $_SERVER['REQUEST_URI'];
$data = explode("?url=", $url);
if (!isset($data[1])) {
    echo json_encode(["error" => "URL mancante"]);
    exit;
}

$original_url = $data[1];
$data_content = file_get_contents($original_url);

if (isJson($data_content)) {
    echo $data_content;
    exit;
}

$clean_url = preg_replace('/([&?])outputFormat=application\/json(&?)/', '$1', $original_url);
$clean_url = rtrim($clean_url, '&?');

$parsedUrl = parse_url($clean_url);
parse_str($parsedUrl['query'], $queryParams);

$typeName = $queryParams['typeName'] ?? null;

$xml_content = file_get_contents($clean_url);
//echo($xml_content);
libxml_use_internal_errors(true);
$xml = simplexml_load_string($xml_content);


$features = [];
$namespaces = $xml->getNamespaces(true);

if ($typeName && strpos($typeName, ':') !== false) {
    list($prefix, $elementName) = explode(':', $typeName);

    if (isset($namespaces[$prefix])) {
        $xml->registerXPathNamespace($prefix, $namespaces[$prefix]);
        $results = $xml->xpath("//$prefix:$elementName");

        foreach ($results as $node) {
            $gml = $node->attributes('gml', true);
            $featureId = (string) $gml['id'];

            $geometry = extractGeometry($node);

            $props = [];
            $nsP = $node->getNamespaces(true)[$prefix] ?? null;
            if ($nsP) {
                foreach ($node->children($nsP) as $childName => $child) {
                    if ($childName === 'Shape') continue;
                    $local = preg_replace('/^.*:/', '', $childName);
                    $props[$local] = (string)$child;
                }
            }

            $features[] = [
                'type' => 'Feature',
                'id' => $featureId,
                'geometry_name' => 'the_geom',
                'geometry' => $geometry,
                'properties' => $props
            ];
        }
    } else {
        echo json_encode(["error" => "Namespace '$prefix' not found."]);
        exit;
    }
} else {
    echo json_encode(["error" => "Parametro 'typeName' not found."]);
    exit;
}

$result = [
    "type" => "FeatureCollection",
    "features" => $features
];

header("Content-Type: application/json");
echo json_encode($result);
?>
