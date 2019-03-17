#!/usr/bin/env bash

readlink_bin="${READLINK_PATH:-readlink}"
if ! "${readlink_bin}" -f test &> /dev/null; then
  __DIR__="$(dirname "$(python -c "import os,sys; print(os.path.realpath(os.path.expanduser(sys.argv[1])))" "${0}")")"
else
  __DIR__="$(dirname "$("${readlink_bin}" -f "${0}")")"
fi

# required libs
source "${__DIR__}/.bash/functions.shlib"

set -E
trap 'throw_exception' ERR

# create version file
if [[ ! -z "${TRAVIS_TAG}" ]]; then
  echo "${TRAVIS_TAG}" > version.txt
elif [[ ! -z "${TRAVIS_COMMIT}" ]]; then
  echo "${TRAVIS_COMMIT}" > version.txt
fi

if [[ -z "${DOCKER_REPO}" ]]; then
  DOCKER_REPO="$(basename "${__DIR__}")"
fi

# composer
consolelog "composer install"
composer install \
  --no-interaction \
  --prefer-dist \
  --no-suggest \
  --quiet \
  --verbose

# run tests
if ! vendor/bin/phpunit; then
  exit 1
fi

consolelog "composer cleanup"
composer install \
  --no-dev \
  --quiet \
  --verbose

composer dump-autoload \
  --no-dev \
  --classmap-authoritative \
  --quiet \
  --verbose

php create-phar.php myformer.phar
chmod +x myformer.phar

./myformer.phar --version

# build docker container
docker build --pull -t "${DOCKER_REPO}:latest" .
