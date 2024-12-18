<?php

declare(strict_types=1);

namespace CourseBundle\Recommendation\Infrastructure\Symfony\Logging;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

class AddRequestIdProcessor implements ProcessorInterface
{
    public function __invoke(LogRecord $record): LogRecord
    {
        $record->extra['request_id'] = uniqid();

        return $record;
    }
}
