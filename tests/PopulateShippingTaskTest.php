<?php

namespace SilverShop\Shipping\Tests;

use SilverShop\Shipping\Model\TableShippingMethod;
use SilverShop\Shipping\Model\ZonedShippingMethod;
use SilverShop\Shipping\Tasks\PopulateTableShippingTask;
use SilverShop\Shipping\Tasks\PopulateZonedShippingTask;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\PolyExecution\PolyOutput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateShippingTaskTest extends SapphireTest
{
    protected $usesDatabase = true;

    private function runTask(PopulateTableShippingTask|PopulateZonedShippingTask $task): int
    {
        $output = PolyOutput::create(
            PolyOutput::FORMAT_ANSI,
            OutputInterface::VERBOSITY_QUIET,
            false,
            new NullOutput()
        );

        return $task->run(new ArrayInput([]), $output);
    }

    public function testPopulateTableShippingCreatesMethods(): void
    {
        $this->assertFalse(TableShippingMethod::get()->exists(), 'No table shipping methods to start with');

        $result = $this->runTask(PopulateTableShippingTask::create());

        $this->assertSame(Command::SUCCESS, $result);
        $this->assertTrue(
            TableShippingMethod::get()->exists(),
            'The task should populate table shipping methods'
        );
    }

    public function testPopulateZonedShippingCreatesMethods(): void
    {
        $this->assertFalse(ZonedShippingMethod::get()->exists(), 'No zoned shipping methods to start with');

        $result = $this->runTask(PopulateZonedShippingTask::create());

        $this->assertSame(Command::SUCCESS, $result);
        $this->assertTrue(
            ZonedShippingMethod::get()->exists(),
            'The task should populate zoned shipping methods'
        );
    }
}
