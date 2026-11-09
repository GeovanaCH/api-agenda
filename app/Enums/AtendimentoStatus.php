<?php

namespace App\Enums;

enum AtendimentoStatus: string
{
    case Agendado = 'agendado';
    case Concluido = 'concluido';
    case Cancelado = 'cancelado';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
