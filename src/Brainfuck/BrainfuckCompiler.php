<?php

namespace POPSuL\Brainfuck;

use POPSuL\Brainfuck\Exception\ParserException;

/**
 * Class BrainfuckCompiler
 *
 * @package POPSuL\Brainfuck
 */
class BrainfuckCompiler implements CompilerInterface
{

    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     *
     * @param $code
     * @return BrainfuckProgram
     * @throws ParserException
     */
    public function compile($code)
    {
        $code = str_replace([" ", "\n", "\t"], "", $code);

        $out = [];
        $labels = [];

        for ($i = 0; $i < strlen($code); $i++) {
            switch ($code[$i]) {
                case '>':
                    $out[] = [self::TOKEN_SHIFT, 1];
                    break;
                case '<':
                    $out[] = [self::TOKEN_SHIFT, -1];
                    break;
                case '+':
                    $out[] = [self::TOKEN_ADD, 1];
                    break;
                case '-':
                    $out[] = [self::TOKEN_SUB, 1];
                    break;
                case '.':
                    $out[] = [self::TOKEN_OUT];
                    break;
                case ',':
                    $out[] = [self::TOKEN_IN];
                    break;
                case '[':
                    $level = 0;
                    for ($j = $i + 1; $j < strlen($code); $j++) {
                        if ($code[$j] === '[') {
                            $level++;
                        } elseif ($code[$j] === ']') {
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
                case ';':
                    $out[] = [self::TOKEN_DELAY, 50];
                    break;
                default:
                    throw new ParserException(sprintf("invalid token %s", $code[$i]));
            }
        }
        $out[] = [self::TOKEN_NOOP];
        return new BrainfuckProgram($out);
    }
}
