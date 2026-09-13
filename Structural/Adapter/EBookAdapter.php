<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Adapter;

/**
 * Adapter: implementa Book (Target) para que o cliente continue
 * usando a mesma interface, mas por baixo dos panos delega para
 * uma instância de EBook (Adaptee) recebida via injeção de dependência.
 */
class EBookAdapter implements Book
{
    public function __construct(protected EBook $eBook)
    {
    }

    /**
     * Traduz open() do Book para unlock() do EBook.
     */
    public function open()
    {
        $this->eBook->unlock();
    }

    /**
     * Traduz turnPage() do Book para pressNext() do EBook.
     */
    public function turnPage()
    {
        $this->eBook->pressNext();
    }

    /**
     * EBook::getPage() devolve [páginaAtual, totalPáginas] (array),
     * mas Book::getPage() precisa devolver só um int — pegamos a
     * primeira posição do array.
     */
    public function getPage(): int
    {
        return $this->eBook->getPage()[0];
    }
}