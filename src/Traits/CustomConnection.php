<?php

namespace WeirdoPanel\Traits;

trait CustomConnection
{
    /**
     * @return void
     */
    public function setDefaultConnection(): void
    {
        $db = env('DB_DATABASE', null);

        if (!empty($db)) {
            \Illuminate\Support\Facades\DB::connection()->useDatabases($db);
        }
    }
}
