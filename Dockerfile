FROM docker.io/library/composer:2.9.8@sha256:2e558bf87f17c847d9f14e72ecedbeee0f8564efee0a3ba93d24a72cb59593b5 AS builder

COPY . /app

WORKDIR /app/

RUN composer install --no-dev -a

FROM docker.io/library/alpine:3.23.4@sha256:5b10f432ef3da1b8d4c7eb6c487f2f5a8f096bc91145e68878dd4a5019afde11

RUN apk add --no-cache php83 php83-openssl php83-dom php83-simplexml php83-xmlreader ca-certificates

WORKDIR /app/

COPY --from=builder /app .

CMD [ "sh", "/app/help.sh" ]
