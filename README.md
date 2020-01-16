# Deployment

- frontend

```bash
$ docker-compose build --build-arg APP_ENV=prod php
$ docker image tag gildasquemenerme_php:latest gcr.io/oss-contrib-265212/frontend:latest
$ docker push gcr.io/oss-contrib-265212/frontend:latest
```
