# Design Pattern: Adapter — EBookAdapter

Atividade em dupla sobre o padrão estrutural **Adapter**, aplicado ao exemplo `Structural/Adapter` do repositório [DesignPatternsPHP](https://github.com/DesignPatternsPHP/DesignPatternsPHP).

## Integrantes

- Victor Hugo Navarro Taveira
- Vinicius Valero Chabariberi

## Objetivo

Fazer com que um leitor digital de terceiros (`Kindle`, que implementa a interface `EBook`) possa ser usado pelo cliente exatamente como um `Book` comum (`PaperBook`), sem que o código cliente precise conhecer os métodos internos do `Kindle`.

## Passo 1 — Clonagem do repositório

```bash
git clone https://github.com/DesignPatternsPHP/DesignPatternsPHP.git
cd DesignPatternsPHP
```

## Passo 2 — Instalação das dependências

```bash
composer install
```

## Passo 3 — Mapeamento do domínio

Diretório analisado: `Structural/Adapter/`

| Componente | Papel no padrão | Descrição |
|---|---|---|
| `Book` | Target (interface esperada) | Define `open()`, `turnPage()`, `getPage(): int` |
| `PaperBook` | Implementação nativa | Implementa `Book` diretamente, sem conflitos |
| `EBook` | Adaptee (interface externa) | Define `unlock()`, `pressNext()`, `getPage(): array` |
| `Kindle` | Implementação do Adaptee | Simula um leitor digital de terceiros com nomenclatura própria |

## Passo 4 — Identificação do conflito

O código cliente conhece apenas a interface `Book`. Uma instância de `Kindle` não pode ser usada nesse contexto porque:

- os nomes dos métodos divergem (`unlock` × `open`, `pressNext` × `turnPage`);
- o tipo de retorno de `getPage()` diverge (`array` no `EBook` vs `int` no `Book`).

## Passo 5 — Criação da classe adaptadora (`EBookAdapter.php`)

Arquivo editado em `Structural/Adapter/EBookAdapter.php`, implementando `Book` e recebendo um `EBook` via injeção de dependência no construtor (composição):

```php
<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Adapter;

class EBookAdapter implements Book
{
    public function __construct(protected EBook $eBook)
    {
    }

    public function open()
    {
        $this->eBook->unlock();
    }

    public function turnPage()
    {
        $this->eBook->pressNext();
    }

    public function getPage(): int
    {
        return $this->eBook->getPage()[0];
    }
}
```

### Como a tradução acontece

- `open()` → delega para `unlock()`
- `turnPage()` → delega para `pressNext()`
- `getPage(): int` → chama `getPage(): array` do `EBook` e retorna apenas a primeira posição (página atual)

## Passo 6 — Validação com testes

```bash
vendor/bin/phpunit Structural/Adapter/Tests/AdapterTest.php
```

O teste `testCanTurnPageOnKindleLikeInANormalBook` cria um `Kindle`, envolve numa instância de `EBookAdapter` e o utiliza exatamente como um `Book`, confirmando que o Adapter resolve a incompatibilidade sem alterar o código cliente nem as classes originais.

## Passo 7 — Versionamento no GitHub

```bash
git add Structural/Adapter/EBookAdapter.php README.md
git commit -m "feat: implementa EBookAdapter para adaptar Kindle (EBook) à interface Book"
git push origin main
```

## Conclusão

O padrão Adapter permitiu integrar um subsistema externo (`Kindle`/`EBook`) a um contrato já existente (`Book`) **sem modificar nenhuma das duas partes**, usando composição em vez de herança. Isso mantém o código cliente desacoplado de detalhes de implementação de bibliotecas de terceiros.
