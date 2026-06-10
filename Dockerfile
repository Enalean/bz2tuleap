FROM docker.io/library/composer:2.10.1@sha256:26cb27d1f66af16f8f4d83d522f79ce9d1ea6d80e6bc2d3926af01b000f102ba AS builder

COPY . /app

WORKDIR /app/

RUN composer install --no-dev -a

FROM docker.io/library/alpine:3.24.0@sha256:a2d49ea686c2adfe3c992e47dc3b5e7fa6e6b5055609400dc2acaeb241c829f4

RUN apk add --no-cache php83 php83-openssl php83-dom php83-simplexml php83-xmlreader ca-certificates

WORKDIR /app/

COPY --from=builder /app .

CMD [ "sh", "/app/help.sh" ]
