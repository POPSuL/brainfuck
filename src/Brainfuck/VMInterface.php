<?php

namespace POPSuL\Brainfuck;

/**
 * Interface VMInterface
 *
 * @package POPSuL\Brainfuck
 */
interface VMInterface
{

    /**
     * Execute a program on this VM
     *
     * @param ProgramInterface $program
     * @return void
     */
    public function execute(ProgramInterface $program);
}