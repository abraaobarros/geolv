<?php

namespace GeoLV\Geocode\Scoring;

use GeoLV\Address;

interface IRelevanceCalculator
{
    function calculate(Address $address): float;
    function getName(): string;
}