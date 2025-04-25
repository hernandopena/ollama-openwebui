<html>
<head>
</head>

<body>

<?php
include("clase/clienteIA.php"); //ruta relativa en include de firmas
$clienteIA = new ClienteIA();

$arrayData = array();

$arrayData['mensaje']="Eres un asistente especializado en ciberseguridad para la institucion TecnoSYS lider comercial en productos informatios, asi como asesoria y consultoria en soluciones tecnologicas.

Tu tarea es realizar una valoración cualitativa del impacto que tendría la materialización de una amenaza específica sobre cada dimensión de la seguridad de un activo de información, considerando su tipo, la descripción del activo y la amenaza identificada, según la metodología MAGERIT.

Se te suministra el nombre y descripción del activo, su tipo, la amenaza asociada y el nivel actual asignado a cada dimensión de la seguridad en la siguiente escala: 1:bajo, 2:medio y 3:alto. la Criticidad inicial bajo la escala: 1-7:bajo, 8-11:medio y 12-15:alto, los componentes del sistema, estos componentes se deber analizar de acuerdo a las vulnerabilidades identificadas en CVE's para estos componentes y que pueden comprometer cada dimension teniendo en cuenta el puntaje de criticidad asignado; la respuesta debe centrarse en describir de manera concreta y detallada el impacto que tendría una afectación en cada dimensión, resaltando la importancia de cada una para la UNAD y evitando enfocarte en controles o medidas preventivas, agrega 1 ejemplo de cve relacionada con los componentes en cada dimension.

No incluyas comentarios iniciales ni finales, solo desarrolla un párrafo descriptivo para cada dimensión en el siguiente formato:

'Disponibilidad: xxx
Integridad: xxx
Confidencialidad: xxx
Trazabilidad: xxx
Autenticidad: xxx'

en donde xxx es la valoracion para cada tipo de dimension del activo.

Datos para la valoración:
Nombre: 'Sistema Tienda virtual osCommerce'
Descrcipcion: 'osCommerce es una plataforma de comercio electrónico de código abierto que permite crear y gestionar tiendas en línea de manera flexible. Desarrollada originalmente en PHP y basada en el modelo cliente-servidor, su arquitectura se fundamenta en una estructura modular que separa claramente la lógica de negocio, la presentación y el acceso a datos. Utiliza PHP para la programación del lado del servidor y MySQL como sistema de gestión de bases de datos, lo que facilita la administración de productos, clientes y pedidos a través de una interfaz web. Además, osCommerce permite la personalización mediante módulos y plantillas, lo que facilita la integración de nuevas funcionalidades y el ajuste del diseño visual sin modificar el núcleo de la aplicación, favoreciendo así su escalabilidad y mantenimiento, esta expuesta a internet y no cuenta con certificado SSL vigente'
Componentes: 'Linux Ubuntu 22.04.5 LTS, Apache 2.4, PHP 5 y MySQL 5'
Tipo de activo: '[S] Servicio'
Criticidad: 14
Amenaza: '[A18] Destrucción de información'
Dimensiones de la seguridad:
Disponibilidad: 3
Integridad: 3
Confidencialidad: 3
Trazabilidad: 1
Autenticidad: 1
";	//esta es la pregunta


echo "Consulta a gemini<br><br>";
echo "Prompt enviado: ".$arrayData['mensaje']."<hr>";



$respuesta = $clienteIA->consulta($arrayData);

//si responde  :
// $respuesta['estado']=='OK': la respuesta esta bien, y el resultado estara en $respuesta['mensaje']
// $respuesta['estado']=='ERROR': la respuesta tuvo inconvenuentes, y el resultado de la transacion estara en $respuesta['mensaje']
// la respuesta se puede decodificar en json y se puede hacer uso de la libreria markdown para su formato
// echo "<pre>";
// print_r($respuesta);
// echo "</pre>";


if ($respuesta['estado'] == 'OK') {
		$patron = '/
		(Disponibilidad|Integridad|Confidencialidad|Trazabilidad|Autenticidad): # Título de la dimensión
		(.*?)                                             # Todo el contenido de ese ítem
		(?=(?:Disponibilidad|Integridad|Confidencialidad|Trazabilidad|Autenticidad):|$) # Hasta el próximo título o final de texto
		/sx';

		preg_match_all($patron, $respuesta['mensaje'], $coincidencias);

		$dimensiones = array_combine($coincidencias[1], array_map('trim', $coincidencias[2]));

		// Ahora puedes acceder así:
		$Disponibilidad   = $dimensiones['Disponibilidad'] ?? '';
		$Integridad       = $dimensiones['Integridad'] ?? '';
		$Confidencialidad = $dimensiones['Confidencialidad'] ?? '';
		$Trazabilidad     = $dimensiones['Trazabilidad'] ?? '';
		$Autenticidad     = $dimensiones['Autenticidad'] ?? '';

		// Ejemplo de impresión:
		echo "<b>Disponibilidad:</b> $Disponibilidad <br><br>";
		echo "<b>Integridad:</b> $Integridad <br><br>";
		echo "<b>Confidencialidad:</b> $Confidencialidad <br><br>";
		echo "<b>Trazabilidad:</b> $Trazabilidad <br><br>";
		echo "<b>Autenticidad:</b> $Autenticidad <br><br>";	
	
	
	
	////////////////////////////////////
	
	
	
	
	
} 
else {
    echo "<div class='alert alert-danger'>" . htmlspecialchars($respuesta['mensaje']) . "</div>";
}

?>

</body>
</html>





