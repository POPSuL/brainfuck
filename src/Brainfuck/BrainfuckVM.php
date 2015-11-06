<?php

namespace POPSuL\Brainfuck;

use POPSuL\Brainfuck\BrainfuckCompiler as BC;

/**
 * Class BrainfuckVM
 *
 * @package POPSuL\Brainfuck
 */
class BrainfuckVM implements VMInterface
{

    private $memoryCapacity = 1024;

    private $mem = "";

    private $cursor = 0;

    /**
     * @param int $memoryCapacity
     */
    public function __construct($memoryCapacity = 1024)
    {
        $this->memoryCapacity = $memoryCapacity;
    }

    /**
     * {@inheritdoc}
     *
     * @param ProgramInterface $program
     */
    public function execute(ProgramInterface $program)
    {
        $this->mem = array_fill(0, $this->memoryCapacity, 0x00);
        $this->cursor = 0;
        $p = $program->getInstructions();
        for ($i = 0; $i < count($p); $i++) {
            $instr = $p[$i];
            //printf("%d\t%s\n", $i, is_array($instr) ? sprintf("%s:%d", $instr[0], $instr[1]) : $instr);
            switch ($instr[0]) {
                case BC::TOKEN_ADD:
                    $this->mem[$this->cursor] += $instr[1];
                    break;
                case BC::TOKEN_SUB:
                    $this->mem[$this->cursor] -= $instr[1];
                    break;
                case BC::TOKEN_SHIFT:
                    $this->cursor += $instr[1];
                    break;
                case BC::TOKEN_OUT:
                    echo chr($this->mem[$this->cursor]);
                    break;
                case BC::TOKEN_IN:
                    while (true) {
                        echo "Input integer value (0-255): ";
                        $v = (int)trim(readline());
                        if (!is_int($v) || $v < 0x00 || $v > 0xff) {
                            printf("Invalid value, try again\n");
                            continue;
                        }
                        $this->mem[$this->cursor] = $v;
                        break;
                    }
                    break;
                case BC::TOKEN_NOOP:
                    //noop
                    break;
                case BC::TOKEN_JNE:
                    if (!$this->mem[$this->cursor]) {
                        $i = $instr[1] - 1;
                    }
                    break;
                case BC::TOKEN_JMP:
                    $i = $instr[1] - 1;
                    break;
            }
        }
    }
}
