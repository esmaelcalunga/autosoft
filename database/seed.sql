-- =====================================================================
--  AutoSOFT — Dados iniciais (executar DEPOIS de schema.sql)
--  Stock de exemplo (realidade angolana, preços em Kwanza).
--  Nota: o utilizador administrador é criado automaticamente no primeiro
--  acesso ao painel  ( /admin )  com as credenciais:
--        email:  admin@autosoft.ao
--        senha:  admin123
--  (altere a senha no painel após o primeiro login)
-- =====================================================================

USE `autosoft`;

-- --- Marcas ----------------------------------------------------------
INSERT INTO `brands` (`id`, `name`, `slug`) VALUES
  (1, 'Toyota',         'toyota'),
  (2, 'Hyundai',        'hyundai'),
  (3, 'Kia',            'kia'),
  (4, 'Land Rover',     'land-rover'),
  (5, 'Mercedes-Benz',  'mercedes-benz'),
  (6, 'Mitsubishi',     'mitsubishi'),
  (7, 'Nissan',         'nissan');

-- --- Categorias ------------------------------------------------------
INSERT INTO `categories` (`id`, `name`, `slug`, `description`) VALUES
  (1, 'SUV',      'suv',      'Utilitários desportivos e crossovers'),
  (2, 'Pick-up',  'pick-up',  'Pick-ups e viaturas de carga ligeira'),
  (3, 'Sedan',    'sedan',    'Berlinas e sedans'),
  (4, 'Citadino', 'citadino', 'Viaturas compactas para a cidade'),
  (5, 'Premium',  'premium',  'Segmento de luxo e alta gama');

-- --- Viaturas --------------------------------------------------------
INSERT INTO `vehicles`
  (`brand_id`,`category_id`,`model`,`version`,`slug`,`year`,`km`,`fuel`,`transmission`,`power`,`color`,`price`,`installment`,`location`,`condition`,`badges`,`description`,`status`,`featured`)
VALUES
  (1, 2, 'Hilux', '2.8 SRX 4x4 AT', 'toyota-hilux-2-8-srx-4x4-at', '2023/2023', 41200, 'Gasóleo', 'Automática', '204 cv', 'Branco Pérola', 38500000, 'ou 48x de Kz 870.000', 'Luanda', 'seminova', '4x4', 'Toyota Hilux 2.8 SRX 4x4 em estado impecável, inspeção técnica aprovada nos 240 pontos da AutoSOFT. Único dono, revisões em dia, aceita retoma.', 'disponivel', 1),
  (1, 3, 'Corolla', '2.0 GLi', 'toyota-corolla-2-0-gli', '2025/2025', 0, 'Gasolina', 'Automática', '171 cv', 'Prata Lunar', 32000000, 'ou 48x de Kz 723.000', 'Talatona', 'novo', '', 'Toyota Corolla 2.0 GLi 0 km, totalmente equipado, garantia de fábrica e documentação pronta.', 'disponivel', 1),
  (1, 1, 'Land Cruiser Prado', '3.0 VX', 'toyota-land-cruiser-prado-3-0-vx', '2022/2022', 58000, 'Gasóleo', 'Automática', '204 cv', 'Cinza Granito', 78000000, 'ou 48x de Kz 1.762.000', 'Luanda', 'seminova', 'Blindada', 'Toyota Land Cruiser Prado 3.0 VX blindada, robustez e conforto para qualquer terreno. Revisões completas em dia.', 'disponivel', 1),
  (2, 1, 'Tucson', '1.6 T-GDI Premium', 'hyundai-tucson-1-6-t-gdi-premium', '2024/2024', 19000, 'Gasolina', 'Automática', '180 cv', 'Preto Cristal', 27900000, 'ou 48x de Kz 630.000', 'Benguela', 'seminova', '', 'Hyundai Tucson 1.6 T-GDI Premium, baixa quilometragem e pacote tecnológico completo.', 'disponivel', 1),
  (6, 1, 'Pajero Sport', '2.4 D 4x4', 'mitsubishi-pajero-sport-2-4-d-4x4', '2023/2023', 33000, 'Gasóleo', 'Automática', '181 cv', 'Branco Diamante', 41500000, 'ou 48x de Kz 938.000', 'Huambo', 'seminova', '4x4', 'Mitsubishi Pajero Sport 2.4 D 4x4, ideal para estrada e fora de estrada. Manutenção em dia.', 'disponivel', 1),
  (1, 1, 'RAV4', '2.5 Hybrid', 'toyota-rav4-2-5-hybrid', '2024/2024', 15600, 'Híbrida', 'Automática', '218 cv', 'Vermelho Sunset', 36900000, 'ou 48x de Kz 834.000', 'Luanda', 'seminova', '', 'Toyota RAV4 2.5 Hybrid, economia e desempenho com tecnologia híbrida Toyota.', 'disponivel', 1),
  (3, 1, 'Sportage', '1.6 GDI EX', 'kia-sportage-1-6-gdi-ex', '2023/2023', 28300, 'Gasolina', 'Automática', '150 cv', 'Cinza Titânio', 25900000, 'ou 48x de Kz 585.000', 'Lobito', 'seminova', '', 'Kia Sportage 1.6 GDI EX, design moderno e bom equipamento de série.', 'disponivel', 0),
  (2, 1, 'Creta', '1.5 Ultimate', 'hyundai-creta-1-5-ultimate', '2025/2025', 0, 'Gasolina', 'Automática', '115 cv', 'Branco Polar', 19900000, 'ou 48x de Kz 450.000', 'Luanda', 'novo', '', 'Hyundai Creta 1.5 Ultimate 0 km, o SUV compacto perfeito para a cidade.', 'disponivel', 0),
  (4, 5, 'Defender 110', '3.0 D300 X-Dynamic', 'land-rover-defender-110-3-0-d300-x-dynamic', '2022/2022', 39000, 'Gasóleo', 'Automática', '300 cv', 'Preto Vulcão', 95000000, 'sob consulta', 'Luanda', 'seminova', 'Premium,Blindada', 'Land Rover Defender 110 D300 X-Dynamic blindada, presença e capacidade off-road de topo.', 'disponivel', 0),
  (5, 5, 'GLE 450', '4MATIC', 'mercedes-benz-gle-450-4matic', '2023/2023', 24000, 'Gasolina', 'Automática', '381 cv', 'Prata Iridium', 88000000, 'sob consulta', 'Luanda', 'seminova', 'Premium', 'Mercedes-Benz GLE 450 4MATIC, luxo, tecnologia e potência num só SUV.', 'disponivel', 0);
