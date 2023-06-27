# Prueba FullStack EmQu
- Brayan Rincón
- [LinkedIn](https://www.linkedin.com/in/bracodev)

## Requisitos para el Backend

- Docker
- Docker Compose

## Requisitos para el Frontend
- NodeJS
- npm

### Instalación del Backend

#### Cambiar los valores de la variables de entorno

```php
# Modificar el archivo .env de la raíz del direcotio y modificar las variables

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=emqu_test
DB_USERNAME=sail
DB_PASSWORD=password
```

Existen dos formas para instalar por primera vez el proyecto usando **EmQu Console** o la **forma manual**.

**NOTA**: *EmQu Console* solo está soportado en Linux y Mac

##### 1. Usando EmQu Console

```sh
./qc install # Instala el proyecto

./qc start # Inicia los contenedores

./qc init # Fuerza la creación de las tablas y los datos de ejemplo
```

##### 2. Forma manual

```sh
# Instala el proyecto
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs

# Crea alias de Sail
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'

# Inicia los contenedores
sail up -d 

# Crea las tablas
sail php artisan migrate

# Crea los datos de ejemplo
sail php artisan db:seed 
```

### Instalación del Frontend

#### Cambiar los valores de la variables de entorno
```php
# Crear el archivo .env en la raíz del direcotio con el siguiente contenido

VITE_API_URL=http://localhost
```

#### Instalación de dependendias y levantameinto del proyecto

```sh
# Instalación de dependendias
npm install

# Correr el proyecto en modo desarrollo
npm run build
```
