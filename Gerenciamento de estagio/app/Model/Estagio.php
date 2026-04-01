<?php
class Estagio {
    private string $empresa;
    private string $funcionario;
    private string $data;
    private string $horario;
    private string $id;
    private float $duracao; 

   
    public function getempresa() {
        return $this->empresa;
    }

    public function setempresa($empresa) {
        $this->empresa = $empresa;
    }

   
    public function getfuncionario() {
        return $this->funcionario;
    }

    public function setfuncionario($funcionario) {
        $this->funcionario = $funcionario;
    }

   
    public function getdata() {
        return $this->data;
    }

    public function setdata($data) {
        $this->data = $data;
    }

    // Getter e Setter para horario
    public function gethorario() {
        return $this->horario;
    }

    public function sethorario($horario) {
        $this->horario = $horario;
    }


    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    
    public function setduracao(float $duracao) {
        $this->duracao = $duracao;
    }

    public function getduracao(): float {
        return $this->duracao;
    }
}
?>
