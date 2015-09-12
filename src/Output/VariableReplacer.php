<?php

namespace LaravelDoctrine\Migrations\Output;

class VariableReplacer
{
    /**
     * @param string $contents
     * @param array  $variables
     * @param array  $replacements
     *
     * @return string
     */
    public function replace($contents, array $variables = [], array $replacements = [])
    {
        $contents = str_replace($variables, $replacements, $contents);
        $contents = preg_replace('/^ +$/m', '', $contents);

        return $contents;
    }
}
