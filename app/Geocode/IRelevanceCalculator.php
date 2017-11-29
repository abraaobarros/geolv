<?php

namespace GeoLV\Geocode;

use GeoLV\Address;

interface IRelevanceCalculator
{
    function calculateRelevance(Address $address);
}