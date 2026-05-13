<?php
namespace es\ucm\fdi\aw\Valoracion;

use es\ucm\fdi\aw\Aplicacion;

class ValoracionDAO {
    public function crear(ValoracionDTO $v) {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $stmt = $conn->prepare(
            "INSERT INTO Valoraciones (pedido_id, cliente_id, puntuacion, comentario) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param('iiis', $v->pedido_id, $v->cliente_id, $v->puntuacion, $v->comentario);

        if ($stmt->execute()) {
            $v->id = $conn->insert_id;
            $stmt->close();
            return $v;
        }

        $stmt->close();
        return false;
    }

    public function buscarPorPedido($pedidoId) {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $stmt = $conn->prepare(
            "SELECT v.*, p.numero_pedido, u.nombre AS nombre_cliente
             FROM Valoraciones v
             JOIN Pedidos p ON v.pedido_id = p.id
             JOIN Usuarios u ON v.cliente_id = u.id
             WHERE v.pedido_id = ?"
        );
        $stmt->bind_param('i', $pedidoId);
        $stmt->execute();
        $result = $stmt->get_result();
        $fila = $result->fetch_assoc();
        $stmt->close();

        if (!$fila) {
            return null;
        }

        return $this->filaADto($fila);
    }

    public function existeValoracionPedido($pedidoId) {
        return $this->buscarPorPedido($pedidoId) !== null;
    }

    public function listarTodas() {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $stmt = $conn->prepare(
            "SELECT v.*, p.numero_pedido, u.nombre AS nombre_cliente
             FROM Valoraciones v
             JOIN Pedidos p ON v.pedido_id = p.id
             JOIN Usuarios u ON v.cliente_id = u.id
             ORDER BY v.fecha_creacion DESC"
        );
        $stmt->execute();
        $result = $stmt->get_result();

        $valoraciones = [];
        while ($fila = $result->fetch_assoc()) {
            $valoraciones[] = $this->filaADto($fila);
        }

        $stmt->close();
        return $valoraciones;
    }

    public function listarPorCliente($clienteId) {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $stmt = $conn->prepare(
            "SELECT v.*, p.numero_pedido, u.nombre AS nombre_cliente
             FROM Valoraciones v
             JOIN Pedidos p ON v.pedido_id = p.id
             JOIN Usuarios u ON v.cliente_id = u.id
             WHERE v.cliente_id = ?
             ORDER BY v.fecha_creacion DESC"
        );
        $stmt->bind_param('i', $clienteId);
        $stmt->execute();
        $result = $stmt->get_result();

        $valoraciones = [];
        while ($fila = $result->fetch_assoc()) {
            $valoraciones[] = $this->filaADto($fila);
        }

        $stmt->close();
        return $valoraciones;
    }

    private function filaADto(array $fila) {
        $v = new ValoracionDTO();
        $v->id = (int)$fila['id'];
        $v->pedido_id = (int)$fila['pedido_id'];
        $v->cliente_id = (int)$fila['cliente_id'];
        $v->puntuacion = (int)$fila['puntuacion'];
        $v->comentario = $fila['comentario'];
        $v->fecha_creacion = $fila['fecha_creacion'];
        $v->numero_pedido = $fila['numero_pedido'] ?? null;
        $v->nombre_cliente = $fila['nombre_cliente'] ?? '';
        return $v;
    }
}
