<?php

declare(strict_types=1);

namespace webignition\QuotedString;

/**
 * A quoted string as defined in RFC2822 section 3.2.5:
 * http://tools.ietf.org/html/rfc2822#section-3.2.5
 *
 * This does not include the ability for a quoted string
 * to have leading and trailing commands and folding
 * white space.
 *
 * Quoted string format:
 *
 * "{anything apart from quote or backslash, unless \" is used to escape quote}"
 */
class QuotedString
{
    /**
     * The unquoted value
     *
     * @var string|null
     */
    private $value;

    /**
     * @param string $value The unquoted raw value
     */
    public function __construct(?string $value = null)
    {
        $this->value = $value;
    }

    public function getValue(): string
    {
        return (string) $this->value;
    }

    public function __toString(): string
    {
        return '"' .  str_replace('"', '\"', (string) $this->value) . '"';
    }
}
