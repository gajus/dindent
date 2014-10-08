<?php
class IndenterTest extends PHPUnit_Framework_TestCase {
    /**
     * @expectedException Gajus\Dindent\Exception\InvalidArgumentException
     * @expectedExceptionMessage Unrecognized option.
     */
    public function testInvalidSetupOption () {
        new \Gajus\Dindent\Indenter(array('foo' => 'bar'));
    }

    /**
     * @expectedException Gajus\Dindent\Exception\InvalidArgumentException
     * @expectedExceptionMessage Unrecognized element type.
     */
    public function testSetInvalidElementType () {
        $indenter = new \Gajus\Dindent\Indenter();
        $indenter->setElementType('foo', 'bar');
    }

    /*public function testSetElementTypeInline () {
        $indenter = new \Gajus\Dindent\Indenter();
        $indenter->setElementType('foo', \Gajus\Dindent\Indenter::ELEMENT_TYPE_BLOCK);

        $output = $indenter->indent('<p><span>X</span></p>');

        die(var_dump( $output ));
    }*/

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

        $input = file_get_contents(__DIR__ . '/sample/input/' . $name . '.html');
        $expected_output = file_get_contents(__DIR__ . '/sample/output/' . $name . '.html');

        $this->assertSame($expected_output, $indenter->indent($input));
    }

    public function indentProvider () {
        return array_map(function ($e) {
            return array(pathinfo($e, \PATHINFO_FILENAME));
        }, glob(__DIR__ . '/sample/input/*.html'));
    }
}