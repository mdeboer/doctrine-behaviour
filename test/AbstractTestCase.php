<?php

declare(strict_types=1);

namespace mdeboer\DoctrineBehaviour\Test;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use mdeboer\DoctrineBehaviour\Filter\ExpirableFilter;
use mdeboer\DoctrineBehaviour\Filter\SoftDeleteFilter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

abstract class AbstractTestCase extends TestCase
{
    /**
     * Create entity manager.
     *
     * @param string[]                          $paths
     * @param array{0: array|string, 1: object} $eventListeners
     *
     * @return EntityManagerInterface
     */
    protected function createEntityManager(
        ?array $paths = null,
        array $eventListeners = [],
    ): EntityManagerInterface {
        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: $paths ?? [__DIR__ . '/Fixture/Entity'],
            isDevMode: true,
        );

        // Add filters.
        $config->addFilter('expirable', ExpirableFilter::class);
        $config->addFilter('softdelete', SoftDeleteFilter::class);

        // Register event listeners.
        $eventManager = new EventManager();

        foreach ($eventListeners as $eventListener) {
            $eventManager->addEventListener(
                (array) $eventListener[0],
                $eventListener[1],
            );
        }

        // Create connection.
        $connection = DriverManager::getConnection(
            params: [
                'url' => 'sqlite:///:memory:',
            ],
            config: $config,
            eventManager: $eventManager,
        );

        // Create entity manager.
        $em = new EntityManager(
            conn: $connection,
            config: $config,
            eventManager: $eventManager,
        );

        // Create schema.
        $this->createSchema($em);

        return $em;
    }

    protected function createSchema(EntityManagerInterface $em): void
    {
        $app = ConsoleRunner::createApplication(new SingleManagerProvider($em));

        $app->get('orm:schema-tool:create')->run(
            new StringInput(''),
            new NullOutput(),
        );
    }
}
