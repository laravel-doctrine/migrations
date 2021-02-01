<?php

declare(strict_types=1);

namespace LaravelDoctrine\Migrations\Output;

class VariableReplacer
{
    /**
     * @param string $contents
     * @param array $variables
     * @param array $replacements
     *
     * @return string
     */
    public function replace(string $contents, array $variables = [], array $replacements = []): string
    {
        $contents = str_replace($variables, $replacements, $contents);
        $contents = preg_replace('/^ +$/m', '', $contents);

        return $contents;
    }
}
