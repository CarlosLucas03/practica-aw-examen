CREATE TABLE Valoraciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL UNIQUE,
    cliente_id INT NOT NULL,
    puntuacion TINYINT NOT NULL,
    comentario TEXT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pedido_id) REFERENCES Pedidos(id),
    FOREIGN KEY (cliente_id) REFERENCES Usuarios(id),
    CHECK (puntuacion BETWEEN 1 AND 5)
);
