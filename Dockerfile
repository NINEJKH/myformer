FROM php:7.3-alpine

ENTRYPOINT ["/usr/local/bin/myformer"]

WORKDIR /dumps

COPY myformer.phar /usr/local/bin/myformer
