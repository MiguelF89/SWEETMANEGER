SWEETMANAGER

Sistema de gestão com foco em controle financeiro e leitura automatizada de boletos (imagem, PDF e câmera), integrado a uma API própria.

Visão Geral

O SweetManager é uma aplicação desenvolvida em Laravel que combina:

Gestão administrativa
Controle financeiro
Leitura automatizada de boletos bancários

O objetivo é reduzir o trabalho manual no registro de contas e permitir um acompanhamento mais eficiente de gastos.

Funcionalidades
Gestão
Estrutura administrativa base
Preparado para expansão (relatórios e dashboards)
Leitor de Boletos
Upload de imagem (JPG/PNG)
Upload de PDF
Captura via câmera (mobile e desktop)
Extração automática de:
valor
data de vencimento
banco emissor
linha digitável
Como Funciona
Arquivo (imagem ou PDF)
        ↓
Conversão para imagem (se necessário)
        ↓
Leitura de código de barras (zbar)
        ↓
Fallback OCR (tesseract)
        ↓
Normalização do código
        ↓
Extração de dados
🛠️ Stack Tecnológica
Backend
PHP (Laravel)
API REST
Laravel Sanctum (autenticação)
Frontend
Blade (Laravel)
JavaScript (Fetch API)
Tailwind CSS
Processamento
zbar → leitura de código de barras
ghostscript → conversão de PDF
tesseract → OCR (fallback)
Ambiente

O projeto roda totalmente em ambiente containerizado com Docker, não sendo necessário instalar PHP manualmente na máquina local.

Instalação (Docker)
1. Clonar o repositório
git clone https://github.com/MiguelF89/SWEETMANEGER.git
cd SWEETMANEGER
2. Subir os containers
docker compose up -d --build
3. Acessar o sistema

Abra no navegador:

http://localhost
4. Executar comandos do Laravel (quando necessário)
docker exec -it nome_do_container php artisan migrate

Para verificar o nome do container:

docker ps
API
Leitura de boleto
POST /api/boleto/read
Exemplo de requisição
curl -X POST http://localhost/api/boleto/read \
  -F "file=@boleto.jpg"
Exemplo de resposta
{
  "success": true,
  "data": {
    "amount": 100.00,
    "due_date": "2026-05-10",
    "bank": "341",
    "linha_digitavel": "..."
  }
}
Interface

A interface permite:

Upload de arquivos
Captura via câmera
Visualização dos dados extraídos

Acesso via:

/boleto/reader
Limitações
Leitura pode falhar com:
imagens de baixa qualidade
PDFs escaneados com baixa resolução
Dependência de ferramentas externas
OCR não é 100% preciso
🔧 Estrutura do Projeto
app/
 ├── Services/
 │   └── BoletoReaderService.php
 ├── Http/
 │   ├── Controllers/
 │   │   ├── API/
 │   │   └── BoletoController.php
 │   └── Requests/

resources/
 └── views/
     └── boleto/

routes/
 ├── api.php
 └── web.php
Roadmap
 Melhorar precisão da leitura
 Criar dashboard financeiro
 Implementar relatórios
 Categorizar gastos automaticamente
 Integração com APIs bancárias
Status do Projeto

Em desenvolvimento

Backend funcional
Frontend em fase de ajuste
Autor

Miguel Francisco Barbosa Domingues

Licença

Projeto para fins educacionais e portfólio
