<?php


function instantiate_dummy()
{
    // Dummy data
    $dummyData = [
        'model' => 'XXXX',
        'Messages' => [
            ['role' => 'system', 'content' => 'You are a helpful assistant.'],
            ['role' => 'user', 'content' => 'Please tell me, why did the chicken cross the road?'],
        ],
        'Functions' => [
            [
                'name' => 'FunctionA',
                'description' => 'A function that does something fun',
                'parameters' => ['fooA', 'barA'],
            ],
            [
                'name' => 'FunctionB',
                'description' => 'A function that does something serious',
                'parameters' => ['fooB', 'barB'],
            ],
        ],
        'status' => 1,
        'OpenAI_api_key' => 'dummyOpenAIKey',
        'WP_api_key' => 'dummyWPKey',
        'remote_user_id' => 1001,
        'remote_user_email' => 'dummy@example.com',
        'account_id' => 2002,
        'user_id' => 3003,
        'thread_id' => 4004,
        'max_tokens' => 500,
        'completion_tokens' => 600,
        'prompt_tokens' => 700,
        'total_tokens' => 800,
        'remote_domain_url' => 'https://dummy.com',
        'remote_thread_id' => 5005,
        'Choices' => [
            [
                "finish_reason" => "stop",
                "index" => 0,
                "message" => ["role" => "assistant", "content" => "The 2020 World Series was played in Texas at Globe Life Field in Arlington."],
            ]

        ],
        'model_created' => 1234567890,
        'model_id' => 'ModelID123',
        'model_object_name' => 'ModelObjectXYZ'
    ];

    // Create a new Prompt object and fill in the properties with the dummy data
    $prompt = new \IonChat\Prompt();
    foreach ($dummyData as $key => $value) {
        $prompt->$key = $value;
    }

    return $prompt;
}
