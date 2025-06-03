<?php

namespace src\enum;

class RolEnum
{
    public const ADMINISTRADOR = 'Administrador';
    public const GESTOR_GENERAL = 'Gestor general';
    public const GESTOR_HOSPITAL = 'Gestor de hospital';
    public const GESTOR_PLANTA = 'Gestor de planta';
    public const USUARIO_BOTIQUIN = 'Usuario de botiquín';

    public static function getValues(): array
    {
        return [
            self::ADMINISTRADOR,
            self::GESTOR_GENERAL,
            self::GESTOR_HOSPITAL,
            self::GESTOR_PLANTA,
            self::USUARIO_BOTIQUIN,
        ];
    }

    public static function isValid(string $value): bool
    {
        return in_array($value, self::getValues(), true);
    }
}