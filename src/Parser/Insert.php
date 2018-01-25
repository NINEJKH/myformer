<?php

namespace MyFormer\Parser;

use Exception;

class Insert
{
    public $table;

    public $columns = [];

    public $value_groups = [];

    public $values = [];

    public function __construct($raw)
    {
        $this->parse($raw);
    }

    public function parse($raw)
    {
        if (preg_match('~^INSERT INTO `([^`]+)` \((.*)\) VALUES (.*);$~i', $raw, $matches)) {
            $this->table = $matches[1];
            $this->splitColumns($matches[2]);
            $this->splitValueGroups($matches[3]);
            $this->splitValues($this->value_groups);
        }
    }

    protected function splitColumns($raw)
    {
        // https://stackoverflow.com/a/6243797/567193
        $raw_columns = preg_split('~\\\\.(*SKIP)(*FAIL)|,\s*~s', $raw);
        foreach ($raw_columns as $raw_column) {
            $this->columns[] = trim($raw_column, '`');
        }
    }

    protected function splitValueGroups($raw)
    {
        $str_len = strlen($raw);
        $cur = -1;

        $tok_start = '(';
        $tok_end = ')';
        $started = false;
        $skipped = false;

        for ($i = 0; $i < $str_len; ++$i) {

            // start
            if (!$skipped && !$started && $raw[$i] === $tok_start) {
                ++$cur;
                $this->value_groups[$cur] = '';
                $started = true;
                continue;
            }
            // end
            elseif (!$skipped && $started && $raw[$i] === $tok_end) {
                $started = false;
                // validation?
                if (!isset($raw[$i + 1])) {
                    continue;
                } elseif ($raw[$i + 1] === ',') {
                    ++$i;
                    continue;
                } else {
                    throw new Exception("token error");
                }
            }

            if (!$started) {
                continue;
            }

            // don't tokenise escaped
            if ($raw[$i] === '\\') {
                $this->value_groups[$cur] .= $raw[$i] . $raw[++$i];
                continue;
            }

            // start skip sequence
            if ($raw[$i] === "'") {
                $skipped = ! $skipped;
            }

            $this->value_groups[$cur] .= $raw[$i];
        }
    }

    protected function splitValues(array $value_groups)
    {
        $n = -1;

        foreach ($value_groups as $raw) {
            $this->values[++$n] = [];
            $str_len = strlen($raw);
            $cur = 0;
            $tok_end = '';

            for ($i = 0; $i < $str_len; ++$i) {

                // token-end reached
                if ($raw[$i] === $tok_end) {
                    // skip next char if token-end is not the delimiter
                    if ($tok_end === "'") {
                        $this->values[$n][$cur] .= $raw[$i];
                        ++$i;
                    }
                    ++$cur;
                    $tok_end = '';
                    continue;
                }

                // token-end not yet defined
                if ($tok_end === '') {
                    // skip whitespaces
                    if ($raw[$i] === ' ') {
                        continue;
                    }

                    // define token-end
                    if ($raw[$i] === "'") {
                        $tok_end = "'";
                    } else {
                        $tok_end = ',';
                    }
                }

                // don't tokenise escaped
                if ($raw[$i] === '\\') {
                    $this->values[$n][$cur] .= $raw[$i] . $raw[++$i];
                    continue;
                }

                if (!isset($this->values[$n][$cur])) {
                    $this->values[$n][$cur] = '';
                }

                $this->values[$n][$cur] .= $raw[$i];
            }
        }
    }
}
