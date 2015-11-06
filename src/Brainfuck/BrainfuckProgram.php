<?php

namespace POPSuL\Brainfuck;

/**
 * Class BrainfuckProgram
 *
 * @package POPSuL\Brainfuck
 */
class BrainfuckProgram implements ProgramInterface
{

    private $instructions;

    /**
     * @param array $instructions
     */
    function __construct(array $instructions)
    {
        $this->instructions = $instructions;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getInstructions()
    {
        return $this->instructions;
    }

    /**
     * {@inheritdoc}
     *
     * @param OptimizerInterface $optimizer
     * @return ProgramInterface
     */
    public function optimize(OptimizerInterface $optimizer)
    {
        return $optimizer->optimize($this);
    }

    /**
     * Translate this program to some program code (e.g. PHP) via
     * GeneratorInterface implementation
     *
     * @param GeneratorInterface $generator
     * @return mixed
     */
    public function generateCode(GeneratorInterface $generator)
    {
        return $generator->generate($this);
    }
}
