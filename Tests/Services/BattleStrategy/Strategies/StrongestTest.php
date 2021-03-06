<?php

namespace Tests\Services\BattleStrategy\Strategies;

use App\Models\Squad;
use Services\BattleStrategy\Strategies\Strongest;

class StrongestTest extends StrateyAbstract
{
    /**
     * @var Strongest
     */
    public $strongest;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->mockUnits = $this->getMockUnitsArray();
        $mockSquadSorter = $this->mockSquadSorter($this->mockUnits);
        $this->strongest = new Strongest($mockSquadSorter);
    }

    /**
     * Test get method
     *
     * @covers \Services\BattleStrategy\Strategies\Strongest::get()
     */
    public function testGet()
    {
        $strongestSquad = $this->strongest->get($this->mockUnits);

        $this->assertInternalType('object', $strongestSquad);
        $this->assertInstanceOf(Squad::class, $strongestSquad);
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $this->strongest = null;
        gc_collect_cycles();
    }
}
