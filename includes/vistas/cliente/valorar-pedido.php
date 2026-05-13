<?php
require_once __DIR__ . '/../../config.php';

use es\ucm\fdi\aw\Formularios\FormularioValoracion;
use es\ucm\fdi\aw\Pedido\PedidoAppService;
use es\ucm\fdi\aw\Valoracion\ValoracionAppService;

if (!estaLogueado()) {
    header('Location: ' . RUTA_BASE . '/login.php');
    exit();
}

$pedidoId = (int)($_GET['id'] ?? 0);
$pedidoService = new PedidoAppService();
$pedido = $pedidoService->getPedido($pedidoId);

if (!$pedido || (int)$pedido->cliente_id !== (int)$_SESSION['idUsuario']) {
    header('Location: mis-pedidos.php');
    exit();
}

if ($pedido->estado !== 'entregado') {
    header('Location: mis-pedidos.php');
    exit();
}

$valoracionService = new ValoracionAppService();
$valoracionExistente = $valoracionService->getValoracionPedido($pedidoId);
if ($valoracionExistente) {
    header('Location: mis-pedidos.php');
    exit();
}

$form = new FormularioValoracion($pedidoId);
$htmlForm = $form->gestiona();

$tituloPagina = 'Valorar pedido';
$contenidoPrincipal = <<<HTML
<h1>Valorar pedido #{$pedido->numero_pedido}</h1>
<div class="pedido-lista-card">
    <p><strong>Total:</strong> {$pedido->total_con_iva} €</p>
    <p><strong>Estado:</strong> Entregado</p>
</div>
{$htmlForm}
HTML;

require RAIZ_APP . '/includes/vistas/comun/plantilla.php';
