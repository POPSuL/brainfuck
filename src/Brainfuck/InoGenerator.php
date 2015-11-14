<?php

namespace POPSuL\Brainfuck;

use POPSuL\Brainfuck\BrainfuckCompiler as C;
use POPSuL\Brainfuck\Exception\GeneratorException;

class InoGenerator implements GeneratorInterface
{

    private $code = [];

    /**
     * Generates some code for program
     *
     * @param ProgramInterface $program
     * @return string
     */
    public function generate(ProgramInterface $program)
    {
        try {
            $this->code = $program->getInstructions();
            $out = [];
            $out[] = $this->generateHeader();
            $out[] = $this->generateBody();
            return implode("\n", $out);
        } finally {
            $this->code = [];
        }
    }

    private function calculateMemoryCapacity()
    {
        $shiftMax = 0;
        $shiftCurrent = 0;

        foreach ($this->code as $inst) {
            if ($inst[0] === C::TOKEN_SHIFT) {
                $shiftCurrent += $inst[1];
                if ($shiftCurrent > $shiftMax) {
                    $shiftMax = $shiftCurrent;
                }
            }
        }
        return $shiftMax + 1;
    }

    private function generateHeader()
    {
        $capacity = $this->calculateMemoryCapacity();
        return <<<HEADER
//header
uint8_t mem[$capacity];
int cursor = 0;

void setup() {}

HEADER;
    }

    private function generateBody()
    {
        $out = [];
        $out[] = '//body';
        $out[] = 'void loop() {';

        for ($address = 0; $address < count($this->code); $address++) {
            $inst = $this->code[$address];
            switch ($inst[0]) {
                case C::TOKEN_ADD:
                    $out[] = 'mem[cursor] += ' . $inst[1] . ';';
                    break;
                case C::TOKEN_SUB:
                    $out[] = 'mem[cursor] -= ' . $inst[1] . ';';
                    break;
                case C::TOKEN_SHIFT:
                    $out[] = 'cursor += ' . $inst[1] . ';';
                    break;
                case C::TOKEN_OUT:
                    $out[] = 'pinMode(cursor, OUTPUT);';
                    $out[] = 'digitalWrite(cursor, mem[cursor]);';
                    break;
                case C::TOKEN_IN:
                    $out[] = 'pinMode(cursor, INPUT);';
                    $out[] = 'mem[cursor] = (uint8_t) digitalRead(cursor);';
                    break;
                case C::TOKEN_JNE:
                    $out[] = 'label' . $address . ':';
                    $out[] = 'if (!mem[cursor]) goto label' . $inst[1] . ';';
                    break;
                case C::TOKEN_JMP:
                    $out[] = 'goto label' . $inst[1] . ';';
                    $out[] = 'label' . ($address + 1) . ':';
                    $out[] = ';';
                    break;
                case C::TOKEN_DELAY:
                    $out[] = "delay(" . $inst[1] . ");";
                    break;
                case C::TOKEN_NOOP:
                    //noop
                    break;
                default:
                    throw new GeneratorException(sprintf('Invalid token %s', $inst[0]));
            }
        }

        $out[] = '}';

        return implode("\n", $out);
    }
}