<?php

include __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Intents;
use Discord\WebSockets\Event;

use GuzzleHttp\Client;

$discord = new Discord([
    'token' => $_ENV['Bot_Token'],      // Token del bot de discord
    'intents' => Intents::getDefaultIntents() | Intents::MESSAGE_CONTENT,   // Permiso para mandar mensajes
]);

$discord->on('ready', function (Discord $discord) {
    echo "Bot is ready!", PHP_EOL;

    $discord->on(Event::MESSAGE_CREATE, function (Message $message) {

        // Ignora todo mensaje que no inicia con el prefijo
        if (!str_starts_with($message->content, $_ENV['Bot_prefix'])) {
            return;
        }

        $input = substr($message->content, 1);      // Elimina el prefijo del mensaje;

        $url_endpoint = "https://router.huggingface.co/cerebras/v1/chat/completions";
        $modelo_ia = "llama-3.3-70b";

        $client = new Client();
        try {
            $response = $client->post($url_endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $_ENV['IA_Key'],     // Token de acceso
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    "messages" => [
                        [
                            "role" => "user",
                            "content" => $input,
                        ],
                    ],
                    "max_tokens" => 512,
                    "model" => $modelo_ia,
                    "stream" => false,
                ],
            ]);

            $data_content = $response->getBody()->getContents();
            $data_decode = json_decode($data_content, true);
            $msg_decode = json_encode($data_decode["choices"][0]["message"]["content"], JSON_UNESCAPED_UNICODE);
            $msg = str_replace("\\n", "\n", $msg_decode); // Para evitar errores con los saltos de linea

            $message->channel->sendMessage($msg);
        } catch (Exception $e) {
            $message->channel->sendMessage("Error: " . $e->getMessage());
        }
    });
});

$discord->run();
