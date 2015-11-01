<?php

namespace POPSuL\Tests\Brainfuck;

use POPSuL\Brainfuck\BrainfuckCompiler as C;

class CompilerTest extends \PHPUnit_Framework_TestCase
{

    public function testSimple()
    {
        $code = '+-,.<>';
        $expected = [
            C::TOKEN_ADD,
            C::TOKEN_SUB,
            C::TOKEN_IN,
            C::TOKEN_OUT,
            C::TOKEN_PREV,
            C::TOKEN_NEXT
        ];

        $this->assertEquals($expected, C::compile($code));
    }

    public function testLoop()
    {
        $code = '[+]';
        $expected = [
            [C::TOKEN_JNE, 3],
            C::TOKEN_ADD,
            [C::TOKEN_JMP, 0]
        ];

        $this->assertEquals($expected, C::compile($code));
    }

    public function testNestedLoop()
    {
        $code = '[+[+]]';
        $expected = [
            [C::TOKEN_JNE, 6],
            C::TOKEN_ADD,
            [C::TOKEN_JNE, 5],
            C::TOKEN_ADD,
            [C::TOKEN_JMP, 2],
            [C::TOKEN_JMP, 0]
        ];
        $this->assertEquals($expected, C::compile($code));
    }
}