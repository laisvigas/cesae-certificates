# 📜 CESAE Certificates

Plataforma interna para emissão e partilha de certificados de eventos do CESAE.  

---

## Tecnologias
- Laravel 12
- Laravel Breeze (autenticação)
- Tailwind CSS
- Spatie Laravel PDF / Dompdf (geração de certificados em PDF)
- MySQL

---

## Funcionalidades até agora
- Autenticação básica (login, logout, registro).
- Perfis com papéis: **admin, staff, aluno**.
- Dashboard protegido (somente admin/staff).
- CRUD de **Eventos** (com tipos pré-definidos).
- CRUD de **Participantes**.
- Emissão de **Certificados em PDF** via Spatie.

---

## Instalação

### 1. Clonar o repositório
- git clone git@github.com:laisvigas/cesae-certificates.git
- cd cesae-certificates

### 2. Instalar dependências
- composer install
- npm install

### 3. Configurar variáveis de ambiente
- cp .env.example .env
- php artisan key:generate

Editar `.env` para definir o banco de dados.  

### 4. Criar banco e rodar migrações
- mkdir -p database && touch database/database.sqlite
- php artisan migrate --seed

Para carregar também dados de demo:
- php artisan db:seed --class=DevDemoSeeder

### 5. Rodar servidor local
- php artisan serve
- npm run dev

A aplicação estará disponível em:
http://127.0.0.1:8000

---

## Checklist Pós-Pull
- composer install
- npm install
- php artisan migrate
- php artisan db:seed
- npm run dev
- php artisan serve

---

## Equipe
- Laís Vigas  
- Daniel Caseiro  
