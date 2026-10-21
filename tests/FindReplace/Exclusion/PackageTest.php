<?php

namespace Tests\FindReplace\Exclusion;

use Nails\CustomForms\FindReplace\Exclusion\Package;
use PHPUnit\Framework\TestCase;

class PackageTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        if (!defined('NAILS_DB_PREFIX')) {
            define('NAILS_DB_PREFIX', 'nails_');
        }
    }

    public function testHidden(): void
    {
        self::assertSame(
            [],
            (new Package())->hidden()
        );
    }

    public function testReadOnly(): void
    {
        self::assertSame(
            [
                NAILS_DB_PREFIX . 'custom_form.form_attributes',
                NAILS_DB_PREFIX . 'custom_form.cta_attributes',
                NAILS_DB_PREFIX . 'custom_form_notification.email',
                NAILS_DB_PREFIX . 'custom_form_notification.condition_value',
            ],
            (new Package())->readOnly()
        );
    }
}
