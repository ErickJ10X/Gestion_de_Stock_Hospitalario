<?php

namespace App\model\enum;

enum RolEnum: string
{
    case ADMINISTRADOR = 'Administrador';
    case GESTOR_GENERAL = 'Gestor general';
    case GESTOR_HOSPITAL = 'Gestor de hospital';
    case GESTOR_PLANTA = 'Gestor de planta';
    case USUARIO_BOTIQUIN = 'Usuario de botiquín';

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function isValid(string $value): bool
    {
        return in_array($value, self::getValues());
    }
}
