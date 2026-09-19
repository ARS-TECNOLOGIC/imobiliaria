<?php

namespace App\Enums;

enum CategoriaArmazenamento: string
{
    case CONTAS_DE_CONSUMO = 'CONTAS_DE_CONSUMO';
    case DOCUMENTOS_ASSINADOS = 'DOCUMENTOS_ASSINADOS';
    case DOCUMENTOS_PARA_ASSINAR = 'DOCUMENTOS_PARA_ASSINAR';
    case LOCADOR = 'LOCADOR';
    case LOCATARIO = 'LOCATARIO';
    case RECIBOS = 'RECIBOS';
    case SEGUROS = 'SEGUROS';
    case REAJUSTES_ANUAIS = 'REAJUSTES_ANUAIS';
    case COMPROVANTES_RENDIMENTOS_ANUAIS = 'COMPROVANTES_RENDIMENTOS_ANUAIS';
    case FIADOR = 'FIADOR';

    public function label(): string
    {
        return match ($this) {
            self::CONTAS_DE_CONSUMO => 'Contas de Consumo',
            self::DOCUMENTOS_ASSINADOS => 'Documentos Assinados',
            self::DOCUMENTOS_PARA_ASSINAR => 'Documentos para Assinar',
            self::LOCADOR => 'Locador',
            self::LOCATARIO => 'Locatário',
            self::RECIBOS => 'Recibos',
            self::SEGUROS => 'Seguros',
            self::REAJUSTES_ANUAIS => 'Reajustes Anuais',
            self::COMPROVANTES_RENDIMENTOS_ANUAIS => 'Comprovantes de Rendimentos Anuais',
            self::FIADOR => 'Fiador',
        };
    }

    public function pasta(): string
    {
        return match ($this) {
            self::CONTAS_DE_CONSUMO => 'contas-de-consumo',
            self::DOCUMENTOS_ASSINADOS => 'documentos-assinados',
            self::DOCUMENTOS_PARA_ASSINAR => 'documentos-para-assinar',
            self::LOCADOR => 'locador',
            self::LOCATARIO => 'locatario',
            self::RECIBOS => 'recibos',
            self::SEGUROS => 'seguros',
            self::REAJUSTES_ANUAIS => 'reajustes-anuais',
            self::COMPROVANTES_RENDIMENTOS_ANUAIS => 'comprovantes-rendimentos-anuais',
            self::FIADOR => 'fiador',
        };
    }
}
