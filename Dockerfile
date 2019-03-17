FROM php:7.2-alpine

ENTRYPOINT ["/usr/local/bin/myformer"]

COPY myformer.phar /usr/local/bin/myformer
