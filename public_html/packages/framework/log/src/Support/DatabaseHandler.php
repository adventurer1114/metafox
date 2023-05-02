<?php

namespace MetaFox\Log\Support;

use Illuminate\Support\Facades\DB;
use Monolog\Handler\AbstractProcessingHandler;

class DatabaseHandler extends AbstractProcessingHandler
{
    protected string $table;

    /**
     * @param string $table
     */
    public function __construct(string $table)
    {
        $this->table = $table;
    }

    /**
     * @inheritDoc
     */
    protected function write(array $record): void
    {
        DB::table($this->table)->insert([
            'env'        => config('app.env'),
            'level'      => $record['level'],
            'level_name' => $record['level_name'],
            'message'    => $record['message'],
            'timestamp'  => $record['datetime'],
            'context'    => json_encode($record['context']),
            'extra'      => json_encode($record['extra']),
        ]);
    }
}
