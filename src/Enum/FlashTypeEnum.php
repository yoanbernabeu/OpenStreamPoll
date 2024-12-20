<?php

namespace App\Enum;

enum FlashTypeEnum: string
{
    case SUCCESS = 'success';
    case ERROR = 'error';
    case WARNING = 'warning';
    case INFO = 'info';

    public function getAlertClass(): string
    {
        return match ($this) {
            self::SUCCESS => 'alert-success',
            self::ERROR => 'alert-error',
            self::WARNING => 'alert-warning',
            self::INFO => 'alert-info',
        };
    }
}
