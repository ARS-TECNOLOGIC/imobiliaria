<?php

namespace App\Enums;

enum ServicoCondominio: string
{
    case AGUA = 'AGUA';
    case GAS = 'GAS';
    case LUZ = 'LUZ';
    case INTERNET = 'INTERNET';
    case PORTARIA = 'PORTARIA';
    case ACADEMIA = 'ACADEMIA';
    case OUTRO = 'OUTRO';
}
