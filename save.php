<?php
// save.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$filename = 'curriculum_data.json';

// Falls die Datei noch nicht existiert, leere Struktur anlegen
if (!file_exists($filename)) {
    file_put_contents($filename, json_encode([]));
}

// GET-Anfrage: Daten an das Dashboard senden
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo file_get_contents($filename);
    exit;
}

// POST-Anfrage: Neue Daten vom Dashboard empfangen und speichern
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    
    // Validierung: Ist es valides JSON?
    if (json_decode($input) !== null) {
        // LOCK_EX verhindert, dass zwei Lehrer gleichzeitig schreiben und Daten korrumpieren
        if (file_put_contents($filename, $input, LOCK_EX) !== false) {
            echo json_encode(["status" => "success", "message" => "Daten erfolgreich gespeichert."]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Schreibfehler auf dem Server."]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Ungültiges JSON-Format."]);
    }
    exit;
}
?>
