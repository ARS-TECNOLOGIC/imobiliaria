<?php

namespace App\Enums;

enum TipoCobranca: string
{
    case INCLUSO_NO_CONDOMINIO = 'INCLUSO_NO_CONDOMINIO';
    case INDIVIDUALIZADO_BOLETO = 'INDIVIDUALIZADO_BOLETO';
    case CONTA_SEPARADA = 'CONTA_SEPARADA';
}
