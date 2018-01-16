<?php

namespace MyFormer\Parser;

class Insert
{
    public $table;

    public $columns = [];

    public $values = [];

    public function __construct($raw)
    {
        $this->parse($raw);
    }

    public function parse($raw)
    {
        if (preg_match('~^INSERT INTO `([^`]+)` \((.*)\) VALUES \((.*)\);$~i', $raw, $matches)) {
            $this->table = $matches[1];
            $this->splitColumns($matches[2]);
            $this->splitValues($matches[3]);
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

    protected function splitValues($raw)
    {
        $str_len = strlen($raw);
        $cur = 0;
        $tok_end = '';

        for ($i = 0; $i < $str_len; ++$i) {

            // token-end reached
            if ($raw[$i] === $tok_end) {
                // skip next char if token-end is not the delimiter
                if ($tok_end === "'") {
                    $this->values[$cur] .= $raw[$i];
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
                $this->values[$cur] .= $raw[$i] . $raw[++$i];
                continue;
            }

            if (!isset($this->values[$cur])) {
                $this->values[$cur] = '';
            }

            $this->values[$cur] .= $raw[$i];
        }
    }
}
