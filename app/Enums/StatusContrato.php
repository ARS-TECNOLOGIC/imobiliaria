<?php

namespace App\Enums;

enum StatusContrato: string
{
    case ATIVO = 'ATIVO';
    case ENCERRADO = 'ENCERRADO';
    case SUSPENSO = 'SUSPENSO';
}
