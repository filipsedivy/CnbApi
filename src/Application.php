<?php declare(strict_types=1);

namespace CnbApi;

use CnbApi\Caching;
use CnbApi\Entity;
use CnbApi\Exceptions;
use CnbApi\Source;
use CnbApi\Utils;
use DateTimeInterface;

class Application
{
    /** @var Source\ISource */
    private $source;

    /** @var Caching\ICaching */
    private $caching;

    public function __construct(Source\ISource $source, Caching\ICaching $caching)
    {
        $this->source = $source;
        $this->caching = $caching;
    }

    public function findRateByCountry(string $country, ?DateTimeInterface $date = null): Entity\Rate
    {
        $entity = $this->getEntity($date);
        $rates = $entity->getRates();
        $country = Utils\Strings::toUpper($country);

        foreach ($rates as $rate) {
            if (Utils\Strings::toUpper($rate->getCountry()->getName()) === $country) {
                return $rate;
            }
        }

        throw new Exceptions\InvalidArgumentException("Country '$country' not found");
    }

    public function findRateByCode(string $code, ?DateTimeInterface $date = null): Entity\Rate
    {
        $entity = $this->getEntity($date);
        $rates = $entity->getRates();

        foreach ($rates as $rate) {
            if ($rate->getCurrency()->getCode() === Utils\Strings::toUpper($code)) {
                return $rate;
            }
        }

        throw new Exceptions\InvalidArgumentException("Code '$code' not found");
    }

    public function getEntity(?DateTimeInterface $date = null): Entity\ExchangeRate
    {
        $date === null && $date = Utils\DateTime::now();

        $entity = $this->caching->load($date);

        if ($entity === null) {
            $className = $this->getSource()->getTranslator();

            /** @var Translator\ITranslator $translator */
            $translator = new $className($this->getSource()->getByDate($date));

            $entity = $translator->getEntity();

            $this->caching->save($date, $entity);
        }

        return $entity;
    }

    public function getSource(): Source\ISource
    {
        return $this->source;
    }
}
