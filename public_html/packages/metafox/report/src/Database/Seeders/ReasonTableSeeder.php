<?php

namespace MetaFox\Report\Database\Seeders;

use Illuminate\Database\Seeder;
use MetaFox\Report\Models\ReportReason;
use Prettus\Validator\Exceptions\ValidatorException;

class ReasonTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (ReportReason::query()->exists()) {
            return;
        }

        ReportReason::query()->whereIn('name', ['Abuse Content', 'Violence Content'])->delete();

        $data = [
            'report::phrase.abuse_content_title',
            'report::phrase.violence_content_title',
        ];

        $count = 1;
        foreach ($data as $name) {
            ReportReason::query()->updateOrCreate(['name' => $name], ['ordering' => $count++]);
        }
    }
}
