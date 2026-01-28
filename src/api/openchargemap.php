<?php
/******************************************************
 * openchargemap.php
 * Proxy server-side per configurazione API Open Charge Map
 ******************************************************/

// -- HEADER HTTP --
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// -- CONFIGURAZIONE API --
$API_KEY  = '8062adbb-9f3f-4cab-9a2d-ada3ef4a5594';
$BASE_URL = 'https://api.openchargemap.io/v3/poi/';

// -- VALIDAZIONE PARAMETRI INPUT --
$lat        = filter_input(INPUT_GET, 'lat', FILTER_VALIDATE_FLOAT);
$lon        = filter_input(INPUT_GET, 'lon', FILTER_VALIDATE_FLOAT);
$distance   = filter_input(INPUT_GET, 'distance', FILTER_VALIDATE_INT);
$maxresults = filter_input(INPUT_GET, 'maxresults', FILTER_VALIDATE_INT);

$distance   = $distance   ?: 20;
$maxresults = $maxresults ?: 30;

// -- CONTROLLO PARAMETRI OBBLIGATORI --
if ($lat === false || $lon === false) {
    http_response_code(400);
    echo json_encode([
        'error'   => 'Parametri non validi',
        'message' => 'Errore nei parametri relativi alla geolocalizzazione.'
    ]);
    exit;
}

// -- COSTRUZIONE QUERY STRING --
$query = http_build_query([
    'output'        => 'json',
    'latitude'      => $lat,
    'longitude'     => $lon,
    'distance'      => $distance,
    'distanceunit'  => 'KM',
    'maxresults'    => $maxresults,
    'compact'       => true,
    'verbose'       => false,
    'key'           => $API_KEY
]);

$requestUrl = $BASE_URL . '?' . $query;

// -- CHIAMATA API ESTERNA --
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => $requestUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 10,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_FAILONERROR    => false
]);

$response  = curl_exec($ch);
$httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);

curl_close($ch);

// -- GESTIONE ERRORI API OCM --
if ($httpCode !== 200) {
    http_response_code($httpCode);
    echo json_encode([
        'error'     => 'Errore API OpenChargeMap',
        'http_code'=> $httpCode
    ]);
    exit;
}

// -- DECODIFICA JSON --
$data = json_decode($response, true);

if ($data === null) {
    http_response_code(500);
    echo json_encode([
        'error'   => 'Errore parsing JSON',
        'message' => 'Risposta API non valida'
    ]);
    exit;
}

echo json_encode($data);
exit;
