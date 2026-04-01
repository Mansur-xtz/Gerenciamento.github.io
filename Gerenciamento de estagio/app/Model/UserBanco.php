<?php
require_once __DIR__ . '/User.php';

class UserBanco {
    private PDO $pdo;

    public function __construct() {
        require_once __DIR__ . '/../Database/Conectar.php';
        $this->pdo = getDB();
    }

    // Cadastra um novo usuário com senha hasheada
    public function cadastrarUsuario(string $nome, string $senha, bool $ativo): bool {
        $existente = $this->buscarPorUsername($nome);
        if (!empty($existente)) {
            throw new Exception('Este nome de usuário já está em uso.');
        }

        $senhaHash = password_hash($senha, PASSWORD_BCRYPT);
        $sql = 'INSERT INTO usuario (nome, senha, perfil_ativo) VALUES (:u, :p, :a)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':u', $nome);
        $stmt->bindValue(':p', $senhaHash);
        $stmt->bindValue(':a', $ativo, PDO::PARAM_BOOL);
        return $stmt->execute();
    }

    // Verifica credenciais e retorna o ID do usuário (ou null)
    public function verificarCredenciais(string $usuario, string $senha): ?int {
        $sql = 'SELECT id, senha FROM usuario WHERE nome = :u AND perfil_ativo = 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':u', $usuario);
        $stmt->execute();
        $resultado = $stmt->fetch();

        if ($resultado && password_verify($senha, $resultado['senha'])) {
            return (int) $resultado['id'];
        }
        return null;
    }

    // Busca usuário pelo nome
    public function buscarPorUsername(string $nome): ?array {
        $sql = 'SELECT * FROM usuario WHERE nome = :u';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':u', $nome);
        $stmt->execute();
        $resultado = $stmt->fetch();
        return $resultado ?: null;
    }

    // Busca usuário pelo ID
    public function buscarPorId(int $id): ?array {
        $sql = 'SELECT id, nome, perfil_ativo FROM usuario WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch();
        return $resultado ?: null;
    }

    // Lista todos os usuários (sem expor senha)
    public function listarUsuario(): array {
        $sql = 'SELECT id, nome, perfil_ativo FROM usuario';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function excluirUsuario(int $id): bool {
        $sql = 'DELETE FROM usuario WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
