<?php

namespace Alura\Leilao\Model;

class Leilao
{
    /** @var Lance[] */

    private $lances;
    /** @var string */
    private $descricao;
    private $finalizado;

    public function __construct(string $descricao)
    {
        $this->descricao = $descricao;
        $this->lances = [];
        $this->finalizado = false;
    }

    public function recebeLance(Lance $lance)
    {
        if (!empty($this->lances) && $this->ehDoUltimoUsuario($lance)) {
            throw new \DomainException('Usuário não pode propor 2 lances seguidos');
        }

        $totalLancesUsuario = $this->quantidadeDeLancesPorUsuario($lance->getUsuario());

        if ($totalLancesUsuario >= 5) {
            throw new \DomainException('Usuário não propor mais de 5 lances por leilão');
        }

        $this->lances[] = $lance;
    }

    public function estaFinalizado(): bool
    {
        return $this->finalizado;
    }

    public function finaliza()
    {
        $this->finalizado = true;
    }
    /**
     * @param Lance $lance
     * @return bool
     */

    public function ehDoUltimoUsuario(Lance $lance): bool
    {
        $ultimoLance = $this->lances[count($this->lances) - 1];
        return $lance->getUsuario() == $ultimoLance->getUsuario();
    }

    /**
     * @return Lance[]
     */
    public function getLances(): array
    {
        return $this->lances;
    }

    private function quantidadeDeLancesPorUsuario(Usuario $usuario): int
    {
        $totalLancesUsuario = array_reduce(
            $this->lances,
            function (int $totalAcumulado, Lance $lanceAtual) use ($usuario) {
                if ($lanceAtual->getUsuario() == $usuario) {
                    return $totalAcumulado + 1;
                }
                return $totalAcumulado;
            },
            0
        );

        return $totalLancesUsuario;
    }
}
