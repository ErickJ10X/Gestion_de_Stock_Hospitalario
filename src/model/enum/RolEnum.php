<?php

namespace model\enum;

class RolEnum {
    public const ADMINISTRADOR = 1;
    public const GESTOR_PLANTA = 2;
    public const USUARIO_BOTIQUIN = 3;

    public static function getValues(): array {
        return [
            'Administrador',
            'Gestor de planta',
            'Usuario de botiquín'
        ];
    }
    
    public static function getKeyValues(): array {
        return [
            self::ADMINISTRADOR => 'Administrador',
            self::GESTOR_PLANTA => 'Gestor de planta',
            self::USUARIO_BOTIQUIN => 'Usuario de botiquín'
        ];
    }

    public static function isValid($value): bool {
        if (is_numeric($value)) {
            return in_array((int)$value, [self::ADMINISTRADOR, self::GESTOR_PLANTA, self::USUARIO_BOTIQUIN]);
        } else {
            return in_array($value, self::getValues());
        }
    }

    public static function getValue($key): string {
        $values = self::getKeyValues();
        return $values[$key] ?? 'Desconocido';
    }
    
    public static function getKey($value): ?int {
        $keyValues = self::getKeyValues();
        $keys = array_flip($keyValues);
        return $keys[$value] ?? null;
    }
}
