<?php

declare(strict_types=1);

namespace Tests\Unit\Builder;

use Builder\Ast\Ast;
use Builder\Ast\Visitor\ObfuscateFunctionBodyVisitor;
use Builder\Support\Builder;
use Hyperf\Contract\StdoutLoggerInterface;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;

/** @internal */
#[CoversNothing]
final class BuilderPackageTest extends TestCase
{
    public function testBuilderEnablesFirstPartyPhpSourceHardening(): void
    {
        $root = dirname(__DIR__, 3);
        $builder = file_get_contents($root . '/Support/Builder.php');
        $custom = file_get_contents($root . '/Support/Custom.php');

        self::assertIsString($builder);
        self::assertIsString($custom);
        self::assertStringContainsString('stripPhpSources', $builder);
        self::assertStringContainsString("'plugin/'", $builder);
        self::assertStringContainsString("'vendor/zoujingli/'", $builder);
        self::assertStringContainsString('xadmin_obfuscate.php', $builder);
        self::assertStringContainsString('storage/extra/web-dist.zip', $builder);
        self::assertStringContainsString('createFrontendArchive', $builder);
        self::assertStringContainsString('php_strip_whitespace', $custom);
        self::assertStringContainsString('ObfuscateFunctionBodyVisitor', $custom);
    }

    public function testFunctionBodyObfuscationKeepsRuntimeAttributesAndSignatures(): void
    {
        $code = <<<'PHP_CODE'
<?php
namespace Demo;

use Hyperf\HttpServer\Annotation\Controller;

#[Controller]
final class DemoController
{
    public function index(string $name): string
    {
        $message = '设备不存在';
        $total = strlen($message . $name);
        return $message . $total;
    }
}
PHP_CODE;

        $obfuscated = (new Ast())->parse($code, [new ObfuscateFunctionBodyVisitor('demo.php')]);

        self::assertStringContainsString('#[Controller]', $obfuscated);
        self::assertMatchesRegularExpression('/public function index\(string \$name\)\s*:\s*string/', $obfuscated);
        self::assertStringContainsString('\\xadmin_obf_s(', $obfuscated);
        self::assertStringNotContainsString('设备不存在', $obfuscated);
    }

    public function testFunctionBodyObfuscationKeepsStringCastForPureInterpolatedVariable(): void
    {
        $code = <<<'PHP_CODE'
<?php
namespace Demo;

final class DemoStringCast
{
    public function normalize(int $item): string
    {
        return trim("{$item}");
    }
}
PHP_CODE;

        $obfuscated = (new Ast())->parse($code, [new ObfuscateFunctionBodyVisitor('string-cast.php')]);

        self::assertStringContainsString('trim((string) $item)', $obfuscated);
        self::assertStringNotContainsString('trim($item)', $obfuscated);
    }

    public function testObfuscationRuntimeDecodesAndCachesStrings(): void
    {
        $logger = new class extends AbstractLogger implements StdoutLoggerInterface {
            public function log($level, string|\Stringable $message, array $context = []): void {}
        };
        $builder = new Builder(dirname(__DIR__, 3) . '/composer.json', $logger);
        $runtime = $builder->getObfuscationRuntimeCode();

        self::assertStringContainsString('static $cache = [], $tables = [];', $runtime);
        self::assertStringContainsString('strtr($binary', $runtime);

        if (!function_exists('xadmin_obf_s')) {
            eval((string)preg_replace('/^<\?php\s*declare\(strict_types=1\);\s*/', '', $runtime));
        }

        $source = '执行效率';
        $key = 137;
        $encoded = '';
        foreach (str_split($source) as $char) {
            $encoded .= chr(ord($char) ^ $key);
        }
        $encoded = strrev(base64_encode($encoded));

        $decoder = \Closure::fromCallable('xadmin_obf_s');
        self::assertSame($source, $decoder($encoded, $key));
        self::assertSame($source, $decoder($encoded, $key));
    }
}
