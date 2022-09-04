<?php

namespace Database\Seeders;

use App\Models\FaerieTemplate;
use App\Services\Names;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class GenerateFaerieTemplates extends Seeder
{
/**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // First check if we need to upload any images
//        foreach (Storage::disk('server')->allFiles('scripts/generate-pfps/saved-outputs') as $file){
//            $name = last(explode('/', $file));
//            $localFile = Storage::disk('server')->get($file);
//            $remote = Storage::disk('s3')->get('/templates/'.$name);
//            if ($remote == null){
//                Storage::disk('s3')->put('/templates/'.$name, $localFile);
//                $this->command->info('Wrote '.$name);
//            }
//        }

        foreach (Storage::disk('s3')->allFiles('/templates') as $remote){
            $check = FaerieTemplate::where('image', $remote)->first();
            if ($check != null){
                continue;
            }

            $template = new FaerieTemplate();
            $template->image = $remote;
            $template->name = Names::generate(rand(0, 1));
            $template->save();

        }
    }
}
