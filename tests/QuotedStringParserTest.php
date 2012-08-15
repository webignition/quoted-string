<?php

class QuotedStringParserTest extends PHPUnit_Framework_TestCase {

    public function testParseSimpleQuotedString() {
        $parser = new \webignition\QuotedString\Parser();
        $quotedString = $parser->parse('"one two three"');
        $this->assertEquals('one two three', $quotedString->getValue());
    }
    
    public function testParseWithInvalidLeadingCharacters() {
        $parser = new \webignition\QuotedString\Parser();
        
        try {
           $parser->parse('one two three');                     
        } catch (\webignition\QuotedString\Exception $exception) {
            $this->assertEquals(1, $exception->getCode());
            return;
        }          
        
        $this->fail('Invalid leading character exception not thrown'); 
    }    
    
    public function testParseWithInvalidTrailingCharacters() {
        $parser = new \webignition\QuotedString\Parser();
        
        try {
           $parser->parse('"one two three" invalid trailing characters');                     
        } catch (\webignition\QuotedString\Exception $exception) {
            $this->assertEquals(2, $exception->getCode());
            return;
        }         
        
        $this->fail('Invalid trailing character exception not thrown'); 
    }
    
    public function testParseWithEscapedInternalQuote() {
        $parser = new \webignition\QuotedString\Parser();
        $quotedString = $parser->parse('"one \"two\" three"');
        $this->assertEquals('one "two" three', $quotedString->getValue());
    }
    
    public function testParseWithInvalidEscapeCharacter() {
        $parser = new \webignition\QuotedString\Parser();
        
        try {
           $parser->parse('"one \two three"');                     
        } catch (\webignition\QuotedString\Exception $exception) {
            $this->assertEquals(3, $exception->getCode());
            return;
        }
        
        $this->fail('Invalid escape character exception not thrown'); 
    }
    
    
    
}