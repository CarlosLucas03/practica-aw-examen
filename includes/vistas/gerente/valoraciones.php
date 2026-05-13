<?php
require_once __DIR__ . '/../../config.php';

use es\ucm\fdi\aw\Valoracion\ValoracionAppService;

if (!esAdmin()) {
    header('Location: ' . RUTA_BASE . '/login.php');
    exit();
}

$service = new ValoracionAppService();
$valoraciones = $service->getTodasValoraciones();

$tituloPagina = 'Valoraciones';

if (empty($valoraciones)) {
    $contenidoPrincipal = <<<HTML
    <h1>Valoraciones</h1>
    <p>No hay valoraciones todavía.</p>
HTML;
} else {
    $html = '';
    foreach ($valoraciones as $v) {
        $estrellas = str_repeat('⭐', $v->puntuacion);
        $fecha = date('d/m/Y H:i', strtotime($v->fecha_creacion));
        $comentario = htmlspecialchars($v->comentario ?? '', ENT_QUOTES, 'UTF-8');

        $html .= <<<HTML
        <div class="pedido-lista-card">
            <div class="flex-between">
                <div>
                    <strong>Pedido #{$v->numero_pedido}</strong>
                    <p>Cliente: {$v->nombre_cliente}</p>
                </div>
                <div class="text-right">
                    <strong>{$estrellas}</strong>
                    <div class="icon-small color-gray">{$fecha}</div>
                </div>
            </div>
            <p>{$comentario}</p>
        </div>
HTML;
    }

    $contenidoPrincipal = <<<HTML
    <h1>Valoraciones</h1>
    {$html}
HTML;
}

require RAIZ_APP . '/includes/vistas/comun/plantilla.php';
