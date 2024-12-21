<?php

namespace App\Tests\Enum;

use App\Enum\FlashTypeEnum;
use PHPUnit\Framework\TestCase;

class FlashTypeEnumTest extends TestCase
{
    /**
     * @dataProvider provideFlashTypes
     */
    public function testGetAlertClass(FlashTypeEnum $type, string $expectedClass): void
    {
        $this->assertEquals($expectedClass, $type->getAlertClass());
    }

    public function provideFlashTypes(): array
    {
        return [
            'success type' => [FlashTypeEnum::SUCCESS, 'alert-success'],
            'error type' => [FlashTypeEnum::ERROR, 'alert-error'],
            'warning type' => [FlashTypeEnum::WARNING, 'alert-warning'],
            'info type' => [FlashTypeEnum::INFO, 'alert-info'],
        ];
    }

    public function testEnumValues(): void
    {
        $this->assertEquals('success', FlashTypeEnum::SUCCESS->value);
        $this->assertEquals('error', FlashTypeEnum::ERROR->value);
        $this->assertEquals('warning', FlashTypeEnum::WARNING->value);
        $this->assertEquals('info', FlashTypeEnum::INFO->value);
    }
}
