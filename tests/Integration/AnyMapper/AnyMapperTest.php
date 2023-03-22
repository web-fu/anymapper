<?php

declare(strict_types=1);

namespace WebFu\Tests\Integration\AnyMapper;

use DateTime;
use PHPUnit\Framework\TestCase;
use Vimeo\MysqlEngine\Php8\FakePdo;
use WebFu\AnyMapper\AnyMapper;
use WebFu\AnyMapper\MapperException;
use WebFu\AnyMapper\Strategy\DataCastingStrategy;
use WebFu\AnyMapper\Strategy\DocBlockDetectStrategy;
use WebFu\AnyMapper\Strategy\SQLFetchStrategy;
use WebFu\Tests\Fixture\ChildClass;
use WebFu\Tests\Fixture\EntityWithAnnotation;
use WebFu\Tests\Fixture\GameScoreEntity;

class AnyMapperTest extends TestCase
{
    public function testMapInto(): void
    {
        $class = new ChildClass();

        (new AnyMapper())->map([
            'byConstructor' => 'byConstructor',
            'public' => 'public',
            'bySetter' => 'bySetter',
        ])->into($class)->run();

        $this->assertSame('byConstructor is set by constructor', $class->getByConstructor());
        $this->assertSame('public', $class->public);
        $this->assertSame('bySetter is set by setter', $class->getBySetter());
    }

    public function testMapAs(): void
    {
        $class = (new AnyMapper())->map([
            'byConstructor' => 'byConstructor',
            'public' => 'public',
            'bySetter' => 'bySetter',
        ])
            ->as(ChildClass::class)
            ->run();

        $this->assertInstanceOf(ChildClass::class, $class);

        $this->assertSame('byConstructor is set by constructor', $class->getByConstructor());
        $this->assertSame('public', $class->public);
        $this->assertSame('bySetter is set by setter', $class->getBySetter());
    }

    public function testMapAsFail(): void
    {
        $this->expectException(MapperException::class);
        $this->expectExceptionMessage('Class IDoNotExist does not exist');

        /** @phpstan-ignore-next-line */
        (new AnyMapper())->as('IDoNotExist');
    }

    public function testSerialize(): void
    {
        $class = new class () {
            public string $public = 'public';
            private string $value;

            public function __construct()
            {
                $this->value = 'construct';
            }

            public function getValue(): string
            {
                return $this->value;
            }

            public function getClass(): object
            {
                return new class () {
                    public string $element = 'element';
                };
            }

            /**
             * @return string[]
             */
            public function getArray(): array
            {
                return [
                    'foo',
                    'bar',
                ];
            }
        };

        $serialized = (new AnyMapper())->map($class)->serialize();

        $this->assertEquals([
            'public' => 'public',
            'value' => 'construct',
            'class' => [
                'element' => 'element',
            ],
            'array' => [
                'foo',
                'bar',
            ],
        ], $serialized);
    }

    public function testUsing(): void
    {
        $class = new class () {
            public DateTime $value;
        };

        $source = [
            'value' => '2022-12-01',
        ];

        (new AnyMapper())
            ->map($source)
            ->using(
                (new DataCastingStrategy())->allow('string', DateTime::class)
            )
            ->into($class)
            ->run();

        $this->assertEquals(new DateTime('2022-12-01'), $class->value);
    }

    public function testUseDocBlocks(): void
    {
        /** @var EntityWithAnnotation $class */
        $class = (new AnyMapper())->map([
            'foo' => 1,
        ])->using(new DocBlockDetectStrategy())
            ->as(EntityWithAnnotation::class)
            ->run();

        $this->assertSame(1, $class->getFoo()->getValue());
    }

    public function testSQLFetchStrategy(): void
    {
        $dbConnection = self::createConnection();
        /** @var \PDOStatement $stmt */
        $stmt = $dbConnection->query('SELECT * FROM game_scores');

        $sqlMapper = (new AnyMapper())
            ->using(new SQLFetchStrategy())
            ->as(GameScoreEntity::class);

        while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            assert(is_array($result));

            $entity = $sqlMapper->map($result)->run();

            assert($entity instanceof  GameScoreEntity);

            $this->assertIsInt($entity->getId());
            $this->assertIsString($entity->getName());
            $this->assertIsInt($entity->getScore());
        }
    }

    public static function createConnection(): \PDO
    {
        $dbConnection = new FakePdo('mysql:foo;dbname=test;', '', ' ');

        $dbConnection->query(
            'CREATE TABLE `game_scores` (
                `id` INT(10) NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(16) NOT NULL DEFAULT "",
                `score` INT(10) NOT NULL,
                PRIMARY KEY (`id`)
            ) CHARSET=utf8 ENGINE=InnoDB'
        );
        $dbConnection->query(
            'INSERT INTO `game_scores` (`name`, `score`)
               VALUES ("Matt", 20), ("Matt", 1200),
               ("Matt", 2300), ("Kathleen", 6700),
               ("Will", 6200), ("Will", 4800)'
        );

        return $dbConnection;
    }
}
