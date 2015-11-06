<?php

namespace POPSuL\Brainfuck;

/**
 * Interface GeneratorInterface
 *
 * @package POPSuL\Brainfuck
 */
interface GeneratorInterface
{

    /**
     * Generates some code for program
     *
     * @param ProgramInterface $program
     * @return string
     */
    public function generate(ProgramInterface $program);
}