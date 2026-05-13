<?php
namespace es\ucm\fdi\aw\Valoracion;

class ValoracionDTO {
    public $id;
    public $pedido_id;
    public $cliente_id;
    public $puntuacion;
    public $comentario;
    public $fecha_creacion;

    // Campos extra para mostrar en vistas
    public $numero_pedido;
    public $nombre_cliente;
}
