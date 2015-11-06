<?php

namespace POPSuL\Brainfuck;

/**
 * Interface ProgramInterface
 *
 * @package POPSuL\Brainfuck
 */
interface ProgramInterface
{

    /**
     * Returns instructions set
     *
     * @return array
     */
    public function getInstructions();

    /**
     * Optimize this program via some OptimizerInterface implementation
     *
     * @param OptimizerInterface $optimizer
     * @return ProgramInterface
     */
    public function optimize(OptimizerInterface $optimizer);

    /**
     * Translate this program to some program code (e.g. PHP) via
     * GeneratorInterface implementation
     *
     * @param GeneratorInterface $generator
     * @return mixed
     */
    public function generateCode(GeneratorInterface $generator);
}