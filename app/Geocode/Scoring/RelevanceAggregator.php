<?php

namespace GeoLV\Geocode\Scoring;

use GeoLV\Address;

class RelevanceAggregator implements IRelevanceCalculator
{
    /** @var IRelevanceCalculator[] */
    protected $calculators = [];

    /**
     * RelevanceAggregator constructor.
     * @param IRelevanceCalculator[] $calculators
     */
    public function __construct(array $calculators = [])
    {
        $this->setCalculators($calculators);
    }

    public function calculate(Address $address): float
    {
        $sum = 0;

        foreach ($this->calculators as $calculator) {
            $progress = $calculator->calculate($address);
            $address->{$calculator->getName()} = $progress * 100;

            $sum += $progress;
        }

        $totalRelevance = $sum / count($this->calculators);
        $address->{$this->getName()} = $totalRelevance * 100;

        return $totalRelevance;
    }

    public function setCalculators(array $calculators) {
        foreach ($calculators as $calculator)
            $this->addCalculator($calculator);
    }

    public function addCalculator(IRelevanceCalculator $calculator)
    {
        array_push($this->calculators, $calculator);
        return $this;
    }

    public function getName(): string
    {
        return 'total_relevance';
    }


}