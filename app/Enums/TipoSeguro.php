<?php

namespace App\Enums;

enum TipoSeguro: string
{
    case SEGURO_INCENDIO = 'SEGURO_INCENDIO';
    case FIANCA_LOCATICA = 'FIANCA_LOCATICA';
    case TITULO_CAPITALIZACAO = 'TITULO_CAPITALIZACAO';
}
