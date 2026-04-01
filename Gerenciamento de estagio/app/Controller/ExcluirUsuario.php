<?php
require_once __DIR__ . '/../Model/UserBanco.php';

class ExcluirUsuario {
    public function retornar(): void {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id > 0) {
            (new UserBanco())->excluirUsuario($id);
        }
    }
}
