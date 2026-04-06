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

    #[DataProvider('langProvider')]
    public function testProfilePlayernameXtenseInfoKeyExistsInAllLangs(string $langCode): void
    {
        global $lang;
        $lang = [];
        $filePath = "lang/{$langCode}/lang_profile.php";
        $this->assertFileExists($filePath, "Lang file missing for: {$langCode}");
        require $filePath;
        $this->assertArrayHasKey(
            'PROFILE_PLAYERNAME_XTENSE_INFO',
            $lang,
            "PROFILE_PLAYERNAME_XTENSE_INFO missing in lang/{$langCode}/lang_profile.php"
        );
        $this->assertNotEmpty($lang['PROFILE_PLAYERNAME_XTENSE_INFO']);
    }

    #[DataProvider('langProvider')]
    public function testProfileGameKeyHasNoOGameReferenceinAllLangs(string $langCode): void
    {
        global $lang;
        $lang = [];
        $filePath = "lang/{$langCode}/lang_profile.php";
        $this->assertFileExists($filePath);
        require $filePath;
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
