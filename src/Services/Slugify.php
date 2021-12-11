<?php

namespace App\Services;

class Slugify
{
    public function generate(string $input, $divider = "-"): string
    {
        /*
            à, ç, etc. deviennent a, c, etc.;
            !, apostrophes et autres ponctuations sont supprimées;
            les espaces en début et fin de chaînes sont supprimées;
            il ne peut pas contenir plusieurs - (tirets) successifs;
            la chaîne générée est en minuscules
        */

        // replace non letter or digits by divider
        $slug = preg_replace('~[^\pL\d]+~u', $divider, $input);

        // transliterate
        $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);

        // remove unwanted characters
        $slug = preg_replace('~[^-\w]+~', '', $slug);

        // trim
        $slug = trim($slug, $divider);

        // remove duplicate divider
        $slug = preg_replace('~-+~', $divider, $slug);

        // lowercase
        $slug = strtolower($slug);

        if (empty($slug)) {
            return '';
        }

        return $slug;
    }
}
