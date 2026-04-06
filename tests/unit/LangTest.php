<?php

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class LangTest extends TestCase
{
    protected function setUp(): void
    {
        if (!function_exists('lang_print')) {
            global $ui_lang;
            $ui_lang = 'fr';
            require_once 'lang/lang_main.php';
        }
    }

    public function testLangPrintEncodesAmpersand(): void
    {
        $result = lang_print('Empire & Buildings');
        $this->assertEquals('Empire &amp; Buildings', $result);
    }

    public function testLangPrintEncodesLessThan(): void
    {
        $result = lang_print('Score <100');
        $this->assertEquals('Score &lt;100', $result);
    }

    public function testLangPrintEncodesGreaterThan(): void
    {
        $result = lang_print('Score >100');
        $this->assertEquals('Score &gt;100', $result);
    }

    public function testLangPrintEncodesDoubleQuote(): void
    {
        $result = lang_print('Say "hello"');
        $this->assertEquals('Say &quot;hello&quot;', $result);
    }

    public function testLangPrintEncodesSingleQuote(): void
    {
        $result = lang_print("L'ancien mot de passe");
        $this->assertEquals('L&apos;ancien mot de passe', $result);
    }

    public function testLangPrintConvertsNewlineToBr(): void
    {
        $result = lang_print("Recherches\neffectuées");
        $this->assertEquals('Recherches<br>effectuées', $result);
    }

    public function testLangPrintReturnsEmptyStringForNonHtml(): void
    {
        $result = lang_print('', 'OTHER');
        $this->assertEquals('', $result);
    }

    public function testLangSecureEncodesAllValues(): void
    {
        $input = [
            'KEY1' => 'Empire & Buildings',
            'KEY2' => 'Score <100',
            'KEY3' => 'Say "hello"',
        ];
        $result = lang_secure($input);
        $this->assertEquals('Empire &amp; Buildings', $result['KEY1']);
        $this->assertEquals('Score &lt;100', $result['KEY2']);
        $this->assertEquals('Say &quot;hello&quot;', $result['KEY3']);
    }

    public function testLangSecureConvertsNewlines(): void
    {
        $input = ['KEY' => "Line1\nLine2"];
        $result = lang_secure($input);
        $this->assertEquals('Line1<br>Line2', $result['KEY']);
    }

    /**
     * Loads a lang file in an isolated local scope (lang files assign to $lang without global declaration).
     * require (not require_once) is intentional: the same file may have been pre-loaded by lang_main.php,
     * requiring it again into a local $lang is the only way to get a clean, isolated array per test.
     */
    private function loadProfileLang(string $langCode): array
    {
        $lang = [];
        require "lang/{$langCode}/lang_profile.php"; // NOSONAR
        return $lang;
    }

    #[DataProvider('langProvider')]
    public function testProfileLangKeysForBug501(string $langCode): void
    {
        $lang = $this->loadProfileLang($langCode);

        // PROFILE_PLAYERNAME_XTENSE_INFO must exist and not be empty (pseudo is xtense-managed)
        $this->assertArrayHasKey(
            'PROFILE_PLAYERNAME_XTENSE_INFO',
            $lang,
            "PROFILE_PLAYERNAME_XTENSE_INFO missing in lang/{$langCode}/lang_profile.php"
        );
        $this->assertNotEmpty($lang['PROFILE_PLAYERNAME_XTENSE_INFO']);

        // PROFILE_GAME must not cite the game name (copyright)
        $this->assertArrayHasKey('PROFILE_GAME', $lang);
        $this->assertStringNotContainsStringIgnoringCase(
            'ogame',
            $lang['PROFILE_GAME'],
            "PROFILE_GAME contains 'OGame' in lang/{$langCode}/lang_profile.php (copyright)"
        );
    }

    public static function langProvider(): array
    {
        return [
            ['fr'], ['en'], ['en_US'], ['es'], ['it'], ['pt_BR'],
            ['bs'], ['bs_BA'], ['hr'], ['hr_HR'],
        ];
    }
}
