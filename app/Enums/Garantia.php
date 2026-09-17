<?php

namespace App\Enums;

enum Garantia: string
{
    case FIADOR = 'FIADOR';
    case SEGURO_FIANCA = 'SEGURO_FIANCA';
    case TITULO_CAPITALIZACAO = 'TITULO_CAPITALIZACAO';
    case CAUCAO = 'CAUCAO';
    case SEM_GARANTIA = 'SEM_GARANTIA';
}
