<?php

namespace Tests\FindReplace\Resolver;

use Nails\CustomForms\FindReplace\Resolver\CustomForm;
use Nails\FindReplace\Factory\Hit;
use Nails\FindReplace\Factory\Matched;
use PHPUnit\Framework\TestCase;

class CustomFormTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        if (!defined('NAILS_DB_PREFIX')) {
            define('NAILS_DB_PREFIX', 'nails_');
        }
    }

    public function testTables(): void
    {
        self::assertSame(
            [NAILS_DB_PREFIX . 'custom_form', NAILS_DB_PREFIX . 'custom_form_notification'],
            (new CustomForm())->tables()
        );
    }

    public function testResolvesFormsByLabel(): void
    {
        $table = NAILS_DB_PREFIX . 'custom_form';
        $hits = $this->resolver()->resolve($table, [
            $this->match($table, '3', 'header'),
            $this->match($table, '3', 'footer'),
            $this->match($table, '4', 'header'),
        ]);

        self::assertCount(2, $hits);
        self::assertSame('Custom Form', $this->hit($hits, '3')->getEntityLabel());
        self::assertSame('3', $this->hit($hits, '3')->getEntityId());
        self::assertSame('Contact us', $this->hit($hits, '3')->getTitle());
        self::assertSame('Edit', $this->hit($hits, '3')->getActions()[0]->getLabel());
        self::assertSame('https://example.com/admin/forms/forms/edit/3', $this->hit($hits, '3')->getActions()[0]->getUrl());
        self::assertNull($this->hit($hits, '4')->getTitle());
    }

    public function testResolvesNotificationsThroughTheirForm(): void
    {
        $table = NAILS_DB_PREFIX . 'custom_form_notification';
        $hits = $this->resolver()->resolve($table, [
            $this->match($table, '10', 'email'),
            $this->match($table, '11', 'email'),
            $this->match($table, '12', 'email'),
        ]);

        self::assertSame(['10', '11'], array_map('strval', array_keys($hits)));

        self::assertSame('Custom Form Notification', $this->hit($hits, '10')->getEntityLabel());
        self::assertSame('10', $this->hit($hits, '10')->getEntityId());
        self::assertSame('Contact us', $this->hit($hits, '10')->getTitle());
        self::assertSame('Form #4', $this->hit($hits, '11')->getTitle());

        $actions = $this->hit($hits, '10')->getActions();
        self::assertCount(1, $actions);
        self::assertSame('Edit form', $actions[0]->getLabel());
        self::assertSame('https://example.com/admin/forms/forms/edit/3', $actions[0]->getUrl());
    }

    public function testNoMatchesResolvesNothing(): void
    {
        self::assertSame([], $this->resolver()->resolve(NAILS_DB_PREFIX . 'custom_form', []));
    }

    /**
     * Numeric-string keys become integers at runtime, so look hits up by either.
     *
     * @param array<array-key, Hit> $hits
     */
    private function hit(array $hits, string $id): Hit
    {
        self::assertArrayHasKey($id, $hits);

        return $hits[$id];
    }

    private function match(string $table, string $id, string $column): Matched
    {
        return new Matched($table, $column, 'html', 'example', $id, 'example');
    }

    private function resolver(): CustomForm
    {
        return new class ([
            NAILS_DB_PREFIX . 'custom_form' => [
                (object) ['id' => 3, 'label' => 'Contact us'],
                (object) ['id' => 4, 'label' => ''],
            ],
            NAILS_DB_PREFIX . 'custom_form_notification' => [
                (object) ['id' => 10, 'form_id' => 3],
                (object) ['id' => 11, 'form_id' => 4],
                (object) ['id' => 12, 'form_id' => 99],
            ],
        ]) extends CustomForm {
            /**
             * @param array<string, list<\stdClass>> $stub Rows keyed by table.
             */
            public function __construct(private readonly array $stub)
            {
            }

            protected function rows(string $table, string $columns, array $ids): array
            {
                return array_values(array_filter(
                    $this->stub[$table] ?? [],
                    fn(\stdClass $row): bool => in_array((string) $row->id, $ids, true)
                ));
            }

            protected function editUrl(): string
            {
                return 'https://example.com/admin/forms/forms/edit';
            }
        };
    }
}
