FROM php:8.5 AS base
WORKDIR /app
COPY --from=composer/composer:latest-bin /composer /usr/bin/composer
RUN apt-get update && apt-get install -y git zip
RUN composer create-project symfony/skeleton:"8.0.*" .
COPY Makefile.docker composer.json composer.lock symfony.lock ./
RUN make -f Makefile.docker ENVIRONMENT=docker
RUN make build
RUN make install
EXPOSE 3000
CMD ["php", "-S", "0.0.0.0:3000", "-t", "./public"]

FROM base AS build
COPY . .

FROM scratch AS artifact
COPY --from=build /app /artifact