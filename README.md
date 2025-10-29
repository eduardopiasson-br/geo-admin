# Geo Admin - Sistema de Gerenciamento de Dados Georreferenciados

Sistema full-stack para gerenciamento e visualização de dados geográficos usando Laravel, Filament, PostgreSQL com PostGIS e ArcGIS Maps SDK.

## 📋 Pré-requisitos

- Docker Desktop (ou Docker + Docker Compose)
- PHP 8.2+
- Composer
- Node.js e NPM

## 🚀 Instalação e Configuração

### 1. Clone o repositório

```bash
git clone <repository-url>
cd geo-admin
```

### 2. Configure o ambiente

Copie o arquivo de exemplo do ambiente:

```bash
cp .env.example .env
```

### 3. Configure as variáveis de ambiente

Edite o arquivo `.env` e configure:

```env
APP_NAME="Geo Admin"
APP_URL=http://localhost

DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=geo_admin
DB_USERNAME=sail
DB_PASSWORD=password

ARCGIS_API_KEY=sua-chave-api-aqui
```

**Importante:** Adicione sua chave API do ArcGIS na variável `ARCGIS_API_KEY`.

### 4. Inicie os serviços com Laravel Sail

```bash
./vendor/bin/sail up -d
```

Se ainda não tiver o Sail instalado localmente:

```bash
composer install
./vendor/bin/sail up -d
```

### 5. Gere a chave da aplicação

```bash
./vendor/bin/sail artisan key:generate
```

### 6. Execute as migrações

```bash
./vendor/bin/sail artisan migrate
```

### 7. Execute os seeders

```bash
./vendor/bin/sail artisan db:seed
```

Isso criará:
- **Usuários padrão** (veja credenciais abaixo)
- **Camadas geográficas de exemplo** 

## 🔑 Credenciais Padrão

Após executar os seeders, você terá acesso com as seguintes credenciais:

### Administrador
- **Email:** `admin@geo-admin.local`
- **Senha:** `password`

### Usuário de Teste
- **Email:** `test@example.com`
- **Senha:** `password`

## 🎯 Acessando a Aplicação

### Painel Administrativo (Filament)
- **URL:** http://localhost/painel
- **Credenciais:** Use uma das contas acima

### Mapa Público
- **URL:** http://localhost/

## 📁 Estrutura do Projeto

```
geo-admin/
├── app/
│   ├── Filament/Painel/Resources/   # Resources do Filament
│   ├── Http/Controllers/            # Controladores da API
│   └── Models/                       # Modelos Eloquent
├── database/
│   ├── migrations/                   # Migrações do banco
│   └── seeders/                      # Seeders de dados
├── resources/
│   └── views/
│       └── map.blade.php             # View do mapa público
└── routes/
    └── web.php                       # Rotas da aplicação
```

## 🛠️ Comandos Úteis

### Docker/Sail

```bash
# Iniciar containers
./vendor/bin/sail up -d

# Parar containers
./vendor/bin/sail down

# Ver logs
./vendor/bin/sail logs -f

# Entrar no container Laravel
./vendor/bin/sail shell

# Entrar no PostgreSQL
./vendor/bin/sail psql
```

### Artisan

```bash
# Limpar cache
./vendor/bin/sail artisan optimize:clear

# Executar migrações
./vendor/bin/sail artisan migrate

# Executar seeders
./vendor/bin/sail artisan db:seed

# Executar apenas UserSeeder
./vendor/bin/sail artisan db:seed --class=UserSeeder

# Executar apenas LayerSeeder
./vendor/bin/sail artisan db:seed --class=LayerSeeder

# Criar novo usuário via tinker
./vendor/bin/sail artisan tinker
>>> User::create(['name' => 'Nome', 'email' => 'email@exemplo.com', 'password' => bcrypt('senha')]);
```

### Testes

```bash
# Executar todos os testes
./vendor/bin/sail artisan test

# Executar testes específicos
./vendor/bin/sail artisan test --filter MapPageTest
./vendor/bin/sail artisan test --filter LayerControllerTest
./vendor/bin/sail artisan test --filter LayerTest
```

### Banco de Dados

```bash
# Conectar ao PostgreSQL
./vendor/bin/sail psql

# Verificar extensão PostGIS
SELECT PostGIS_version();

# Verificar camadas no banco
SELECT id, name, ST_AsText(geometry) as geometry FROM layers;

# Verificar geometria em GeoJSON
SELECT id, name, ST_AsGeoJSON(geometry) as geojson FROM layers WHERE id = 1;
```

## 📚 Funcionalidades

### Painel Administrativo (`/painel`)

- ✅ CRUD completo de camadas geográficas
- ✅ Upload de arquivos GeoJSON
- ✅ Validação de geometrias
- ✅ Visualização de camadas cadastradas
- ✅ Autenticação protegida

### Mapa Público (`/`)

- ✅ Visualização interativa com ArcGIS Maps SDK
- ✅ Carregamento dinâmico de camadas do banco
- ✅ Legenda e popups informativos
- ✅ Zoom e navegação do mapa

### API

- ✅ Endpoint `/api/layers` retorna todas as camadas em GeoJSON
- ✅ Formato: FeatureCollection com metadados

## 🗄️ Banco de Dados

O projeto utiliza **PostgreSQL com PostGIS** para armazenamento de dados geoespaciais.

### Estrutura da tabela `layers`

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | integer | ID único (auto-increment) |
| name | string(100) | Nome da camada |
| geometry | geometry | Geometria PostGIS (SRID 4326) |
| created_at | timestamp | Data de criação |
| updated_at | timestamp | Data de atualização |

### Índices

- **GIST index** na coluna `geometry` para otimização de consultas espaciais

## 🔧 Tecnologias Utilizadas

- **Backend:** Laravel 11+
- **Admin Panel:** Filament v3
- **Banco de Dados:** PostgreSQL + PostGIS
- **Mapas:** ArcGIS Maps SDK for JavaScript v4.34
- **Containerização:** Docker + Laravel Sail

## 📝 Formatos Suportados

### GeoJSON

O sistema aceita arquivos GeoJSON com os seguintes tipos de geometria:
- Point
- LineString
- Polygon
- MultiPoint
- MultiLineString
- MultiPolygon
- GeometryCollection

### Exemplo de GeoJSON válido:

```json
{
  "type": "Polygon",
  "coordinates": [
    [
      [-47.0, -15.0],
      [-47.5, -15.0],
      [-47.5, -15.5],
      [-47.0, -15.5],
      [-47.0, -15.0]
    ]
  ]
}
```

## 🐛 Troubleshooting

### Mapa não carrega

1. Verifique se a `ARCGIS_API_KEY` está configurada no `.env`
2. Verifique o console do navegador para erros
3. Confirme que as camadas estão cadastradas no banco

### Erro ao executar migrações

```bash
# Recriar banco do zero
./vendor/bin/sail down -v
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate:fresh --seed
```

### Erro de conexão com banco

Verifique se o serviço PostgreSQL está rodando:

```bash
./vendor/bin/sail ps
```

### Limpar tudo e começar do zero

```bash
# Parar e remover volumes
./vendor/bin/sail down -v

# Reiniciar
./vendor/bin/sail up -d

# Recriar banco e popular
./vendor/bin/sail artisan migrate:fresh --seed
```

## 📄 Licença

Este projeto é privado e de uso interno.

## 👥 Contribuindo

Para contribuir com o projeto:

1. Crie uma branch a partir de `main`
2. Faça suas alterações
3. Execute os testes: `./vendor/bin/sail artisan test`
4. Abra um Pull Request

## 📞 Suporte

Para dúvidas ou problemas, entre em contato com a equipe de desenvolvimento.
