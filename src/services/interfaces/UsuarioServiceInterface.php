<?php

namespace services\interfaces;

use Models\Usuario;

interface UsuarioServiceInterface {
    /**
     * Obtiene todos los usuarios
     * @return Usuario[] Lista de usuarios
     */
    public function getAllUsuarios(): array;
    
    /**
     * Obtiene un usuario por su ID
     * @param int $id ID del usuario
     * @return Usuario|null Usuario encontrado o null si no existe
     */
    public function getUsuarioById(int $id): ?Usuario;
    
    /**
     * Obtiene un usuario por su email
     * @param string $email Email del usuario
     * @return Usuario|null Usuario encontrado o null si no existe
     */
    public function getUsuarioByEmail(string $email): ?Usuario;
    
    /**
     * Obtiene todos los usuarios con un rol específico
     * @param string $rol Rol de usuario
     * @return Usuario[] Lista de usuarios con el rol especificado
     */
    public function getUsuariosByRol(string $rol): array;
    
    /**
     * Obtiene todos los usuarios activos
     * @return Usuario[] Lista de usuarios activos
     */
    public function getActiveUsuarios(): array;
    
    /**
     * Obtiene todos los usuarios asignados a una ubicación específica
     * @param string $tipoUbicacion Tipo de ubicación (Hospital, Planta, Botiquin)
     * @param int $idUbicacion ID de la ubicación
     * @return Usuario[] Lista de usuarios asignados a la ubicación
     */
    public function getUsuariosByUbicacion(string $tipoUbicacion, int $idUbicacion): array;
    
    /**
     * Autentica un usuario con su email y contraseña
     * @param string $email Email del usuario
     * @param string $contrasena Contraseña sin encriptar
     * @return Usuario|null Usuario autenticado o null si las credenciales son inválidas
     */
    public function autenticar(string $email, string $contrasena): ?Usuario;
    
    /**
     * Crea un nuevo usuario
     * @param Usuario $usuario Usuario a crear
     * @param string $contrasena Contraseña sin encriptar
     * @param array $ubicaciones Arreglo con las ubicaciones asignadas al usuario [['tipo' => 'Hospital', 'id' => 1], ...]
     * @return Usuario Usuario creado
     */
    public function createUsuario(Usuario $usuario, string $contrasena, array $ubicaciones = []): Usuario;
    
    /**
     * Actualiza un usuario existente
     * @param Usuario $usuario Usuario con los datos actualizados
     * @param string|null $contrasena Nueva contraseña sin encriptar, o null para no cambiarla
     * @return bool True si se actualizó correctamente
     */
    public function updateUsuario(Usuario $usuario, ?string $contrasena = null): bool;
    
    /**
     * Actualiza las ubicaciones de un usuario
     * @param int $idUsuario ID del usuario
     * @param array $ubicaciones Arreglo con las nuevas ubicaciones [['tipo' => 'Hospital', 'id' => 1], ...]
     * @return bool True si se actualizaron correctamente
     */
    public function updateUbicaciones(int $idUsuario, array $ubicaciones): bool;
    
    /**
     * Elimina un usuario por su ID
     * @param int $id ID del usuario a eliminar
     * @return bool True si se eliminó correctamente
     */
    public function deleteUsuario(int $id): bool;
    
    /**
     * Activa un usuario
     * @param int $id ID del usuario
     * @return bool True si se activó correctamente
     */
    public function activateUsuario(int $id): bool;
    
    /**
     * Desactiva un usuario
     * @param int $id ID del usuario
     * @return bool True si se desactivó correctamente
     */
    public function deactivateUsuario(int $id): bool;
    
    /**
     * Cambia la contraseña de un usuario
     * @param int $id ID del usuario
     * @param string $contrasena Nueva contraseña sin encriptar
     * @return bool True si se cambió correctamente
     */
    public function cambiarContrasena(int $id, string $contrasena): bool;
}
