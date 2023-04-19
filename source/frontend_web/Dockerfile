# Docker image for build frontend as a service
FROM bitnami/node:16.14.0

# setup docker in docker
RUN apt-get -y update \
    && apt-get -y autoremove \
    && apt-get clean \
    && apt-get install -y curl \
    zip \
    unzip 

COPY . /app

RUN mkdir /app/dist

WORKDIR /app
RUN yarn

ENV MFOX_WORKING_DIR=/app