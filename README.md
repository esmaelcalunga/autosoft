# AutoSOFT — Site de stand automóvel (PHP + MySQL)

Site institucional + catálogo de viaturas com **URLs amigáveis** (carro, marca,
categoria), base de dados **MySQL** e **painel administrativo com login** para
gestão completa dos dados.

---

## ✨ Funcionalidades

**Site público**
- Página inicial com destaques, pesquisa rápida e banda de consignação
- Catálogo `/estoque` com filtros (marca, categoria, combustível, preço) e ordenação
- Páginas por marca `/marca/{slug}` e por categoria `/categoria/{slug}`
- Detalhe da viatura `/viatura/{slug}` com ficha técnica, galeria e formulário de interesse
- Página "Vender viatura" e "A AutoSOFT"
- Identidade visual fiel ao design system AutoSOFT (Saira / Archivo / JetBrains Mono, vermelho `#DA1E2F`)

**Painel administrativo `/admin`**
- Login seguro (palavras-passe com `password_hash`)
- Dashboard com contadores
- CRUD de **viaturas** (com upload de várias fotografias)
- CRUD de **marcas** e **categorias**
- Caixa de **contactos/leads** recebidos do site
- Proteção CSRF em todos os formulários

---

## 🔗 URLs amigáveis

| Página            | URL                                   |
|-------------------|---------------------------------------|
| Início            | `/`                                   |
| Catálogo          | `/estoque`                            |
| Por marca         | `/marca/toyota`                       |
| Por categoria     | `/categoria/suv`                      |
| Detalhe viatura   | `/viatura/toyota-hilux-2-8-srx-4x4-at`|
| Vender            | `/vender`                             |
| Painel            | `/admin`                              |

Os *slugs* são gerados automaticamente a partir de marca + modelo + versão.

---

## ⚙️ Instalação

### 1. Requisitos
- PHP 8.0+ (com PDO MySQL, GD opcional)
- MySQL 5.7+ ou MariaDB 10.3+
- Apache com `mod_rewrite` **ativado** (ou Nginx — ver abaixo)

### 2. Base de dados
No phpMyAdmin ou linha de comando:

```bash
mysql -u root -p < database/schema.sql
mysql -u root -p autosoft < database/seed.sql
```

### 3. Configuração
Edite **`config.php`** com as credenciais da sua base de dados:

```php
'db' => [
    'host' => '127.0.0.1',
    'name' => 'autosoft',
    'user' => 'root',
    'pass' => 'A_SUA_SENHA',
],
```

> Se publicar numa **subpasta** (ex.: `http://localhost/autosoft`), defina
> `'base_url' => '/autosoft'` e descomente `RewriteBase /autosoft/` no `.htaccess`.

### 4. Permissões
Garanta que a pasta `uploads/` tem permissão de escrita:

```bash
chmod -R 775 uploads
```

### 5. Abrir
- Site:   `http://localhost/autosoft/`
- Painel: `http://localhost/autosoft/admin`

---

## 🔐 Acesso ao painel

No primeiro acesso a `/admin` é criado automaticamente o utilizador:

```
Email:  admin@autosoft.ao
Senha:  admin123
```

**Altere a senha** após o primeiro login (no phpMyAdmin ou criando novo registo
em `admin_users` com `password_hash`).

---

## 🗂️ Estrutura

```
autosoft/
├── index.php            ← front controller / router
├── .htaccess            ← reescrita de URLs
├── config.php           ← credenciais e definições
├── src/
│   ├── db.php           ← ligação PDO
│   ├── helpers.php      ← slug, formatação Kz, CSRF, render…
│   ├── auth.php         ← autenticação do painel
│   ├── models.php       ← queries (viaturas, marcas…)
│   └── components.php   ← cartão de viatura (HTML)
├── controllers/
│   ├── site.php         ← páginas públicas
│   └── admin.php        ← painel + CRUD
├── views/
│   ├── layout.php, home.php, catalog.php, vehicle.php, sell.php, about.php, 404.php
│   └── admin/           ← login, layout, dashboard, vehicle_form, brands, categories, leads
├── assets/
│   ├── site.css         ← estilos do site
│   └── admin.css        ← estilos do painel
├── uploads/             ← fotografias das viaturas (escrita)
└── database/
    ├── schema.sql       ← estrutura
    └── seed.sql         ← dados de exemplo (10 viaturas)
```

---

## 🌐 Nginx (alternativa ao Apache)

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
location ~ \.php$ {
    include fastcgi_params;
    fastcgi_pass unix:/run/php/php8.2-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
}
```

---

## 🧱 Tabelas principais

- `vehicles` — viaturas (FK para `brands` e `categories`, slug único)
- `brands` — marcas
- `categories` — categorias
- `vehicle_images` — fotografias
- `leads` — pedidos de contacto / interesse
- `admin_users` — utilizadores do painel

© AutoSOFT — código de exemplo para fins de demonstração.
