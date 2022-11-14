FROM composer:2.4.4 as builder

COPY . /app

WORKDIR /app/

RUN composer install --no-dev -a

FROM alpine:3.16.3

RUN apk add --no-cache php8 php8-openssl php8-dom php8-simplexml php8-xmlreader ca-certificates

WORKDIR /app/

COPY --from=builder /app .

CMD [ "sh", "/app/help.sh" ]
