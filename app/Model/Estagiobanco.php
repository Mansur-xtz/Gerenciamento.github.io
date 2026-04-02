<?php
require_once __DIR__ . '/Estagio.php';

class Estagiosbanco {
    private PDO $pdo;

    public function __construct() {
        require_once __DIR__ . '/../Database/Conectar.php';
        $this->pdo = getDB();
    }

    public function cadastrarestagios(string $empresa, string $funcionario, string $data, string $dataTermino, string $horario, int $usuarioId): ?int {
        $sql = 'INSERT INTO estagios (empresa, funcionario, data, data_termino, horario, usuario_id)
                VALUES (:empresa, :funcionario, :data, :data_termino, :horario, :usuario_id)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':empresa'      => $empresa,
            ':funcionario'  => $funcionario,
            ':data'         => $data,
            ':data_termino' => $dataTermino,
            ':horario'      => $horario,
            ':usuario_id'   => $usuarioId,
        ]);
        $id = $this->pdo->lastInsertId();
        return $id ? (int) $id : null;
    }

    public function editarestagios(string $empresa, string $funcionario, string $data, string $horario, int $id): bool {
        $sql = 'UPDATE estagios SET empresa=:empresa, funcionario=:funcionario, data=:data, horario=:horario WHERE id=:id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':empresa' => $empresa, ':funcionario' => $funcionario, ':data' => $data, ':horario' => $horario, ':id' => $id]);
    }

    public function buscarestagiosPorId(int $id): ?array {
        $sql = 'SELECT * FROM estagios WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ?: null;
    }

    public function excluirestagios(int $id): bool {
        $stmt = $this->pdo->prepare('DELETE FROM estagios WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function listarestagios(int $usuarioId): array {
        $sql = 'SELECT e.*, e.data_termino
                FROM estagios e
                INNER JOIN user_est ue ON e.id = ue.estagio_id
                WHERE ue.usuario_id = :usuario_id
                ORDER BY e.data DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarEstagiarios(int $usuarioId): int {
        $sql = 'SELECT COUNT(DISTINCT est.id) as total
                FROM estagiarios est
                INNER JOIN estagios e ON est.estagio_id = e.id
                INNER JOIN user_est ue ON e.id = ue.estagio_id
                WHERE ue.usuario_id = :usuario_id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($res['total'] ?? 0);
    }

    public function contarAvaliacoes(int $usuarioId): int {
        $sql = 'SELECT COUNT(*) as total FROM avaliacoes WHERE usuario_id = :usuario_id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($res['total'] ?? 0);
    }

    public function contarRelatorios(int $usuarioId): int {
        $sql = 'SELECT COUNT(*) as total FROM relatorios WHERE usuario_id = :usuario_id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($res['total'] ?? 0);
    }
}
