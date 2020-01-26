<?php declare(strict_types=1);

namespace Tests\Cases;

use CnbApi;
use Tester\Assert;
use Tester\TestCase;
use Tests\Engine;

require_once __DIR__ . '/../bootstrap.php';

class ApplicationTest extends TestCase
{
    private $application;

    public function __construct()
    {
        $source = new Engine\Source\FileSource;
        $cache = new CnbApi\Caching\NullCaching;

        $this->application = new CnbApi\Application($source, $cache);
    }

    public function testGetEntity(): void
    {
        Assert::type(CnbApi\Entity\ExchangeRate::class, $this->application->getEntity());
    }

    public function testRateByCountry(): void
    {
        Assert::type(CnbApi\Entity\Rate::class, $this->application->findRateByCountry('USA'));

        Assert::exception(function () {
            $this->application->findRateByCountry('NotExists');
        }, CnbApi\Exceptions\InvalidArgumentException::class, "Country 'NOTEXISTS' not found");
    }

    public function testRateByCode(): void
    {
        Assert::type(CnbApi\Entity\Rate::class, $this->application->findRateByCode('CZK'));

        Assert::exception(function () {
            $this->application->findRateByCode('TEST');
        }, CnbApi\Exceptions\InvalidArgumentException::class, "Code 'TEST' not found");
    }
}

(new ApplicationTest)->run();
