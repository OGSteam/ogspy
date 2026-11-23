<?php

use PHPUnit\Framework\TestCase;

if (!defined('IN_SPYOGAME')) {
    define('IN_SPYOGAME', true);
}

require_once dirname(__DIR__, 2) . '/includes/lifeform.php';

class LifeformHelpersTest extends TestCase
{
    public function testResolvesTheFourLifeformSpecies(): void
    {
        $this->assertSame('HUMANS', lifeform_species_code(11101));
        $this->assertSame('ROCKTAL', lifeform_species_code(12201));
        $this->assertSame('MECHAS', lifeform_species_code(13101));
        $this->assertSame('KAELESH', lifeform_species_code(14218));
        $this->assertSame('UNKNOWN', lifeform_species_code(999));
    }

    public function testCollectsUniqueSortedIdsForOneCategory(): void
    {
        $lifeforms = [
            101 => ['buildings' => [12101 => 8, 11101 => 12], 'research' => [12201 => 4]],
            102 => ['buildings' => [11101 => 7, 13101 => 3], 'research' => [11201 => 2]],
        ];

        $this->assertSame([11101, 12101, 13101], lifeform_technology_ids($lifeforms, 'buildings'));
        $this->assertSame([11201, 12201], lifeform_technology_ids($lifeforms, 'research'));
        $this->assertSame([], lifeform_technology_ids($lifeforms, 'invalid'));
    }
}
