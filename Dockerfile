FROM composer:2.8.11 as builder

COPY . /app

WORKDIR /app/

RUN composer install --no-dev -a

FROM alpine:3.22.1

RUN apk add --no-cache php83 php83-openssl php83-dom php83-simplexml php83-xmlreader ca-certificates

WORKDIR /app/

COPY --from=builder /app .

CMD [ "sh", "/app/help.sh" ]
