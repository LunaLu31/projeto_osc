-- ============================================================
-- Usuários do MySQL com privilégio mínimo (produção)
--
-- A aplicação não deve usar root nem um usuário pessoal. Separe:
--   uoscc_app    -> usado pelo Laravel no dia a dia (.env DB_USERNAME)
--   uoscc_deploy -> usado só para rodar migrations no deploy
--
-- Troque as senhas por valores longos e aleatórios, por exemplo:
--   php -r "echo bin2hex(random_bytes(24)), PHP_EOL;"
-- ============================================================

CREATE USER IF NOT EXISTS 'uoscc_app'@'localhost' IDENTIFIED BY 'TROQUE_POR_UMA_SENHA_FORTE';
GRANT SELECT, INSERT, UPDATE, DELETE ON uoscc_redecam.* TO 'uoscc_app'@'localhost';

CREATE USER IF NOT EXISTS 'uoscc_deploy'@'localhost' IDENTIFIED BY 'TROQUE_POR_OUTRA_SENHA_FORTE';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, DROP, INDEX, REFERENCES
    ON uoscc_redecam.* TO 'uoscc_deploy'@'localhost';

FLUSH PRIVILEGES;
