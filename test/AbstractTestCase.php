<?php

namespace mdeboer\DoctrineBehaviour\Test;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\ORM\Exception\MissingMappingDriverImplementation;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use mdeboer\DoctrineBehaviour\Filter\ExpirableFilter;
use mdeboer\DoctrineBehaviour\Filter\SoftDeleteFilter;
use mdeboer\DoctrineBehaviour\Listener\TimestampableListener;
use mdeboer\DoctrineBehaviour\Listener\TranslatableListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

abstract class AbstractTestCase extends TestCase
{
    /**
     * @throws MissingMappingDriverImplementation
     * @throws Exception
     */
    protected function createEntityManager(
        ?array $paths = null,
        ?EventManager $eventManager = null
    ): EntityManagerInterface {
        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: $paths ?? [__DIR__ . '/Fixtures/Entities'],
            isDevMode: true
        );

        // Add filters.
        $config->addFilter('expirable', ExpirableFilter::class);
        $config->addFilter('softdelete', SoftDeleteFilter::class);

        // Create event manager.
        if ($eventManager === null) {
            $eventManager = new EventManager();

            $eventManager->addEventListener(
                [Events::loadClassMetadata],
                new TimestampableListener()
            );

            $eventManager->addEventListener(
                [Events::loadClassMetadata],
                new TranslatableListener()
            );
        }

        // Create connection.
        $connection = DriverManager::getConnection(
            params: [
                'url' => 'sqlite:///:memory:'
            ],
            config: $config,
            eventManager: $eventManager
        );

        // Create entity manager.
        $em = new EntityManager(
            conn: $connection,
            config: $config,
            eventManager: $eventManager
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
            new NullOutput()
        );
    }
}
