<?php

namespace POPSuL\Tests\Brainfuck;

use POPSuL\Brainfuck\BrainfuckCompiler;
use POPSuL\Brainfuck\BrainfuckOptimizer;
use POPSuL\Brainfuck\CompilerInterface as C;
use POPSuL\Brainfuck\OptimizerInterface;

class OptimizerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var C
     */
    private $compiler = null;

    /**
     * @var OptimizerInterface
     */
    private $optimizer = null;

    protected function setUp()
    {
        parent::setUp();

        $this->compiler = new BrainfuckCompiler();
        $this->optimizer = new BrainfuckOptimizer();
    }

    public function optimizerDataProdiver()
    {
        return [
            //increments
            [
                "++++++",
                [
                    [C::TOKEN_ADD, 6],
                    [C::TOKEN_NOOP]
                ]
            ],
            [
                "+[++++]",
                [
                    [C::TOKEN_ADD, 1],
                    [C::TOKEN_JNE, 4],
                    [C::TOKEN_ADD, 4],
                    [C::TOKEN_JMP, 1],
                    [C::TOKEN_NOOP]
                ]
            ],
            [
                '+.+[+++]+',
                [
                    [C::TOKEN_ADD, 1],
                    [C::TOKEN_OUT],
                    [C::TOKEN_ADD, 1],
                    [C::TOKEN_JNE, 6],
                    [C::TOKEN_ADD, 3],
                    [C::TOKEN_JMP, 3],
                    [C::TOKEN_ADD, 1],
                    [C::TOKEN_NOOP]
                ]
            ],
            //decrements
            [
                "------",
                [
                    [C::TOKEN_SUB, 6],
                    [C::TOKEN_NOOP]
                ]
            ],
            [
                "-[----]",
                [
                    [C::TOKEN_SUB, 1],
                    [C::TOKEN_JNE, 4],
                    [C::TOKEN_SUB, 4],
                    [C::TOKEN_JMP, 1],
                    [C::TOKEN_NOOP]
                ]
            ],
            [
                '-.-[---]-',
                [
                    [C::TOKEN_SUB, 1],
                    [C::TOKEN_OUT],
                    [C::TOKEN_SUB, 1],
                    [C::TOKEN_JNE, 6],
                    [C::TOKEN_SUB, 3],
                    [C::TOKEN_JMP, 3],
                    [C::TOKEN_SUB, 1],
                    [C::TOKEN_NOOP]
                ]
            ],

            //increments and decrements
            [
                "----++",
                [
                    [C::TOKEN_SUB, 2],
                    [C::TOKEN_NOOP]
                ]
            ],
            [
                "--++++",
                [
                    [C::TOKEN_ADD, 2],
                    [C::TOKEN_NOOP]
                ]
            ],
            [
                "-+[--++]",
                [
                    [C::TOKEN_JNE, 2],
                    [C::TOKEN_JMP, 0],
                    [C::TOKEN_NOOP]
                ]
            ],
            [
                '-.+[--+]+',
                [
                    [C::TOKEN_SUB, 1],
                    [C::TOKEN_OUT],
                    [C::TOKEN_ADD, 1],
                    [C::TOKEN_JNE, 6],
                    [C::TOKEN_SUB, 1],
                    [C::TOKEN_JMP, 3],
                    [C::TOKEN_ADD, 1],
                    [C::TOKEN_NOOP]
                ]
            ],
            //shifts
            [
                '>>><<<',
                [
                    [C::TOKEN_NOOP]
                ]
            ],
            [
                '>><<>>',
                [
                    [C::TOKEN_SHIFT, 2],
                    [C::TOKEN_NOOP]
                ]
            ],
            [
                '>>><<<<',
                [
                    [C::TOKEN_SHIFT, -1],
                    [C::TOKEN_NOOP]
                ]
            ],

            //both
            [
                '>>+++<<--',
                [
                    [C::TOKEN_SHIFT, 2],
                    [C::TOKEN_ADD, 3],
                    [C::TOKEN_SHIFT, -2],
                    [C::TOKEN_SUB, 2],
                    [C::TOKEN_NOOP]
                ]
            ]
        ];
    }

    /**
     * @dataProvider optimizerDataProdiver
     */
    public function testOptimizer($code, $expected)
    {
        $program = $this->compiler
            ->compile($code)
            ->optimize($this->optimizer);

        $this->assertEquals($expected, $program->getInstructions());
    }


}