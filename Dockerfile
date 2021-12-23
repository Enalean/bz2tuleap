FROM composer:2.2.0 as builder

COPY . /app

WORKDIR /app/

RUN composer install --no-dev -a

FROM alpine:3.15.0

RUN apk add --no-cache php7 php7-openssl php7-dom php7-simplexml php7-xmlreader ca-certificates

WORKDIR /app/

COPY --from=builder /app .

CMD [ "sh", "/app/help.sh" ]
