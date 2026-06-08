FROM docker.io/library/composer:2.10.1@sha256:41959f55087549989efcdfe953977b64e98e07ca0d7532d7e4b7fe1a90cc4159 AS builder

COPY . /app

WORKDIR /app/

RUN composer install --no-dev -a

FROM docker.io/library/alpine:3.24.1@sha256:28bd5fe8b56d1bd048e5babf5b10710ebe0bae67db86916198a6eec434943f8b

RUN apk add --no-cache php83 php83-openssl php83-dom php83-simplexml php83-xmlreader ca-certificates

WORKDIR /app/

COPY --from=builder /app .

CMD [ "sh", "/app/help.sh" ]
