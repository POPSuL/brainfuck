<?php

namespace POPSuL\Tests\Brainfuck;

use POPSuL\Brainfuck\BrainfuckCompiler as C;
use POPSuL\Brainfuck\BrainfuckVM as V;

class VMTest extends \PHPUnit_Framework_TestCase
{
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

        $compiled = C::compile($code);
        $vm = new V($compiled);
        $vm->run();
        $this->expectOutputString('Hello World!');
    }

    public function testComplex()
    {
        $code = <<<FUCK_MY_BRAIN
>++++++++[-<+++++++++>]<.>>+>-[+]++>++>+++[>[-
>+++<<+++>]<<]>-----.>->+++..+++.>-.<<+[>[+>+]
>>]<--------------.>>.+++.------.--------.>+.
FUCK_MY_BRAIN;

        $compiled = C::compile($code);
        $vm = new V($compiled);
        $vm->run();
        $this->expectOutputString('Hello World!');
    }
}