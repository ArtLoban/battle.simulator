<?php

namespace Services\ClassFactory;

use App\Models\Soldier;
use App\Models\Squad;
use App\Models\Vehicle;
use App\SimulatorController;
use Exception;
use Services\ArmyConfigurator\ArmyConfigurator;
use Services\ArmyConfigurator\ConfigurationFactory;
use Services\ArmyConfigurator\Strategies\FromConfigCollector;
use Services\ArmyGenerator\ArmyGenerator;
use Services\BattleLogger\BattleLogger;
use Services\BattleSimulator\BattleMaster;
use Services\BattleSimulator\BattleSimulator;
use Services\BattleStrategy\Strategies\Strongest;
use Services\BattleStrategy\Strategies\Weakest;
use Services\BattleStrategy\StrategyFactory;
use Services\Calculator\SoldierCalculator;
use Services\Calculator\SquadCalculator;
use Services\Calculator\VehicleCalculator;
use Services\ClassFactory\Units\ArmyFactory;
use Services\ClassFactory\Units\SquadFactory;
use Services\ClassFactory\Units\Strategies\SoldierFactory;
use Services\ClassFactory\Units\Strategies\VehicleFactory;
use Services\ClassFactory\Units\UnitBuildingStrategy;
use Services\ConfigUploader\ConfigFactory;
use Services\ConfigUploader\ConfigUploader;
use Services\LogWriter\LogWriter;
use Services\Sorter\SquadSorter;
use Throwable;

class Factory
{
    /**
     * @var array
     */
    private $classes = [
        SimulatorController::class => [
            ArmyConfigurator::class,
            ArmyGenerator::class,
            BattleSimulator::class,
            BattleLogger::class,
        ],
        ArmyGenerator::class => [ArmyFactory::class],
        ArmyFactory::class => [SquadFactory::class],
        SquadFactory::class => [self::class, UnitBuildingStrategy::class],
        VehicleFactory::class => [self::class, SoldierFactory::class],
        BattleSimulator::class => [BattleMaster::class, StrategyFactory::class, BattleLogger::class],
        StrategyFactory::class => [self::class],
        Weakest::class => [SquadSorter::class],
        Strongest::class => [SquadSorter::class],
        UnitBuildingStrategy::class => [self::class],
        BattleMaster::class => [BattleLogger::class],
        SoldierFactory::class => [self::class],
        Soldier::class => [SoldierCalculator::class],
        Vehicle::class => [VehicleCalculator::class],
        Squad::class => [SquadCalculator::class],
        BattleLogger::class => [LogWriter::class],
        ArmyConfigurator::class => [ConfigurationFactory::class],
        ConfigurationFactory::class => [self::class],
        ConfigUploader::class => [ConfigFactory::class],
        ConfigFactory::class => [self::class],
        FromConfigCollector::class => [ConfigUploader::class],
    ];

    /**
     * @param string $className
     * @return object
     */
    public function create(string $className): object
    {
        return isset($this->classes[$className])
            ? $this->createClassWithParams($className, $this->classes[$className])
            : $this->createInstance($className);
    }

    /**
     * @param string $className
     * @param array $params
     * @return object
     * @throws Exception
     */
    private function createClassWithParams(string $className, array $params): object
    {
        $paramsInstances = [];
        foreach ($params as $paramClassName) {
            $paramsInstances[] = $this->create($paramClassName);
        }

        return $this->createInstance($className, $paramsInstances);
    }

    /**
     * @param string $className
     * @param array $paramsInstances
     * @return object
     * @throws Exception
     */
    private function createInstance(string $className, array $paramsInstances = []): object
    {
        try {
            return new $className(... $paramsInstances);
        } catch (Throwable $exception) {
            throw new Exception("Custom Error: there is no \"{$className}\" class name in the given array");
        }
    }
}
