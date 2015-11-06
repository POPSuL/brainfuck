<?php

namespace POPSuL\Brainfuck;

/**
 * Interface OptimizerInterface
 *
 * @package POPSuL\Brainfuck
 */
interface OptimizerInterface
{

    /**
     * Make optimization
     *
     * @param ProgramInterface $program
     * @return ProgramInterface optimizer program
     */
    public function optimize(ProgramInterface $program);
}