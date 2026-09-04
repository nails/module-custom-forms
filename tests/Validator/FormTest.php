<?php

namespace Tests\Validator;

use Nails\Common\Exception\ValidationException;
use Nails\Common\Service\FormValidation;
use Nails\Common\Validation\Context;
use Nails\CustomForms\Resource;
use Nails\CustomForms\Validator\Form;
use PHPUnit\Framework\TestCase;

class FormTest extends TestCase
{
    /**
     * A validator whose slug uniqueness rules pass, recording the params they were given
     *
     * @param array $aSeen Receives rule => params
     */
    private function validator(?Resource\Form $oForm = null, array &$aSeen = []): Form
    {
        $cRecord = function ($mValue, Context $oContext) use (&$aSeen) {
            $aSeen[] = $oContext->getParams();
            return true;
        };

        return (new Form($oForm))
            ->stubRule(FormValidation::RULE_IS_UNIQUE, $cRecord)
            ->stubRule(FormValidation::RULE_UNIQUE_IF_DIFF, $cRecord);
    }

    private function cleanPost(): array
    {
        return [
            'label'               => 'Contact us',
            'slug'                => 'contact-us',
            'thankyou_page_title' => 'Thanks!',
            'thankyou_page_body'  => '',
        ];
    }

    private function errorsFor(array $aData, ?Resource\Form $oForm = null): array
    {
        try {
            $this->validator($oForm)->run($aData);
            return [];
        } catch (ValidationException $e) {
            return $e->getData();
        }
    }

    // --------------------------------------------------------------------------

    public function test_a_clean_form_passes(): void
    {
        self::assertSame([], $this->errorsFor($this->cleanPost()));
    }

    public function test_the_label_is_required(): void
    {
        $aErrors = $this->errorsFor(array_merge($this->cleanPost(), ['label' => '']));
        self::assertSame(['label'], array_keys($aErrors));
    }

    public function test_a_new_form_checks_its_slug_is_unique(): void
    {
        $aSeen = [];
        $this->validator(null, $aSeen)->run($this->cleanPost());

        self::assertCount(1, $aSeen);
        self::assertSame('slug', $aSeen[0][1]);
        self::assertCount(2, $aSeen[0], 'no exemption for a new form');
    }

    public function test_an_edited_form_is_exempt_from_its_own_slug(): void
    {
        $aSeen = [];
        $this->validator(new Resource\Form(['slug' => 'contact-us']), $aSeen)->run($this->cleanPost());

        self::assertCount(1, $aSeen);
        self::assertSame(['slug', 'contact-us'], array_slice($aSeen[0], 1));
    }

    public function test_a_slug_collision_is_reported(): void
    {
        try {
            (new Form())
                ->stubRule(FormValidation::RULE_IS_UNIQUE, fn() => false)
                ->run($this->cleanPost());
            self::fail('Expected a ValidationException');
        } catch (ValidationException $e) {
            self::assertSame(['slug'], array_keys($e->getData()));
        }
    }

    public function test_the_thank_you_page_needs_a_title_or_a_body(): void
    {
        $aErrors = $this->errorsFor(array_merge($this->cleanPost(), [
            'thankyou_page_title' => '   ',
            'thankyou_page_body'  => '',
        ]));

        self::assertSame(
            [
                'thankyou_page_title' => 'Thank you page title is required if no body is set.',
                'thankyou_page_body'  => 'Thank you page body is required if no title is set.',
            ],
            $aErrors
        );

        self::assertSame([], $this->errorsFor(array_merge($this->cleanPost(), [
            'thankyou_page_title' => '',
            'thankyou_page_body'  => json_encode([['type' => 'text', 'body' => 'Thanks']]),
        ])));
    }
}
