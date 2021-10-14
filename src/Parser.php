<?php

declare(strict_types=1);

namespace webignition\QuotedString;

use webignition\StringParser\StringParser;
use webignition\StringParser\UnknownStateException;

/**
 * Parse a given input string into a QuotedString.
 */
class Parser
{
    private const QUOTE_DELIMITER = '"';
    private const ESCAPE_CHARACTER = '\\';

    private const STATE_IN_QUOTED_STRING = 1;
    private const STATE_LEFT_QUOTED_STRING = 2;
    private const STATE_INVALID_LEADING_CHARACTERS = 3;
    private const STATE_INVALID_TRAILING_CHARACTERS = 4;
    private const STATE_ENTERING_QUOTED_STRING = 5;
    private const STATE_INVALID_ESCAPE_CHARACTER = 6;

    private StringParser $stringParser;

    public function __construct()
    {
        $this->stringParser = new StringParser([
            StringParser::STATE_UNKNOWN => function (StringParser $stringParser) {
                if ($stringParser->isCurrentCharacterFirstCharacter()) {
                    if (self::QUOTE_DELIMITER === $stringParser->getCurrentCharacter()) {
                        $stringParser->setState(self::STATE_ENTERING_QUOTED_STRING);
                    } else {
                        $stringParser->setState(self::STATE_INVALID_LEADING_CHARACTERS);
                    }
                }
            },
            self::STATE_IN_QUOTED_STRING => function (StringParser $stringParser) {
                $isQuoteDelimiter = self::QUOTE_DELIMITER === $stringParser->getCurrentCharacter();
                $isEscapeCharacter = self::ESCAPE_CHARACTER === $stringParser->getCurrentCharacter();

                if ($isQuoteDelimiter) {
                    if (self::ESCAPE_CHARACTER === $stringParser->getPreviousCharacter()) {
                        $stringParser->appendOutputString();
                        $stringParser->incrementPointer();
                    } else {
                        $stringParser->setState(self::STATE_LEFT_QUOTED_STRING);
                        $stringParser->incrementPointer();
                    }
                }

                if ($isEscapeCharacter) {
                    if (self::QUOTE_DELIMITER === $stringParser->getNextCharacter()) {
                        $stringParser->incrementPointer();
                    } else {
                        $stringParser->setState(self::STATE_INVALID_ESCAPE_CHARACTER);
                    }
                }

                if (!$isQuoteDelimiter && !$isEscapeCharacter) {
                    $stringParser->appendOutputString();
                    $stringParser->incrementPointer();
                }
            },
            self::STATE_LEFT_QUOTED_STRING => function (StringParser $stringParser) {
                if (!$stringParser->isCurrentCharacterLastCharacter()) {
                    $stringParser->setState(self::STATE_INVALID_TRAILING_CHARACTERS);
                }
            },
            self::STATE_INVALID_LEADING_CHARACTERS => function (StringParser $stringParser) {
                throw new Exception('Invalid leading characters before first quote character', 1);
            },
            self::STATE_INVALID_TRAILING_CHARACTERS => function (StringParser $stringParser) {
                $exceptionMessage = implode(' ', [
                    'Invalid trailing characters after last quote character at position',
                    $stringParser->getPointer(),
                ]);

                throw new Exception($exceptionMessage, 2);
            },
            self::STATE_ENTERING_QUOTED_STRING => function (StringParser $stringParser) {
                $stringParser->incrementPointer();
                $stringParser->setState(self::STATE_IN_QUOTED_STRING);
            },
            self::STATE_INVALID_ESCAPE_CHARACTER => function (StringParser $stringParser) {
                throw new Exception('Invalid escape character at position ' . $stringParser->getPointer(), 3);
            },
        ]);
    }

    /**
     * @throws Exception
     * @throws UnknownStateException
     */
    public function parseToObject(string $input): QuotedString
    {
        return new QuotedString($this->stringParser->parse($input));
    }

    /**
     * @throws Exception
     */
    public function parse(string $input): string
    {
        return (string) $this->parseToObject($input);
    }
}
