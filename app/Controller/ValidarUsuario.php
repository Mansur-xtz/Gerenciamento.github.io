<?php
require_once __DIR__ . '/../Model/UserBanco.php';

class ValidarUsuario {
    // Retorna o ID do usuário se válido, ou null
    public function retornar(string $usuario, string $senha): ?int {
        if (empty($usuario) || empty($senha)) {
            return null;
        }
        return (new UserBanco())->verificarCredenciais($usuario, $senha);
    }
}
