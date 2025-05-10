Buenas, este sera mi primer intento para documentar xd.

Dependencias requeridas:
-team-reflex/discord-php  // Para el bot de discord
-vlucas/phpdotenv         // Para las variables de entorno
-guzzlehttp/guzzle        // Para realizar peticiones al modelo de IA

Instalacion de dependencias:
composer require "dependencia"

Estructura de la .env(variables de entorno):
Bot_Token="Token del bot de discord"
IA_Key="Token de autentificacion para la IA"
Bot_prefix="tu prefijo"

Estructura para realizar peticiones al modelo de IA(para realizar pruebas):
url(con el metodo POST): https://router.huggingface.co/cerebras/v1/chat/completions(url valida de ejemplo)
headers: 
  -Authorization: Bearer "Token de autentificacion para la IA"    // Nota: debe haber un espacio entre la palabra Bearer y tu token
  -Content-Type: application/json
json:
  {
  "messages": [
        {
            "role": "user",
            "content": "hola, como estas"       // Mensaje Input
        }
    ],
    "model": "llama-3.3-70b",    // Modelo de IA
    "max_tokens": 512,
}

Comando para iniciar el proyecto:
php index.php
