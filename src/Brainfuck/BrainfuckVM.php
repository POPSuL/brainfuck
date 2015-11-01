<?php

namespace POPSuL\Brainfuck;

class BrainfuckVM
{
    private $code = [];

    private $mem = "";

    private $cursor = 0;

    public function __construct(array $code)
    {
        $this->code = $code;
    }

    public function run($capacity = 1024)
    {
        $this->mem = array_fill(0, $capacity, 0x00);
        $this->cursor = 0;
        for ($i = 0; $i < count($this->code); $i++) {
            $instr = $this->code[$i];
            //printf("%d\t%s\n", $i, is_array($instr) ? sprintf("%s:%d", $instr[0], $instr[1]) : $instr);
            switch($instr) {
                case BrainfuckCompiler::TOKEN_ADD:
                    $this->mem[$this->cursor]++;
                    break;
                case BrainfuckCompiler::TOKEN_SUB:
                    $this->mem[$this->cursor]--;
                    break;
                case BrainfuckCompiler::TOKEN_NEXT:
                    $this->cursor++;
                    break;
                case BrainfuckCompiler::TOKEN_PREV:
                    $this->cursor--;
                    break;
                case BrainfuckCompiler::TOKEN_OUT:
                    echo chr($this->mem[$this->cursor]);
                    break;
                case BrainfuckCompiler::TOKEN_IN:
                    while(true) {
                        echo "input value: ";
                        $v = (int)trim(readline());
                        if (!is_int($v) || $v < 0x00 || $v > 0xff) {
                            printf("Invalid value, try again\n");
                            continue;
                        }
                        $this->mem[$this->cursor] = $v;
                        break;
                    }
                   break;
                case BrainfuckCompiler::TOKEN_NOOP:
                    //noop
                    break;
                default:
                    if (is_array($instr)) {
                        if ($instr[0] === BrainfuckCompiler::TOKEN_JNE) {
                            if (!$this->mem[$this->cursor]) {
                                $i = $instr[1] - 1;
                            }
                        } elseif ($instr[0] === BrainfuckCompiler::TOKEN_JMP) {
                            $i = $instr[1] - 1;
                        }
                    }
            }
        }
    }
}
