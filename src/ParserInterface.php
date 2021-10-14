<?php

declare(strict_types=1);

namespace webignition\QuotedString;

use webignition\StringParser\UnknownStateException;

interface ParserInterface
{
    public const QUOTE_DELIMITER = '"';
    public const ESCAPE_CHARACTER = '\\';

    public const STATE_IN_QUOTED_STRING = 1;
    public const STATE_LEFT_QUOTED_STRING = 2;
    public const STATE_INVALID_LEADING_CHARACTERS = 3;
    public const STATE_INVALID_TRAILING_CHARACTERS = 4;
    public const STATE_ENTERING_QUOTED_STRING = 5;
    public const STATE_INVALID_ESCAPE_CHARACTER = 6;

    /**
     * @throws Exception
     * @throws UnknownStateException
     */
    public function parseToObject(string $input): QuotedString;

    /**
     * @throws Exception
     * @throws UnknownStateException
     */
    public function parse(string $input): string;
}
