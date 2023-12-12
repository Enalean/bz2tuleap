FROM composer:2.6.6 as builder

COPY . /app

WORKDIR /app/

RUN composer install --no-dev -a

FROM alpine:3.19.0

RUN apk add --no-cache php81 php81-openssl php81-dom php81-simplexml php81-xmlreader ca-certificates

WORKDIR /app/

COPY --from=builder /app .

CMD [ "sh", "/app/help.sh" ]
