<?php

namespace App\Modules\Auth\Enums;

enum DocumentType: string
{
    case CPF = 'CPF';
    case RG = 'RG';
    case PASSPORT = 'PASSPORT';
    case SSN = 'SSN';
}
