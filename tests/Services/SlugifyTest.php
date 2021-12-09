<?php

namespace App\Tests\Services;

use App\Services\Slugify;
use PHPUnit\Framework\TestCase;

class SlugifyTest extends TestCase
{
   /**
    * @dataProvider getSlugs
    * @test
    */
   public function slugify(string $string, string $slug)
   {
        $slugify = new Slugify();
        $this->assertEquals($slug, $slugify->generate($string));
   }

   public function getSlugs()
   {
        return [
            ['aàâáäãåā', 'aaaaaaaa'],
            ['AÀÂÁÄÃÅĀ', 'aaaaaaaa'],
            ['éêèë', 'eeee'],
            ['íîìï', 'iiii'],
            ['óôòøõö', 'oooooo'],
            ['úûùü', 'uuuu'],
            ['[ÿ]', 'y'],
            ['[æ]', 'ae'],
            ['[ç]', 'c'],
            ['[œ]', 'oe'],
            ['ça alors!', 'ca-alors'],
            ['Êtes-vous certain ?', 'etes-vous-certain'],
            ['Longtemps je me suis couché de bonne heure.', 'longtemps-je-me-suis-couche-de-bonne-heure'],
        ];
   }
}
