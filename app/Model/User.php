<?php

class User {
    private int $usuarioId;     
    private string $nome;
    private string $senha;
    private bool $ativo;

    public function getusuarioId() {
        return $this->usuarioId;
    }

    public function setusuarioId(int $usuarioId) {
        $this->usuarioId = $usuarioId;
    }

    public function getNome() {
        return $this->nome;
    }

    public function setNome(string $nome) {
        $this->nome = $nome;
    }

    public function getSenha() {
        return $this->senha;
    }

    public function setSenha(string $senha) {
        $this->senha = $senha;
    }

    public function getAtivo() {
        return $this->ativo;
    }

    public function setAtivo(bool $ativo) {
        $this->ativo = $ativo;
    }
}
?>
