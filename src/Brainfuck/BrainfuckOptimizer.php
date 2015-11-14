<?php

namespace POPSuL\Brainfuck;

use POPSuL\Brainfuck\Exception\BrainfuckException;
use POPSuL\Brainfuck\CompilerInterface as C;
use POPSuL\Brainfuck\Exception\OptimizerException;

class BrainfuckOptimizer implements OptimizerInterface
{

    const JUMP_HERE_LABEL = 'jump-here';

    /**
     * Make optimization
     *
     * @param ProgramInterface $program
     * @return ProgramInterface optimizer program
     * @throws BrainfuckException
     */
    public function optimize(ProgramInterface $program)
    {
        if (!($program instanceof BrainfuckProgram)) {
            throw new BrainfuckException("Ouch crap... I can't optimize a not brainfuck programs");
        }

        $code = $program->getInstructions();

        $code = $this->toFixedAdressSpace($code);
        $code = $this->optimizeIncDecMasturbation($code);
        $code = $this->optimizeShiftMasturbation($code);
        $code = $this->optimizeDelays($code);
        $code = $this->restoreNativeAddressSpace($code);

        return new BrainfuckProgram($code);
    }

    private function toFixedAdressSpace(array $code)
    {
        $out = [];
        foreach (array_values($code) as $i => $instr) {
            $out[strval($i)] = $instr;
        }
        return $out;
    }

    private function restoreNativeAddressSpace(array $code)
    {
        $addressMap = array_flip(array_keys($code));

        $out = [];

        foreach ($code as $address => $instr) {
            if ($instr[0] === C::TOKEN_JMP || $instr[0] === C::TOKEN_JNE) {
                if (isset($addressMap[$instr[1]])) {
                    $newAddress = $addressMap[$instr[1]];
                } else {
                    throw new OptimizerException('Oups');
                }
                $out[] = [
                    $instr[0], //original instruction
                    $newAddress
                ];
            } else {
                $out[] = $instr;
            }
        }

        return $out;
    }

    /**
     * Optimizes code like ++--+-+-+-+++---
     *
     * @param array $code
     * @return array
     */
    private function optimizeIncDecMasturbation(array $code)
    {
        $current = 0;
        $addressRange = [];

        $optimize = function ($address) use (&$current, &$addressRange, &$code) {
            reset($addressRange);
            if (count($addressRange) > 1) {
                if ($current !== 0) {
                    $code[$address] = [
                        $current > 0 ? C::TOKEN_ADD : C::TOKEN_SUB,
                        abs($current)
                    ];
                    array_pop($addressRange);
                }

                foreach ($addressRange as $addr) {
                    unset($code[$addr]);
                }
            }
            $current = 0;
            $addressRange = [];
        };
        $prevAddr = null;
        foreach ($code as $address => $instr) {
            if ($instr[0] === C::TOKEN_ADD || $instr[0] === C::TOKEN_SUB) {
                $addressRange[] = $prevAddr = $address;
                $current += $instr[1] * ($instr[0] === C::TOKEN_ADD ? 1 : -1);
            } else {
                if ($prevAddr !== null) {
                    $optimize($prevAddr);
                    $prevAddr = null;
                }
            }
        }
        if ($prevAddr !== null) {
            $optimize($prevAddr);
        }
        return $code;
    }

    /**
     * Optimizes code like ><>><<>><<
     *
     * @param array $code
     * @return array
     */
    public function optimizeShiftMasturbation(array $code)
    {
        $shiftLevel = 0;
        $shiftSequence = 0;
        $addressRange = [];

        foreach ($code as $address => $instr) {
            if ($instr[0] === C::TOKEN_SHIFT) {
                $shiftLevel += $instr[1];
                $addressRange[] = $address;
                $shiftSequence++;
            } else {
                if ($shiftSequence > 0) {
                    reset($addressRange);
                    if ($shiftLevel !== 0) {
                        $code[$addressRange[0]] = [
                            C::TOKEN_SHIFT,
                            $shiftLevel
                        ];
                        array_shift($addressRange);
                    }
                    foreach ($addressRange as $addr) {
                        unset($code[$addr]);
                    }
                }
                $addressRange = [];
                $shiftSequence = 0;
                $shiftLevel = 0;
            }
        }
        return $code;
    }

    /**
     * @param array $code
     * @return array
     */
    private function optimizeDelays(array $code)
    {
        return $code; //just stub
    }
}