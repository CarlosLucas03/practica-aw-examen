<?php
namespace es\ucm\fdi\aw\Valoracion;

use es\ucm\fdi\aw\Pedido\PedidoDAO;

class ValoracionAppService {
    private $dao;
    private $pedidoDAO;

    public function __construct() {
        $this->dao = new ValoracionDAO();
        $this->pedidoDAO = new PedidoDAO();
    }

    public function crearValoracion($pedidoId, $clienteId, $puntuacion, $comentario) {
        $pedido = $this->pedidoDAO->buscarPorId($pedidoId);
        if (!$pedido) {
            return false;
        }

        if ((int)$pedido->cliente_id !== (int)$clienteId) {
            return false;
        }

        if ($pedido->estado !== 'entregado') {
            return false;
        }

        if ($this->dao->existeValoracionPedido($pedidoId)) {
            return false;
        }

        $puntuacion = (int)$puntuacion;
        if ($puntuacion < 1 || $puntuacion > 5) {
            return false;
        }

        $valoracion = new ValoracionDTO();
        $valoracion->pedido_id = $pedidoId;
        $valoracion->cliente_id = $clienteId;
        $valoracion->puntuacion = $puntuacion;
        $valoracion->comentario = $comentario;

        return $this->dao->crear($valoracion);
    }

    public function getValoracionPedido($pedidoId) {
        return $this->dao->buscarPorPedido($pedidoId);
    }

    public function getValoracionesCliente($clienteId) {
        return $this->dao->listarPorCliente($clienteId);
    }

    public function getTodasValoraciones() {
        return $this->dao->listarTodas();
    }
}
