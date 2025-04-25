<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>IACSIRT | Asistente Gemini</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      background: linear-gradient(120deg, #eef2f3 0%, #dde3f8 100%);
      font-family: 'Segoe UI', sans-serif;
      min-height: 100vh;
    }
	.main-wrap {
	  min-height: 100vh;
	  display: flex;
	  flex-direction: column;
	  justify-content: flex-start;   /* O "center" si prefieres totalmente centrado */
	  align-items: center;
	}

	.chat-container {
	  width: 90vw;              /* Toma el 90% del ancho de la ventana */
	  max-width: 1600px;        /* Puedes ajustar este valor para monitores grandes */
	  min-width: 320px;         /* Evita que se encoja demasiado en móvil */
	  margin: 40px auto;
	  background: #fff;
	  border-radius: 20px;
	  box-shadow: 0 8px 32px rgba(100,120,200,0.15);
	  padding: 25px 28px 15px 28px;
	  display: flex;
	  flex-direction: column;
	  min-height: 68vh;
	  position: relative;
	}

    .chat-title {
      font-weight: bold;
      font-size: 1.6rem;
      margin-bottom: 5px;
      letter-spacing: 1px;
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .btn-limpiar {
      position: absolute;
      top: 18px;
      right: 26px;
      z-index: 10;
    }
    .chat-box {
      max-height: 52vh;
      min-height: 240px;
      overflow-y: auto;
      padding: 10px 2px 8px 2px;
      border: 1px solid #dee2e6;
      border-radius: 14px;
      background-color: #f8f9fa;
      margin-bottom: 1.5rem;
      transition: border 0.2s;
    }
    .message.user {
      text-align: right;
      color: #006dca;
      margin-bottom: 8px;
      background: #e9f4ff;
      padding: 6px 14px;
      border-radius: 16px 16px 2px 18px;
      display: inline-block;
      max-width: 85%;
      float: right;
      font-weight: 500;
    }
    .message.model {
      text-align: left;
      color: #158248;
      background: #f3fdf6;
      padding: 6px 14px;
      border-radius: 16px 16px 18px 2px;
      margin-bottom: 8px;
      display: inline-block;
      max-width: 85%;
      float: left;
      font-weight: 500;
    }
    .chat-form,
    .config-form {
      margin-bottom: 0.5rem;
    }
    .preloader {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px 0 10px 0;
    }
    .loader {
      border: 4px solid #f3f3f3;
      border-top: 4px solid #1465ae;
      border-radius: 50%;
      width: 32px;
      height: 32px;
      animation: spin 0.9s linear infinite;
    }
    @keyframes spin {
      0% { transform: rotate(0deg);}
      100% { transform: rotate(360deg);}
    }
	@media (max-width: 768px) {
	  .chat-container {
		width: 98vw;
		max-width: 100vw;
		padding: 7px 1vw 7px 1vw;
	  }
	}

    .config-form label,
    .config-form select,
    .config-form input[type="range"] {
      font-size: 0.98rem;
    }
  </style>
</head>
<body>
<div class="main-wrap">
  <div class="chat-container shadow">
    <div class="chat-title">
      <img src="https://img.icons8.com/fluency/48/cyber-security.png" width="36" alt="">
      IACSIRT <span class="d-none d-md-inline">| Asistente Gemini</span>
    </div>
    <button type="button" id="limpiar" class="btn btn-outline-danger btn-sm btn-limpiar" title="Limpiar conversación">
      <span class="d-none d-md-inline">🧹 Limpiar</span>
      <span class="d-md-none"><i class="bi bi-trash"></i></span>
    </button>
    <div class="chat-box mb-3" id="chat-box">
      <!-- Mensajes serán cargados aquí -->
    </div>
    <div class="preloader" id="preloader" style="display:none;">
      <div class="loader"></div>
    </div>
    <form id="config-form" class="config-form mb-2">
      <div class="row g-2 align-items-center">
        <div class="col-12 col-md-6">
          <label for="modelo" class="form-label">Modelo:</label>
          <select id="modelo" class="form-select">
            <option value="gemini-2.0-flash">Gemini 2.0 Flash</option>
            <option value="gemini-1.5-pro">Gemini 1.5 Pro</option>
          </select>
        </div>
        <div class="col-12 col-md-6">
          <label for="temperature" class="form-label">Temperatura: <span id="temp-value">0.7</span></label>
          <input type="range" id="temperature" class="form-range" min="0.1" max="1.0" step="0.1" value="0.7">
        </div>
      </div>
    </form>
    <form id="chat-form" class="chat-form">
      <div class="input-group">
        <input type="text" name="user_message" id="user_message" class="form-control" placeholder="Escribe tu mensaje..." required autocomplete="off">
        <button class="btn btn-primary" type="submit">Enviar</button>
      </div>
    </form>
  </div>
</div>

<!-- Bootstrap Icons (para móvil, opcional) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
const chatBox = document.getElementById('chat-box');
const preloader = document.getElementById('preloader');

async function cargarChat() {
  const res = await fetch('back.php');
  const data = await res.json();
  chatBox.innerHTML = '';
  data.forEach(msg => {
    const div = document.createElement('div');
    div.className = 'message ' + msg.role;
    if (msg.role === 'model') {
      div.innerHTML = marked.parse(msg.text);
    } else {
      div.innerText = msg.text;
    }
    chatBox.appendChild(div);
  });
  chatBox.scrollTop = chatBox.scrollHeight;
}

// Manejar envío de mensaje
document.getElementById('chat-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  const input = document.getElementById('user_message');
  const userMessage = input.value.trim();
  if (userMessage === '') return;
  input.disabled = true;
  preloader.style.display = 'flex';

  await fetch('back.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      user_message: userMessage,
      model: document.getElementById('modelo').value,
      temperature: parseFloat(document.getElementById('temperature').value)
    })
  });

  input.value = '';
  input.disabled = false;
  preloader.style.display = 'none';
  cargarChat();
});

window.onload = cargarChat;

document.getElementById('limpiar').addEventListener('click', async () => {
  await fetch('back.php?action=reset');
  cargarChat();
});

const tempSlider = document.getElementById('temperature');
const tempValue = document.getElementById('temp-value');
tempSlider.addEventListener('input', () => {
  tempValue.textContent = tempSlider.value;
});
</script>
</body>
</html>
