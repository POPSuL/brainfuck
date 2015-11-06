<?php

namespace POPSuL\Tests\Brainfuck;

use POPSuL\Brainfuck\BrainfuckCompiler;
use POPSuL\Brainfuck\BrainfuckVM;
use POPSuL\Brainfuck\CompilerInterface;
use POPSuL\Brainfuck\ProgramInterface;
use POPSuL\Brainfuck\VMInterface;

class VMTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var CompilerInterface
     */
    private $compiler = null;

    /**
     * @var VMInterface
     */
    private $vm = null;

    protected function setUp()
    {
        parent::setUp();

        $this->compiler = new BrainfuckCompiler();
        $this->vm = new BrainfuckVM();
    }


    public function testSimple()
    {
        $code = <<<FUCK_MY_BRAIN
+++++++++++++++++++++++++++++++++++++++++++++
+++++++++++++++++++++++++++.+++++++++++++++++
++++++++++++.+++++++..+++.-------------------
---------------------------------------------
---------------.+++++++++++++++++++++++++++++
++++++++++++++++++++++++++.++++++++++++++++++
++++++.+++.------.--------.------------------
---------------------------------------------
----.
FUCK_MY_BRAIN;

        $program = $this->compiler->compile($code);
        $this->assertInstanceOf(ProgramInterface::class, $program);
        $this->vm->execute($program);
        $this->expectOutputString('Hello World!');
    }

    public function testComplex()
    {
        $code = <<<FUCK_MY_BRAIN
>++++++++[-<+++++++++>]<.>>+>-[+]++>++>+++[>[-
>+++<<+++>]<<]>-----.>->+++..+++.>-.<<+[>[+>+]
>>]<--------------.>>.+++.------.--------.>+.
FUCK_MY_BRAIN;
        $program = $this->compiler->compile($code);
        $this->assertInstanceOf(ProgramInterface::class, $program);
        $this->vm->execute($program);
        $this->expectOutputString('Hello World!');
    }
}