<?php

namespace webignition\Tests\QuotedString;

use PHPUnit_Framework_TestCase;
use webignition\QuotedString\QuotedString;

class QuotedStringTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider castToStringDataProvider
     *
     * @param $input
     * @param $expectedQuotedString
     */
    public function testCastToString($input, $expectedQuotedString)
    {
        $quotedString = new QuotedString($input);

        $this->assertEquals($expectedQuotedString, (string)$quotedString);
    }

    /**
     * @return array
     */
    public function castToStringDataProvider()
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
