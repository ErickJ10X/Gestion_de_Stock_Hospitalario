<?php
session_start();

require_once(__DIR__ . '/../../controller/AlmacenesController.php');
require_once(__DIR__ . '/../../util/Session.php');
require_once(__DIR__ . '/../../util/AuthGuard.php');

use controller\AlmacenesController;
use util\Session;
use util\AuthGuard;

$almacenesController = new AlmacenesController();
$session = new Session();
$authGuard = new AuthGuard();

// Verificar permisos
$authGuard->requireHospitalGestor();

// Determinar la acción a realizar
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'crear_almacen':
        // Validar datos del formulario
        if (empty($_POST['planta_id']) || empty($_POST['tipo']) || empty($_POST['id_hospital'])) {
            $session->setMessage('error', 'Todos los campos son obligatorios');
            header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
            exit;
        }

        // Procesar la creación
        $planta_id = filter_input(INPUT_POST, 'planta_id', FILTER_VALIDATE_INT);
        $tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_STRING);
        $id_hospital = filter_input(INPUT_POST, 'id_hospital', FILTER_VALIDATE_INT);

        $resultado = $almacenesController->store($planta_id, $tipo, $id_hospital);

        if (!$resultado['error']) {
            $session->setMessage('success', $resultado['mensaje']);
        } else {
            $session->setMessage('error', $resultado['mensaje']);
        }

        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
        break;

    case 'editar_almacen':
        // Validar datos del formulario
        if (empty($_POST['id']) || empty($_POST['planta_id']) || empty($_POST['tipo']) || empty($_POST['id_hospital'])) {
            $session->setMessage('error', 'Todos los campos son obligatorios');
            header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
            exit;
        }

        // Procesar la actualización
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $planta_id = filter_input(INPUT_POST, 'planta_id', FILTER_VALIDATE_INT);
        $tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_STRING);
        $id_hospital = filter_input(INPUT_POST, 'id_hospital', FILTER_VALIDATE_INT);

        $resultado = $almacenesController->update($id, $planta_id, $tipo, $id_hospital);

        if (!$resultado['error']) {
            $session->setMessage('success', $resultado['mensaje']);
        } else {
            $session->setMessage('error', $resultado['mensaje']);
        }

        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
        break;

    case 'eliminar_almacen':
        // Validar datos del formulario
        if (empty($_POST['id'])) {
            $session->setMessage('error', 'ID del almacén no proporcionado');
            header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
            exit;
        }

        // Procesar la eliminación
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $resultado = $almacenesController->destroy($id);

        if (!$resultado['error']) {
            $session->setMessage('success', $resultado['mensaje']);
        } else {
            $session->setMessage('error', $resultado['mensaje']);
        }

        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
        break;

    default:
        $session->setMessage('error', 'Acción no válida');
        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
        break;
}
