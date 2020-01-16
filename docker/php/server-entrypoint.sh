#!/bin/sh

PORT=${PORT:-8080}
APP_ENV=${APP_ENV:-dev}

APP_ENV=${APP_ENV} php -S 0.0.0.0:"${PORT}" -t public
