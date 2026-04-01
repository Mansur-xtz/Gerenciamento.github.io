<?php
class Estagiario {
    private PDO $banco;

    public function __construct(PDO $banco) {
        $this->banco = $banco;
    }

    public function buscarPorId(int $id): ?array {
        $stmt = $this->banco->prepare('SELECT * FROM estagiarios WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ?: null;
    }

    public function listarEstagiarios(): array {
        $stmt = $this->banco->query('SELECT * FROM estagiarios ORDER BY nome ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cadastrarEstagiario(string $nome, string $telefone, string $curso, string $data_inicio, int $duracao, string $cpf_cnpj): bool {
        $stmt = $this->banco->prepare(
            'INSERT INTO estagiarios (nome, cpf_cnpj, telefone, curso, data_inicio, duracao)
             VALUES (:nome, :cpf_cnpj, :telefone, :curso, :data_inicio, :duracao)'
        );
        return $stmt->execute([
            ':nome'        => $nome,
            ':cpf_cnpj'    => $cpf_cnpj,
            ':telefone'    => $telefone,
            ':curso'       => $curso,
            ':data_inicio' => $data_inicio,
            ':duracao'     => $duracao,
        ]);
    }

    public function atualizarEstagiario(int $id, string $nome, string $cpf_cnpj, string $telefone, string $empresa, string $data_inicio, int $duracao): bool {
        $stmt = $this->banco->prepare(
            'UPDATE estagiarios SET nome=:nome, cpf_cnpj=:cpf_cnpj, telefone=:telefone,
             empresa=:empresa, data_inicio=:data_inicio, duracao=:duracao WHERE id=:id'
        );
        return $stmt->execute([
            ':nome'        => $nome,
            ':cpf_cnpj'    => $cpf_cnpj,
            ':telefone'    => $telefone,
            ':empresa'     => $empresa,
            ':data_inicio' => $data_inicio,
            ':duracao'     => $duracao,
            ':id'          => $id,
        ]);
    }

    public function excluirEstagiario(int $id): bool {
        $stmt = $this->banco->prepare('DELETE FROM estagiarios WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
