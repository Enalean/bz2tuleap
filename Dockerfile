FROM alpine:3.3

RUN apk add --no-cache php-cli php-openssl php-dom php-xmlreader ca-certificates

COPY . /app

CMD [ "sh", "/app/help.sh" ]

