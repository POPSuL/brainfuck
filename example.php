<?php

use POPSuL\Brainfuck\BrainfuckOptimizer;
use POPSuL\Brainfuck\BrainfuckVM;
use POPSuL\Brainfuck\BrainfuckCompiler;
use POPSuL\Brainfuck\PHPGenerator;

require __DIR__ . '/vendor/autoload.php';

$code = <<<FUCK_MY_BRAIN_AGAIN
++++++++++[>+++++++>++++++++++>+++>+<<<<-]>++
 .>+.+++++++..+++.>++.<<+++++++++++++++.>.+++.
 ------.--------.>+.>.
FUCK_MY_BRAIN_AGAIN;

$program = (new BrainfuckCompiler())->compile($code);
$optimized = $program->optimize(new BrainfuckOptimizer());

printf("Not optimized size: %d instructions\n", count($program->getInstructions()));
printf("Optimized size: %d instructions\n\n", count($optimized->getInstructions()));

$vm = new BrainfuckVM();

printf("Not optimized program:\n");
$vm->execute($program);

printf("Optimized program:\n");
$vm->execute($optimized);

//echo $program->generateCode(new PHPGenerator());