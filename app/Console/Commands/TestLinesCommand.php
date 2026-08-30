<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('test:lines {survey}')]
#[Description('Command description')]
class TestLinesCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $c = app()->make(\App\Http\Controllers\SurveyLocationController::class);
        $s = \App\Models\SurveyLocation::find($this->argument('survey'));
        $p = \App\Models\Project::find($s->project_id ?? 1);
        $resp = $c->mapLines($p, $s);
        $json = $resp->getContent();
        
        $this->info("Length of content: " . strlen($json));
        $this->info("Preview: " . substr($json, 0, 800));
        
        // decode and count
        $data = json_decode($json, true);
        if ($data) {
            $this->info("Feature count: " . count($data['features'] ?? []));
        } else {
            $this->error("Invalid JSON");
        }
    }
}
