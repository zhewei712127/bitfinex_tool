<?php

namespace App\Services;


class ToolService
{
    public function substr_cut($str)
    {
        $strlen = mb_strlen($str, 'utf-8');

        if ($strlen < 2) {
            return $str;
        } else {
            $first_str = mb_substr($str, 0, 1, 'utf-8');
            $last_str = mb_substr($str, -1, 1, 'utf-8');

            return $strlen == 2 ?
                $first_str . str_repeat('*', mb_strlen($str, 'utf-8') - 1) :
                $first_str . str_repeat('*', $strlen - 2) . $last_str;
        }
    }
}
