# Perfil do Estudante: Versão 2 (com Classes e POO)

**Autor(a):** Gabriel Soares Vieira  
**Curso:** Tecnologia em Análise e Desenvolvimento de Sistemas (ADS)  
**Instituição:** IFCE Campus Boa Viagem  
**Disciplina:** Programação Web I &bull; Prof. Dr. Renato William Rodrigues de Souza  

---

## Como executar

```bash
cd projeto/v2
php -S localhost:8000 -d display_errors=1 -d output_buffering=0
```

Abra no navegador: [http://localhost:8000](http://localhost:8000)

---

## Telas

| Tema claro | Tema escuro |
|---|---|
| ![Tema claro](prints/claro.png) | ![Tema escuro](prints/escuro.png) |

---

## 1. As Classes

A arquitetura da Versão 2 migrou de estruturas puramente associativas para **Programação Orientada a Objetos (POO)** com tipagem estrita, encapsulamento rígido e tratamento de exceções.

### 1.1 Classe `Projeto`

Representa a entidade de um projeto individual desenvolvido no perfil acadêmico/profissional.

#### Atributos

| Atributo | Tipo | Visibilidade | Descrição |
|---|---|---|---|
| `$titulo` | `string` | `private` | Título identificador do projeto (validado contra vazio) |
| `$descricao` | `string` | `private` | Descrição detalhada do escopo, arquitetura e funcionalidades |
| `$tecnologias` | `string` | `private` | Relação textual de linguagens, frameworks e bibliotecas |

#### Métodos

| Método | Assinatura | Responsabilidade |
|---|---|---|
| `__construct` | `public function __construct(string $titulo, string $descricao, string $tecnologias)` | Inicializa o objeto. Aplica a regra de validação via `trim($titulo) === ''` e lança `Exception('O projeto precisa de um título.')` caso inválido. |
| `getTitulo` | `public function getTitulo(): string` | Retorna o título do projeto com garantia de tipagem. |
| `getDescricao` | `public function getDescricao(): string` | Retorna o texto descritivo do projeto. |
| `getTecnologias` | `public function getTecnologias(): string` | Retorna as tecnologias utilizadas. |
| `paraArray` | `public function paraArray(): array` | Serializa os atributos privados em um array associativo padrão (`titulo`, `descricao`, `tecnologias`). |

---

### 1.2 Classe `Perfil`

Agrega as informações cadastrais do estudante e gerencia a coleção de projetos técnicos associados.

#### Atributos

| Atributo | Tipo | Visibilidade | Descrição |
|---|---|---|---|
| `$nome` | `string` | `private` | Nome completo do titular do perfil |
| `$curso` | `string` | `private` | Denominação oficial do curso |
| `$campus` | `string` | `private` | Campus da instituição de ensino |
| `$cidade` | `string` | `private` | Localização geográfica (Cidade - UF) |
| `$apresentacao` | `string` | `private` | Biografia detalhada e objetivos de carreira |
| `$habilidades` | `array` | `private` | Lista de competências técnicas (mínimo de 5 exigidas) |
| `$projetos` | `array` | `private` | Coleção de instâncias de `Projeto`, indexada a partir de 1 |

#### Métodos

| Método | Assinatura | Responsabilidade |
|---|---|---|
| `__construct` | `public function __construct(string $nome, string $curso, string $campus, string $cidade, string $apresentacao, array $habilidades)` | Inicializa os dados do estudante. Valida se `count($habilidades) < 5`, lançando `Exception('O perfil precisa de pelo menos 5 habilidades.')`. |
| `adicionarProjeto` | `public function adicionarProjeto(Projeto $projeto): void` | Adiciona um objeto `Projeto` à coleção interna, indexando-o numericamente com chave iniciada em 1 (`count + 1`). |
| `getProjeto` | `public function getProjeto(int $id): Projeto` | Recupera a instância de `Projeto` pelo identificador numérico. Se o `$id` inexistir, lança `Exception('Projeto não encontrado.')`. |
| `getNome` | `public function getNome(): string` | Getter para o nome do estudante. |
| `getCurso` | `public function getCurso(): string` | Getter para o curso. |
| `getCampus` | `public function getCampus(): string` | Getter para o campus. |
| `getCidade` | `public function getCidade(): string` | Getter para a cidade. |
| `getApresentacao` | `public function getApresentacao(): string` | Getter para a biografia/apresentação. |
| `getHabilidades` | `public function getHabilidades(): array` | Getter para a lista de habilidades. |
| `getProjetos` | `public function getProjetos(): array` | Getter para a coleção de objetos `Projeto`. |
| `paraArray` | `public function paraArray(): array` | Mapeia o perfil completo e invoca recursivamente `paraArray()` em cada projeto da coleção, gerando a estrutura associativa para a API JSON. |

---

## 2. Antes e Depois

### 2.1 Tratamento do 404 de Projetos: `isset` vs `try/catch`

#### Versão 1 (Array associativo com verificação procedural `isset`)
```php
// Roteamento na v1:
if ($pagina === 'projeto') {
    if (!isset($projetos[$id])) {
        http_response_code(404);
    }
}

// No HTML:
<?php if (isset($projetos[$id])): ?>
    <h2><?= htmlspecialchars($projetos[$id]['titulo'], ENT_QUOTES, 'UTF-8') ?></h2>
<?php else: ?>
    <h2>Projeto não encontrado</h2>
<?php endif; ?>
```

#### Versão 2 (POO com exceção de domínio e captura via `try/catch`)
```php
// Roteamento na v2:
$projetoAtual = null;
$erroProjeto = null;

if ($pagina === 'projeto') {
    try {
        $projetoAtual = $perfil->getProjeto($id);
    } catch (Exception $e) {
        http_response_code(404);
        $erroProjeto = $e->getMessage(); // "Projeto não encontrado."
    }
}

// No HTML:
<?php if ($projetoAtual !== null): ?>
    <h2><?= htmlspecialchars($projetoAtual->getTitulo(), ENT_QUOTES, 'UTF-8') ?></h2>
<?php else: ?>
    <h2>Projeto não encontrado</h2>
    <p><?= htmlspecialchars($erroProjeto ?? 'O projeto solicitado não foi localizado no catálogo.', ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>
```

---

### 2.2 Renderização de Projetos no HTML: Acesso a Arrays vs Iteração de Coleção com Getters

#### Versão 1 (Acesso a chaves literais de array solto)
```php
<?php foreach ($projetos as $id => $projeto): ?>
    <article class="card card-projeto">
        <h3><?= htmlspecialchars($projeto['titulo'], ENT_QUOTES, 'UTF-8') ?></h3>
        <p class="descricao-curta"><?= htmlspecialchars($projeto['descricao'], ENT_QUOTES, 'UTF-8') ?></p>
        <p class="tecnologias-projeto">
            <strong>Tecnologias:</strong> <?= htmlspecialchars($projeto['tecnologias'], ENT_QUOTES, 'UTF-8') ?>
        </p>
        <a href="?pagina=projeto&id=<?= (int)$id ?>" class="btn-detalhes">Ver detalhes &rarr;</a>
    </article>
<?php endforeach; ?>
```

#### Versão 2 (Iteração sobre coleção de objetos tipados e métodos getters)
```php
<?php foreach ($perfil->getProjetos() as $id => $projeto): ?>
    <article class="card card-projeto">
        <h3><?= htmlspecialchars($projeto->getTitulo(), ENT_QUOTES, 'UTF-8') ?></h3>
        <p class="descricao-curta"><?= htmlspecialchars($projeto->getDescricao(), ENT_QUOTES, 'UTF-8') ?></p>
        <p class="tecnologias-projeto">
            <strong>Tecnologias:</strong> <?= htmlspecialchars($projeto->getTecnologias(), ENT_QUOTES, 'UTF-8') ?>
        </p>
        <a href="?pagina=projeto&id=<?= (int)$id ?>" class="btn-detalhes">Ver detalhes &rarr;</a>
    </article>
<?php endforeach; ?>
```

---

### 2.3 Explicação Técnica das Vantagens do Novo Modelo

1. **Encapsulamento e Imutabilidade Controlada**: Na v1, os arrays associativos eram estruturas abertas que podiam ser modificadas arbitrariamente por qualquer trecho do código, além de estarem sujeitas a erros de digitação de chaves (como `$projeto['titlo']`), gerando avisos de `Undefined array key`. Na v2, todos os atributos são `private`, impedindo mutações não autorizadas e assegurando consistência em tempo de compilação/execução através de métodos getters tipados.
2. **Delegação de Responsabilidades (Princípio Tell, Don't Ask)**: Na v1, o controlador precisava saber a estrutura interna de armazenamento para verificar `isset($projetos[$id])`. Na v2, o controlador simplesmente solicita o projeto ao modelo (`$perfil->getProjeto($id)`). A responsabilidade de saber se o projeto existe ou não pertence à classe `Perfil`, que lança uma exceção com mensagem contextual caso o recurso inexista.
3. **Escalabilidade Dinâmica**: Ao gerenciar os projetos através de `$perfil->adicionarProjeto()`, novos projetos podem ser adicionados dinamicamente em tempo de execução sem demandar refatoração nos laços do HTML ou na lógica de verificação de IDs, tornando a aplicação extensível e manutenível.

---

## 3. As Classes se Recusam (Testes Negativos de Validação)

Durante a fase de testes, o código foi submetido intencionalmente a entradas inválidas para validar se as classes barram a criação de objetos em estado inconsistente.

### 3.1 Teste Negativo: Título de Projeto Vazio
Tentativa de executar: `new Projeto('', 'Descrição...', 'Tecnologias...')`

**Saída real disparada pelo PHP:**
```text
PHP Fatal error:  Uncaught Exception: O projeto precisa de um título. in C:\Users\luffy\OneDrive\Documents\projeto\projeto\v2\index.php:16
Stack trace:
#0 C:\Users\luffy\OneDrive\Documents\projeto\projeto\v2\index.php(160): Projeto->__construct('', 'API RESTful des...', 'Java 21, Spring...')
#1 {main}
  thrown in C:\Users\luffy\OneDrive\Documents\projeto\projeto\v2\index.php on line 16
```

---

### 3.2 Teste Negativo: Perfil com Menos de 5 Habilidades
Tentativa de executar: `new Perfil(..., ['Java', 'Spring Boot', 'SQL & Modelagem de Dados', 'Git & GitHub'])`

**Saída real disparada pelo PHP:**
```text
PHP Fatal error:  Uncaught Exception: O perfil precisa de pelo menos 5 habilidades. in C:\Users\luffy\OneDrive\Documents\projeto\projeto\v2\index.php:67
Stack trace:
#0 C:\Users\luffy\OneDrive\Documents\projeto\projeto\v2\index.php(144): Perfil->__construct('Gabriel Soares ...', 'Tecnologia em A...', 'IFCE Campus Boa...', 'Boa Viagem - CE', 'Concluí o ensi...', Array)
#1 {main}
  thrown in C:\Users\luffy\OneDrive\Documents\projeto\projeto\v2\index.php on line 67
```

---

### 3.3 Explicação: Por que o objeto não é instanciado quando uma exceção é lançada no construtor?

No ciclo de instanciação da linguagem PHP e no paradigma de Orientação a Objetos, o operador `new` realiza duas operações fundamentais:
1. Aloca um bloco de memória para a nova instância da classe;
2. Executa o método construtor `__construct` para inicializar o estado interno do objeto.

A criação do objeto só é considerada finalizada e bem-sucedida se o construtor for executado até o fim sem interrupções. Quando uma exceção (`throw new Exception(...)`) é disparada no corpo do construtor:
* A linha de execução do método é interrompida imediatamente;
* O PHP desfaz a alocação e o Garbage Collector descarta a estrutura temporária do objeto incompleto;
* A referência ao objeto **nunca é atribuída** à variável receptora (`$perfil` ou `$projeto`), preservando a variável inalterada ou indefinida;
* Essa é uma garantia arquitetural de **Integridade de Invariantes**: o sistema impede categoricamente que exista em memória qualquer objeto cujo estado viole as regras de negócio basilares estabelecidas pela classe.

---

## 4. Saídas do Terminal

Os comandos a seguir foram executados contra o servidor embutido (`php -S localhost:8000 -t projeto/v2`):

### 4.1 Projeto Existente (Status 200 OK)
Comando: `curl.exe -i "http://localhost:8000/?pagina=projeto&id=2"`

```text
HTTP/1.1 200 OK
Host: localhost:8000
Date: Thu, 29 Oct 2026 00:39:24 GMT
Connection: close
X-Powered-By: PHP/8.4.24
Content-type: text/html; charset=UTF-8

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gabriel Soares Vieira | Perfil Profissional</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body class="tema-claro">
    <header class="cabecalho">
        <div class="container cabecalho-conteudo">
            <div class="perfil-identificacao">
                <h1 class="perfil-nome">Gabriel Soares Vieira</h1>
                <p class="perfil-subtitulo">
                    Tecnologia em Análise e Desenvolvimento de Sistemas &bull; 
                    IFCE Campus Boa Viagem                </p>
                <p class="perfil-local">Boa Viagem - CE</p>
            </div>
            <nav class="menu-navegacao">
                <a href="?pagina=inicio" class="link-nav ">Início</a>
                <a href="?pagina=habilidades" class="link-nav ">Habilidades</a>
                <a href="?pagina=projetos" class="link-nav ">Projetos</a>
                                <a href="?tema=escuro&amp;pagina=projeto&amp;id=2" class="link-nav btn-tema">
                    🌙 Modo Escuro                </a>
            </nav>
        </div>
    </header>

    <main class="container conteudo-principal">
                                    <article class="card secao-detalhe-projeto">
                    <h2>Store Management Database Design</h2>
                    <div class="bloco-info-projeto">
                        <h3>Descrição Detalhada</h3>
                        <p class="descricao-completa">Projeto de modelagem e arquitetura de banco de dados relacional para gestão operacional de loja comercial. Abrange controle de estoque, compras, entregas, fornecedores e histórico temporal de preços. Implementa integridade transacional ACID com Stored Procedures para compras atômicas e Triggers automáticas para auditoria de preços.</p>
                        
                        <h3>Tecnologias Utilizadas</h3>
                        <p class="tecnologias-destaque">
                            SQL, Modelagem Relacional, Triggers, Stored Procedures, ACID, MySQL                        </p>
                    </div>
                    <div class="acoes-projeto">
                        <a href="?pagina=projetos" class="btn-voltar">&larr; Voltar para Projetos</a>
                    </div>
                </article>
                        </main>

    <footer class="rodape">
        <div class="container">
            <p>&copy; 2026 Gabriel Soares Vieira &mdash; Tecnologia em Análise e Desenvolvimento de Sistemas</p>
            <p><small>Programação Web I &bull; IFCE Campus Boa Viagem</small></p>
        </div>
    </footer>
</body>
</html>
```

---

### 4.2 Projeto Inexistente (Tratamento 404 Not Found com Exceção)
Comando: `curl.exe -i "http://localhost:8000/?pagina=projeto&id=99"`

```text
HTTP/1.1 404 Not Found
Host: localhost:8000
Date: Thu, 29 Oct 2026 00:39:29 GMT
Connection: close
X-Powered-By: PHP/8.4.24
Content-type: text/html; charset=UTF-8

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gabriel Soares Vieira | Perfil Profissional</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body class="tema-claro">
    <header class="cabecalho">
        <div class="container cabecalho-conteudo">
            <div class="perfil-identificacao">
                <h1 class="perfil-nome">Gabriel Soares Vieira</h1>
                <p class="perfil-subtitulo">
                    Tecnologia em Análise e Desenvolvimento de Sistemas &bull; 
                    IFCE Campus Boa Viagem                </p>
                <p class="perfil-local">Boa Viagem - CE</p>
            </div>
            <nav class="menu-navegacao">
                <a href="?pagina=inicio" class="link-nav ">Início</a>
                <a href="?pagina=habilidades" class="link-nav ">Habilidades</a>
                <a href="?pagina=projetos" class="link-nav ">Projetos</a>
                                <a href="?tema=escuro&amp;pagina=projeto&amp;id=99" class="link-nav btn-tema">
                    🌙 Modo Escuro                </a>
            </nav>
        </div>
    </header>

    <main class="container conteudo-principal">
                                    <section class="card secao-erro">
                    <h2>Projeto não encontrado</h2>
                    <p>Projeto não encontrado.</p>
                    <a href="?pagina=projetos" class="btn-voltar">&larr; Voltar para Lista de Projetos</a>
                </section>
                        </main>

    <footer class="rodape">
        <div class="container">
            <p>&copy; 2026 Gabriel Soares Vieira &mdash; Tecnologia em Análise e Desenvolvimento de Sistemas</p>
            <p><small>Programação Web I &bull; IFCE Campus Boa Viagem</small></p>
        </div>
    </footer>
</body>
</html>
```

---

### 4.3 API JSON com `paraArray()` (Status 200 OK)
Comando: `curl.exe -i "http://localhost:8000/?formato=json"`

```text
HTTP/1.1 200 OK
Host: localhost:8000
Date: Thu, 29 Oct 2026 00:39:37 GMT
Connection: close
X-Powered-By: PHP/8.4.24
Content-Type: application/json; charset=utf-8

{
    "nome": "Gabriel Soares Vieira",
    "curso": "Tecnologia em Análise e Desenvolvimento de Sistemas",
    "campus": "IFCE Campus Boa Viagem",
    "cidade": "Boa Viagem - CE",
    "apresentacao": "Concluí o ensino médio integrado ao curso técnico em Redes de Computadores no IFCE e, motivado pelo dinamismo da área de tecnologia, decidi dar continuidade à minha formação acadêmica no mesmo campus, ingressando no curso superior de Tecnologia em Análise e Desenvolvimento de Sistemas (ADS). Meu principal objetivo profissional é consolidar minha carreira como Engenheiro de Software, atuando no desenvolvimento de sistemas robustos, escaláveis e de alto impacto, compreendendo todo o ciclo de vida e a arquitetura de produtos tecnológicos para além da simples escrita de código.",
    "habilidades": [
        "Java",
        "Spring Boot",
        "SQL & Modelagem de Dados",
        "Git & GitHub",
        "Testes Unitários",
        "Fundamentos de Computação & Algoritmos"
    ],
    "projetos": {
        "1": {
            "titulo": "GymTracker API",
            "descricao": "API RESTful desenvolvida em Java 21 e Spring Boot para rastreamento e gerenciamento de sessões de treino físico. Implementa arquitetura baseada no modelo de maturidade de Richardson com suporte a HATEOAS para hipermídia navegável, persistência com Spring Data JPA\/H2, documentação interativa via Swagger UI e validações de regras de negócio.",
            "tecnologias": "Java 21, Spring Boot, Spring HATEOAS, Spring Data JPA, H2 Database, Maven, Swagger"
        },
        "2": {
            "titulo": "Store Management Database Design",
            "descricao": "Projeto de modelagem e arquitetura de banco de dados relacional para gestão operacional de loja comercial. Abrange controle de estoque, compras, entregas, fornecedores e histórico temporal de preços. Implementa integridade transacional ACID com Stored Procedures para compras atômicas e Triggers automáticas para auditoria de preços.",
            "tecnologias": "SQL, Modelagem Relacional, Triggers, Stored Procedures, ACID, MySQL"
        },
        "3": {
            "titulo": "Sistema de Controle de Consumo de Água",
            "descricao": "Sistema web full-stack desenvolvido em PHP e Laravel para automação e gestão do abastecimento de água de associações comunitárias. Inclui registro mensal de leituras de hidrômetros com regras de validação, cálculo tarifário automático (taxa fixa e m³ excedentes), gestão financeira de faturas e integração com WhatsApp para envio automatizado de cobranças.",
            "tecnologias": "PHP 8.3, Laravel 13, MySQL, Eloquent ORM, Blade, Bootstrap 5"
        }
    }
}
```

---

## 5. Perguntas Conceituais Obrigatórias

### (a) Por que `json_encode($perfil)` devolve `{}`?

**Resposta Técnica:**  
O serializador nativo `json_encode()` do interpretador PHP inspeciona o objeto a partir de um escopo de chamada externo e serializa **única e exclusivamente as propriedades com visibilidade pública (`public`)**.

Como todos os atributos da classe `Perfil` (`$nome`, `$curso`, `$campus`, `$cidade`, `$apresentacao`, `$habilidades`, `$projetos`) foram declarados estritamente como `private` em respeito às boas práticas de encapsulamento, o reflexo do `json_encode()` não possui visibilidade de acesso sobre nenhum atributo direto da instância, resultando na representação de um objeto vazio (`{}`).

Para resolver essa limitação sem abrir mão da segurança dos modificadores de acesso, implementa-se o método público `paraArray()`. Esse método opera no escopo interno da classe e extrai os valores privados, gerando uma estrutura de array associativo pura que pode ser convertida fielmente em JSON.

---

### (b) Os objetos `$perfil` e `$projeto` continuam na memória do servidor depois que a página é enviada? Por quê?

**Resposta Técnica:**  
**Não. Os objetos são totalmente descartados da memória do servidor assim que a resposta HTTP é enviada.**

O modelo de execução da arquitetura padrão do PHP é baseado no paradigma **Shared-Nothing (nada compartilhado)** por requisição HTTP. Toda execução de script obedece a um ciclo de vida efêmero e isolado:
1. Ao receber a solicitação HTTP, o interpretador instancia o ambiente de execução e aloca os objetos na memória RAM (Heap);
2. O script processa a lógica, manipula os objetos e despacha os cabeçalhos e corpo da resposta para o cliente;
3. No exato instante em que o script encerra a execução (ao término do arquivo ou após um comando `exit`), o mecanismo de **Garbage Collection (GC)** do PHP desaloca todos os recursos e limpa a memória RAM;
4. Na requisição HTTP seguinte, absolutamente nada do estado anterior permanece na memória do processo PHP. Caso seja necessária persistência entre requisições, ela deve ocorrer através de mecanismos externos de persistência de estado (como Cookies HTTP, Sessões PHP, bancos de dados relacionais ou caches distribuídos).

---

### (c) Por que os atributos são `private`, e não `public`?

**Resposta Técnica:**  
A utilização de atributos `private` é o alicerce fundamental do princípio de **Encapsulamento** da Programação Orientada a Objetos.

Os três pilares técnicos dessa decisão são:
1. **Preservação de Invariantes e Validação Rigorosa**: Ao manter os atributos privados, nenhum código externo consegue alterar diretamente o estado de um objeto. Toda mutação de estado fica restrita ao construtor ou métodos específicos, garantindo que regras de negócio essenciais (como a exigência de no mínimo 5 habilidades ou a obrigatoriedade de título não vazio em um projeto) jamais sejam violadas em momento algum do ciclo de vida da aplicação;
2. **Prevenção de Corrupção de Estado e Bugs Silenciosos**: Atributos públicos (`public`) permitiriam atribuições externas desordenadas em qualquer parte do script (por exemplo, `$perfil->habilidades = [];` ou `$projeto->titulo = ''`), gerando estados corrompidos, falhas de renderização e inconsistências difíceis de rastrear;
3. **Ocultação da Estrutura de Armazenamento (Baixo Acoplamento)**: Atributos privados ocultam como os dados são organizados por baixo dos panos. Se no futuro a coleção interna de projetos migrar de um array para uma coleção orientada a objetos ou uma fonte externa, a interface pública da classe (seus métodos getters) permanece inalterada, evitando que alterações internas quebrem o código consumidor.
