<?php

namespace App\Enums;

enum StatusPagamento: string
{
    case PENDENTE = 'PENDENTE';
    case PAGO = 'PAGO';
    case PAGO_COM_ATRASO = 'PAGO_COM_ATRASO';
    case ATRASADO = 'ATRASADO';
    case CANCELADO = 'CANCELADO';
}
