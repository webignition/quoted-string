<?php

declare(strict_types=1);

namespace webignition\QuotedString;

use webignition\StringParser\StringParser;

/**
 * Parse a given input string into a QuotedString
 */
class Parser extends StringParser
{
    const QUOTE_DELIMITER = '"';
    const ESCAPE_CHARACTER = '\\';

    const STATE_IN_QUOTED_STRING = 1;
    const STATE_LEFT_QUOTED_STRING = 2;
    const STATE_INVALID_LEADING_CHARACTERS = 3;
    const STATE_INVALID_TRAILING_CHARACTERS = 4;
    const STATE_ENTERING_QUOTED_STRING = 5;
    const STATE_INVALID_ESCAPE_CHARACTER = 6;

    public function parseToObject(string $inputString): QuotedString
    {
        return new QuotedString(parent::parse($inputString));
    }

    public function parse(string $inputString): string
    {
        return (string) $this->parseToObject($inputString);
    }

    /**
     * @throws Exception
     */
    protected function parseCurrentCharacter(): void
    {
        switch ($this->getCurrentState()) {
            case self::STATE_ENTERING_QUOTED_STRING:
                $this->incrementCurrentCharacterPointer();
                $this->setCurrentState(self::STATE_IN_QUOTED_STRING);
                break;

            case self::STATE_IN_QUOTED_STRING:
                if ($this->isCurrentCharacterQuoteDelimiter()) {
                    if ($this->isPreviousCharacterEscapeCharacter()) {
                        $this->appendOutputString();
                        $this->incrementCurrentCharacterPointer();
                    } else {
                        $this->setCurrentState(self::STATE_LEFT_QUOTED_STRING);
                        $this->incrementCurrentCharacterPointer();
                    }
                }

                if ($this->isCurrentCharacterEscapeCharacter()) {
                    if ($this->isNextCharacterQuoteCharacter()) {
                        $this->incrementCurrentCharacterPointer();
                    } else {
                        $this->setCurrentState(self::STATE_INVALID_ESCAPE_CHARACTER);
                    }
                }

                if (!$this->isCurrentCharacterQuoteDelimiter() && !$this->isCurrentCharacterEscapeCharacter()) {
                    $this->appendOutputString();
                    $this->incrementCurrentCharacterPointer();
                }

                break;

            case self::STATE_LEFT_QUOTED_STRING:
                if (!$this->isCurrentCharacterLastCharacter()) {
                    $this->setCurrentState(self::STATE_INVALID_TRAILING_CHARACTERS);
                    $this->incrementCurrentCharacterPointer();
                }

                break;

            case self::STATE_UNKNOWN:
                $this->deriveCurrentState();
                break;

            case self::STATE_INVALID_LEADING_CHARACTERS:
                throw new Exception('Invalid leading characters before first quote character', 1);
                break;

            case self::STATE_INVALID_ESCAPE_CHARACTER:
                throw new Exception('Invalid escape character at position '.$this->getCurrentCharacterPointer(), 3);
                break;

            case self::STATE_INVALID_TRAILING_CHARACTERS:
                $exceptionMessage = implode(' ', [
                    'Invalid trailing characters after last quote character at position',
                    $this->getCurrentCharacterPointer(),
                ]);

                throw new Exception($exceptionMessage, 2);
                break;
        }
    }

    private function deriveCurrentState(): void
    {
        if ($this->isCurrentCharacterFirstCharacter()) {
            if ($this->isCurrentCharacterQuoteDelimiter()) {
                $this->setCurrentState(self::STATE_ENTERING_QUOTED_STRING);

                return;
            }

            $this->setCurrentState(self::STATE_INVALID_LEADING_CHARACTERS);
        }
    }

    private function isCurrentCharacterQuoteDelimiter(): bool
    {
        return $this->getCurrentCharacter() == self::QUOTE_DELIMITER;
    }

    private function isCurrentCharacterEscapeCharacter(): bool
    {
        return $this->getCurrentCharacter() == self::ESCAPE_CHARACTER;
    }

    private function isPreviousCharacterEscapeCharacter(): bool
    {
        return $this->getPreviousCharacter() == self::ESCAPE_CHARACTER;
    }

    private function isNextCharacterQuoteCharacter(): bool
    {
        return $this->getNextCharacter() == self::QUOTE_DELIMITER;
    }
}
