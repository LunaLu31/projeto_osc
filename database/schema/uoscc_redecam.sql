-- ============================================================
-- BANCO DE DADOS: UOSCC / REDCAM
-- Descrição: Sistema de gestão de demandas de OSCs
-- Versão: MVP (Versão A)
--
-- Este script é a referência do modelo físico. As migrations em
-- database/migrations geram exatamente este esquema; mantenha os dois
-- sincronizados ao alterar qualquer tabela.
--
-- Nomes de tabelas no plural para seguir as convenções dos models Laravel.
-- ============================================================

CREATE DATABASE IF NOT EXISTS uoscc_redecam
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE uoscc_redecam;

-- ============================================================
-- TABELA: adms (Administradores do sistema)
-- ============================================================
CREATE TABLE adms (
    id_admin INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- TABELA: oscs (Organizações da Sociedade Civil)
-- ============================================================
CREATE TABLE oscs (
    id_osc INT AUTO_INCREMENT PRIMARY KEY,
    id_admin INT NOT NULL,
    nome VARCHAR(150) NOT NULL,
    area_atuacao VARCHAR(100) NOT NULL,
    descricao TEXT,
    endereco VARCHAR(255),
    telefone VARCHAR(20),
    email VARCHAR(150) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_admin) REFERENCES adms(id_admin)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    INDEX idx_id_admin (id_admin),
    INDEX idx_area_atuacao (area_atuacao)
) ENGINE=InnoDB;

-- ============================================================
-- TABELA: demandas (Necessidades publicadas pelas OSCs)
-- status: 'Parcial' é exibido como "Parcialmente atendida".
-- ============================================================
CREATE TABLE demandas (
    id_demanda INT AUTO_INCREMENT PRIMARY KEY,
    id_osc INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    categoria VARCHAR(100) NOT NULL,
    descricao TEXT NOT NULL,
    quantidade INT NOT NULL DEFAULT 1,
    prazo DATE,
    status ENUM('Aberta', 'Parcial', 'Concluída') NOT NULL DEFAULT 'Aberta',
    urgencia BOOLEAN NOT NULL DEFAULT FALSE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (id_osc) REFERENCES oscs(id_osc)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    INDEX idx_id_osc (id_osc),
    INDEX idx_status (status),
    INDEX idx_categoria (categoria),
    INDEX idx_urgencia (urgencia),
    INDEX idx_prazo (prazo)
) ENGINE=InnoDB;

-- ============================================================
-- TABELA: manifestacoes_interesses
-- (Registro de apoiadores interessados em demandas)
-- ============================================================
CREATE TABLE manifestacoes_interesses (
    id_manifestacao INT AUTO_INCREMENT PRIMARY KEY,
    id_demanda INT NOT NULL,
    nome_apoiador VARCHAR(100) NOT NULL,
    telefone VARCHAR(20),
    email VARCHAR(150),
    tipo_interesse ENUM('Apoiador/Doação', 'Voluntário', 'Aluno') NOT NULL,
    mensagem TEXT,
    data_manifestacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_demanda) REFERENCES demandas(id_demanda)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    -- Garantir que pelo menos um contato seja fornecido
    CONSTRAINT chk_contato_apoiador CHECK (telefone IS NOT NULL OR email IS NOT NULL),

    -- Evitar manifestações duplicadas do mesmo contato na mesma demanda
    UNIQUE KEY uk_demanda_telefone (id_demanda, telefone),
    UNIQUE KEY uk_demanda_email (id_demanda, email),

    INDEX idx_id_demanda (id_demanda),
    INDEX idx_tipo_interesse (tipo_interesse)
) ENGINE=InnoDB;

-- ============================================================
-- TABELA: personal_access_tokens
-- Tokens Bearer da API REST (Laravel Sanctum) para OSCs e administradores.
-- tokenable_type/tokenable_id apontam para App\Models\Osc ou App\Models\Adm.
-- ============================================================
CREATE TABLE personal_access_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT UNSIGNED NOT NULL,
    name TEXT NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    abilities TEXT NULL,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    INDEX personal_access_tokens_tokenable_type_tokenable_id_index (tokenable_type, tokenable_id),
    INDEX personal_access_tokens_expires_at_index (expires_at)
) ENGINE=InnoDB;

-- ============================================================
-- DADOS INICIAIS (desenvolvimento)
-- Administrador padrão: admin@uoscc.org / admin123 (troque em produção).
-- Hash gerado com password_hash('admin123', PASSWORD_BCRYPT).
-- ============================================================
INSERT INTO adms (nome, email, senha_hash) VALUES
('Administrador UOSCC', 'admin@uoscc.org', '$2y$12$ExfvCJtDF0aadF1qIhvBNe/wfeSnt5tIpHAw2XnzMZPC4FU3h5UaO');

-- ============================================================
-- FIM DO SCRIPT
-- ============================================================
