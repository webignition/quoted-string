<?php

declare(strict_types=1);

namespace webignition\Tests\QuotedString;

use webignition\QuotedString\QuotedString;

class QuotedStringTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider castToStringDataProvider
     */
    public function testCastToString(string $input, string $expectedQuotedString)
    {
        $quotedString = new QuotedString($input);

        $this->assertEquals($expectedQuotedString, (string)$quotedString);
    }

    public function castToStringDataProvider(): array
    {
        return [
            'without inner quotes' => [
                'value' => 'foo',
                'expectedQuotedString' => '"foo"',
            ],
            'with inner quotes' => [
                'value' => 'f"o"o',
                'expectedQuotedString' => '"f\"o\"o"',
            ],
        ];
    }
}
