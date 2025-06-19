

## 📌 ¿Qué es QuickCodeIA?

QuickCodeIA es un módulo para Moodle que permite gestionar ejercicios con soporte de Inteligencia Artificial (IA). Facilita la creación, administración y evaluación de ejercicios integrando servicios automáticos para ejecutar código y ofrecer una interfaz intuitiva para estudiantes y profesores.

Este proyecto configura un entorno de Moodle con Apache, MySQL, Ollama y módulos adicionales usando Docker, incluyendo QuickCodeIA.

# Instalación de Moodle con Docker y QuickCodeIA

## 📥 Requisitos

Antes de comenzar, asegúrate de tener instalados:

* **Docker**: [Descargar e instalar Docker](https://www.docker.com/get-started)
* **Docker Compose**: [Guía de instalación](https://docs.docker.com/compose/install/)

## 🧩 Componentes del proyecto

### QuickCodeIA

QuickCodeIA es un módulo para Moodle diseñado para gestionar ejercicios con integración de IA, permitiendo crear, administrar y evaluar ejercicios directamente desde Moodle.

### QuickCodeIA Code Runner

QuickCodeIA Code Runner es un servicio que permite ejecutar ejercicios de programación de manera segura, gestionando las respuestas y evaluando el código enviado por los usuarios. Este servicio está configurado automáticamente mediante Docker Compose.

### QuickCodeIA Solver

QuickCodeIA Solver es una interfaz web interactiva que permite resolver y gestionar ejercicios desde una plataforma amigable e intuitiva.

## 🚀 Instalación

### 1️⃣ Descargar Moodle

Ejecuta el siguiente comando para descargar Moodle en la carpeta `moodle/`:

```bash
chmod +x download_moodle.sh
./download_moodle.sh
```

### 2️⃣ Construir y levantar los contenedores con Docker

Comenta la siguiente línea en el archivo `docker-compose-dev.yml o docker-compose-prod.yml`:

```yaml
# - ./quickcodeia:/var/www/html/mod/quickcodeia
```

Ejecuta el siguiente comando para construir la imagen y ejecutar los contenedores:

```bash
docker-compose -f docker-compose-dev.yml up -d --build

docker-compose -f docker-compose-prod.yml up -d --build
```

Este comando iniciará:

* Un servidor **Apache con PHP 8.1**
* Una base de datos **MySQL 8.0**
* Un contenedor **Ollama** para modelos de IA
* Moodle en `http://localhost:8080`
* El servicio QuickCodeIA Code Runner en `http://localhost:8081`

### 3️⃣ Configuración de Moodle

1. Abre tu navegador y accede a:
   👉 [http://localhost:8080](http://localhost:8080)
2. Sigue los pasos del instalador de Moodle:

   * **Tipo de base de datos**: `MySQL mejorado (native/mysqli)`
   * **Servidor de base de datos**: `db`
   * **Usuario de la base de datos**: `moodleuser`
   * **Contraseña**: `moodlepassword`
   * **Nombre de la base de datos**: `moodle`
3. Completa la instalación y crea una cuenta de administrador.

### Instalación del módulo QuickCodeIA

Descomenta la siguiente línea en el archivo `docker-compose-dev.yml o docker-compose-prod.yml`:

```yaml
- ./quickcodeia:/var/www/html/mod/quickcodeia
```

Instala y activa el módulo desde el panel administrador de Moodle.

### Instalación de QuickCodeIA Solver

Ejecuta los siguientes comandos:

```bash
cd quickcodeia_solver
npm install
npm run build
```

El comando `npm run build` automáticamente mueve los archivos generados desde la carpeta `dist` hacia:

```
moodle/mod/quickcodeia/iframe
```

### 4️⃣ Activar LLaMA 3 en Ollama

Una vez que Ollama esté levantado, carga el modelo de IA:

```bash
docker exec -it ollama ollama pull llama3
```

## 📂 Estructura del proyecto

```
.
├── docker-compose.yml           # Configuración de Docker
├── Dockerfile                   # Configuración de Apache y PHP
├── download_moodle.sh           # Script para descargar Moodle
├── moodle/                      # Carpeta donde se descarga Moodle
├── moodle_data/                 # Carpeta de datos de Moodle
├── mysql_data/                  # Carpeta de datos de MySQL
├── ollama_data/                 # Configuración de Ollama
├── quickcodeia/                 # Módulo QuickCodeIA para Moodle
├── quickcodeia_coderunner/      # Servicio QuickCodeIA Code Runner
└── quickcodeia_solver/          # Frontend QuickCodeIA Solver
```

## 🛑 Detener y eliminar los contenedores

Si deseas detener los contenedores, usa:

```bash
docker compose down
```

¡Moodle está listo para usarse con QuickCodeIA! 🎉

👤 Autor

André Filipe Sales Mendes📧 salesmendesandre@gmail.com
