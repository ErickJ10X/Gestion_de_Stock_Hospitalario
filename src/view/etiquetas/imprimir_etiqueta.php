<?php
require_once(__DIR__ . '/../../controller/EtiquetasController.php');
require_once(__DIR__ . '/../../controller/ProductoController.php');

use controller\EtiquetasController;
use controller\ProductoController;

$etiquetasController = new EtiquetasController();
$productoController = new ProductoController();

$idEtiqueta = isset($_GET['id']) ? $_GET['id'] : null;
$etiqueta = null;
$producto = null;

if ($idEtiqueta) {
    $resultado = $etiquetasController->show($idEtiqueta);
    if (!$resultado['error'] && isset($resultado['etiqueta'])) {
        $etiqueta = $resultado['etiqueta'];
        
        // Obtener información del producto
        $resultadoProducto = $productoController->show($etiqueta->getIdProducto());
        if (!$resultadoProducto['error'] && isset($resultadoProducto['producto'])) {
            $producto = $resultadoProducto['producto'];
        }
        
        // Marcar como impresa
        $etiquetasController->marcarComoImpresa($idEtiqueta);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimir Etiqueta</title>
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        
        .print-container {
            width: 100%;
            max-width: 380px;
            margin: 0 auto;
        }
        
        .etiqueta-impresion {
            border: 1px solid #000;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        .etiqueta-impresion.normal {
            border-top: 10px solid #0d6efd;
        }
        
        .etiqueta-impresion.urgente {
            border-top: 10px solid #dc3545;
        }
        
        .etiqueta-header {
            padding: 10px;
            border-bottom: 1px solid #000;
            font-weight: bold;
            background-color: #f0f0f0;
        }
        
        .etiqueta-body {
            padding: 15px;
        }
        
        .etiqueta-body p {
            margin: 5px 0;
            font-size: 14px;
        }
        
        .etiqueta-footer {
            padding: 10px;
            border-top: 1px solid #000;
            font-size: 12px;
            text-align: center;
        }
        
        .btn-container {
            text-align: center;
            margin: 20px 0;
        }
        
        .btn {
            padding: 10px 20px;
            background-color: #0d6efd;
            color: white;
            border: none;
            cursor: pointer;
            text-decoration: none;
            border-radius: 4px;
        }
        
        .btn:hover {
            background-color: #0b5ed7;
        }
    </style>
</head>
<body>
    <div class="print-container">
        <?php if ($etiqueta && $producto): ?>
            <div class="etiqueta-impresion <?php echo strtolower($etiqueta->getPrioridad()); ?>">
                <div class="etiqueta-header">
                    <?php echo $etiqueta->getPrioridad(); ?> - <?php echo $etiqueta->getTipo(); ?>
                </div>
                <div class="etiqueta-body">
                    <p><strong>Producto:</strong> <?php echo $producto->getNombre(); ?></p>
                    <p><strong>Referencia:</strong> <?php echo $producto->getReferencia(); ?></p>
                    <p><strong>Reposición ID:</strong> <?php echo $etiqueta->getIdReposicion(); ?></p>
                    <p><strong>Fecha:</strong> <?php echo date('d/m/Y'); ?></p>
                    <?php if ($etiqueta->getTipo() === 'RFID'): ?>
                        <p><strong>RFID:</strong> <?php echo '10000' . $etiqueta->getIdEtiqueta(); ?></p>
                    <?php endif; ?>
                </div>
                <div class="etiqueta-footer">
                    Hospital Universitario - Sistema Pegasus Medical
                </div>
            </div>
            
            <div class="btn-container no-print">
                <button onclick="window.print()" class="btn">Imprimir Etiqueta</button>
                <a href="index.php" class="btn">Volver a la lista</a>
            </div>
        <?php else: ?>
            <div class="no-print">
                <p>No se encontró la etiqueta especificada</p>
                <div class="btn-container">
                    <a href="index.php" class="btn">Volver a la lista</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Imprimir automáticamente al cargar la página
        window.onload = function() {
            if (<?php echo ($etiqueta && $producto) ? 'true' : 'false'; ?>) {
                window.print();
            }
        };
    </script>
</body>
</html>
