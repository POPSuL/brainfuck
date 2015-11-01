<?php

use POPSuL\Brainfuck\BrainfuckVM;
use POPSuL\Brainfuck\BrainfuckCompiler;
use POPSuL\Brainfuck\Generator;

require __DIR__ . '/vendor/autoload.php';

$program = <<<FUCK_MY_BRAIN_AGAIN
++++++++++[>+++++++>++++++++++>+++>+<<<<-]>++
 .>+.+++++++..+++.>++.<<+++++++++++++++.>.+++.
 ------.--------.>+.>.
FUCK_MY_BRAIN_AGAIN;

$code = BrainfuckCompiler::compile($program);
$vm = new BrainfuckVM($code);
$vm->run();

$generator = new Generator($code);
echo $generator->generate();