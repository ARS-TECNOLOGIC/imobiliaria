<?php

namespace App\Enums;

enum TipoCampoHistorico: string
{
    case ALUGUEL = 'ALUGUEL';
    case IPTU = 'IPTU';
    case CONDOMINIO = 'CONDOMINIO';
    case SEGURO = 'SEGURO';
}
