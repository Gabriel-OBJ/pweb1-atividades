# Perfil do Estudante: versão 1

**Autor(a):** _(preencha)_

## Como executar

```
cd projeto/v1
php -S localhost:8000 -d display_errors=1 -d output_buffering=0
```

Abra http://localhost:8000

## Telas

| Tema claro | Tema escuro |
|---|---|
| ![Tema claro](prints/claro.png) | ![Tema escuro](prints/escuro.png) |

## 1. Mapa de rotas

Complete com todas as rotas do seu projeto.

| URL | O que mostra | Status |
|---|---|---|
| `/` | | |
| `/?pagina=habilidades` | | |
| `/?pagina=projetos` | | |
| `/?pagina=projeto&id=1` | | |
| `/?pagina=projeto&id=99` | | |
| `/?pagina=qualquercoisa` | | |
| `/?tema=escuro` | | |
| `/?formato=json` | | |

## 2. Saídas do terminal

**2.1 Uma página que existe:** `curl.exe -i "http://localhost:8000/?pagina=habilidades"` (cole só até o início do HTML)

```text
(cole aqui a saída do terminal)
```

**Explicação:**

_(responda aqui)_

**2.2 Uma página que não existe:** `curl.exe -i "http://localhost:8000/?pagina=projeto&id=99"` (cole só até o início do HTML)

```text
(cole aqui a saída do terminal)
```

**Explicação:**

_(responda aqui)_

**2.3 A troca de tema:** `curl.exe -i "http://localhost:8000/?tema=escuro"` (cole só até a linha do `<body>`)

```text
(cole aqui a saída do terminal)
```

**Explicação (qual cabeçalho grava o tema e como o PHP sabe o tema na próxima requisição):**

_(responda aqui)_

**2.4 A API:** `curl.exe -i "http://localhost:8000/?formato=json"`

```text
(cole aqui a saída do terminal)
```

**Explicação:**

_(responda aqui)_

**2.5 Log do servidor** das quatro requisições acima

```text
(cole aqui a saída do terminal)
```

**Explicação:**

_(responda aqui)_

## 3. Onde usei cada conceito

| Conceito | Linha(s) do `index.php` | Para que usei |
|---|---|---|
| Array associativo | | |
| `$_GET` e `??` | | |
| `if` / `elseif` / `else` | | |
| `htmlspecialchars` | | |
| `setcookie` e `$_COOKIE` | | |
| `http_response_code(404)` | | |
| `header` e `json_encode` | | |
| `implode` e `count` | | |

## 4. Decisões e dificuldades

**4.1 Por que toda a lógica PHP fica no topo do arquivo, antes do HTML?**

_(responda aqui)_

**4.2 Por que o tema escolhido na URL precisa ser validado?**

_(responda aqui)_

**4.3 Qual foi a maior dificuldade e como você resolveu?**

_(responda aqui)_
