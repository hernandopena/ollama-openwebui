<?php
class ClienteIA {
	function consulta($arrayData) {
		$retorno = array();

		// Si tienes historial/contexto, ejemplo:
		$context = [
			// ["role" => "user", "text" => "¿primer pegunta?"],
			// ["role" => "model", "text" => "respuesta a primer pregunta"],
			// ["role" => "user", "text" => "¿segunda pegunta?"],
			// ["role" => "model", "text" => "respuesta a segunda pregunta"],
		];

		// Endpoint de tu API (ajusta host si es remoto)
		$url = 'http://localhost:9000/chat';

		$data = [
			"message" => $arrayData['mensaje'],
			"context" => $context
		];

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json'
		]);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));




		$response = curl_exec($ch);
		if ($response === false) {
			$retorno['estado']="ERROR";
			$retorno['mensaje']="Error en la consulta: " . curl_error($ch);
			curl_close($ch);
			exit;
		}
		curl_close($ch);

		$result = json_decode($response, true);

		// Si la respuesta fue exitosa:
		if (isset($result['response'])) {
			$retorno['estado']="OK";
			$retorno['mensaje']=nl2br(htmlspecialchars($result['response']));
		}
		elseif (isset($result['error'])) {
			echo "Error de API: <pre>" . htmlspecialchars(print_r($result['error'], true)) . "</pre>";
			$retorno['estado']="ERROR";
			$retorno['mensaje']="Error de API: <pre>" . htmlspecialchars(print_r($result['error'], true)) . "</pre>";
		}
		else {
			$retorno['estado']="ERROR";
			$retorno['mensaje']="Respuesta inesperada: <pre>" . htmlspecialchars($response) . "</pre>";
		}

		return ($retorno);

	}

}

?>
