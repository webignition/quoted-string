<?php

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

    /**
     * @param string $inputString
     *
     * @return QuotedString
     */
    public function parse($inputString)
    {
        return new QuotedString(parent::parse($inputString));
    }

    protected function parseCurrentCharacter()
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

    private function deriveCurrentState()
    {
        if ($this->isCurrentCharacterFirstCharacter()) {
            if ($this->isCurrentCharacterQuoteDelimiter()) {
                $this->setCurrentState(self::STATE_ENTERING_QUOTED_STRING);

                return;
            }

            $this->setCurrentState(self::STATE_INVALID_LEADING_CHARACTERS);
        }
    }

    /**
     * @return boolean
     */
    private function isCurrentCharacterQuoteDelimiter()
    {
        return $this->getCurrentCharacter() == self::QUOTE_DELIMITER;
    }

    /**
     * @return boolean
     */
    private function isCurrentCharacterEscapeCharacter()
    {
        return $this->getCurrentCharacter() == self::ESCAPE_CHARACTER;
    }

    /**
     * @return boolean
     */
    private function isPreviousCharacterEscapeCharacter()
    {
        return $this->getPreviousCharacter() == self::ESCAPE_CHARACTER;
    }

    /**
     * @return boolean
     */
    private function isNextCharacterQuoteCharacter()
    {
        return $this->getNextCharacter() == self::QUOTE_DELIMITER;
    }
}
