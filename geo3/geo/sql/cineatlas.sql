-- =====================================================================
-- CineAtlas: Cinema e Geografia  |  Banco de dados (MySQL 8 / MariaDB 10.5+)
-- Cobre RF001–RF030 e RNF001–RNF017 (US01–US25)
-- =====================================================================
USE if0_43517900_cineatlas;
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP VIEW  IF EXISTS vw_catalogo, vw_ranking_avaliacao, vw_ranking_acessos;
DROP TABLE IF EXISTS
log_admin, sugestoes_filmes, favoritos, avaliacoes, acessos_filmes,
filme_referencias, referencias, categorias_referencia,
quiz_opcoes, quiz_perguntas, guia_estudo_perguntas, locais_mapa, trailers,
analises_geograficas, filme_conceitos, filme_temas, filme_paises, filme_generos,
filmes, conceitos_geograficos, temas_geograficos, paises, continentes,
generos, periodos_historicos, classificacoes_indicativas,
ppi_etapas, ppi_disciplinas, ppi_projeto, professores, integrantes_equipe,
usuarios, administradores;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
-- 1. CONTAS (RF027, RF029, RF030, RNF011, RNF016)
-- =====================================================================
CREATE TABLE administradores (
id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
nome        VARCHAR(120) NOT NULL,
email       VARCHAR(190) NOT NULL UNIQUE,
senha_hash  VARCHAR(255) NOT NULL,
ativo       TINYINT(1)   NOT NULL DEFAULT 1,
criado_em   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE usuarios (
id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
nome        VARCHAR(120) NOT NULL,
email       VARCHAR(190) NOT NULL UNIQUE,
senha_hash  VARCHAR(255) NOT NULL,
ativo       TINYINT(1)   NOT NULL DEFAULT 1,
criado_em   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================================
-- 2. TABELAS DE APOIO (filtros: RF002)
-- =====================================================================
CREATE TABLE continentes (
id    TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
nome  VARCHAR(40) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE paises (
id            SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
nome          VARCHAR(100) NOT NULL UNIQUE,
codigo_iso    CHAR(2) NULL UNIQUE,
continente_id TINYINT UNSIGNED NOT NULL,
latitude      DECIMAL(9,6) NULL,
longitude     DECIMAL(9,6) NULL,
FOREIGN KEY (continente_id) REFERENCES continentes(id)
) ENGINE=InnoDB;

CREATE TABLE generos (
id    SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
nome  VARCHAR(60) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE periodos_historicos (
id    SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
nome  VARCHAR(80) NOT NULL UNIQUE,
ordem TINYINT UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE classificacoes_indicativas (
id    TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
nome  VARCHAR(20) NOT NULL UNIQUE,
idade_minima TINYINT UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE temas_geograficos (
id         SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
nome       VARCHAR(100) NOT NULL UNIQUE,
slug       VARCHAR(120) NOT NULL UNIQUE,
descricao  TEXT NULL,
destaque   TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE conceitos_geograficos (
id          SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
slug        VARCHAR(60)  NOT NULL UNIQUE,
termo       VARCHAR(100) NOT NULL,
definicao   TEXT NOT NULL,
exemplo     TEXT NULL,
palavras_chave VARCHAR(300) NULL
) ENGINE=InnoDB;

-- =====================================================================
-- 3. FILMES (RF003–RF007, RF017, RNF012–RNF015)
-- =====================================================================
CREATE TABLE filmes (
id                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
slug               VARCHAR(160) NOT NULL UNIQUE,
titulo             VARCHAR(200) NOT NULL,
titulo_original    VARCHAR(200) NULL,
ano_lancamento     SMALLINT UNSIGNED NOT NULL,
diretor            VARCHAR(200) NOT NULL,
duracao_min        SMALLINT UNSIGNED NULL,
classificacao_id   TINYINT UNSIGNED NULL,
idioma_original    VARCHAR(60) NULL,
periodo_id         SMALLINT UNSIGNED NULL,
tema_principal_id  SMALLINT UNSIGNED NULL,
capa_url           VARCHAR(500) NULL,
capa_alt           VARCHAR(300) NULL,
capa_fonte         VARCHAR(300) NULL,
sinopse            TEXT NOT NULL,
sinopse_fonte      VARCHAR(300) NULL,
sinopse_autoral    TINYINT(1) NOT NULL DEFAULT 0,
tem_audiodescricao TINYINT(1) NOT NULL DEFAULT 0,
tem_libras         TINYINT(1) NOT NULL DEFAULT 0,
destaque           TINYINT(1) NOT NULL DEFAULT 0,
situacao           ENUM('rascunho','revisao','publicado') NOT NULL DEFAULT 'rascunho',
criado_por         INT UNSIGNED NULL,
criado_em          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
atualizado_em      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
FOREIGN KEY (classificacao_id)  REFERENCES classificacoes_indicativas(id) ON DELETE SET NULL,
FOREIGN KEY (periodo_id)        REFERENCES periodos_historicos(id)        ON DELETE SET NULL,
FOREIGN KEY (tema_principal_id) REFERENCES temas_geograficos(id)          ON DELETE SET NULL,
FOREIGN KEY (criado_por)        REFERENCES administradores(id)            ON DELETE SET NULL,
CONSTRAINT ck_filme_ano CHECK (ano_lancamento BETWEEN 1888 AND 2100),
INDEX idx_filme_titulo (titulo),
INDEX idx_filme_ano (ano_lancamento),
INDEX idx_filme_situacao (situacao)
) ENGINE=InnoDB;

CREATE TABLE filme_generos (
filme_id  INT UNSIGNED NOT NULL,
genero_id SMALLINT UNSIGNED NOT NULL,
PRIMARY KEY (filme_id, genero_id),
FOREIGN KEY (filme_id)  REFERENCES filmes(id)  ON DELETE CASCADE,
FOREIGN KEY (genero_id) REFERENCES generos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE filme_paises (
filme_id INT UNSIGNED NOT NULL,
pais_id  SMALLINT UNSIGNED NOT NULL,
PRIMARY KEY (filme_id, pais_id),
FOREIGN KEY (filme_id) REFERENCES filmes(id) ON DELETE CASCADE,
FOREIGN KEY (pais_id)  REFERENCES paises(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE filme_temas (
filme_id INT UNSIGNED NOT NULL,
tema_id  SMALLINT UNSIGNED NOT NULL,
PRIMARY KEY (filme_id, tema_id),
FOREIGN KEY (filme_id) REFERENCES filmes(id) ON DELETE CASCADE,
FOREIGN KEY (tema_id)  REFERENCES temas_geograficos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE filme_conceitos (
filme_id    INT UNSIGNED NOT NULL,
conceito_id SMALLINT UNSIGNED NOT NULL,
PRIMARY KEY (filme_id, conceito_id),
FOREIGN KEY (filme_id)    REFERENCES filmes(id) ON DELETE CASCADE,
FOREIGN KEY (conceito_id) REFERENCES conceitos_geograficos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================================
-- 4. ANÁLISE GEOGRÁFICA
-- =====================================================================
CREATE TABLE analises_geograficas (
id                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
filme_id           INT UNSIGNED NOT NULL UNIQUE,
eixo_tematico      VARCHAR(200) NOT NULL,
analise            MEDIUMTEXT NOT NULL,
cena_relevante     TEXT NOT NULL,
importancia_cena   TEXT NOT NULL,
conceito_geografico VARCHAR(200) NOT NULL,
autores            VARCHAR(300) NULL,
revisado           TINYINT(1) NOT NULL DEFAULT 0,
revisado_por       INT UNSIGNED NULL,
revisado_em        DATETIME NULL,
atualizado_em      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
FOREIGN KEY (filme_id)     REFERENCES filmes(id) ON DELETE CASCADE,
FOREIGN KEY (revisado_por) REFERENCES administradores(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =====================================================================
-- 5. TRAILER
-- =====================================================================
CREATE TABLE trailers (
id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
filme_id          INT UNSIGNED NOT NULL,
url               VARCHAR(500) NOT NULL,
url_embed         VARCHAR(500) NULL,
plataforma        VARCHAR(40)  NOT NULL DEFAULT 'YouTube',
canal_oficial     VARCHAR(150) NOT NULL,
possui_legenda    TINYINT(1) NOT NULL DEFAULT 0,
idioma_legenda    VARCHAR(40) NULL,
atualizado_em     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
FOREIGN KEY (filme_id) REFERENCES filmes(id) ON DELETE CASCADE,
INDEX idx_trailer_filme (filme_id)
) ENGINE=InnoDB;

-- =====================================================================
-- 6. MAPAS
-- =====================================================================
CREATE TABLE locais_mapa (
id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
filme_id    INT UNSIGNED NOT NULL,
tipo        ENUM('origem','narrativa','filmagem','territorio') NOT NULL,
nome        VARCHAR(150) NOT NULL,
pais_id     SMALLINT UNSIGNED NULL,
latitude    DECIMAL(9,6) NOT NULL,
longitude   DECIMAL(9,6) NOT NULL,
descricao   TEXT NULL,
FOREIGN KEY (filme_id) REFERENCES filmes(id) ON DELETE CASCADE,
FOREIGN KEY (pais_id)  REFERENCES paises(id) ON DELETE SET NULL,
CONSTRAINT ck_lat CHECK (latitude  BETWEEN -90  AND 90),
CONSTRAINT ck_lng CHECK (longitude BETWEEN -180 AND 180),
INDEX idx_mapa_filme (filme_id),
INDEX idx_mapa_tipo (tipo)
) ENGINE=InnoDB;

-- =====================================================================
-- 7. GUIA DE ESTUDO e QUIZ
-- =====================================================================
CREATE TABLE guia_estudo_perguntas (
id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
filme_id  INT UNSIGNED NOT NULL,
ordem     TINYINT UNSIGNED NOT NULL DEFAULT 1,
pergunta  TEXT NOT NULL,
FOREIGN KEY (filme_id) REFERENCES filmes(id) ON DELETE CASCADE,
UNIQUE KEY uq_guia_ordem (filme_id, ordem)
) ENGINE=InnoDB;

CREATE TABLE quiz_perguntas (
id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
filme_id   INT UNSIGNED NOT NULL,
pergunta   TEXT NOT NULL,
explicacao TEXT NULL,
FOREIGN KEY (filme_id) REFERENCES filmes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE quiz_opcoes (
id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
pergunta_id INT UNSIGNED NOT NULL,
texto       VARCHAR(400) NOT NULL,
correta     TINYINT(1) NOT NULL DEFAULT 0,
FOREIGN KEY (pergunta_id) REFERENCES quiz_perguntas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================================
-- 8. INTERAÇÃO DO USUÁRIO
-- =====================================================================
CREATE TABLE avaliacoes (
id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
usuario_id INT UNSIGNED NOT NULL,
filme_id   INT UNSIGNED NOT NULL,
nota       TINYINT UNSIGNED NOT NULL,
criado_em  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
FOREIGN KEY (filme_id)   REFERENCES filmes(id)   ON DELETE CASCADE,
UNIQUE KEY uq_avaliacao (usuario_id, filme_id),
CONSTRAINT ck_nota CHECK (nota BETWEEN 1 AND 5)
) ENGINE=InnoDB;

CREATE TABLE favoritos (
usuario_id INT UNSIGNED NOT NULL,
filme_id   INT UNSIGNED NOT NULL,
criado_em  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (usuario_id, filme_id),
FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
FOREIGN KEY (filme_id)   REFERENCES filmes(id)   ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE acessos_filmes (
id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
filme_id   INT UNSIGNED NOT NULL,
usuario_id INT UNSIGNED NULL,
acessado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (filme_id)   REFERENCES filmes(id)   ON DELETE CASCADE,
FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
INDEX idx_acesso_filme (filme_id, acessado_em)
) ENGINE=InnoDB;

CREATE TABLE sugestoes_filmes (
id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
usuario_id  INT UNSIGNED NOT NULL,
titulo      VARCHAR(200) NOT NULL,
ano         SMALLINT UNSIGNED NULL,
pais        VARCHAR(100) NULL,
justificativa TEXT NULL,
status      ENUM('pendente','aprovada','recusada') NOT NULL DEFAULT 'pendente',
resposta_equipe TEXT NULL,
criado_em   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================================
-- 9. REFERÊNCIAS E FONTES
-- =====================================================================
CREATE TABLE categorias_referencia (
id    TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
nome  VARCHAR(60) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE referencias (
id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
categoria_id TINYINT UNSIGNED NOT NULL,
titulo       VARCHAR(300) NOT NULL,
autores      VARCHAR(300) NULL,
ano          SMALLINT UNSIGNED NULL,
url          VARCHAR(500) NULL,
data_acesso  DATE NULL,
observacao   TEXT NULL,
FOREIGN KEY (categoria_id) REFERENCES categorias_referencia(id)
) ENGINE=InnoDB;

CREATE TABLE filme_referencias (
filme_id      INT UNSIGNED NOT NULL,
referencia_id INT UNSIGNED NOT NULL,
PRIMARY KEY (filme_id, referencia_id),
FOREIGN KEY (filme_id)      REFERENCES filmes(id)      ON DELETE CASCADE,
FOREIGN KEY (referencia_id) REFERENCES referencias(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================================
-- 10. PÁGINAS INSTITUCIONAIS: PPI e EQUIPE
-- =====================================================================
CREATE TABLE ppi_projeto (
id               TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
titulo           VARCHAR(200) NOT NULL,
objetivo_geral   TEXT NOT NULL,
descricao        TEXT NOT NULL,
relacao_geo_info TEXT NULL,
importancia_integracao TEXT NULL
) ENGINE=InnoDB;

CREATE TABLE ppi_disciplinas (
id           SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
nome         VARCHAR(100) NOT NULL,
contribuicao TEXT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE ppi_etapas (
id        SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
ordem     TINYINT UNSIGNED NOT NULL,
titulo    VARCHAR(150) NOT NULL,
descricao TEXT NULL
) ENGINE=InnoDB;

CREATE TABLE professores (
id            SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
nome          VARCHAR(120) NOT NULL,
disciplina_id SMALLINT UNSIGNED NULL,
FOREIGN KEY (disciplina_id) REFERENCES ppi_disciplinas(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE integrantes_equipe (
id      SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
nome    VARCHAR(120) NOT NULL,
turma   VARCHAR(40)  NOT NULL,
funcao  VARCHAR(200) NOT NULL,
ordem   TINYINT UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB;

-- =====================================================================
-- 11. AUDITORIA DA ÁREA ADMINISTRATIVA
-- =====================================================================
CREATE TABLE log_admin (
id        BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
admin_id  INT UNSIGNED NULL,
acao      ENUM('criar','editar','excluir','login') NOT NULL,
entidade  VARCHAR(60) NOT NULL,
entidade_id INT UNSIGNED NULL,
detalhe   VARCHAR(500) NULL,
criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (admin_id) REFERENCES administradores(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =====================================================================
-- VIEWS
-- =====================================================================
CREATE VIEW vw_ranking_avaliacao AS
SELECT f.id AS filme_id, f.titulo,
ROUND(AVG(a.nota), 2) AS media_nota,
COUNT(a.id)           AS total_avaliacoes
FROM filmes f
JOIN avaliacoes a ON a.filme_id = f.id
WHERE f.situacao = 'publicado'
GROUP BY f.id, f.titulo
ORDER BY media_nota DESC, total_avaliacoes DESC;

CREATE VIEW vw_ranking_acessos AS
SELECT f.id AS filme_id, f.titulo, COUNT(ac.id) AS total_acessos
FROM filmes f
LEFT JOIN acessos_filmes ac ON ac.filme_id = f.id
WHERE f.situacao = 'publicado'
GROUP BY f.id, f.titulo
ORDER BY total_acessos DESC;

CREATE VIEW vw_catalogo AS
SELECT f.id, f.slug, f.titulo, f.ano_lancamento, f.capa_url, f.capa_alt,
t.nome AS tema_principal,
c.nome AS classificacao,
p.nome AS periodo,
(SELECT GROUP_CONCAT(g.nome ORDER BY g.nome SEPARATOR ', ')
FROM filme_generos fg JOIN generos g ON g.id = fg.genero_id
WHERE fg.filme_id = f.id) AS generos,
(SELECT GROUP_CONCAT(pa.nome ORDER BY pa.nome SEPARATOR ', ')
FROM filme_paises fp JOIN paises pa ON pa.id = fp.pais_id
WHERE fp.filme_id = f.id) AS paises,
(SELECT GROUP_CONCAT(DISTINCT co.nome ORDER BY co.nome SEPARATOR ', ')
FROM filme_paises fp JOIN paises pa ON pa.id = fp.pais_id
JOIN continentes co ON co.id = pa.continente_id
WHERE fp.filme_id = f.id) AS continentes,
(SELECT ROUND(AVG(nota),2) FROM avaliacoes WHERE filme_id = f.id) AS media_nota
FROM filmes f
LEFT JOIN temas_geograficos t          ON t.id = f.tema_principal_id
LEFT JOIN classificacoes_indicativas c ON c.id = f.classificacao_id
LEFT JOIN periodos_historicos p        ON p.id = f.periodo_id
WHERE f.situacao = 'publicado';

-- =====================================================================
-- DADOS INICIAIS (tabelas de apoio)
-- =====================================================================
INSERT INTO continentes (nome) VALUES
('África'),('América do Norte'),('América Central'),('América do Sul'),
('Ásia'),('Europa'),('Oceania'),('Antártida');

INSERT INTO classificacoes_indicativas (nome, idade_minima) VALUES
('Livre',0),('10',10),('12',12),('14',14),('16',16),('18',18);

INSERT INTO periodos_historicos (nome, ordem) VALUES
('Antiguidade',1),('Idade Média',2),('Idade Moderna',3),
('Século XIX',4),('Primeira metade do século XX',5),
('Segunda metade do século XX',6),('Século XXI / Contemporâneo',7);

INSERT INTO generos (nome) VALUES
('Drama'),('Documentário'),('Ficção científica'),('Aventura'),('Animação'),
('Curta-metragem'),('Biografia'),('Guerra'),('Suspense'),('Comédia'),('Romance');

INSERT INTO categorias_referencia (nome) VALUES
('Filmes'),('Livros'),('Artigos'),('Sites'),('Bases de dados'),
('Imagens'),('Vídeos'),('Conteúdos de Geografia');

INSERT INTO temas_geograficos (nome, slug, destaque) VALUES
('Globalização','globalizacao',1),('Migrações','migracoes',1),
('Urbanização','urbanizacao',1),('Desigualdades sociais e territoriais','desigualdades',0),
('Conflitos territoriais','conflitos-territoriais',1),('Meio ambiente','meio-ambiente',1),
('Mudanças climáticas','mudancas-climaticas',0),('Espaço rural e questões agrárias','espaco-rural',0),
('Industrialização','industrializacao',0),('Trabalho e economia','trabalho-economia',0),
('Geopolítica','geopolitica',0),('Cultura','cultura',0),('Turismo','turismo',0),
('Relações entre sociedade e natureza','sociedade-natureza',0);

INSERT INTO conceitos_geograficos (slug, termo, definicao) VALUES
('territorio','Território','Espaço delimitado e apropriado por um grupo, instituição ou Estado, onde se exercem poder, controle e identidade.'),
('paisagem','Paisagem','Conjunto de elementos naturais e culturais perceptíveis em um lugar, resultado da relação entre sociedade e natureza.'),
('lugar','Lugar','Espaço vivido, carregado de memórias, afetos e identidade.'),
('regiao','Região','Porção do espaço com características comuns que a distinguem de outras.'),
('globalizacao','Globalização','Intensificação das conexões econômicas, culturais, tecnológicas e políticas entre lugares do mundo.'),
('migracao','Migração','Deslocamento de pessoas de um lugar para outro, com mudança de residência.'),
('urbanizacao','Urbanização','Crescimento da população e das áreas urbanas em relação ao campo.'),
('fronteira','Fronteira','Limite que separa territórios ou Estados; também espaço de contato, controle e disputa.'),
('desigualdade','Desigualdade socioespacial','Distribuição desigual de renda, serviços e oportunidades no espaço.'),
('vulnerabilidade','Vulnerabilidade socioambiental','Condição de grupos com pouca capacidade de enfrentar riscos ambientais.');

INSERT INTO paises (nome, codigo_iso, continente_id, latitude, longitude) VALUES
('Brasil','BR',4,-14.235,-51.925),
('Índia','IN',5,20.594,78.963),
('Austrália','AU',7,-25.274,133.775),
('Estados Unidos','US',2,37.090,-95.713),
('Serra Leoa','SL',1,8.460,-11.780),
('Malawi','MW',1,-13.254,34.302);