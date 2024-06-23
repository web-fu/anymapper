<?php

declare(strict_types=1);

/**
 * This file is part of web-fu/anymapper
 *
 * @copyright Web-Fu <info@web-fu.it>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebFu\Tests\Integration\AnyMapper;

use DateTime;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Vimeo\MysqlEngine\Php8\FakePdo;
use WebFu\AnyMapper\AnyMapper;
use WebFu\AnyMapper\MapperException;
use WebFu\AnyMapper\Strategy\AllowedCastingStrategy;
use WebFu\AnyMapper\Strategy\AutodetectStrategy;
use WebFu\AnyMapper\Strategy\CallbackCastingStrategy;
use WebFu\AnyMapper\Strategy\DocBlockDetectStrategy;
use WebFu\AnyMapper\Strategy\SQLFetchStrategy;
use WebFu\Tests\Fixtures\BackedEnum;
use WebFu\Tests\Fixtures\ChildClass;
use WebFu\Tests\Fixtures\ClassWithEnumParameters;
use WebFu\Tests\Fixtures\EntityWithAnnotation;
use WebFu\Tests\Fixtures\Foo;
use WebFu\Tests\Fixtures\GameScoreEntity;

/**
 * @coversDefaultClass \WebFu\AnyMapper\AnyMapper
 */
class AnyMapperTest extends TestCase
{
    /**
     * @covers ::map
     * @covers ::into
     */
    public function testMapInto(): void
    {
        $class = new ChildClass();

        (new AnyMapper())
            ->map([
                'byConstructor' => 'byConstructor',
                'public'        => 'public',
                'bySetter'      => 'bySetter',
            ])
            ->into($class)->run();

        $this->assertSame('byConstructor is set by constructor', $class->getByConstructor());
        $this->assertSame('public', $class->public);
        $this->assertSame('bySetter is set by setter', $class->getBySetter());
    }

    /**
     * @covers ::map
     * @covers ::as
     */
    public function testMapAs(): void
    {
        $class = (new AnyMapper())
            ->map([
                'byConstructor' => 'byConstructor',
                'public'        => 'public',
                'bySetter'      => 'bySetter',
            ])
            ->as(ChildClass::class)
            ->run();

        $this->assertInstanceOf(ChildClass::class, $class);

        $this->assertSame('byConstructor is set by constructor', $class->getByConstructor());
        $this->assertSame('public', $class->public);
        $this->assertSame('bySetter is set by setter', $class->getBySetter());
    }

    /**
     * @covers ::map
     * @covers ::into
     */
    public function testEnum(): void
    {
        $class = new ClassWithEnumParameters();

        (new AnyMapper())
            ->map([
                'backedEnum' => 1,
            ])
            ->using(new AutodetectStrategy())
            ->into($class)
            ->run();

        $this->assertSame(BackedEnum::ONE, $class->backedEnum);
    }

    /**
     * @covers ::map
     * @covers ::as
     */
    public function testMapAsFail(): void
    {
        $this->expectException(MapperException::class);
        $this->expectExceptionMessage('Class IDoNotExist does not exist');

        /* @phpstan-ignore-next-line */
        (new AnyMapper())->as('IDoNotExist');
    }

    /**
     * @covers ::map
     * @covers ::serialize
     */
    public function testSerialize(): void
    {
        $class = new class() {
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
                return new class() {
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

        $serialized = (new AnyMapper())
            ->map($class)
            ->serialize();

        $this->assertEquals([
            'public' => 'public',
            'value'  => 'construct',
            'class'  => [
                'element' => 'element',
            ],
            'array' => [
                'foo',
                'bar',
            ],
        ], $serialized);
    }

    /**
     * @covers ::map
     * @covers ::using
     * @covers ::into
     */
    public function testUsing(): void
    {
        $class = new class() {
            public DateTime $value;
        };

        $source = [
            'value' => '2022-12-01',
        ];

        (new AnyMapper())
            ->map($source)
            ->using(
                (new AllowedCastingStrategy())->allow('string', DateTime::class)
            )
            ->into($class)
            ->run();

        $this->assertEquals(new DateTime('2022-12-01'), $class->value);
    }

    /**
     * @covers ::map
     * @covers ::using
     * @covers ::as
     */
    public function testDocBlockStrategy(): void
    {
        /** @var EntityWithAnnotation $class */
        $class = (new AnyMapper())
            ->map([
                'foo' => 1,
            ])
            ->using(new DocBlockDetectStrategy())
            ->as(EntityWithAnnotation::class)
            ->run();

        $this->assertInstanceOf(Foo::class, $class->getFoo());
        $this->assertSame(1, $class->getFoo()->getValue());
    }

    /**
     * @covers ::map
     * @covers ::using
     * @covers ::as
     */
    public function testSQLFetchStrategy(): void
    {
        $dbConnection = self::createConnection();
        /** @var PDOStatement $stmt */
        $stmt = $dbConnection->query('SELECT * FROM game_scores');

        $sqlMapper = (new AnyMapper())
            ->using(new SQLFetchStrategy())
            ->as(GameScoreEntity::class);

        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            assert(is_array($result));

            $entity = $sqlMapper->map($result)->run();

            assert($entity instanceof GameScoreEntity);

            $this->assertIsInt($entity->getId());
            $this->assertIsString($entity->getName());
            $this->assertIsInt($entity->getScore());
        }
    }

    /**
     * @covers ::map
     * @covers ::using
     * @covers ::into
     */
    public function testCallbackCastingStrategy(): void
    {
        $class = new class() {
            public int $value;
        };

        (new AnyMapper())
            ->map([
                'value' => true,
            ])
            ->using(
                (new CallbackCastingStrategy())
                    ->addMethod('bool', 'int', static fn (bool $value): int => (int) $value)
            )
            ->into($class)
            ->run();

        $this->assertSame(1, $class->value);
    }

    public static function createConnection(): PDO
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
