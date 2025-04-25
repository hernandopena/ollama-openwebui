from fastapi import FastAPI, Request
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
import httpx
import os

app = FastAPI(
    title="IACsirt Gemini API",
    description="API para consultas de chat Gemini con contexto para IACSIRT.",
)

# Permitir CORS (ajusta según tu seguridad)
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"], 
    allow_methods=["*"],
    allow_headers=["*"],
)

GEMINI_API_KEY = os.environ.get("GEMINI_API_KEY", "")

class ChatRequest(BaseModel):
    message: str
    context: list = []

@app.post("/chat")
async def chat_endpoint(req: ChatRequest):
    if not GEMINI_API_KEY:
        return {"error": "API KEY not set"}
    url = f"https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={GEMINI_API_KEY}"

    # Prepara el contexto (opcional: añade instrucciones de sistema como primer mensaje)
    contents = []
    # Mensaje de sistema (perfil)
    contents.append({
        "role": "user",
        "parts": [{
            "text": (
                "Actúa como IACSIRT, un asistente experto en ciberseguridad..."
                "Siempre responde de forma profesional, técnica y clara para apoyar al CSIRT UNAD."
            )
        }]
    })
    # Mensajes anteriores del usuario y modelo, si se recibe un contexto (opcional)
    for msg in req.context:
        contents.append({
            "role": msg.get("role", "user"),
            "parts": [{"text": msg.get("text", "")}]
        })
    # Mensaje actual del usuario
    contents.append({
        "role": "user",
        "parts": [{"text": req.message}]
    })

    payload = {
        "contents": contents,
        "generationConfig": {
            "temperature": 0.7
        }
    }

    async with httpx.AsyncClient() as client:
        resp = await client.post(url, json=payload, timeout=60)
    data = resp.json()
    if "candidates" in data and data["candidates"]:
        reply = (
            data["candidates"][0]
            .get("content", {})
            .get("parts", [{}])[0]
            .get("text", "Lo siento, no hubo respuesta.")
        )
        return {"response": reply}
    else:
        # Devuelve el error exacto de Gemini
        return {"error": data}


@app.get("/")
async def root():
    return {"message": "IACSIRT Gemini API is running"}


