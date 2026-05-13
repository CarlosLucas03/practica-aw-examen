<?php
namespace es\ucm\fdi\aw\Formularios;

use es\ucm\fdi\aw\Valoracion\ValoracionAppService;

class FormularioValoracion extends Formulario {
    private $pedidoId;

    public function __construct($pedidoId) {
        parent::__construct('formValoracion', [
            'urlRedireccion' => 'mis-pedidos.php'
        ]);
        $this->pedidoId = $pedidoId;
    }

    protected function generaCamposFormulario(&$datos) {
        $comentario = htmlspecialchars($datos['comentario'] ?? '', ENT_QUOTES, 'UTF-8');

        $html = <<<HTML
        <input type="hidden" name="pedido_id" value="{$this->pedidoId}">
        <div class="campo">
            <label for="puntuacion">Puntuación:</label>
            <select id="puntuacion" name="puntuacion" required>
                <option value="">Selecciona una puntuación</option>
                <option value="1">⭐ 1</option>
                <option value="2">⭐⭐ 2</option>
                <option value="3">⭐⭐⭐ 3</option>
                <option value="4">⭐⭐⭐⭐ 4</option>
                <option value="5">⭐⭐⭐⭐⭐ 5</option>
            </select>
            {$this->getError('puntuacion')}
        </div>
        <div class="campo">
            <label for="comentario">Comentario:</label>
            <textarea id="comentario" name="comentario" rows="4">{$comentario}</textarea>
            {$this->getError('comentario')}
        </div>
        <button type="submit" class="btn-pedido btn-primary">Enviar valoración</button>
HTML;

        return $html;
    }

    protected function procesaFormulario(&$datos) {
        $this->errores = [];

        $pedidoId = (int)($datos['pedido_id'] ?? 0);
        $puntuacion = (int)($datos['puntuacion'] ?? 0);
        $comentario = trim($datos['comentario'] ?? '');

        if ($pedidoId <= 0) {
            $this->errores['pedido_id'] = 'Pedido no válido';
        }
        if ($puntuacion < 1 || $puntuacion > 5) {
            $this->errores['puntuacion'] = 'La puntuación debe estar entre 1 y 5';
        }
        if (strlen($comentario) > 1000) {
            $this->errores['comentario'] = 'El comentario no puede superar los 1000 caracteres';
        }

        if (count($this->errores) > 0) {
            return $this->errores;
        }

        $comentarioSafe = filter_var($comentario, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $service = new ValoracionAppService();
        $ok = $service->crearValoracion($pedidoId, $_SESSION['idUsuario'], $puntuacion, $comentarioSafe);

        if (!$ok) {
            $this->errores[] = 'No se ha podido crear la valoración';
            return $this->errores;
        }

        return 'mis-pedidos.php';
    }
}
