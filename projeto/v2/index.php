<?php
// =====================================================================
// Projeto Integrador v2: Perfil do Estudante com Classes
// =====================================================================

// 1. Classes
class Projeto
{
    private string $titulo;
    private string $descricao;
    private string $tecnologias;

    public function __construct(string $titulo, string $descricao, string $tecnologias)
    {
        if (trim($titulo) === '') {
            throw new Exception('O projeto precisa de um título.');
        }
        $this->titulo = $titulo;
        $this->descricao = $descricao;
        $this->tecnologias = $tecnologias;
    }

    public function getTitulo(): string
    {
        return $this->titulo;
    }

    public function getDescricao(): string
    {
        return $this->descricao;
    }

    public function getTecnologias(): string
    {
        return $this->tecnologias;
    }

    public function paraArray(): array
    {
        return [
            'titulo'      => $this->titulo,
            'descricao'   => $this->descricao,
            'tecnologias' => $this->tecnologias,
        ];
    }
}

class Perfil
{
    private string $nome;
    private string $curso;
    private string $campus;
    private string $cidade;
    private string $apresentacao;
    private array $habilidades;
    private array $projetos = [];

    public function __construct(
        string $nome,
        string $curso,
        string $campus,
        string $cidade,
        string $apresentacao,
        array $habilidades
    ) {
        if (count($habilidades) < 5) {
            throw new Exception('O perfil precisa de pelo menos 5 habilidades.');
        }
        $this->nome = $nome;
        $this->curso = $curso;
        $this->campus = $campus;
        $this->cidade = $cidade;
        $this->apresentacao = $apresentacao;
        $this->habilidades = $habilidades;
    }

    public function adicionarProjeto(Projeto $projeto): void
    {
        $this->projetos[count($this->projetos) + 1] = $projeto;
    }

    public function getProjeto(int $id): Projeto
    {
        if (!isset($this->projetos[$id])) {
            throw new Exception('Projeto não encontrado.');
        }
        return $this->projetos[$id];
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getCurso(): string
    {
        return $this->curso;
    }

    public function getCampus(): string
    {
        return $this->campus;
    }

    public function getCidade(): string
    {
        return $this->cidade;
    }

    public function getApresentacao(): string
    {
        return $this->apresentacao;
    }

    public function getHabilidades(): array
    {
        return $this->habilidades;
    }

    public function getProjetos(): array
    {
        return $this->projetos;
    }

    public function paraArray(): array
    {
        $projetosArray = [];
        foreach ($this->projetos as $id => $projeto) {
            $projetosArray[$id] = $projeto->paraArray();
        }
        return [
            'nome'         => $this->nome,
            'curso'        => $this->curso,
            'campus'       => $this->campus,
            'cidade'       => $this->cidade,
            'apresentacao' => $this->apresentacao,
            'habilidades'  => $this->habilidades,
            'projetos'     => $projetosArray,
        ];
    }
}

// 2. Os objetos
$perfil = new Perfil(
    'Gabriel Soares Vieira',
    'Tecnologia em Análise e Desenvolvimento de Sistemas',
    'IFCE Campus Boa Viagem',
    'Boa Viagem - CE',
    'Concluí o ensino médio integrado ao curso técnico em Redes de Computadores no IFCE e, motivado pelo dinamismo da área de tecnologia, decidi dar continuidade à minha formação acadêmica no mesmo campus, ingressando no curso superior de Tecnologia em Análise e Desenvolvimento de Sistemas (ADS). Meu principal objetivo profissional é consolidar minha carreira como Engenheiro de Software, atuando no desenvolvimento de sistemas robustos, escaláveis e de alto impacto, compreendendo todo o ciclo de vida e a arquitetura de produtos tecnológicos para além da simples escrita de código.',
    [
        'Java',
        'Spring Boot',
        'SQL & Modelagem de Dados',
        'Git & GitHub',
        'Testes Unitários',
        'Fundamentos de Computação & Algoritmos'
    ]
);

$perfil->adicionarProjeto(new Projeto(
    'GymTracker API',
    'API RESTful desenvolvida em Java 21 e Spring Boot para rastreamento e gerenciamento de sessões de treino físico. Implementa arquitetura baseada no modelo de maturidade de Richardson com suporte a HATEOAS para hipermídia navegável, persistência com Spring Data JPA/H2, documentação interativa via Swagger UI e validações de regras de negócio.',
    'Java 21, Spring Boot, Spring HATEOAS, Spring Data JPA, H2 Database, Maven, Swagger'
));

$perfil->adicionarProjeto(new Projeto(
    'Store Management Database Design',
    'Projeto de modelagem e arquitetura de banco de dados relacional para gestão operacional de loja comercial. Abrange controle de estoque, compras, entregas, fornecedores e histórico temporal de preços. Implementa integridade transacional ACID com Stored Procedures para compras atômicas e Triggers automáticas para auditoria de preços.',
    'SQL, Modelagem Relacional, Triggers, Stored Procedures, ACID, MySQL'
));

$perfil->adicionarProjeto(new Projeto(
    'Sistema de Controle de Consumo de Água',
    'Sistema web full-stack desenvolvido em PHP e Laravel para automação e gestão do abastecimento de água de associações comunitárias. Inclui registro mensal de leituras de hidrômetros com regras de validação, cálculo tarifário automático (taxa fixa e m³ excedentes), gestão financeira de faturas e integração com WhatsApp para envio automatizado de cobranças.',
    'PHP 8.3, Laravel 13, MySQL, Eloquent ORM, Blade, Bootstrap 5'
));

$projetos = $perfil->getProjetos();

// 3. Tema claro ou escuro
$tema = $_GET['tema'] ?? $_COOKIE['tema'] ?? 'claro';
if ($tema !== 'claro' && $tema !== 'escuro') {
    $tema = 'claro';
}
if (isset($_GET['tema'])) {
    setcookie('tema', $tema, time() + (30 * 24 * 60 * 60), '/');
}
$temaOposto = ($tema === 'claro') ? 'escuro' : 'claro';

// 4. Rota JSON
if (isset($_GET['formato']) && $_GET['formato'] === 'json') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'perfil'   => $perfil,
        'projetos' => $projetos
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// 5. Qual página mostrar
$pagina = $_GET['pagina'] ?? 'inicio';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Validação de rotas e definição de status 404
if ($pagina === 'projeto') {
    if (!isset($projetos[$id])) {
        http_response_code(404);
    }
} elseif (!in_array($pagina, ['inicio', 'habilidades', 'projetos'], true)) {
    http_response_code(404);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($perfil->getNome(), ENT_QUOTES, 'UTF-8') ?> | Perfil Profissional</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body class="tema-<?= htmlspecialchars($tema, ENT_QUOTES, 'UTF-8') ?>">
    <header class="cabecalho">
        <div class="container cabecalho-conteudo">
            <div class="perfil-identificacao">
                <h1 class="perfil-nome"><?= htmlspecialchars($perfil->getNome(), ENT_QUOTES, 'UTF-8') ?></h1>
                <p class="perfil-subtitulo">
                    <?= htmlspecialchars($perfil->getCurso(), ENT_QUOTES, 'UTF-8') ?> &bull; 
                    <?= htmlspecialchars($perfil->getCampus(), ENT_QUOTES, 'UTF-8') ?>
                </p>
                <p class="perfil-local"><?= htmlspecialchars($perfil->getCidade(), ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <nav class="menu-navegacao">
                <a href="?pagina=inicio" class="link-nav <?= ($pagina === 'inicio') ? 'ativo' : '' ?>">Início</a>
                <a href="?pagina=habilidades" class="link-nav <?= ($pagina === 'habilidades') ? 'ativo' : '' ?>">Habilidades</a>
                <a href="?pagina=projetos" class="link-nav <?= ($pagina === 'projetos') ? 'ativo' : '' ?>">Projetos</a>
                <?php
                $paramTema = ['tema' => $temaOposto];
                if ($pagina !== 'inicio') {
                    $paramTema['pagina'] = $pagina;
                    if ($pagina === 'projeto' && $id > 0) {
                        $paramTema['id'] = $id;
                    }
                }
                $urlTema = '?' . http_build_query($paramTema);
                ?>
                <a href="<?= htmlspecialchars($urlTema, ENT_QUOTES, 'UTF-8') ?>" class="link-nav btn-tema">
                    <?= ($tema === 'claro') ? '🌙 Modo Escuro' : '☀️ Modo Claro' ?>
                </a>
            </nav>
        </div>
    </header>

    <main class="container conteudo-principal">
        <?php if ($pagina === 'inicio'): ?>
            <section class="card secao-apresentacao">
                <h2>Sobre Mim</h2>
                <p class="texto-apresentacao"><?= htmlspecialchars($perfil->getApresentacao(), ENT_QUOTES, 'UTF-8') ?></p>
                
                <div class="resumo-academico">
                    <div class="item-resumo">
                        <strong>Curso:</strong>
                        <span><?= htmlspecialchars($perfil->getCurso(), ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="item-resumo">
                        <strong>Instituição:</strong>
                        <span><?= htmlspecialchars($perfil->getCampus(), ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="item-resumo">
                        <strong>Localização:</strong>
                        <span><?= htmlspecialchars($perfil->getCidade(), ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                </div>
            </section>
        <?php elseif ($pagina === 'habilidades'): ?>
            <section class="card secao-habilidades">
                <h2>Minhas Habilidades</h2>
                <p class="total-habilidades">
                    Total de habilidades cadastradas: <strong><?= count($perfil->getHabilidades()) ?></strong>
                </p>
                <div class="lista-habilidades-texto">
                    <p><strong>Visão geral:</strong> <?= htmlspecialchars(implode(', ', $perfil->getHabilidades()), ENT_QUOTES, 'UTF-8') ?></p>
                </div>
                <div class="grid-habilidades">
                    <?php foreach ($perfil->getHabilidades() as $habilidade): ?>
                        <div class="badge-habilidade">
                            <?= htmlspecialchars($habilidade, ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php elseif ($pagina === 'projetos'): ?>
            <section class="secao-projetos">
                <h2>Projetos Desenvolvidos</h2>
                <p class="subtitulo-secao">Conheça alguns dos principais projetos técnicos desenvolvidos em minha trajetória acadêmica e profissional:</p>
                <div class="grid-projetos">
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
                </div>
            </section>
        <?php elseif ($pagina === 'projeto'): ?>
            <?php if (isset($projetos[$id])): ?>
                <article class="card secao-detalhe-projeto">
                    <h2><?= htmlspecialchars($projetos[$id]->getTitulo(), ENT_QUOTES, 'UTF-8') ?></h2>
                    <div class="bloco-info-projeto">
                        <h3>Descrição Detalhada</h3>
                        <p class="descricao-completa"><?= htmlspecialchars($projetos[$id]->getDescricao(), ENT_QUOTES, 'UTF-8') ?></p>
                        
                        <h3>Tecnologias Utilizadas</h3>
                        <p class="tecnologias-destaque">
                            <?= htmlspecialchars($projetos[$id]->getTecnologias(), ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </div>
                    <div class="acoes-projeto">
                        <a href="?pagina=projetos" class="btn-voltar">&larr; Voltar para Projetos</a>
                    </div>
                </article>
            <?php else: ?>
                <section class="card secao-erro">
                    <h2>Projeto não encontrado</h2>
                    <p>O projeto solicitado não foi localizado no catálogo.</p>
                    <a href="?pagina=projetos" class="btn-voltar">&larr; Voltar para Lista de Projetos</a>
                </section>
            <?php endif; ?>
        <?php else: ?>
            <section class="card secao-erro">
                <h2>Página não encontrada</h2>
                <p>A rota solicitada não existe.</p>
                <a href="?pagina=inicio" class="btn-voltar">&larr; Voltar para o Início</a>
            </section>
        <?php endif; ?>
    </main>

    <footer class="rodape">
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($perfil->getNome(), ENT_QUOTES, 'UTF-8') ?> &mdash; <?= htmlspecialchars($perfil->getCurso(), ENT_QUOTES, 'UTF-8') ?></p>
            <p><small>Programação Web I &bull; <?= htmlspecialchars($perfil->getCampus(), ENT_QUOTES, 'UTF-8') ?></small></p>
        </div>
    </footer>
</body>
</html>