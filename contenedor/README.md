# Montaje de contenedor para api de gemini

El presente es un contenedor basico, que ofrece una servicio construido en python, la cual se conecta a la api de gemini.


---

# Cómo obtener una API Key de Gemini (Google AI Studio)

Este proyecto requiere una API Key de Gemini para funcionar.
Sigue los pasos a continuación para obtener la tuya:

## 1. Accede a Google AI Studio

- Ingresa a: [https://aistudio.google.com/app/apikey](https://aistudio.google.com/app/apikey)
- Inicia sesión con tu cuenta de Google.

## 2. Crea un nuevo proyecto (si es necesario)

- Si el sistema lo solicita, crea un nuevo proyecto o selecciona uno existente en Google Cloud.

## 3. Genera la clave API

- Haz clic en **Create API Key**.
- Asigna un nombre identificador para tu clave.
- Haz clic en **Create**.

## 4. Copia y guarda tu API Key

- Una vez generada, **copia la clave** y guárdala en un lugar seguro.
- No compartas esta clave públicamente ni la incluyas en el código fuente del repositorio.

## 5. Restringe la clave (opcional)

- Por motivos de seguridad, puedes restringir la clave para que solo funcione en ciertos entornos (web, IPs específicas, etc.).


---

# Crear la red en docker

Crear la red interna en docker primero

```bash
docker network create \
  --driver bridge \
  --subnet 10.101.0.0/16 \
  --gateway 10.101.0.1 \
  --attachable \
  themisNetwork 
```

---

# Lanzar el contenedor

Ingresar al directorio en donde se encuentra el archivo `docker-compose.yml` y lanzar los comandos

```bash
docker-compose up --build -d
```

---

# Verificar su funcionamiento

Desde la consola de comandos, podemos hacer una prueba de la api:

```bash
curl -X POST http://localhost:9000/chat \
 -H "Content-Type: application/json" \
 -d '{"message": "¿Qué es un CSIRT?", "context": []}'
```


---

# Créditos

Guía elaborada para FLISOL 2025 — Instalación y personalización de modelos IA para ciberseguridad en Ubuntu 24.04.

**CSIRT Académico UNAD**
**Hernando José Peña H. - Luis Fernando Zambrano**
