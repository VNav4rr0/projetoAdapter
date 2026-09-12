<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Adapter;

/**
 * EBookAdapter
 *
 * Este é o Adapter (Adaptador) do padrão estrutural Adapter.
 *
 * Problema: o código cliente conhece e trabalha apenas com o contrato definido
 * pela interface Book (Target). Porém, precisamos reaproveitar a classe Kindle,
 * que implementa EBook (Adaptee) e possui métodos com nomes e tipos de retorno
 * totalmente diferentes (unlock(), pressNext(), getPage(): array).
 *
 * Solução: em vez de alterar o cliente ou a classe Kindle, criamos esta classe
 * intermediária que:
 *  - implementa a interface Book, para ser 100% compatível com o cliente;
 *  - recebe uma instância de EBook via injeção de dependência (composição),
 *    em vez de herança — por isso é chamado de Object Adapter.
 *
 * Internamente, cada método do contrato Book é "traduzido" para a chamada
 * correspondente no objeto EBook adaptado.
 */
class EBookAdapter implements Book
{
    /**
     * Composição: o adaptador guarda uma referência ao objeto adaptado (Adaptee)
     * em vez de estender uma classe concreta. Isso é o que caracteriza o
     * Object Adapter (em oposição ao Class Adapter, que usaria herança múltipla).
     */
    public function __construct(protected EBook $eBook)
    {
    }

    /**
     * O cliente chama open() (contrato Book), e o adaptador traduz
     * a chamada para unlock() (contrato EBook).
     */
    public function open()
    {
        $this->eBook->unlock();
    }

    /**
     * O cliente chama turnPage() (contrato Book), e o adaptador traduz
     * a chamada para pressNext() (contrato EBook).
     */
    public function turnPage()
    {
        $this->eBook->pressNext();
    }

    /**
     * Aqui ocorre a tradução mais interessante: EBook::getPage() devolve um
     * array no formato [paginaAtual, totalDePaginas], enquanto Book::getPage()
     * espera apenas um inteiro com a página atual.
     *
     * O adaptador extrai a primeira posição do array retornado pelo Kindle
     * para cumprir exatamente o contrato exigido pela interface Book.
     */
    public function getPage(): int
    {
        return $this->eBook->getPage()[0];
    }
}
