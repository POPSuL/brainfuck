<?php

namespace POPSuL\Brainfuck;

class BrainfuckCompiler
{

    const TOKEN_NEXT = 'NEXT';
    const TOKEN_PREV = 'PREV';
    const TOKEN_ADD = 'ADD';
    const TOKEN_SUB = 'SUB';
    const TOKEN_OUT = 'OUT';
    const TOKEN_IN = 'IN';
    const TOKEN_JMP = 'JMP';
    const TOKEN_JNE = 'JNE';
    const TOKEN_NOOP = 'NOOP';

    public static function compile($programm)
    {
        $programm = str_replace(" ", "", $programm);
        $programm = str_replace("\n", "", $programm);
        $out = [];

        $labels = [];

        for ($i = 0; $i < strlen($programm); $i++) {
            switch ($programm[$i]) {
                case '>':
                    $out[] = self::TOKEN_NEXT;
                    break;
                case '<':
                    $out[] = self::TOKEN_PREV;
                    break;
                case '+':
                    $out[] = self::TOKEN_ADD;
                    break;
                case '-':
                    $out[] = self::TOKEN_SUB;
                    break;
                case '.':
                    $out[] = self::TOKEN_OUT;
                    break;
                case ',':
                    $out[] = self::TOKEN_IN;
                    break;
                case '[':
                    $level = 0;
                    for ($j = $i + 1; $j < strlen($programm); $j++) {
                        if ($programm[$j] === '[') {
                            $level++;
                        } elseif ($programm[$j] === ']') {
                            if ($level === 0) {
                                array_push($labels, $i);
                                $out[] = [self::TOKEN_JNE, $j + 1];
                                break;
                            }
                            $level--;
                        }
                    }
                    if ($level > 0) {
                        throw new ParserException('Unexpected end of statement, level ' . $level);
                    }
                    break;
                case ']':
                    $out[] = [self::TOKEN_JMP, array_pop($labels)];
                    break;
                default:
                    throw new ParserException(sprintf("invalid token %s", $programm[$i]));
            }
        }
        return $out;
    }
}
