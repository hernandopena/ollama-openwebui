# Instalación de Ollama + OpenWebUI en Ubuntu 24.04

Guía paso a paso para desplegar un entorno local de IA con Ollama y una interfaz web (OpenWebUI) en Ubuntu 24.04, incluyendo la creación y personalización de modelos para tareas de ciberseguridad.

---

## **1. Crear la máquina virtual o entorno**

**Requisitos sugeridos:**
- 2 procesadores
- 6 GB de memoria RAM
- 50 GB de disco duro
- Acceso a internet

---

## **2. Actualización del sistema**

```bash
sudo apt update && sudo apt upgrade -y
```

---

## **3. Instalación de Ollama**

```bash
curl -fsSL https://ollama.com/install.sh | sh
```
![Instalación de Ollama](img/ollama-001.png)


> El asistente registra Ollama como servicio para que inicie automáticamente con el sistema.

Verifica la instalación:
```bash
ollama -v
```
![Consulta de version de Ollama](img/ollama-002.png)
---

## **4. Descargar y ejecutar un modelo (ejemplo: llama 3.2 3b)**

```bash
ollama pull llama3.2:3b
```
![Descarga del modelo llama3.2:3b](img/ollama-003.png)


```bash
ollama run llama3.2:3b
```
![Puesta en marcha del modelo llama3.2:3b](img/ollama-004.png)

---

## **5. Instalación de OpenWebUI (Interfaz web para Ollama)**

### Instalar Docker y Docker Compose

```bash
sudo apt update
```
![Actualización del sistema](img/ollama-005.png)

```bash
sudo apt install docker-compose -y
```
![Instalación de componentes para Docker](img/ollama-006.png)

Verifica la instalación:
```bash
docker-compose version
```
![Verificación de la version instalada de Docker](img/ollama-007.png)
---

### Desplegar OpenWebUI

```bash
docker run -d -p 3000:8080 \
  --add-host=host.docker.internal:host-gateway \
  -v open-webui:/app/backend/data \
  --name open-webui \
  --restart always \
  ghcr.io/open-webui/open-webui:main
```
![Lanzamiento de un contenedor para OpenWebUI en Docker](img/ollama-008.png)

Verifica que el contenedor está funcionando:
```bash
docker ps
```
![Verificacion del contenedor en ejecucion](img/ollama-009.png)

Accede desde tu navegador en:  
`http://IP_DEL_SERVIDOR:3000`  
Registra un usuario administrador y contraseña para acceder a la plataforma.

![Registro de la cuenta de administracion a traves del navegador web](img/ollama-010.png)

Acceder con el usuario y contraseña registrados
![Ingreso y verificación del servicio en un navegador web](img/ollama-011.png)

---

## **6. Integración de Ollama y los modelos en OpenWebUI**

### Habilitar la API pública de Ollama

Edita el servicio de Ollama:
```bash
sudo nano /etc/systemd/system/ollama.service
```
Agrega o modifica la instrucción para que Ollama escuche desde cualquier origen, justo en el apartado de Service, como se muestra a continuación.
```bash
Environment="OLLAMA_HOST=0.0.0.0"
```
![Habilitacion de la consulta a traves de la API en ollama](img/ollama-012.png)


Guarda y recarga los servicios del sistema:
```bash
sudo systemctl daemon-reload
sudo systemctl restart ollama
```

### Configurar OpenWebUI para usar Ollama

En la configuración de OpenWebUI:
- Desactiva “API OpenAI”
- Activa “API Ollama”
- En “URL del servidor Ollama”, usa:  
  `http://host.docker.internal:11434`

![Habilitamos la conexión entre Ollama y OpenWebUI](img/ollama-013.png)

Verifica que OpenWebUI reconoce y puede comunicarse con Ollama.
![Verificamos la conexión](img/ollama-014.png)

---

## **7. Personalización de modelos en Ollama**

### Crear un archivo de modelo personalizado

Ejemplo: `modeloBaseCiberseguridadFlisol2025`

```dockerfile
FROM llama3.2

# Parámetros de generación

PARAMETER temperature 0.7
PARAMETER num_ctx 4096
PARAMETER repeat_penalty 1.1

# Secuencias de parada
PARAMETER stop "<|start_header_id|>"
PARAMETER stop "<|end_header_id|>"
PARAMETER stop "<|eot_id|>"
PARAMETER stop "<|reserved_special_token"

# Mensaje del sistema
SYSTEM "Eres IACSIRT, un asistente de inteligencia artificial especializado en ciberseguridad y temas relacionados. Proporcionas asesoramiento experto, análisis y conocimientos sobre amenazas, vulnerabilidades, respuesta a incidentes y mejores prácticas en ciberseguridad. Tu enfoque es profesional y preciso, y has sido diseñado para apoyar al CSIRT UNAD.

Áreas de especialización adicionales:
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

Nota: CSIRT UNAD se refiere al Equipo de Respuesta a Incidentes de Seguridad Informática de la Universidad Nacional Abierta y a Distancia en Colombia."

# Plantilla del prompt
TEMPLATE """
{{ if .System }}<|start_header_id|>system<|end_header_id|>
{{ .System }}<|eot_id|>{{ end }}{{ if .Prompt }}<|start_header_id|>user<|end_header_id|>
{{ .Prompt }}<|eot_id|>{{ end }}<|start_header_id|>assistant<|end_header_id|>
{{ .Response }}<|eot_id|>
"""
```

Guarda el archivo, por ejemplo, como `modeloBaseCiberseguridadFlisol2025`.

### Crear el modelo en Ollama

```bash
cd /ruta/a/tu/modelo_guardado
ollama create IACSIRT -f modeloBaseCiberseguridadFlisol2025
```
![Cargamos desde consola el nuevo modelo generado](img/ollama-015.png)

Ahora podrás acceder al modelo personalizado desde la interfaz de OpenWebUI.
![Uso del nuevo modelo en OpenWebUI](img/ollama-016.png)
 
---

# Créditos

Guía elaborada para FLISOL 2025 — Instalación y personalización de modelos IA para ciberseguridad en Ubuntu 24.04.

**CSIRT Académico UNAD**  
**Hernando José Peña H. - Luis Fernando Zambrano**
