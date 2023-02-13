FROM composer:2.5.3 as builder

COPY . /app

WORKDIR /app/

RUN composer install --no-dev -a

FROM alpine:3.17.2

RUN apk add --no-cache php81 php81-openssl php81-dom php81-simplexml php81-xmlreader ca-certificates

WORKDIR /app/

COPY --from=builder /app .

CMD [ "sh", "/app/help.sh" ]
