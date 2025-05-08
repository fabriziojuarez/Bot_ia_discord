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
    'token' => $_ENV['Bot_Token'],
    'intents' => Intents::getDefaultIntents() | Intents::MESSAGE_CONTENT,
]);

$discord->on('ready', function (Discord $discord) {
    echo "Bot is ready!", PHP_EOL;

    $discord->on(Event::MESSAGE_CREATE, function (Message $message) {
        if (!str_starts_with($message->content, '!')) {
            return;
        }

        $input = substr($message->content, 1);

        $client = new Client();
        try {
            $response = $client->post('https://router.huggingface.co/cerebras/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $_ENV['IA_Key'],
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
                    "model" => "llama-3.3-70b",
                    "stream" => false,
                ],
            ]);

            $data = $response->getBody()->getContents();
            $res = json_decode($data, true);
            $msg_d = json_encode($res["choices"][0]["message"]["content"], JSON_UNESCAPED_UNICODE);
            $msg = str_replace("\\n", "\n", $msg_d);

            $message->channel->sendMessage($msg);
        } catch (Exception $e) {
            $message->channel->sendMessage("Error al consultar Hugging Face: " . $e->getMessage());
        }
    });
});

$discord->run();
