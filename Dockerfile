FROM alpine:3.7

RUN apk add --no-cache php7 php7-openssl php7-dom php7-xmlreader ca-certificates

COPY . /app

CMD [ "sh", "/app/help.sh" ]
