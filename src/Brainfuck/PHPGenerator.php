<?php

namespace POPSuL\Brainfuck;

use POPSuL\Brainfuck\BrainfuckCompiler as C;
use POPSuL\Brainfuck\Exception\GeneratorException;

class PHPGenerator implements GeneratorInterface
{

    private $code = [];

    public function __construct()
    {
    }

    public function generate(ProgramInterface $program)
    {
        try {
            $this->code = $program->getInstructions();

            $out = [];
            $out[] = $this->generateHeader();
            $out[] = $this->generateBody();
            $out[] = $this->generateFooter();
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
<?php

//header
\$mem = array_fill(0, $capacity, 0x00);
\$cursor = 0;
HEADER;
    }

    private function generateBody()
    {
        $out = [];
        $out[] = '//body';

        for ($address = 0; $address < count($this->code); $address++) {
            $inst = $this->code[$address];
            switch ($inst[0]) {
                case C::TOKEN_ADD:
                    $out[] = '$mem[$cursor] += ' . $inst[1] . ';';
                    break;
                case C::TOKEN_SUB:
                    $out[] = '$mem[$cursor] -= ' . $inst[1] . ';';
                    break;
                case C::TOKEN_SHIFT:
                    $out[] = '$cursor += ' . $inst[1] . ';';
                    break;
                case C::TOKEN_OUT:
                    $out[] = 'echo chr($mem[$cursor]);';
                    break;
                case C::TOKEN_IN:
                    $out[] = <<<'SHIT'
echo "Input integer value (0-255): ";
while (1) {
    $inValue = (int)trim(readline());
    if (!is_int($v) || $v < 0x00 || $v > 0xff) {
        printf("Invalid value, try again\n");
        continue;
    }
    $mem[$cursor] = $inValue;
    unset($inValue);
}
SHIT;

                    break;
                case C::TOKEN_JNE:
                    $out[] = 'label' . $address . ':';
                    $out[] = 'if (!$mem[$cursor]) goto label' . $inst[1] . ';';
                    break;
                case C::TOKEN_JMP:
                    $out[] = 'goto label' . $inst[1] . ';';
                    $out[] = 'label' . ($address + 1) . ':';
                    break;
                default:
                    throw new GeneratorException(sprintf('Invalid token %s', $inst[0]));
            }
        }

        return implode("\n", $out);
    }

    private function generateFooter()
    {
        return <<<'FOOTER'
unset($mem);
unset($cursor);
FOOTER;

    }
}