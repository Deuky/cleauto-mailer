<?php

namespace App\Enum;

enum FuelType: string
{
    case PETROL = 'petrol';
    case ESSENCE = 'essence';
    case DIESEL = 'diesel';
    case ELECTRIC = 'electric';
    case HYBRID = 'hybrid';
    case GAS = 'gas';
}