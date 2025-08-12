<?php
return [
    'key' => env('GROQ_API_KEY', ''),
    'bot_name' => env('GROQ_BOT_NAME', 'JOÃO'),
    'model' => env('GROQ_MODEL', 'meta-llama/llama-4-maverick-17b-128e-instruct'),
    'base_url' => env('GROQ_BASE_URL', 'https://api.groq.com/openai/v1'),
    'temperature' => env('GROQ_TEMPERATURE', 0.7),
];
