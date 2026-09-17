<?php

namespace App\Enums;

enum StatusRepasse: string
{
    case PENDENTE = 'PENDENTE';
    case EFETUADO = 'EFETUADO';
    case CANCELADO = 'CANCELADO';
}
