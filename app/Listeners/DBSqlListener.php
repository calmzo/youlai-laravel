<?php

namespace App\Listeners;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Log;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class DBSqlListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  QueryExecuted  $event
     * @return void
     */
    public function handle(QueryExecuted $event)
    {
        if (env('APP_DEBUG') == true) {
            $sql      = $event->sql;
            $bindings = $event->bindings;
            $time     = $event->time;
            $bindings = array_map(function ($binding) {
                if (is_string($binding)) {
                    return "'$binding'";
                } elseif ($binding instanceof \DateTime) {
                    return $binding->format("'Y-m-d H:i:s'");
                }
                return $binding;
            }, $bindings);
            $exist = strpos($sql, '%');
            if ($exist !== 0) {
                $sql = str_replace("%", "'%%'", $sql);
            }
            $sql      = str_replace('?', '%s', $sql);
            $log      = sprintf($sql, ...$bindings);
            $log = str_replace("''", "'", $log);
            $log = $log . '  [ RunTime:' . $event->time . 'ms ] ';
            (new Logger('sql'))->pushHandler(new RotatingFileHandler(storage_path('logs/sql/sql_' . php_sapi_name() . '.log')))->info($log);
        }
    }
}
