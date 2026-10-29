# Perfil do Estudante: versão 1

**Autor(a):** Gabriel Soares Vieira

## Como executar

```bash
cd projeto/v1
php -S localhost:8000 -d display_errors=1 -d output_buffering=0
```

Abra http://localhost:8000

## Telas

| Tema claro | Tema escuro |
|---|---|
| ![Tema claro](prints/claro.png) | ![Tema escuro](prints/escuro.png) |

## 1. Mapa de rotas

Abaixo estão listadas todas as rotas suportadas pela aplicação, seu objetivo e o código de status HTTP retornado:

| URL | O que mostra | Status |
|---|---|---|
| `/` | Página inicial com dados do estudante e biografia pessoal | `200 OK` |
| `/?pagina=inicio` | Apresentação pessoal (nome, curso, campus, cidade e biografia) | `200 OK` |
| `/?pagina=habilidades` | Lista de habilidades técnicas com contagem (`count`) e visão geral (`implode`) | `200 OK` |
| `/?pagina=projetos` | Catálogo com todos os projetos desenvolvidos e links para detalhes | `200 OK` |
| `/?pagina=projeto&id=1` | Detalhes completos do Projeto 1 (GymTracker API) com tecnologias | `200 OK` |
| `/?pagina=projeto&id=99` | Mensagem "Projeto não encontrado" para identificador inexistente | `404 Not Found` |
| `/?pagina=qualquercoisa` | Mensagem "Página não encontrada" para rota desconhecida | `404 Not Found` |
| `/?tema=escuro` | Aplica e grava cookie persistente para o tema escuro | `200 OK` |
| `/?formato=json` | Retorno em JSON puro contendo dados de `$perfil` e `$projetos` | `200 OK` |

## 2. Saídas do terminal

**2.1 Uma página que existe:** `curl.exe -i "http://localhost:8000/?pagina=habilidades"` (cole só até o início do HTML)

```text
HTTP/1.1 200 OK
Host: localhost:8000
Date: Wed, 28 Oct 2026 23:30:52 GMT
Connection: close
X-Powered-By: PHP/8.4.24
Content-type: text/html; charset=UTF-8

<!DOCTYPE html>
```

**Explicação:**

A requisição HTTP GET solicitou a rota `?pagina=habilidades`. O PHP capturou o parâmetro através de `$_GET['pagina']`, validou que se trata de uma rota válida presente na lista de páginas conhecidas e respondeu com o código de status HTTP `200 OK` e cabeçalho `Content-type: text/html; charset=UTF-8`, iniciando imediatamente após o envio do documento HTML correspondente.

**2.2 Uma página que não existe:** `curl.exe -i "http://localhost:8000/?pagina=projeto&id=99"` (cole só até o início do HTML)

```text
HTTP/1.1 404 Not Found
Host: localhost:8000
Date: Wed, 28 Oct 2026 23:31:00 GMT
Connection: close
X-Powered-By: PHP/8.4.24
Content-type: text/html; charset=UTF-8

<!DOCTYPE html>
```

**Explicação:**

A requisição tentou acessar um projeto com o parâmetro `id=99`. No bloco PHP no topo do arquivo, o ID foi convertido com segurança para número inteiro (`(int)$_GET['id']`) e a verificação `isset($projetos[$id])` retornou `false`, visto que o catálogo possui apenas os IDs 1, 2 e 3. Diante disso, a função `http_response_code(404)` foi chamada antes de qualquer saída de corpo, fazendo com que o servidor retornasse o cabeçalho `HTTP/1.1 404 Not Found` e renderizasse o aviso de "Projeto não encontrado" na interface.

**2.3 A troca de tema:** `curl.exe -i "http://localhost:8000/?tema=escuro"` (cole só até a linha do `<body>`)

```text
HTTP/1.1 200 OK
Host: localhost:8000
Date: Wed, 28 Oct 2026 23:31:06 GMT
Connection: close
X-Powered-By: PHP/8.4.24
Set-Cookie: tema=escuro; expires=Fri, 27 Nov 2026 23:31:06 GMT; Max-Age=2592000; path=/
Content-type: text/html; charset=UTF-8

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gabriel Soares Vieira | Perfil Profissional</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body class="tema-escuro">
```

**Explicação (qual cabeçalho grava o tema e como o PHP sabe o tema na próxima requisição):**

O cabeçalho responsável por instruir o navegador do cliente a armazenar o tema é o `Set-Cookie: tema=escuro; expires=Fri, 27 Nov 2026 23:31:06 GMT; Max-Age=2592000; path=/`, emitido pela função `setcookie()` no topo do script ao identificar que o parâmetro `tema` foi fornecido via `$_GET`.

Na requisição seguinte realizada pelo usuário, o navegador anexa automaticamente o cabeçalho `Cookie: tema=escuro` no pacote da requisição HTTP. O interpretador PHP lê esse cabeçalho e popula a superglobal `$_COOKIE['tema']`. Graças à prioridade implementada (`$tema = $_GET['tema'] ?? $_COOKIE['tema'] ?? 'claro'`), mesmo que a URL subsequente não contenha o parâmetro `?tema=`, o PHP recuperará o valor persistido no cookie e aplicará a classe `<body class="tema-escuro">`.

**2.4 A API:** `curl.exe -i "http://localhost:8000/?formato=json"`

```text
HTTP/1.1 200 OK
Host: localhost:8000
Date: Wed, 28 Oct 2026 23:31:15 GMT
Connection: close
X-Powered-By: PHP/8.4.24
Content-Type: application/json; charset=utf-8

{
    "perfil": {
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
        ]
    },
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

**Explicação:**

Ao identificar o parâmetro `?formato=json` na query string, o bloco 3 do processamento PHP é ativado antes de qualquer renderização de HTML. O script envia o cabeçalho `Content-Type: application/json; charset=utf-8` via `header()`, codifica os arrays associativos `$perfil` e `$projetos` com `json_encode()` usando as opções `JSON_PRETTY_PRINT` (formatação legível com indentação) e `JSON_UNESCAPED_UNICODE` (preservação de caracteres acentuados em português), e encerra a execução imediatamente com `exit;`. Esse encerramento garante que nenhum espaço em branco ou tag HTML seja enviado, entregando um JSON puro e em conformidade com padrões de APIs RESTful.

**2.5 Log do servidor** das quatro requisições acima

```text
[Wed Oct 28 20:30:52 2026] [::1]:56043 Accepted
[Wed Oct 28 20:30:52 2026] [::1]:56043 [200]: GET /?pagina=habilidades
[Wed Oct 28 20:30:52 2026] [::1]:56043 Closing
[Wed Oct 28 20:31:00 2026] [::1]:56044 Accepted
[Wed Oct 28 20:31:00 2026] [::1]:56044 [404]: GET /?pagina=projeto&id=99
[Wed Oct 28 20:31:00 2026] [::1]:56044 Closing
[Wed Oct 28 20:31:06 2026] [::1]:56045 Accepted
[Wed Oct 28 20:31:06 2026] [::1]:56045 [200]: GET /?tema=escuro
[Wed Oct 28 20:31:06 2026] [::1]:56045 Closing
[Wed Oct 28 20:31:15 2026] [::1]:56046 Accepted
[Wed Oct 28 20:31:15 2026] [::1]:56046 [200]: GET /?formato=json
[Wed Oct 28 20:31:15 2026] [::1]:56046 Closing
```

**Explicação:**

O servidor de desenvolvimento embutido do PHP (`php -S`) registra detalhadamente o ciclo de vida de cada requisição HTTP:
1. `Accepted`: Abertura e aceitação da conexão TCP entre o cliente (`::1`, localhost IPv6) e a porta efêmera atribuída;
2. `[STATUS]: MÉTODO URI`: O código de status HTTP final retornado pelo script PHP (por exemplo, `[200]` para rotas bem-sucedidas e `[404]` quando a rota ou recurso não foi localizado), seguido do verbo HTTP e da rota acessada com seus parâmetros;
3. `Closing`: Encerramento da conexão após o envio completo dos cabeçalhos e do corpo da resposta.
Os logs confirmam que o roteamento e a alteração dinâmica de status para `404` foram recebidos e propagados corretamente pelo servidor web.

## 3. Onde usei cada conceito

| Conceito | Linha(s) do `index.php` | Para que usei |
|---|---|---|
| Array associativo | Linhas 7 a 21 e 23 a 39 | Armazenamento estruturado dos dados cadastrais do perfil (`$perfil`) e do catálogo de projetos (`$projetos`) com chaves textuais e dados completos. |
| `$_GET` e `??` | Linhas 42, 52, 62 e 63 | Leitura segura dos parâmetros passados pela query string na URL (`tema`, `formato`, `pagina` e `id`), utilizando coalescência nula para garantir valores padrão quando os parâmetros não forem fornecidos. |
| `if` / `elseif` / `else` | Linhas 43, 46, 52, 66–72 (PHP) e 116–198 (HTML) | Validação de tema, desvio para emissão da API JSON, validação de rotas com código 404 e controle de renderização das páginas no corpo HTML via sintaxe alternativa (`endif;`). |
| `htmlspecialchars` | Linhas 79, 82, 86, 88, 89, 91, 107, 118, 123, 127, 131, 142, 147, 159, 160, 162, 171, 174, 178, 204, 205 | Sanitização de todos os dados dinâmicos emitidos no HTML, convertendo caracteres especiais em entidades HTML para neutralizar vulnerabilidades de Cross-Site Scripting (XSS). |
| `setcookie` e `$_COOKIE` | Linhas 42 e 47 | Leitura da preferência de tema gravada previamente no cliente (`$_COOKIE['tema']`) e persistência da escolha por 30 dias via cabeçalho HTTP (`setcookie`). |
| `http_response_code(404)` | Linhas 68 e 71 | Definição explícita do código de status HTTP 404 Not Found para requisições com IDs de projeto inexistentes ou páginas desconhecidas. |
| `header` e `json_encode` | Linhas 53 e 54 a 57 | Configuração do cabeçalho `Content-Type: application/json` e serialização dos arrays de dados em JSON estruturado com suporte nativo a acentuação UTF-8. |
| `implode` e `count` | Linhas 139 e 142 | Cálculo do total numérico de habilidades cadastradas (`count($perfil['habilidades'])`) e concatenação de todos os elementos em uma lista textual separada por vírgulas (`implode(', ', ...)`). |

## 4. Decisões e dificuldades

**4.1 Por que toda a lógica PHP fica no topo do arquivo, antes do HTML?**

No protocolo HTTP, as respostas do servidor são transmitidas em duas etapas obrigatórias e sequenciais: primeiro os **cabeçalhos HTTP** (que contêm o código de status como `200` ou `404`, cabeçalhos de tipo de conteúdo como `Content-Type` e instruções de cookies via `Set-Cookie`) e, em seguida, o **corpo da resposta** (o documento HTML).

Se houver qualquer caractere emitido antes de chamar funções como `setcookie()`, `header()` ou `http_response_code()` — incluindo uma tag HTML, um espaço em branco ou uma linha vazia antes da tag `<?php` —, o PHP é forçado a despachar os cabeçalhos HTTP imediatamente para dar início ao corpo. Quando a flag `-d output_buffering=0` está ativada (sem buffer de saída), qualquer tentativa posterior de modificar cabeçalhos ou gravar cookies resulta no erro fatal `Warning: Cannot modify header information - headers already sent`.

Ao centralizar toda a lógica de tomada de decisão (leitura de parâmetros, validação de segurança, definição de status HTTP, gravação de cookies e encerramento para JSON) no topo do arquivo antes de qualquer tag HTML, garantimos que todas as manipulações de cabeçalhos ocorram de forma limpa, confiável e sem erros de envio prematuro.

**4.2 Por que o tema escolhido na URL precisa ser validado?**

A entrada de dados via URL (`$_GET`) é uma fonte externa e não confiável que pode ser livremente manipulada por qualquer usuário ou atacante. Se o valor do parâmetro `?tema=` fosse diretamente inserido no HTML (por exemplo: `<body class="tema-<?= $_GET['tema'] ?>">`) ou gravado diretamente em um cookie sem filtragem, a aplicação ficaria suscetível a ataques de **Cross-Site Scripting Refletido (XSS)**. Um invasor poderia elaborar uma URL maliciosa como `?tema="><script>alert(document.cookie)</script>` para injetar scripts no navegador da vítima.

Além da prevenção contra XSS (reforçada pelo uso de `htmlspecialchars`), a validação de regras de negócio garante a integridade visual da aplicação. Ao aplicar uma validação estrita baseada em lista branca (*whitelist*), aceitando unicamente os valores `'claro'` e `'escuro'`, qualquer valor inesperado ou malicioso é imediatamente normalizado para o tema padrão (`'claro'`), evitando anomalias de layout ou quebras no CSS.

**4.3 Qual foi a maior dificuldade e como você resolveu?**

O principal desafio técnico esteve relacionado ao ciclo de vida e tempo de propagação dos cookies HTTP em conjunto com a navegação do usuário. A função `setcookie()` instrui o navegador a salvar o cookie para as próximas requisições, mas ela não altera imediatamente o array global `$_COOKIE` na requisição em andamento. Ou seja, ao clicar em um link como `?tema=escuro`, a superglobal `$_COOKIE['tema']` ainda conteria o valor antigo (ou estaria vazia no primeiro acesso).

Para solucionar essa assincronia e proporcionar uma experiência de troca instantânea de tema sem a necessidade de recarregar a página duas vezes, foi implementada uma ordem de precedência clara:
```php
$tema = $_GET['tema'] ?? $_COOKIE['tema'] ?? 'claro';
```
Dessa maneira, a intenção explícita na URL (`$_GET['tema']`) assume prioridade imediata sobre o cookie preexistente, permitindo que o CSS renderize a página no novo tema no mesmo ciclo de processamento, enquanto o comando `setcookie()` grava a preferência para todas as navegações subsequentes. Complementarmente, foram gerados links dinâmicos no botão do menu que preservam a página e o identificador do projeto atual durante a alternância entre modo claro e escuro.
