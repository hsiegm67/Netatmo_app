<?php
// Stelle sicher, dass die Datei nur von deinem System aufgerufen wird
if (!isset($_SERVER['HTTP_REFERER'])) {
    header('Location: index.html');
    exit();
}

try {
    // Verbinden mit der Netatmo-Wetterstation (hier müssen deine API-Keys verwendet werden)
    $client_id = 'Meine ID';
    $client_secret = 'Meine ID';
    
    // URL zur Netatmo-API
    $url = 'https://api.netatmo.com/api/v2/getstationsdata';

    // Parameter für die Anfrage
    $params = [
        'device_id' => 'xx:xx:xx:xx:xx',
        'access_token' => $_GET['Dein Token']
		  ];

    // Erstelle eine cURL-Verbindung
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    
    // Führe die Anfrage aus
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($http_code != 200) {
        throw new Exception('Fehler bei der API-Anfrage');
    }

    // Zerlege die Antwort
    $data = json_decode($response, true);

    if (!isset($data['body']['devices'][0]['modules'])) {
        throw new Exception('Keine Geräte gefunden');
    }

    $wetterdaten = [
        'temp' => $data['body']['devices'][0]['modules'][0]['temperature'],
        'hum' => $data['body']['devices'][0]['modules'][0]['humidity'],
        'rain' => $data['body']['devices'][0]['modules'][2]['雨'] ?? '-'
    ];

    echo json_encode($wetterdaten);

} catch (Exception $e) {
    error_log('Fehler in fetch.php: ' . $e->getMessage());
    http_response_code(500);
    die(json_encode(['error' => 'Fehler beim Abrufen der Daten']));
}

finally {
    curl_close($ch);
}