<?php

namespace POPSuL\Brainfuck;

use POPSuL\Brainfuck\BrainfuckCompiler as C;

class Generator
{

    private $code = [];

    public function __construct(array $code)
    {
        $this->code = $code;
    }

    public function generate()
    {
        $out = [];

        $out[] = $this->generateHeader();
        $out[] = $this->generateBody();
        $out[] = $this->generateFooter();
        return implode("\n", $out);
    }

    private function calculateMemoryCapacity()
    {
        $shiftMax = 0;
        $shiftCurrent = 0;

        foreach ($this->code as $inst) {
            if ($inst === C::TOKEN_NEXT) {
                $shiftCurrent++;
                if ($shiftCurrent > $shiftMax) {
                    $shiftMax = $shiftCurrent;
                }
            } elseif ($inst === C::TOKEN_PREV) {
                $shiftCurrent--;
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

    private function countNext($token, $offset)
    {
        for ($count = 0, $address = $offset; $address < count($this->code); $address++) {
            if ($this->code[$address] === $token) {
                $count++;
            } else {
                break;
            }
        }
        return $count;
    }

    private function generateBody()
    {
        $out = [];
        $out[] = '//body';

        for ($address = 0; $address < count($this->code); $address++) {
            $inst = $this->code[$address];
            switch ($inst) {
                case C::TOKEN_ADD:
                    $ahead = $this->countNext(C::TOKEN_ADD, $address);
                    if ($ahead > 0) {
                        $out[] = '$mem[$cursor] += ' . $ahead . ';';
                        $address += --$ahead;
                    } else {
                        $out[] = '$mem[$cursor]++;';
                    }
                    break;
                case C::TOKEN_SUB:
                    $ahead = $this->countNext(C::TOKEN_SUB, $address);
                    if ($ahead > 0) {
                        $out[] = '$mem[$cursor] -= ' . $ahead . ';';
                        $address += --$ahead;
                    } else {
                        $out[] = '$mem[$cursor]--;';
                    }
                    break;
                case C::TOKEN_NEXT:
                    $ahead = $this->countNext(C::TOKEN_NEXT, $address);
                    if ($ahead > 0) {
                        $out[] = '$cursor += ' . $ahead . ';';
                        $address += --$ahead;
                    } else {
                        $out[] = '$cursor++;';
                    }
                    break;
                case C::TOKEN_PREV:
                    $ahead = $this->countNext(C::TOKEN_PREV, $address);
                    if ($ahead > 0) {
                        $out[] = '$cursor -= ' . $ahead . ';';
                        $address += --$ahead;
                    } else {
                        $out[] = '$cursor--;';
                    }
                    break;
                case C::TOKEN_OUT:
                    $out[] = 'echo chr($mem[$cursor]);';
                    break;
                case C::TOKEN_IN:
                    $out[] = '//input isnt implemented yet';
                    break;
                default:
                    if (is_array($inst)) {
                        switch ($inst[0]) {
                            case C::TOKEN_JNE:
                                $out[] = 'label' . $address . ':';
                                $out[] = 'if (!$mem[$cursor]) goto label' . $inst[1] . ';';
                                break;
                            case C::TOKEN_JMP:
                                $out[] = 'goto label' . $inst[1] . ';';
                                $out[] = 'label' . ($address + 1) . ':';
                        }
                    }
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