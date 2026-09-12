# Atividade: Padrão de Projeto Adapter (Estrutural)

Implementação do padrão **Adapter** a partir do projeto base
[DesignPatternsPHP](https://github.com/DesignPatternsPHP/DesignPatternsPHP), aplicada
ao caso `Book` / `EBook` / `Kindle`, localizado em `Structural/Adapter/`.

## 1. Clonagem do repositório

```bash
git clone https://github.com/DesignPatternsPHP/DesignPatternsPHP.git
cd DesignPatternsPHP
```

## 2. Instalação das dependências

Pré-requisitos: PHP >= 8.1 e Composer.

```bash
composer install
```

## 3. Mapeamento do domínio

Diretório analisado: `Structural/Adapter/`

| Arquivo | Papel no padrão | Descrição |
|---|---|---|
| `Book.php` | **Target** (interface esperada pelo cliente) | Define o contrato `open()`, `turnPage()` e `getPage(): int` |
| `PaperBook.php` | Implementação concreta do Target | Implementa `Book` diretamente, sem necessidade de adaptação |
| `EBook.php` | **Adaptee** (interface do subsistema externo) | Define `unlock()`, `pressNext()` e `getPage(): array` (retorna `[paginaAtual, totalPaginas]`) |
| `Kindle.php` | Implementação concreta do Adaptee | Simula um leitor digital de terceiros, com nomenclatura e tipos de retorno incompatíveis com `Book` |

### Conflito identificado

O código cliente conhece apenas o contrato `Book`. A classe `Kindle`, no entanto,
implementa `EBook`, cujos métodos têm nomes diferentes (`unlock` em vez de `open`,
`pressNext` em vez de `turnPage`) e um retorno incompatível (`getPage()` devolve um
`array` de dois inteiros, não um único `int`). Por isso, o cliente **não consegue
usar um `Kindle` diretamente onde espera um `Book`**.

## 4. Criação da classe adaptadora — `EBookAdapter.php`

Arquivo criado em `Structural/Adapter/EBookAdapter.php`.

A classe:

- **implementa `Book`**, garantindo compatibilidade total com o código cliente já
  existente (nenhuma linha do cliente precisa mudar);
- recebe uma instância de `EBook` **via injeção de dependência no construtor**,
  usando **composição** (Object Adapter) em vez de herança;
- traduz cada chamada do contrato `Book` para o método correspondente em `EBook`.

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
        // EBook::getPage() retorna [paginaAtual, totalPaginas];
        // Book::getPage() espera apenas a página atual.
        return $this->eBook->getPage()[0];
    }
}
```

### Tradução realizada por método

| Chamada do cliente (`Book`) | Traduzida internamente para (`EBook`) | Observação |
|---|---|---|
| `open()` | `unlock()` | Simples repasse de chamada |
| `turnPage()` | `pressNext()` | Simples repasse de chamada |
| `getPage(): int` | `getPage(): array` e extrai o índice `[0]` | Adapta também o **tipo de retorno**, não só o nome do método |

## 5. Validação da implementação

Ambiente de testes preparado com PHP 8.3 e as extensões `xml`, `mbstring`, `zip`
(exigidas pelas dependências de desenvolvimento do projeto).

Testes existentes em `Structural/Adapter/Tests/AdapterTest.php` executados via PHPUnit:

```bash
./vendor/bin/phpunit Structural/Adapter/Tests/AdapterTest.php
```

Resultado:

```
PHPUnit 9.6.13 by Sebastian Bergmann and contributors.

..                                                                  2 / 2 (100%)

Time: 00:00.002, Memory: 6.00 MB

OK (2 tests, 2 assertions)
```

Também foi feita uma verificação manual:

```php
$kindle = new Kindle();
$book   = new EBookAdapter($kindle);

$book->open();       // internamente chama $kindle->unlock()
$book->turnPage();   // internamente chama $kindle->pressNext()
$book->turnPage();
$book->turnPage();

echo $book->getPage(); // 4 (int), mesmo que Kindle::getPage() devolva um array
```

Saída obtida: `4` (tipo `integer`), confirmando que o `EBookAdapter` cumpre
exatamente o contrato de `Book`, mesmo usando um `Kindle` por trás dos panos.

Verificação de estilo de código (PSR-12) sem apontamentos:

```bash
./vendor/bin/phpcs --standard=PSR12 Structural/Adapter/EBookAdapter.php
```

## 6. Conclusão

O padrão **Adapter** permitiu reaproveitar a classe `Kindle` (código de terceiros,
com interface incompatível) sem alterar nem o código cliente que depende de `Book`,
nem a própria classe `Kindle`. Isso foi possível através de:

- **composição** (o adapter guarda uma referência a um `EBook`, em vez de herdar dele);
- **tradução de assinaturas de métodos** (nomes diferentes → mesmo comportamento esperado);
- **tradução de tipos de retorno** (`array` → `int`), mantendo o contrato do `Target` intacto.

Isso caracteriza este `EBookAdapter` como um **Object Adapter**, a variante mais
comum e recomendada do padrão em linguagens como PHP, que não suportam herança
múltipla de classes.

---

**Autores:** _(preencher com os nomes da dupla)_
**Disciplina/Atividade:** Padrão de modelagem de projetos — Adapter
