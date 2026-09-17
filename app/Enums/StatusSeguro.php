<?php

namespace App\Enums;

enum StatusSeguro: string
{
    case ATIVO = 'ATIVO';
    case A_RENOVAR = 'A_RENOVAR';
    case EXPIRADO = 'EXPIRADO';
    case CANCELADO = 'CANCELADO';
}
