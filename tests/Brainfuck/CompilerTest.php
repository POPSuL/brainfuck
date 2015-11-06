<?php

namespace POPSuL\Tests\Brainfuck;

use POPSuL\Brainfuck\BrainfuckCompiler as C;
use POPSuL\Brainfuck\BrainfuckCompiler;
use POPSuL\Brainfuck\CompilerInterface;
use POPSuL\Brainfuck\ProgramInterface;

class CompilerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var CompilerInterface
     */
    private $compiler = null;

    protected function setUp()
    {
       $this->compiler = new BrainfuckCompiler();
    }


    public function testSimple()
    {
        $code = '+-,.<>';
        $expected = [
            [C::TOKEN_ADD, 1],
            [C::TOKEN_SUB, 1],
            [C::TOKEN_IN],
            [C::TOKEN_OUT],
            [C::TOKEN_SHIFT, -1],
            [C::TOKEN_SHIFT, 1],
            [C::TOKEN_NOOP]
        ];

        $program = $this->compiler->compile($code);
        $this->assertInstanceOf(ProgramInterface::class, $program);
        $this->assertEquals($expected, $program->getInstructions());
    }

    public function testLoop()
    {
        $code = '[+]';
        $expected = [
            [C::TOKEN_JNE, 3],
            [C::TOKEN_ADD, 1],
            [C::TOKEN_JMP, 0],
            [C::TOKEN_NOOP]
        ];
        $program = $this->compiler->compile($code);
        $this->assertInstanceOf(ProgramInterface::class, $program);
        $this->assertEquals($expected, $program->getInstructions());
    }

    public function testNestedLoop()
    {
        $code = '[+[+]]';
        $expected = [
            [C::TOKEN_JNE, 6],
            [C::TOKEN_ADD, 1],
            [C::TOKEN_JNE, 5],
            [C::TOKEN_ADD, 1],
            [C::TOKEN_JMP, 2],
            [C::TOKEN_JMP, 0],
            [C::TOKEN_NOOP]
        ];
        $program = $this->compiler->compile($code);
        $this->assertInstanceOf(ProgramInterface::class, $program);
        $this->assertEquals($expected, $program->getInstructions());
    }
}