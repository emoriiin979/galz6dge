<?php

namespace App\Loggers;

use App\Models\Log as LogModel;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;

class DbLogHandler extends AbstractProcessingHandler
{
    /**
     * ログ記録
     *
     * @param LogRecord $record
     * @return void
     */
    protected function write(LogRecord $record): void
    {
        LogModel::create($record->formatted);
    }
}
