<?php
class IndenterTest extends PHPUnit_Framework_TestCase {
    /**
     * @expectedException Gajus\Dindent\Exception\InvalidArgumentException
     * @expectedExceptionMessage Unrecognised option.
     */
    public function testInvalidOption () {
        new \Gajus\Dindent\Indenter(array('foo' => 'bar'));
    }

    public function testIndentCustomCharacter () {
        $indenter = new \Gajus\Dindent\Indenter(array('indentation_character' => 'X'));

        $indented = $indenter->indent('<p><p></p></p>');

        $expected_output = '<p>X<p></p></p>';

        $this->assertSame($expected_output, str_replace("\n", '', $indented));
    }

    /**
     * @dataProvider logProvider
     */
    public function testLog ($token, $log) {
        $indenter = new \Gajus\Dindent\Indenter();
        $indenter->indent($token);
        
        $this->assertSame(array($log), $indenter->getLog());
    }

    public function logProvider () {
        return array(
            array(
                '<p></p>',
                array(
                    'rule' => 'NO',
                    'pattern' => '/^(<([a-z]+)(?:[^>]*)>(?:[^<]*)<\\/(?:\\2)>)/',
                    'subject' => '<p></p>',
                    'match' => '<p></p>',
                )
            )
        );
    }

    /**
     * @dataProvider indentProvider
     */
    public function testIndent ($name) {
        $indenter = new \Gajus\Dindent\Indenter();

        $input = file_get_contents(__DIR__ . '/input/' . $name . '.html');
        $expected_output = file_get_contents(__DIR__ . '/output/' . $name . '.html');

        $this->assertSame($expected_output, $indenter->indent($input));
    }

    public function indentProvider () {
        return array_map(function ($e) {
            return array(pathinfo($e, \PATHINFO_FILENAME));
        }, glob(__DIR__ . '/input/*.html'));
    }
}