#!/usr/bin/env bash

UNAMEOUT="$(uname -s)"
WHITE='\033[1;37m'
NC='\033[0m'

# Verify operating system is supported...
case "${UNAMEOUT}" in
    Linux*)             MACHINE=linux;;
    Darwin*)            MACHINE=mac;;
    *)                  MACHINE="UNKNOWN"
esac

if [ "$MACHINE" == "UNKNOWN" ]; then
    echo "Unsupported operating system [$(uname -s)]. Emqu Console supports MacOS, Linux, and Windows (WSL2)." >&2
    exit 1
fi


# Define environment variables...
export APP_PORT=${APP_PORT:-80}
export APP_SERVICE=${APP_SERVICE:-"laravel.test"}
export WWWUSER=${WWWUSER:-$UID}
export WWWGROUP=${WWWGROUP:-$(id -g)}

export DB_HOST=${DB_HOST:-"mysql"}
export DB_PORT=${DB_PORT:-3306}
export DB_DATABASE=${DB_DATABASE:-"mysql"}
export DB_USERNAME=${DB_USERNAME:-"sail"}
export DB_PASSWORD=${DB_PASSWORD:-"password"}

if [ "$MACHINE" == "linux" ]; then
    export SEDCMD="sed -i"
elif [ "$MACHINE" == "mac" ]; then
    export SEDCMD="sed -i .bak"
fi

# Ensure that Docker is running...
if ! docker info > /dev/null 2>&1; then
    echo -e "${WHITE}Docker is not running.${NC}" >&2

    exit 1
fi

# Determine if Sail is currently up...
PSRESULT="$(docker-compose ps -q)"

if docker-compose ps | grep 'Exit'; then
    echo -e "${WHITE}Shutting down old Sail processes...${NC}" >&2

    docker-compose down > /dev/null 2>&1

    EXEC="no"
elif [ -n "$PSRESULT" ]; then
    EXEC="yes"
else
    EXEC="no"
fi


# Function that outputs Sail is not running...
function is_not_running {
    echo -e "${WHITE}EmQu is not running.${NC}" >&2
    echo "" >&2
    echo -e "${WHITE}You may use EmQu with the following commands:${NC} './runme up' or './runme up -d'" >&2

    exit 1
}

if [ $# -gt 0 ]; then
    # Source the ".env" file so Laravel's environment variables are available...
    if [ -f ./.env ]; then
        source ./.env
    fi

    # Proxy PHP commands to the "php" binary on the application container...
    if [ "$1" == "php" ]; then
        shift 1

        if [ "$EXEC" == "yes" ]; then
            docker-compose exec \
                -u \
                "$APP_SERVICE" \
                php "$@"
        else
            is_not_running
        fi

    elif [ "$1" == "start" ]; then
        shift 1
        sh vendor/bin/sail up -d

    elif [ "$1" == "stop" ]; then
        shift 1
        sh vendor/bin/sail stop

    elif [ "$1" == "init" ]; then
        shift 1
        if [ "$EXEC" == "yes" ]; then
            docker-compose exec \
                "$APP_SERVICE" \
                php artisan migrate:refresh --seed
        else
            is_not_running
        fi

    # Proxy Composer commands to the "composer" binary on the application container...
    elif [ "$1" == "install" ]; then
        shift 1
        ENV_FILE=.env
        if [ ! -f "$ENV_FILE" ]; then
            echo -e "${WHITE}Copying .env.example to .env ${NC}" >&2
            bash -c "cp .env.example .env"
        fi
        docker run --rm \
            -u "$(id -u):$(id -g)" \
            -v "$(pwd):/var/www/html" \
            -w /var/www/html \
            laravelsail/php82-composer:latest \
            composer install --ignore-platform-reqs

        alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'

    # Proxy Composer commands to the "composer" binary on the application container...
    elif [ "$1" == "composer" ]; then
        shift 1

        if [ "$EXEC" == "yes" ]; then
            docker-compose exec \
                -u \
                "$APP_SERVICE" \
                composer "$@"
        else
            is_not_running
        fi

    # Proxy Artisan commands to the "artisan" binary on the application container...
    elif [ "$1" == "artisan" ] || [ "$1" == "art" ]; then
        shift 1

        if [ "$EXEC" == "yes" ]; then
            docker-compose exec \
                -u \
                "$APP_SERVICE" \
                php artisan "$@"
        else
            is_not_running
        fi

    # Proxy the "test" command to the "php artisan test" Artisan command...
    elif [ "$1" == "test" ]; then
        shift 1

        if [ "$EXEC" == "yes" ]; then
            docker-compose exec \
                -u \
                "$APP_SERVICE" \
                php artisan test "$@"
        else
            is_not_running
        fi

    # Proxy the "dusk" command to the "php artisan dusk" Artisan command...
    elif [ "$1" == "dusk" ]; then
        shift 1

        if [ "$EXEC" == "yes" ]; then
            docker-compose exec \
                -u \
                -e "APP_URL=http://laravel.test" \
                -e "DUSK_DRIVER_URL=http://selenium:4444/wd/hub" \
                "$APP_SERVICE" \
                php artisan dusk "$@"
        else
            is_not_running
        fi

    # Proxy the "dusk:fails" command to the "php artisan dusk:fails" Artisan command...
    elif [ "$1" == "dusk:fails" ]; then
        shift 1

        if [ "$EXEC" == "yes" ]; then
            docker-compose exec \
                -u \
                -e "APP_URL=http://laravel.test" \
                -e "DUSK_DRIVER_URL=http://selenium:4444/wd/hub" \
                "$APP_SERVICE" \
                php artisan dusk:fails "$@"
        else
            is_not_running
        fi

    # Initiate a Laravel Tinker session within the application container...
    elif [ "$1" == "tinker" ] ; then
        shift 1

        if [ "$EXEC" == "yes" ]; then
            docker-compose exec \
                -u \
                "$APP_SERVICE" \
                php artisan tinker
        else
            is_not_running
        fi

    # Proxy Node commands to the "node" binary on the application container...
    elif [ "$1" == "node" ]; then
        shift 1

        if [ "$EXEC" == "yes" ]; then
            docker-compose exec \
                -u \
                "$APP_SERVICE" \
                node "$@"
        else
            is_not_running
        fi

    # Proxy NPM commands to the "npm" binary on the application container...
    elif [ "$1" == "npm" ]; then
        shift 1

        if [ "$EXEC" == "yes" ]; then
            docker-compose exec \
                -u \
                "$APP_SERVICE" \
                npm "$@"
        else
            is_not_running
        fi

    # Proxy NPX commands to the "npx" binary on the application container...
    elif [ "$1" == "npx" ]; then
        shift 1

        if [ "$EXEC" == "yes" ]; then
            docker-compose exec \
                -u \
                "$APP_SERVICE" \
                npx "$@"
        else
            is_not_running
        fi

    # Proxy YARN commands to the "yarn" binary on the application container...
    elif [ "$1" == "yarn" ]; then
        shift 1

        if [ "$EXEC" == "yes" ]; then
            docker-compose exec \
                -u \
                "$APP_SERVICE" \
                yarn "$@"
        else
            is_not_running
        fi

    # Initiate a MySQL CLI terminal session within the "mysql" container...
    elif [ "$1" == "mysql" ]; then
        shift 1

        if [ "$EXEC" == "yes" ]; then
            docker-compose exec \
                mysql \
                bash -c 'MYSQL_PWD=${MYSQL_PASSWORD} mysql -u ${MYSQL_USER} ${MYSQL_DATABASE}'
        else
            is_not_running
        fi

    # Initiate a PostgreSQL CLI terminal session within the "pgsql" container...
    elif [ "$1" == "psql" ]; then
        shift 1

        if [ "$EXEC" == "yes" ]; then
            docker-compose exec \
                 pgsql \
                 bash -c 'PGPASSWORD=${PGPASSWORD} psql -U ${POSTGRES_USER} ${POSTGRES_DB}'
        else
            is_not_running
        fi

    # Initiate a Bash shell within the application container...
    elif [ "$1" == "shell" ] || [ "$1" == "bash" ]; then
        shift 1

        if [ "$EXEC" == "yes" ]; then
            docker-compose exec \
                -u \
                "$APP_SERVICE" \
                bash
        else
            is_not_running
        fi

    # Initiate a root user Bash shell within the application container...
    elif [ "$1" == "root-shell" ] ; then
        shift 1

        if [ "$EXEC" == "yes" ]; then
            docker-compose exec \
                "$APP_SERVICE" \
                bash
        else
            is_not_running
        fi

    # Share the site...
    elif [ "$1" == "share" ]; then
        shift 1

        if [ "$EXEC" == "yes" ]; then
            docker run --init beyondcodegmbh/expose-server:latest share http://host.docker.internal:"$APP_PORT" \
            --server-host=laravel-sail.site \
            --server-port=8080 \
            "$@"
        else
            is_not_running
        fi

    # Proxy PHP commands to the "php" binary on the application container...
    elif [ "$1" == "dump" ]; then
        shift 1

        if [ "$EXEC" == "yes" ]; then

            docker-compose exec \
                mysql \
                mysqldump $DB_DATABASE -u$DB_USERNAME -p$DB_PASSWORD --no-data --skip-comments > dump.sql

        else
            is_not_running
        fi

    # Pass unknown commands to the "docker-compose" binary...
    else
        docker-compose "$@"
    fi
else
    docker-compose ps
fi
