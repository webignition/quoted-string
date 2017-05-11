<?php

namespace webignition\Tests\QuotedString;

use PHPUnit_Framework_TestCase;
use webignition\QuotedString\Exception;
use webignition\QuotedString\Parser;
use webignition\QuotedString\QuotedString;

class QuotedStringParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Parser
     */
    private $parser;

    protected function setUp()
    {
        parent::setUp();
        $this->parser = new Parser();
    }

    /**
     * @dataProvider parseInvalidInputDataProvider
     *
     * @param string $input
     * @param string $expectedExceptionMessage
     * @param int $expectedExceptionCode
     */
    public function testParseInvalidInput($input, $expectedExceptionMessage, $expectedExceptionCode)
    {
        $this->setExpectedException(
            Exception::class,
            $expectedExceptionMessage,
            $expectedExceptionCode
        );

        $this->parser->parse($input);
    }

    public function parseInvalidInputDataProvider()
    {
        return [
            'invalid leading characters' => [
                'input' => 'foo',
                'expectedExceptionMessage' => 'Invalid leading characters before first quote character',
                'expectedExceptionCode' => 1,
            ],
            'invalid trailing characters' => [
                'input' => '"foo" bar',
                'expectedExceptionMessage' => 'Invalid trailing characters after last quote character at position 7',
                'expectedExceptionCode' => 2,
            ],
            'invalid escape characters' => [
                'input' => '"foo \bar"',
                'expectedExceptionMessage' => 'Invalid escape character at position 5',
                'expectedExceptionCode' => 3,
            ],
        ];
    }

    /**
     * @dataProvider parseValidInputDataProvider
     *
     * @param string $input
     * @param string $expectedValue
     */
    public function testParseValidInput($input, $expectedValue)
    {
        $quotedString = $this->parser->parse($input);

        $this->assertInstanceOf(QuotedString::class, $quotedString);
        $this->assertEquals($expectedValue, (string)$quotedString->getValue());
    }

    /**
     * @return array
     */
    public function parseValidInputDataProvider()
    {
        return [
            'without inner quotes' => [
                'input' => '"foo"',
                'expectedValue' => 'foo',
            ],
            'with inner quotes' => [
                'input' => '"foo \"bar\" foobar"',
                'expectedValue' => 'foo "bar" foobar',
            ],
        ];
    }
}
