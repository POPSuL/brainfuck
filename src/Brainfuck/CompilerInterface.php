<?php

namespace POPSuL\Brainfuck;

/**
 * Interface CompilerInterface
 *
 * @package POPSuL\Brainfuck
 */
interface CompilerInterface
{

    const TOKEN_SHIFT = 'SHIFT';
    const TOKEN_ADD = 'ADD';
    const TOKEN_SUB = 'SUB';
    const TOKEN_OUT = 'OUT';
    const TOKEN_IN = 'IN';
    const TOKEN_JMP = 'JMP';
    const TOKEN_JNE = 'JNE';
    const TOKEN_NOOP = 'NOOP';
    const TOKEN_DELAY = 'DELAY';

    /**
     * Compiles a fuckin programm
     *
     * @param $code
     * @return ProgramInterface
     */
    public function compile($code);
}
