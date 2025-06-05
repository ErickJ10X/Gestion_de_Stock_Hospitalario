<?php

namespace model\enum;

class RolEnum {
    const ADMINISTRADOR = 'Administrador';
    const GESTOR_GENERAL = 'Gestor general';
    const GESTOR_HOSPITAL = 'Gestor de hospital';
    const GESTOR_PLANTA = 'Gestor de planta';
    const USUARIO_BOTIQUIN = 'Usuario de botiquín';
    
    /**
     * Obtiene todos los valores de roles disponibles
     */
    public static function getValues(): array {
        return [
            self::ADMINISTRADOR,
            self::GESTOR_GENERAL,
            self::GESTOR_HOSPITAL,
            self::GESTOR_PLANTA,
            self::USUARIO_BOTIQUIN
        ];
    }
    
    /**
     * Verifica si un valor es un rol válido
     */
    public static function isValid(string $value): bool {
        return in_array($value, self::getValues());
    }
}
