<?php

namespace UserFrosting\Tests\Integration\Seeder;

use Mockery as m;
use Interop\Container\ContainerInterface;
use UserFrosting\UniformResourceLocator\ResourceLocator;
use UserFrosting\Sprinkle\Core\Database\Seeder\Seeder;
use UserFrosting\Tests\TestCase;
use Slim\Container;

class DatabaseTests extends TestCase
{
    /**
     * @var Container $fakeCi
     */
    protected $fakeCi;

    /**
     * Setup our fake ci
     *
     * @return void
     */
    public function setUp()
    {
        // Boot parent TestCase
        parent::setUp();

        // We must create our own CI with a custom locator for theses tests
        $this->fakeCi = new Container;

        // Register services stub
        $serviceProvider = new ServicesProviderStub();
        $serviceProvider->register($this->fakeCi);
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @return Seeder
     */
    public function testSeeder()
    {
        $seeder = new Seeder($this->fakeCi);
        $this->assertInstanceOf(Seeder::class, $seeder);
        return $seeder;
    }

    /**
     * @param  Seeder $seeder
     * @depends testSeeder
     */
    /*public function testgetSeedsForSprinkle(Seeder $seeder)
    {

    }*/

    /**
     * @param  Seeder $seeder
     * @depends testSeeder
     */
    public function testgetSeeds(Seeder $seeder)
    {
        $seeds = $seeder->getSeeds();
        $this->assertInternalType('array', $seeds);
        $this->assertCount(3, $seeds);
        $this->assertEquals([
            [
                'name' => 'Test/Seed',
                'class' => '\\UserFrosting\\Sprinkle\\Core\\Database\\Seeds\\Test\\Seed',
                'sprinkle' => 'Core'
            ],
            [
                'name' => 'Seed2',
                'class' => '\\UserFrosting\\Sprinkle\\Core\\Database\\Seeds\\Seed2',
                'sprinkle' => 'Core'
            ],
            [
                'name' => 'Seed1',
                'class' => '\\UserFrosting\\Sprinkle\\Core\\Database\\Seeds\\Seed1',
                'sprinkle' => 'Core'
            ]
        ], $seeds);
    }

    /**
     * @param  Seeder $seeder
     * @depends testSeeder
     */
    /*public function testGetSeed(Seeder $seeder)
    {

    }*/

    /**
     * @param  Seeder $seeder
     * @depends testSeeder
     */
    /*public function testGetSeedClass(Seeder $seeder)
    {

    }*/

    /**
     * @param  Seeder $seeder
     * @depends testSeeder
     */
    public function testExecuteSeed(Seeder $seeder)
    {
        // Get a fake seed
        $seed = m::mock('\UserFrosting\Sprinkle\Core\Database\Seeder\BaseSeed');
        $seed->shouldReceive('run');

        $seeder->executeSeed($seed);
    }
}

/**
 * ServicesProviderStub
 */
class ServicesProviderStub
{
    /**
     * @param ContainerInterface $container A DI container implementing ArrayAccess and container-interop.
     */
    public function register(ContainerInterface $container)
    {
        /**
         * @return \UserFrosting\UniformResourceLocator\ResourceLocator
         */
        $container['locator'] = function ($c) {
            $locator = new ResourceLocator(\UserFrosting\SPRINKLES_DIR);
            $locator->registerStream('seeds', '', 'Seeder/Seeds/');
            $locator->registerLocation('Core', 'core/tests/Integration/');
            return $locator;
        };
    }
}