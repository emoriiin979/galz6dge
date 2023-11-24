<?php

namespace App\Loggers;

use Illuminate\Support\Arr;
use Monolog\Formatter\FormatterInterface;
use Monolog\LogRecord;

class DbLogFormatter implements FormatterInterface
{
    /**
     * ログデータ整形
     *
     * @param LogRecord $record
     * @return array
     */
    public function format(LogRecord $record): array
    {
        $array = $record->toArray();

        return [
            'level' => Arr::get($array, 'level_name'),
            'method' => Arr::get($array, 'context.method'),
            'url' => Arr::get($array, 'context.url'),
            'key' => Arr::get($array, 'context.key'),
            'response_code' => Arr::get($array, 'context.response_code'),
            'message' => Arr::get($array, 'message'),
        ];
    }

    /**
     * ログ複数データ整形
     *
     * @param array $records
     * @return array
     */
    public function formatBatch(array $records): array
    {
        $formatted = [];
        foreach ($records as $key => $record) {
            $formatted[$key] = $this->format($record);
        }

        return $formatted;
    }
}
