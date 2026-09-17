<?php

namespace App\Enums;

enum EstadoCivil: string
{
    case SOLTEIRO = 'SOLTEIRO';
    case CASADO = 'CASADO';
    case DIVORCIADO = 'DIVORCIADO';
    case VIUVO = 'VIUVO';
    case UNIAO_ESTAVEL = 'UNIAO_ESTAVEL';
}
