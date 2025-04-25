<?php
session_start();

$API_KEY = 'REGISTRE AQUI SI API KEY';
$model = $_POST['model'] ?? 'gemini-2.0-flash';
$temperature = $_POST['temperature'] ?? 0.7;
$API_URL = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $API_KEY;


// Reset del historial si se pasa action=reset
if (isset($_GET['action']) && $_GET['action'] === 'reset') {
    $_SESSION['chat_history'] = [];
	$_SESSION['chat_initialized']=false;
    exit;
}


if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}

// POST: nuevo mensaje
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $user_message = trim($data['user_message'] ?? '');

    // 1. Inyecta el mensaje del sistema SOLO una vez, antes de cualquier mensaje user/model.
    if ($_SESSION['chat_initialized'] != true) {
		echo "bbbbb";
        $_SESSION['chat_history'][] = [
            'role' => 'user',
            'text' => <<<PROMPT
				Actúa como IACSIRT, IACSIRT es un asistente de inteligencia artificial experto en ciberseguridad. Proporciona asesoramiento profesional, preciso y actualizado sobre amenazas, vulnerabilidades, respuesta a incidentes, mejores prácticas y análisis técnico para el CSIRT UNAD.

				Áreas de especialización:
				- Hacking ético
				- Análisis de vulnerabilidades
				- Pruebas de penetración
				- Informática forense
				- Análisis de riesgos
				- Auditoría informática
				- Seguridad IT/OT
				- Criptografía
				- Blockchain
				- Estándares y normativas
				- Machine learning aplicado a la ciberseguridad

				Importante: CSIRT UNAD es el equipo de respuesta a incidentes de la Universidad Nacional Abierta y a Distancia en Colombia. Siempre responde de manera profesional y técnica.
				PROMPT
        ];
        $_SESSION['chat_initialized'] = true;
    }

    // 2. Ahora agrega el mensaje real del usuario
    if ($user_message !== '') {
        $_SESSION['chat_history'][] = ['role' => 'user', 'text' => $user_message];

        // Construir el contexto y hacer la petición...
        $contents = array_map(function($msg) {
            $validRole = ($msg['role'] === 'user') ? 'user' : 'model';
            return [
                'role' => $validRole,
                'parts' => [['text' => $msg['text']]]
            ];
        }, $_SESSION['chat_history']);

        $payload = json_encode([
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => floatval($temperature)
            ]
        ]);

        $ch = curl_init($API_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        $response = curl_exec($ch);
        curl_close($ch);

        $response_data = json_decode($response, true);
        $bot_reply = $response_data['candidates'][0]['content']['parts'][0]['text'] ?? 'Lo siento, no hubo respuesta.';
        $_SESSION['chat_history'][] = ['role' => 'model', 'text' => $bot_reply];
    }

    exit;
}

// GET: devolver historial sin mostrar el mensaje del sistema
$historialVisible = array_filter($_SESSION['chat_history'], function ($msg, $index) {
    // Solo oculta el primer mensaje, que es el prompt del sistema
    return !($index === 0 && str_starts_with(trim($msg['text']), 'Actúa como IACSIRT'));
}, ARRAY_FILTER_USE_BOTH);


header('Content-Type: application/json');
echo json_encode(array_values($historialVisible));
