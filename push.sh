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

echo "${DOCKER_PASSWORD}" | docker login -u "${DOCKER_USERNAME}" --password-stdin

consolelog "pushing..."

if [[ ! -z "${TRAVIS_TAG}" ]]; then
  docker tag "${DOCKER_REPO}:latest" "${DOCKER_REPO}:${TRAVIS_TAG}"
  docker push "${DOCKER_REPO}:${TRAVIS_TAG}"
elif [[ ! -z "${TRAVIS_BRANCH}" ]] && [[ "${TRAVIS_BRANCH}" == "master" ]]; then
  docker push "${DOCKER_REPO}:latest"
fi
