<div align="center">
  <img src="https://storage.googleapis.com/homilia/logo_homilia.png" alt="HomilIA Logo" width="200"/>
</div>

---
<div align="center">

[![Gemini](https://img.shields.io/badge/Gemini-886FBF?logo=googlegemini&logoColor=fff)](#)
[![PHP](https://img.shields.io/badge/php-%23777BB4.svg?&logo=php&logoColor=white)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-%23FF2D20.svg?logo=laravel&logoColor=white)](https://laravel.com/)
[![Website](https://img.shields.io/website?url=https%3A%2F%2Fhomilia.app.br&label=homilia.app.br)](https://homilia.app.br)

</div>

### Sobre o Projeto

O HomilIA é uma aplicação web construída com **PHP** e o framework **Laravel**, com o objetivo de auxiliar estudantes de teologia na organização de esboços de sermões. A plataforma utiliza **Tailwind CSS** para a estilização e **Vite** para a compilação de assets de front-end.

Para potencializar a organização e a criatividade, o HomilIA integra-se com a **API do Google Gemini**, utilizando inteligência artificial para sugerir ideias e estruturar os esboços.

---

### ✔️ Requisitos

Certifique-se de ter os seguintes softwares instalados na sua máquina:

* **PHP**
* **Composer**
* **Node.js** e **npm**
* **Docker** e **Docker Compose** (para o ambiente de desenvolvimento com Laravel Sail)

---

### 🔨 Configuração e Instalação

Siga os passos abaixo para configurar e rodar o projeto localmente:

1.  **Clone o repositório:**

    ```bash
    git clone git@github.com:omarcoscardoso/homilia-app.git
    cd homilia-app
    ```

2.  **Copie o arquivo de ambiente e gere a chave da aplicação:**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

3.  **Instale as dependências do Composer e do NPM:**

    ```bash
    composer install
    npm install
    ```

4.  **Compilando os assets de front-end:**

    ```bash
    npm run build
    ```
    *Dica: Para desenvolvimento, use `npm run dev` para recompilar automaticamente os arquivos a cada alteração.*

5.  **Suba o ambiente com Laravel Sail:**

    ```bash
    ./vendor/bin/sail up -d
    ```
    *(A primeira vez que você executar este comando, o Docker pode demorar um pouco para baixar as imagens necessárias.)*

6.  **Execute as migrações do banco de dados:**

    ```bash
    ./vendor/bin/sail artisan migrate
    ```

---

### 🔨 Configurando a API do Gemini

Para que a aplicação funcione corretamente, é necessário configurar a chave de acesso à API do Google Gemini.

1.  **Crie sua chave da API do Gemini:**
    * Acesse o [Google AI Studio](https://aistudio.google.com/app/apikey) e crie uma nova chave.

2.  **Adicione a chave ao arquivo `.env`:**
    * Abra o arquivo `.env` na raiz do seu projeto.
    * Localize a linha `GEMINI_API_KEY=` e adicione a chave gerada:

    ```ini
    GEMINI_API_KEY="SUA_CHAVE_AQUI"
    ```

3.  **Recarregue a aplicação:**
    * Após adicionar a chave, é recomendado reiniciar o ambiente Sail para que a nova variável seja carregada.

    ```bash
    ./vendor/bin/sail down
    ./vendor/bin/sail up -d
    ```

Pronto! Agora a aplicação HomilIA está pronta para ser utilizada, com a integração da API do Gemini funcionando.

---

### Contribuição

Contribuições são bem-vindas! Se você deseja colaborar, por favor, abra uma _issue_ para discutir as mudanças propostas ou envie um _pull request_.

[![LinkedIn](https://custom-icon-badges.demolab.com/badge/LinkedIn-0A66C2?logo=linkedin-white&logoColor=fff)](https://www.linkedin.com/in/omarcoscardoso/)