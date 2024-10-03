<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use OpenAI\Laravel\Facades\OpenAI;

class TestOpenAI extends Command
{
    protected $signature = 'test:openai';
    protected $description = 'Test OpenAI API';

    public function handle()
    {
        try {
            $response = OpenAI::completions()->create([
                'model' => 'text-davinci-003',
                'prompt' => 'Donne moi une formule mathématique simple.',
                'max_tokens' => 20,
            ]);

            $this->info('Réponse de l\'API : ' . $response['choices'][0]['text']);
        } catch (\Exception $e) {
            $this->error('Erreur : ' . $e->getMessage());
        }
    }
}
