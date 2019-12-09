<?php
/**
 * Created by PhpStorm.
 * User: steven
 * Date: 08/12/19
 * Time: 13:09
 */

namespace App\Service;


class Slugify
{
    public function generate(string $input): string
    {
        setlocale(LC_ALL, 'fr_FR');
        $input = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $input);
        $input = preg_replace('#[^\w]+#i', '-', $input);
        $input = str_replace('--', '-', $input);
        $input = trim($input, '-');
        return strtolower($input);
    }
}

