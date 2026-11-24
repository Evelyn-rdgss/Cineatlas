<?php
declare(strict_types=1);
require_once __DIR__ . '/conexao.php';

/* ---------- utilidades ---------- */
function formatar_duracao(?int $min): string {
    if (!$min) { return ''; }
    return $min >= 60 ? intdiv($min, 60) . 'h ' . str_pad((string)($min % 60), 2, '0', STR_PAD_LEFT) . 'min' : $min . 'min';
}

/** Plataformas do catálogo brasileiro (pesquisa em 23/11/2026, JustWatch Brasil e AdoroCinema).
 *  A disponibilidade muda com frequência: reconferir de tempos em tempos.
 *  Não depende de tabelas extras no MySQL.
 */
function plataformas_do_filme(string $slug): array {
    static $mapa = [
        // A Última Floresta: Prime Video (aluguel/compra), Apple TV e Google TV segundo matéria de 2026.
        'a-ultima-floresta' => [
            ['nome' => 'Prime Video', 'url' => 'https://www.primevideo.com/detail/0RLT1BP0MJY07F575XKO16BK2H', 'slug' => 'amazon-prime-video', 'modalidade' => 'Aluguel e compra digital'],
            ['nome' => 'Apple TV', 'url' => 'https://tv.apple.com/br/search?term=A%20%C3%9Altima%20Floresta', 'slug' => 'apple-tv', 'modalidade' => 'Aluguel e compra digital'],
            ['nome' => 'Google Play Filmes e TV', 'url' => 'https://play.google.com/store/search?q=A%20%C3%9Altima%20Floresta&c=movies', 'slug' => 'google-play', 'modalidade' => 'Aluguel e compra digital'],
        ],
        'o-menino-que-descobriu-o-vento' => [['nome' => 'Netflix', 'url' => 'https://www.netflix.com/br/', 'slug' => 'netflix', 'modalidade' => 'Assinatura']],
        'diamante-de-sangue' => [
            ['nome' => 'HBO Max', 'url' => 'https://www.hbomax.com/br/pt', 'slug' => 'hbo-max', 'modalidade' => 'Assinatura'],
            ['nome' => 'Prime Video', 'url' => 'https://www.primevideo.com/search?phrase=Diamante%20de%20Sangue', 'slug' => 'amazon-prime-video', 'modalidade' => 'Aluguel e compra digital'],
            ['nome' => 'Apple TV', 'url' => 'https://tv.apple.com/br/search?term=Diamante%20de%20Sangue', 'slug' => 'apple-tv', 'modalidade' => 'Aluguel e compra digital'],
        ],
        // Ilha das Flores: Doc Canal Brasil é um canal adicional dentro do Prime Video.
        'a-ilha-das-flores' => [['nome' => 'Doc Canal Brasil (Amazon Channel)', 'url' => 'https://www.primevideo.com/', 'slug' => 'amazon-prime-video', 'modalidade' => 'Assinatura do canal']],
        'lion-a-longa-estrada-para-casa' => [
            ['nome' => 'Amazon Prime Video', 'url' => 'https://www.primevideo.com/dp/amzn1.dv.gti.6eba6439-de88-f6cb-2ce4-f6bcd13d89e9?autoplay=0&ref_=atv_cf_strg_wb', 'slug' => 'amazon-prime-video', 'modalidade' => 'Assinatura'],
            ['nome' => 'Apple TV', 'url' => 'https://tv.apple.com/br/movie/un-camino-a-casa/umc.cmc.nzus1ck6odvkz8bom9w3zhpp', 'slug' => 'apple-tv', 'modalidade' => 'Aluguel e compra digital'],
        ],
        // WALL-E: Disney+ (assinatura), segundo o JustWatch Brasil em 23/11/2026.
        'wall-e' => [['nome' => 'Disney+', 'url' => 'https://www.disneyplus.com/pt-br', 'slug' => 'disney-plus', 'modalidade' => 'Assinatura']],
        // Terremoto: A Falha de San Andreas: HBO Max (assinatura) e Prime Video (aluguel/compra), segundo o AdoroCinema.
        // Algumas fontes citam também Apple TV e Google Play (aluguel). A disponibilidade varia: reconferir.
        'terremoto-a-falha-de-san-andreas' => [
            ['nome' => 'HBO Max', 'url' => 'https://www.hbomax.com/br/pt/movies/terremoto-a-falha-de-san-andreas/be5a703f-8fb6-4cd3-847b-9791bf557bce', 'slug' => 'hbo-max', 'modalidade' => 'Assinatura'],
            ['nome' => 'Prime Video', 'url' => 'https://www.primevideo.com/search?phrase=Terremoto%20A%20Falha%20de%20San%20Andreas', 'slug' => 'amazon-prime-video', 'modalidade' => 'Aluguel e compra digital'],
            ['nome' => 'Apple TV', 'url' => 'https://tv.apple.com/br/search?term=San%20Andreas', 'slug' => 'apple-tv', 'modalidade' => 'Aluguel digital'],
        ],
        // Nascido em Gaza: matérias brasileiras (2023–2024) o listam na Netflix; JustWatch Brasil não confirmou em nov/2026. Reconferir.
        'nascido-em-gaza' => [['nome' => 'Netflix', 'url' => 'https://www.netflix.com/br/', 'slug' => 'netflix', 'modalidade' => 'Assinatura']],
    ];
    return $mapa[$slug] ?? [];
}

/* ---------- filmes (mesmo formato do antigo array $filmes) ---------- */
function carregar_filmes(bool $somentePublicados = false): array {
    $sql = "
      SELECT f.id, f.slug, f.titulo, f.ano_lancamento, f.diretor, f.duracao_min, f.idioma_original,
             f.capa_url, f.situacao, f.genero_rotulo, f.pais_rotulo,
             t.nome  AS tema,
             c.nome  AS classificacao,
             a.analise AS conceito,
             (SELECT GROUP_CONCAT(g.nome ORDER BY g.nome SEPARATOR ', ')
                FROM filme_generos fg JOIN generos g ON g.id = fg.genero_id WHERE fg.filme_id = f.id) AS generos,
             (SELECT GROUP_CONCAT(p.nome ORDER BY p.nome SEPARATOR ' · ')
                FROM filme_paises fp JOIN paises p ON p.id = fp.pais_id WHERE fp.filme_id = f.id) AS paises,
             (SELECT GROUP_CONCAT(DISTINCT co.nome ORDER BY co.nome SEPARATOR ' ')
                FROM filme_paises fp JOIN paises p ON p.id = fp.pais_id
                JOIN continentes co ON co.id = p.continente_id WHERE fp.filme_id = f.id) AS continente,
             (SELECT GROUP_CONCAT(l.nome ORDER BY l.id SEPARATOR ', ')
                FROM locais_mapa l WHERE l.filme_id = f.id AND l.tipo = 'narrativa') AS locais,
             (SELECT AVG(nota) FROM avaliacoes WHERE filme_id = f.id) AS media
        FROM filmes f
        LEFT JOIN temas_geograficos t          ON t.id = f.tema_principal_id
        LEFT JOIN classificacoes_indicativas c ON c.id = f.classificacao_id
        LEFT JOIN analises_geograficas a       ON a.filme_id = f.id
       " . ($somentePublicados ? "WHERE f.situacao = 'publicado'" : '') . "
       ORDER BY f.id";
    $filmes = [];
    foreach (db()->query($sql) as $r) {
        $filmes[] = [
            'id'           => (int)$r['id'],
            'titulo'       => $r['titulo'],
            'ano'          => (string)$r['ano_lancamento'],
            'pais'         => $r['pais_rotulo'] ?: ($r['paises'] ?? ''),
            'genero'       => $r['genero_rotulo'] ?: ($r['generos'] ?? ''),
            'tema'         => $r['tema'] ?? '',
            'nota'         => $r['media'] !== null ? number_format((float)$r['media'], 1, ',', '') : '—',
            'imagem'       => preg_replace('#^Posters/#', '', (string)$r['capa_url']),
            'conceito'     => $r['conceito'] ?? '',
            'direcao'      => $r['diretor'],
            'duracao'      => formatar_duracao($r['duracao_min'] !== null ? (int)$r['duracao_min'] : null),
            'classificacao'=> $r['classificacao'] ?? '',
            'idioma'       => $r['idioma_original'] ?? '',
            'plataformas'  => plataformas_do_filme((string)$r['slug']),
            'locais'       => $r['locais'] ?? '',
            'continente'   => $r['continente'] ?? '',
            'situacao'     => $r['situacao'],
        ];
    }
    return $filmes;
}

function carregar_situacoes(): array {
    $o = [];
    foreach (db()->query('SELECT id, situacao FROM filmes') as $r) {
        $o[(int)$r['id']] = in_array($r['situacao'], ['rascunho', 'revisao', 'publicado'], true) ? $r['situacao'] : 'rascunho';
    }
    return $o;
}

/* ---------- glossário e conceitos por filme ---------- */
function carregar_glossario(): array {
    $o = [];
    foreach (db()->query('SELECT slug, termo, definicao, exemplo, palavras_chave FROM conceitos_geograficos ORDER BY id') as $r) {
        $o[$r['slug']] = [
            'termo'     => $r['termo'],
            'definicao' => $r['definicao'],
            'exemplo'   => $r['exemplo'] ?? '',
            'palavras'  => array_values(array_filter(array_map('trim', explode(',', (string)$r['palavras_chave'])))),
        ];
    }
    return $o;
}

function carregar_conceitos_filmes(): array {
    $o = [];
    $sql = 'SELECT fc.filme_id, c.slug FROM filme_conceitos fc JOIN conceitos_geograficos c ON c.id = fc.conceito_id ORDER BY fc.filme_id, c.id';
    foreach (db()->query($sql) as $r) { $o[(int)$r['filme_id']][] = $r['slug']; }
    return $o;
}

/* ---------- "Geografia além do filme" ---------- */
function carregar_alem_filme(): array {
    $pdo = db(); $o = [];
    foreach ($pdo->query('SELECT filme_id, o_que_filme_mostra, o_que_fontes_documentam FROM analises_geograficas') as $r) {
        $o[(int)$r['filme_id']] = ['fatos' => [], 'contexto' => [], 'filme' => (string)$r['o_que_filme_mostra'],
                                   'real' => (string)$r['o_que_fontes_documentam'], 'fontes' => []];
    }
    foreach ($pdo->query('SELECT filme_id, texto FROM filme_fatos ORDER BY filme_id, ordem') as $r) {
        $o[(int)$r['filme_id']]['fatos'][] = $r['texto'];
    }
    foreach ($pdo->query('SELECT filme_id, rotulo, texto FROM filme_contextos ORDER BY filme_id, ordem') as $r) {
        $o[(int)$r['filme_id']]['contexto'][$r['rotulo']] = $r['texto'];
    }
    $sql = 'SELECT fr.filme_id, r.titulo, r.url FROM filme_referencias fr JOIN referencias r ON r.id = fr.referencia_id ORDER BY fr.filme_id, r.id';
    foreach ($pdo->query($sql) as $r) { $o[(int)$r['filme_id']]['fontes'][] = [$r['titulo'], (string)$r['url']]; }
    return $o;
}

function carregar_comparacao(): array {
    $o = [];
    foreach (db()->query('SELECT filme_id, eixo_tematico, espaco_representado, contexto_resumo, conflito_central FROM analises_geograficas') as $r) {
        $o[(int)$r['filme_id']] = ['tema' => $r['eixo_tematico'], 'espaco' => $r['espaco_representado'],
                                   'contexto' => $r['contexto_resumo'], 'conflito' => $r['conflito_central']];
    }
    return $o;
}

/* ---------- quiz e guia ---------- */
function carregar_quizzes(): array {
    $pdo = db(); $o = [];
    $perg = $pdo->query('SELECT id, filme_id, pergunta, explicacao FROM quiz_perguntas ORDER BY filme_id, id')->fetchAll();
    $opts = $pdo->prepare('SELECT texto, correta FROM quiz_opcoes WHERE pergunta_id = ? ORDER BY id');
    foreach ($perg as $p) {
        $opts->execute([$p['id']]);
        $textos = []; $certa = 0;
        foreach ($opts->fetchAll() as $i => $op) { $textos[] = $op['texto']; if ((int)$op['correta'] === 1) { $certa = $i; } }
        $o[(int)$p['filme_id']][] = ['pergunta' => $p['pergunta'], 'opcoes' => $textos, 'resposta' => $certa, 'explicacao' => (string)$p['explicacao']];
    }
    return $o;
}

function carregar_guia(int $filmeId): array {
    $st = db()->prepare('SELECT pergunta FROM guia_estudo_perguntas WHERE filme_id = ? ORDER BY ordem');
    $st->execute([$filmeId]);
    return array_column($st->fetchAll(), 'pergunta');
}

/* ---------- acessos e avaliações ---------- */
function registrar_acesso(int $filmeId): void {
    if (!empty($_SESSION['acessos'][$filmeId])) { return; }          // 1 por sessão e filme
    $st = db()->prepare('INSERT INTO acessos_filmes (filme_id, usuario_id) VALUES (?, ?)');
    $st->execute([$filmeId, $_SESSION['usuario_id'] ?? null]);
    $_SESSION['acessos'][$filmeId] = true;
}

/** Formato antigo: [id_do_filme => [usuario => nota]] (usado pelo painel admin). */
function carregar_avaliacoes(): array {
    $o = [];
    foreach (db()->query('SELECT filme_id, usuario_id, nota FROM avaliacoes') as $r) {
        $o[(string)$r['filme_id']][(string)$r['usuario_id']] = (int)$r['nota'];
    }
    return $o;
}

function salvar_avaliacao(int $filmeId, int $estrelas): bool {
    $uid = (int)($_SESSION['usuario_id'] ?? 0);
    if ($uid <= 0 || $estrelas < 1 || $estrelas > 5) { return false; }
    $st = db()->prepare('INSERT INTO avaliacoes (usuario_id, filme_id, nota) VALUES (?,?,?)
                         ON DUPLICATE KEY UPDATE nota = VALUES(nota)');
    return $st->execute([$uid, $filmeId, $estrelas]);
}

/* ---------- contas ---------- */
function autenticar_admin(string $email, string $senha): bool {
    $email = mb_strtolower(trim($email));

    /* 1) Administrador do banco de dados (se a tabela existir neste computador) */
    try {
        $st = db()->prepare('SELECT id, senha_hash FROM administradores WHERE email = ? AND ativo = 1');
        $st->execute([$email]);
        $a = $st->fetch();
        if ($a && password_verify($senha, $a['senha_hash'])) {
            $_SESSION['admin_id'] = (int)$a['id'];
            return true;
        }
    } catch (PDOException $e) {
        // tabela ausente: segue para o administrador embutido
    }

    /* 2) Administrador embutido no código (ADMIN_EMAIL / ADMIN_SENHA_HASH em conexao.php) */
    if (hash_equals(mb_strtolower(ADMIN_EMAIL), $email) && password_verify($senha, ADMIN_SENHA_HASH)) {
        unset($_SESSION['admin_id']);   // sem registro no banco; o log grava admin_id como NULL
        return true;
    }
    return false;
}

/** @return array{0:bool,1:string} [sucesso, mensagem] */
function cadastrar_usuario(string $nome, string $email, string $senha, bool $consentiu): array {
    $nome = trim($nome); $email = mb_strtolower(trim($email));
    if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($senha) < 6) {
        return [false, 'Preencha nome, e-mail válido e senha com ao menos 6 caracteres.'];
    }
    if (!$consentiu) { return [false, 'É necessário aceitar os termos de uso para criar a conta.']; }
    $st = db()->prepare('SELECT id FROM usuarios WHERE email = ?');
    $st->execute([$email]);
    if ($st->fetch()) { return [false, 'Este e-mail já está cadastrado.']; }
    try {
        $st = db()->prepare('INSERT INTO usuarios (nome, email, senha_hash, ativo) VALUES (?,?,?,1)');
        $st->execute([mb_substr($nome, 0, 120), $email, password_hash($senha, PASSWORD_DEFAULT)]);
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') { return [false, 'Este e-mail já está cadastrado.']; }   // duas requisições ao mesmo tempo
        throw $e;
    }
    session_regenerate_id(true);
    $_SESSION['usuario_id'] = (int)db()->lastInsertId();
    $_SESSION['usuario_nome'] = $nome;
    return [true, 'Conta criada com sucesso.'];
}

/** @return array{0:bool,1:string} */
function autenticar_usuario(string $email, string $senha): array {
    $st = db()->prepare('SELECT id, nome, senha_hash FROM usuarios WHERE email = ? AND ativo = 1');
    $st->execute([mb_strtolower(trim($email))]);
    $u = $st->fetch();
    if ($u && password_verify($senha, $u['senha_hash'])) {
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = (int)$u['id'];
        $_SESSION['usuario_nome'] = $u['nome'];
        return [true, 'Login realizado com sucesso.'];
    }
    return [false, 'E-mail ou senha incorretos.'];
}

/* ---------- favoritos ---------- */
function tipo_coluna(string $tabela, string $coluna): string {
    $st = db()->prepare('SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?');
    $st->execute([$tabela, $coluna]);
    $tipo = $st->fetchColumn();
    return is_string($tipo) && $tipo !== '' ? $tipo : 'INT';
}

/** Cria a tabela `favoritos` na primeira vez (com o mesmo tipo de id de usuarios e filmes). */
function garantir_favoritos(): bool {
    static $pronto = null;
    if ($pronto !== null) { return $pronto; }
    try {
        $pdo  = db();
        $cols = 'usuario_id ' . tipo_coluna('usuarios', 'id') . ' NOT NULL, filme_id ' . tipo_coluna('filmes', 'id')
              . ' NOT NULL, criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (usuario_id, filme_id)';
        $fim  = ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
        try {
            $pdo->exec('CREATE TABLE IF NOT EXISTS favoritos (' . $cols
                . ', CONSTRAINT fk_favoritos_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE'
                . ', CONSTRAINT fk_favoritos_filme FOREIGN KEY (filme_id) REFERENCES filmes (id) ON DELETE CASCADE' . $fim);
        } catch (PDOException $e) {
            $pdo->exec('CREATE TABLE IF NOT EXISTS favoritos (' . $cols . $fim);   // sem chaves estrangeiras
        }
        $pronto = true;
    } catch (PDOException $e) {
        error_log('CineAtlas: não foi possível preparar a tabela favoritos: ' . $e->getMessage());
        $pronto = false;
    }
    return $pronto;
}

/** @return int[] ids dos filmes favoritados, do mais recente para o mais antigo */
function ids_favoritos(int $usuarioId): array {
    if (!garantir_favoritos()) { return []; }
    $st = db()->prepare('SELECT filme_id FROM favoritos WHERE usuario_id = ? ORDER BY criado_em DESC, filme_id');
    $st->execute([$usuarioId]);
    return array_map('intval', $st->fetchAll(PDO::FETCH_COLUMN));
}

/** Favorita ou desfavorita. @return ?bool true = agora é favorito, false = removido, null = erro */
function alternar_favorito(int $usuarioId, int $filmeId): ?bool {
    if (!garantir_favoritos()) { return null; }
    try {
        $del = db()->prepare('DELETE FROM favoritos WHERE usuario_id = ? AND filme_id = ?');
        $del->execute([$usuarioId, $filmeId]);
        if ($del->rowCount() > 0) { return false; }
        db()->prepare('INSERT IGNORE INTO favoritos (usuario_id, filme_id) VALUES (?, ?)')->execute([$usuarioId, $filmeId]);
        return true;
    } catch (PDOException $e) {
        return null;
    }
}

/* =====================================================================
   ÁREA ADMINISTRATIVA: editar e excluir filmes
   ===================================================================== */


/* ---------- sugestões da comunidade ---------- */
function enviar_sugestao_filme(int $usuarioId, string $titulo, string $ano, string $pais, string $justificativa): array {
    $titulo = trim($titulo); $pais = trim($pais); $justificativa = trim($justificativa); $ano = trim($ano);
    if ($usuarioId <= 0) return [false, 'Entre na sua conta para enviar uma sugestão.'];
    if ($titulo === '' || mb_strlen($titulo) > 200) return [false, 'Informe um título válido (até 200 caracteres).'];
    if ($ano !== '' && (!ctype_digit($ano) || (int)$ano < 1888 || (int)$ano > ((int)date('Y') + 5))) return [false, 'Informe um ano de lançamento válido.'];
    if ($pais !== '' && mb_strlen($pais) > 100) return [false, 'O país deve ter até 100 caracteres.'];
    if (mb_strlen($justificativa) > 5000) return [false, 'A justificativa deve ter até 5.000 caracteres.'];
    $st = db()->prepare("INSERT INTO sugestoes_filmes (usuario_id, titulo, ano, pais, justificativa) VALUES (?, ?, ?, ?, ?)");
    $st->execute([$usuarioId, $titulo, $ano === '' ? null : (int)$ano, $pais ?: null, $justificativa ?: null]);
    return [true, 'Sugestão enviada! A equipe irá analisá-la.'];
}

function listar_sugestoes_filmes(): array {
    $sql = 'SELECT s.*, u.nome AS usuario_nome, u.email AS usuario_email FROM sugestoes_filmes s JOIN usuarios u ON u.id = s.usuario_id ORDER BY FIELD(s.status, \'pendente\', \'aprovada\', \'recusada\'), s.criado_em DESC';
    return db()->query($sql)->fetchAll();
}

function moderar_sugestao_filme(int $id, string $status, string $resposta): bool {
    if ($id <= 0 || !in_array($status, ['aprovada', 'recusada', 'pendente'], true)) return false;
    $st = db()->prepare('UPDATE sugestoes_filmes SET status = ?, resposta_equipe = ? WHERE id = ?');
    return $st->execute([$status, trim($resposta) ?: null, $id]);
}

function listar_sugestoes_usuario(int $usuarioId): array {
    $st = db()->prepare('SELECT titulo, ano, pais, justificativa, status, resposta_equipe, criado_em FROM sugestoes_filmes WHERE usuario_id = ? ORDER BY criado_em DESC');
    $st->execute([$usuarioId]);
    return $st->fetchAll();
}

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(32)); }
    return $_SESSION['csrf'];
}
function csrf_valido(string $token): bool {
    return !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}

function registrar_log_admin(string $acao, string $entidade, ?int $id, string $detalhe = ''): void {
    try {
        $st = db()->prepare('INSERT INTO log_admin (admin_id, acao, entidade, entidade_id, detalhe) VALUES (?,?,?,?,?)');
        $st->execute([$_SESSION['admin_id'] ?? null, $acao, $entidade, $id, mb_substr($detalhe, 0, 500)]);
    } catch (PDOException $e) {
        error_log('CineAtlas: falha ao gravar log_admin: ' . $e->getMessage());   // a ação em si já foi salva
    }
}

/** Listas usadas nos <select> do formulário de edição. */
function listas_apoio_filme(): array {
    $pdo = db();
    return [
        'classificacoes' => $pdo->query('SELECT id, nome FROM classificacoes_indicativas ORDER BY idade_minima')->fetchAll(),
        'periodos'       => $pdo->query('SELECT id, nome FROM periodos_historicos ORDER BY ordem')->fetchAll(),
        'temas'          => $pdo->query('SELECT id, nome FROM temas_geograficos ORDER BY nome')->fetchAll(),
    ];
}

/** Filme + análise para o formulário (ou null se não existir). */
function obter_filme_admin(int $id): ?array {
    $st = db()->prepare("
      SELECT f.*, a.eixo_tematico, a.analise, a.cena_relevante, a.importancia_cena,
             a.conceito_geografico, a.revisado
        FROM filmes f
        LEFT JOIN analises_geograficas a ON a.filme_id = f.id
       WHERE f.id = ?");
    $st->execute([$id]);
    return $st->fetch() ?: null;
}

/** @return array{0:bool,1:string} [sucesso, mensagem] */
function atualizar_filme(int $id, array $p): array {
    $txt  = fn(string $k): string => trim((string)($p[$k] ?? ''));
    $nulo = fn(string $k): ?string => ($v = trim((string)($p[$k] ?? ''))) === '' ? null : $v;
    $idn  = fn(string $k): ?int => ($v = (int)($p[$k] ?? 0)) > 0 ? $v : null;
    $flag = fn(string $k): int => !empty($p[$k]) ? 1 : 0;

    $titulo = $txt('titulo'); $diretor = $txt('diretor'); $sinopse = $txt('sinopse');
    $ano = (int)$txt('ano_lancamento');
    $situacao = $txt('situacao');
    $eixo = $txt('eixo_tematico'); $analise = $txt('analise');

    if ($titulo === '' || $diretor === '' || $sinopse === '') { return [false, 'Título, diretor e sinopse são obrigatórios.']; }
    if ($ano < 1888 || $ano > 2100) { return [false, 'Ano de lançamento inválido.']; }
    if (!in_array($situacao, ['rascunho', 'revisao', 'publicado'], true)) { return [false, 'Situação inválida.']; }
    if ($eixo === '' || $analise === '') { return [false, 'Informe o eixo temático e o texto da análise geográfica.']; }
    $capa = $nulo('capa_url');
    if ($capa !== null && !preg_match('#^Posters/[\w\-. ()]+$#u', $capa)) {
        return [false, 'Capa: use um caminho como Posters/arquivo.jpg ou envie uma imagem.'];
    }
    // Envio de imagem de capa (substitui o caminho acima)
    if (!empty($_FILES['capa_arquivo']['name'])) {
        [$okUp, $resUp] = salvar_capa_enviada($_FILES['capa_arquivo'], $id, $titulo, $txt('capa_fonte'));
        if (!$okUp) { return [false, $resUp]; }
        $capa = $resUp;
    }
    if ($situacao === 'publicado') {   // RNF013 e RNF014
        if (empty($p['revisado'])) { return [false, 'Para publicar, marque a análise como revisada.']; }
        if (!$nulo('sinopse_fonte') && empty($p['sinopse_autoral'])) {
            return [false, 'Para publicar, informe a fonte da sinopse ou marque-a como produção autoral.'];
        }
    }
    $duracao = (int)$txt('duracao_min');

    $pdo = db();
    try {
        $pdo->beginTransaction();
        $st = $pdo->prepare("UPDATE filmes SET titulo=?, titulo_original=?, ano_lancamento=?, diretor=?, duracao_min=?,
              classificacao_id=?, idioma_original=?, periodo_id=?, tema_principal_id=?, genero_rotulo=?, pais_rotulo=?,
              capa_url=?, capa_alt=?, capa_fonte=?, sinopse=?, sinopse_fonte=?, sinopse_autoral=?,
              tem_audiodescricao=?, tem_libras=?, destaque=?, situacao=? WHERE id=?");
        $st->execute([$titulo, $nulo('titulo_original'), $ano, $diretor, $duracao > 0 ? $duracao : null,
            $idn('classificacao_id'), $nulo('idioma_original'), $idn('periodo_id'), $idn('tema_principal_id'),
            $nulo('genero_rotulo'), $nulo('pais_rotulo'), $capa, $nulo('capa_alt'), $nulo('capa_fonte'),
            $sinopse, $nulo('sinopse_fonte'), $flag('sinopse_autoral'),
            $flag('tem_audiodescricao'), $flag('tem_libras'), $flag('destaque'), $situacao, $id]);

        $revisado = $flag('revisado');
        $st = $pdo->prepare("INSERT INTO analises_geograficas
              (filme_id, eixo_tematico, analise, cena_relevante, importancia_cena, conceito_geografico, revisado, revisado_por, revisado_em)
              VALUES (?,?,?,?,?,?,?,?,?)
              ON DUPLICATE KEY UPDATE eixo_tematico=VALUES(eixo_tematico), analise=VALUES(analise),
                cena_relevante=VALUES(cena_relevante), importancia_cena=VALUES(importancia_cena),
                conceito_geografico=VALUES(conceito_geografico), revisado=VALUES(revisado),
                revisado_por=VALUES(revisado_por), revisado_em=VALUES(revisado_em)");
        $st->execute([$id, $eixo, $analise, $nulo('cena_relevante'), $nulo('importancia_cena'), $nulo('conceito_geografico'),
            $revisado, $revisado ? ($_SESSION['admin_id'] ?? null) : null, $revisado ? date('Y-m-d H:i:s') : null]);
        $pdo->commit();
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) { $pdo->rollBack(); }
        return [false, 'Não foi possível salvar o filme. Tente novamente.'];
    }
    registrar_log_admin('editar', 'filme', $id, $titulo);
    return [true, 'Filme atualizado com sucesso.'];
}

/** Exclui o filme; análise, trailer, mapa, quiz, avaliações etc. saem junto (ON DELETE CASCADE). */
function excluir_filme(int $id): array {
    $st = db()->prepare('SELECT titulo FROM filmes WHERE id = ?');
    $st->execute([$id]);
    $titulo = $st->fetchColumn();
    if ($titulo === false) { return [false, 'Filme não encontrado.']; }
    try {
        db()->prepare('DELETE FROM filmes WHERE id = ?')->execute([$id]);
    } catch (PDOException $e) {
        return [false, 'Não foi possível excluir o filme.'];
    }
    registrar_log_admin('excluir', 'filme', $id, (string)$titulo);
    return [true, 'Filme “' . $titulo . '” excluído.'];
}


/** Traduz os códigos de erro de upload do PHP em mensagens claras. */
function mensagem_erro_upload(int $codigo): string {
    switch ($codigo) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE: return 'A imagem é maior que o limite permitido (3 MB).';
        case UPLOAD_ERR_PARTIAL:   return 'O envio da imagem foi interrompido. Tente novamente.';
        case UPLOAD_ERR_NO_FILE:   return 'Nenhuma imagem foi selecionada.';
        case UPLOAD_ERR_NO_TMP_DIR:
        case UPLOAD_ERR_CANT_WRITE:
        case UPLOAD_ERR_EXTENSION: return 'O servidor não conseguiu receber a imagem. Verifique as permissões da pasta temporária.';
        default:                   return 'Não foi possível enviar a imagem.';
    }
}

/** Salva a capa enviada em Posters/. @return array{0:bool,1:string} [ok, caminho ou mensagem de erro] */
function salvar_capa_enviada(array $arq, int $filmeId, string $titulo, string $fonte): array {
    $erro = (int)($arq['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($erro !== UPLOAD_ERR_OK) { return [false, mensagem_erro_upload($erro)]; }
    if ($fonte === '') { return [false, 'Informe a fonte da imagem (ex.: distribuidora ou site oficial).']; }
    if (!is_uploaded_file((string)$arq['tmp_name'])) { return [false, 'Arquivo de imagem inválido.']; }
    if ((int)$arq['size'] > 3 * 1024 * 1024) { return [false, 'A imagem deve ter no máximo 3 MB.']; }
    $info = @getimagesize($arq['tmp_name']);
    $tipos = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
    if (!$info || !isset($tipos[$info[2]])) { return [false, 'Envie uma imagem JPG, PNG ou WebP.']; }
    $pasta = __DIR__ . '/Posters';
    if (!is_dir($pasta) && !@mkdir($pasta, 0755, true)) { return [false, 'Não foi possível criar a pasta Posters.']; }
    if (!is_writable($pasta)) { return [false, 'A pasta Posters não tem permissão de gravação no servidor.']; }
    $ascii = function_exists('iconv') ? @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $titulo) : false;
    $base = strtolower(trim((string)preg_replace('/[^a-z0-9]+/i', '-', $ascii !== false && $ascii !== '' ? $ascii : 'filme'), '-'));
    if ($base === '') { $base = 'filme'; }
    // sufixo aleatório: evita que o navegador mostre a capa antiga em cache
    $nome = $base . '-' . $filmeId . '-' . bin2hex(random_bytes(3)) . '.' . $tipos[$info[2]];
    if (!move_uploaded_file($arq['tmp_name'], $pasta . '/' . $nome)) { return [false, 'Falha ao gravar a imagem no servidor.']; }
    return [true, 'Posters/' . $nome];
}


/** Cadastra um novo filme diretamente pelo painel administrativo. */
function cadastrar_filme(array $p): array {
    $txt = fn(string $k): string => trim((string)($p[$k] ?? ''));
    $nulo = fn(string $k): ?string => ($v = trim((string)($p[$k] ?? ''))) === '' ? null : $v;
    $idn = fn(string $k): ?int => ($v = (int)($p[$k] ?? 0)) > 0 ? $v : null;
    $flag = fn(string $k): int => !empty($p[$k]) ? 1 : 0;
    $titulo=$txt('titulo'); $diretor=$txt('diretor'); $sinopse=$txt('sinopse');
    $ano=(int)$txt('ano_lancamento'); $eixo=$txt('eixo_tematico'); $analise=$txt('analise');
    $situacao=$txt('situacao') ?: 'rascunho';
    if ($titulo==='' || $diretor==='' || $sinopse==='') return [false,'Título, diretor e sinopse são obrigatórios.'];
    if ($ano<1888 || $ano>2100) return [false,'Ano de lançamento inválido.'];
    if ($eixo==='' || $analise==='') return [false,'Informe o eixo temático e a análise geográfica.'];
    if (!in_array($situacao,['rascunho','revisao','publicado'],true)) return [false,'Situação inválida.'];
    if ($situacao==='publicado' && (empty($p['revisado']) || (!$nulo('sinopse_fonte') && empty($p['sinopse_autoral'])))) return [false,'Para publicar, marque a análise como revisada e informe a fonte da sinopse ou marque produção autoral.'];
    $capa=$nulo('capa_url');
    if ($capa!==null && !preg_match('#^Posters/[\w\-. ()]+$#u',$capa)) return [false,'Use um caminho de capa como Posters/arquivo.jpg.'];
    $pdo=db();
    try {
        $base=function_exists('iconv') ? @iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$titulo) : $titulo;
        $slug=strtolower(trim((string)preg_replace('/[^a-z0-9]+/i','-',(string)$base),'-')) ?: 'filme';
        $st=$pdo->prepare('SELECT COUNT(*) FROM filmes WHERE slug = ?'); $candidato=$slug; $n=2;
        while (true) { $st->execute([$candidato]); if (!(int)$st->fetchColumn()) break; $candidato=$slug.'-'.$n++; }
        $pdo->beginTransaction();
        $st=$pdo->prepare("INSERT INTO filmes (slug,titulo,titulo_original,ano_lancamento,diretor,duracao_min,classificacao_id,idioma_original,periodo_id,tema_principal_id,capa_url,capa_alt,capa_fonte,sinopse,sinopse_fonte,sinopse_autoral,tem_audiodescricao,tem_libras,destaque,situacao,criado_por) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $st->execute([$candidato,$titulo,$nulo('titulo_original'),$ano,$diretor,(int)$txt('duracao_min') ?: null,$idn('classificacao_id'),$nulo('idioma_original'),$idn('periodo_id'),$idn('tema_principal_id'),$capa,$nulo('capa_alt'),$nulo('capa_fonte'),$sinopse,$nulo('sinopse_fonte'),$flag('sinopse_autoral'),$flag('tem_audiodescricao'),$flag('tem_libras'),$flag('destaque'),$situacao,$_SESSION['admin_id'] ?? null]);
        $id=(int)$pdo->lastInsertId();
        $st=$pdo->prepare('INSERT INTO analises_geograficas (filme_id,eixo_tematico,analise,cena_relevante,importancia_cena,conceito_geografico,revisado,revisado_por,revisado_em) VALUES (?,?,?,?,?,?,?,?,?)');
        $rev=$flag('revisado');
        $st->execute([$id,$eixo,$analise,$nulo('cena_relevante') ?? '',$nulo('importancia_cena') ?? '',$nulo('conceito_geografico') ?? '',$rev,$rev?($_SESSION['admin_id']??null):null,$rev?date('Y-m-d H:i:s'):null]);
        $pdo->commit();
        if (!empty($_FILES['capa_arquivo']['name'])) {
            [$okUp,$resUp]=salvar_capa_enviada($_FILES['capa_arquivo'],$id,$titulo,$txt('capa_fonte'));
            if ($okUp) { $up=$pdo->prepare('UPDATE filmes SET capa_url=? WHERE id=?'); $up->execute([$resUp,$id]); }
            else { return [true,'Filme cadastrado, mas a capa não foi enviada: '.$resUp]; }
        }
        registrar_log_admin('cadastrar','filme',$id,$titulo);
        return [true,'Filme cadastrado com sucesso.'];
    } catch (PDOException $e) { if ($pdo->inTransaction()) $pdo->rollBack(); error_log('CineAtlas cadastro: '.$e->getMessage()); return [false,'Não foi possível cadastrar o filme. Verifique os dados e tente novamente.']; }
}

/** Muda só a situação editorial. Publicar = autorizar: marca a análise como revisada. */
function alterar_situacao_filme(int $id, string $situacao): array {
    if (!in_array($situacao, ['rascunho', 'revisao', 'publicado'], true)) { return [false, 'Situação inválida.']; }
    $f = obter_filme_admin($id);
    if (!$f) { return [false, 'Filme não encontrado.']; }
    $pdo = db();
    if ($situacao === 'publicado') {
        if (empty($f['analise'])) { return [false, 'Cadastre a análise geográfica antes de publicar.']; }
        if (empty($f['sinopse_fonte']) && empty($f['sinopse_autoral'])) {
            return [false, 'Informe a fonte da sinopse (ou marque como autoral) em Editar antes de publicar.'];
        }
    }
    try {
        $pdo->beginTransaction();
        $pdo->prepare('UPDATE filmes SET situacao = ? WHERE id = ?')->execute([$situacao, $id]);
        if ($situacao === 'publicado') {
            $pdo->prepare('UPDATE analises_geograficas SET revisado = 1, revisado_por = ?, revisado_em = ? WHERE filme_id = ?')
                ->execute([$_SESSION['admin_id'] ?? null, date('Y-m-d H:i:s'), $id]);
        } else {
            $pdo->prepare('UPDATE analises_geograficas SET revisado = 0, revisado_por = NULL, revisado_em = NULL WHERE filme_id = ?')
                ->execute([$id]);
        }
        $pdo->commit();
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) { $pdo->rollBack(); }
        return [false, 'Não foi possível alterar a situação.'];
    }
    $rot = ['rascunho' => 'não revisado', 'revisao' => 'em revisão', 'publicado' => 'publicado'][$situacao];
    registrar_log_admin('editar', 'filme', $id, $f['titulo'] . ' → ' . $rot);
    return [true, '“' . $f['titulo'] . '”: ' . ($situacao === 'publicado' ? 'publicação autorizada.' : 'marcado como ' . $rot . '.')];
}