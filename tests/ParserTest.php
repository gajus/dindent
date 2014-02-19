<?php
class ParserTest extends PHPUnit_Framework_TestCase {
    private
        $parser;

    public function setUp () {
        $this->parser = new \Gajus\Pindent\Parser();
    }

    /**
     * @dataProvider indentProvider
     */
    public function testIndent ($name) {
        $this->assertSame(file_get_contents(__DIR__ . '/output/' . $name . '.html'), $this->parser->indent( file_get_contents(__DIR__ . '/input/' . $name . '.html') ));
    }

    public function indentProvider () {
        return array_map(function ($e) {
            return [pathinfo($e, \PATHINFO_FILENAME)];
        }, glob(__DIR__ . '/input/*.html'));
    }
}