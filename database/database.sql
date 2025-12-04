-- Sistema de Orçamentos para Ateliê de Costura
-- Criado em: 04/12/2025

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS `atelie_orcamentos` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `atelie_orcamentos`;

-- --------------------------------------------------------
-- Tabela de usuários (login do sistema)
-- --------------------------------------------------------

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuário padrão: admin@atelie.com / senha: admin123
INSERT INTO `usuarios` (`nome`, `email`, `senha`, `ativo`) VALUES
('Administrador', 'admin@atelie.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- --------------------------------------------------------
-- Tabela de clientes
-- --------------------------------------------------------

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `endereco` text,
  `observacoes` text,
  `ativo` tinyint(1) DEFAULT 1,
  `data_cadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `nome` (`nome`),
  KEY `telefone` (`telefone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabela de serviços
-- --------------------------------------------------------

CREATE TABLE `servicos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) NOT NULL,
  `descricao` text,
  `preco_base` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tempo_estimado` varchar(50) DEFAULT NULL,
  `categoria` enum('ajustes','confeccoes','consertos','reformas','outros') DEFAULT 'outros',
  `ativo` tinyint(1) DEFAULT 1,
  `data_cadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `categoria` (`categoria`),
  KEY `nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Serviços de exemplo
INSERT INTO `servicos` (`nome`, `descricao`, `preco_base`, `tempo_estimado`, `categoria`) VALUES
('Ajuste de Bainha', 'Ajuste de comprimento de calças, saias e vestidos', 20.00, '1 dia útil', 'ajustes'),
('Ajuste de Cintura', 'Diminuir ou alargar cintura de calças e saias', 25.00, '2 dias úteis', 'ajustes'),
('Barra de Calça', 'Fazer barra em calças (simples ou italiana)', 18.00, '1 dia útil', 'ajustes'),
('Colocar Elástico', 'Trocar ou colocar elástico em peças', 12.00, '1 dia útil', 'consertos'),
('Conserto de Zíper', 'Troca ou conserto de zíper em peças diversas', 15.00, '1 dia útil', 'consertos'),
('Costura de Vestido Simples', 'Confecção de vestido modelo simples', 150.00, '7 dias úteis', 'confeccoes'),
('Reforma de Blazer', 'Ajustes gerais em blazers e paletós', 80.00, '5 dias úteis', 'reformas');

-- --------------------------------------------------------
-- Tabela de orçamentos
-- --------------------------------------------------------

CREATE TABLE `orcamentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` varchar(20) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `data_orcamento` date NOT NULL,
  `data_validade` date NOT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `desconto_tipo` enum('percentual','fixo') DEFAULT 'fixo',
  `desconto_valor` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `observacoes` text,
  `prazo_execucao` varchar(100) DEFAULT NULL,
  `forma_pagamento` varchar(100) DEFAULT NULL,
  `status` enum('pendente','aprovado','em_execucao','concluido','cancelado') DEFAULT 'pendente',
  `usuario_id` int(11) NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_atualizacao` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero` (`numero`),
  KEY `cliente_id` (`cliente_id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `status` (`status`),
  KEY `data_orcamento` (`data_orcamento`),
  CONSTRAINT `fk_orcamento_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `fk_orcamento_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabela de itens do orçamento
-- --------------------------------------------------------

CREATE TABLE `itens_orcamento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orcamento_id` int(11) NOT NULL,
  `servico_id` int(11) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 1,
  `valor_unitario` decimal(10,2) NOT NULL,
  `valor_total` decimal(10,2) NOT NULL,
  `ordem` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `orcamento_id` (`orcamento_id`),
  KEY `servico_id` (`servico_id`),
  CONSTRAINT `fk_item_orcamento` FOREIGN KEY (`orcamento_id`) REFERENCES `orcamentos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_item_servico` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabela de configurações do sistema
-- --------------------------------------------------------

CREATE TABLE `configuracoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome_atelie` varchar(150) NOT NULL DEFAULT 'Meu Ateliê',
  `endereco` text,
  `telefone` varchar(20) DEFAULT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `instagram` varchar(100) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `mensagem_rodape` text,
  `validade_padrao` int(11) DEFAULT 7,
  `prazo_execucao_padrao` varchar(100) DEFAULT '3 dias úteis',
  `forma_pagamento_padrao` varchar(100) DEFAULT 'À combinar',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Configurações padrão
INSERT INTO `configuracoes` (`nome_atelie`, `telefone`, `mensagem_rodape`, `validade_padrao`, `prazo_execucao_padrao`, `forma_pagamento_padrao`) VALUES
('Ateliê de Costura', '(00) 0000-0000', 'Agradecemos a sua preferência!', 7, '3 dias úteis', 'À combinar');

-- --------------------------------------------------------
-- Views úteis
-- --------------------------------------------------------

-- View para estatísticas do dashboard
CREATE OR REPLACE VIEW `vw_estatisticas_mes` AS
SELECT 
    COUNT(*) as total_orcamentos,
    SUM(CASE WHEN status = 'aprovado' THEN 1 ELSE 0 END) as aprovados,
    SUM(CASE WHEN status = 'pendente' THEN 1 ELSE 0 END) as pendentes,
    SUM(CASE WHEN status = 'em_execucao' THEN 1 ELSE 0 END) as em_execucao,
    SUM(CASE WHEN status = 'concluido' THEN 1 ELSE 0 END) as concluidos,
    SUM(CASE WHEN status = 'cancelado' THEN 1 ELSE 0 END) as cancelados,
    SUM(total) as valor_total,
    SUM(CASE WHEN status = 'aprovado' THEN total ELSE 0 END) as valor_aprovado
FROM orcamentos
WHERE MONTH(data_orcamento) = MONTH(CURRENT_DATE())
  AND YEAR(data_orcamento) = YEAR(CURRENT_DATE());

-- --------------------------------------------------------
-- Fim da estrutura do banco de dados
-- --------------------------------------------------------
