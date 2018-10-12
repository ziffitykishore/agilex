FROM 754729414608.dkr.ecr.us-east-2.amazonaws.com/magento2:php7.1-nginx

ARG MAGE_MODE=development
ARG NODEJS_VERSION=v9.2.1
ARG MAGE_FRONTEND_THEMES="Magento/backend SomethingDigital/bryantpark"
ARG REVISION=v2.10.0

ENV "MAGENTO_ROOT" /var/www/vhosts/magento/current

# copy private key
COPY --chown=magento:magento files/id_rsa /home/magento/.ssh/id_rsa

# set chomod key
RUN chmod 0700  /home/magento/.ssh -R \
  && chown magento:magento /home/magento/.ssh  -R \
  && chmod 0600 /home/magento/.ssh/id_rsa

# clone repository
COPY --chown=magento:magento ./ ${MAGENTO_ROOT}
RUN rm "${MAGENTO_ROOT}/files" -rf

WORKDIR ${MAGENTO_ROOT}
# add auth.json

# switch to user magento with nginx as group
USER magento:nginx

# update
COPY --chown=magento:magento ./files/auth.json /home/magento/.composer/auth.json

# switch node version
#RUN nvm use "${NODEJS_VERSION}"

# First, let's grab all the latest code, including submodules.
RUN git fetch --recurse-submodules --tags origin \
    && git checkout -f "${REVISION}" \
    && git submodule update --init --recursive

# Remove vendor directory. Without this any removed patches are never reverted.
RUN rm -rf vendor/

# get composer
RUN composer install

# Build bundle
USER root
RUN bash -lc 'bundle install' \
    && chown magento:nginx ${MAGENTO_ROOT} -R \
    && chmod u=rwX,g=rwX,o=rX ${MAGENTO_ROOT} -R
USER magento:nginx

# build yarn
RUN yarn \
    && pushd vendor/somethingdigital/magento2-theme-bryantpark && yarn && popd \
    && pushd vendor/snowdog/frontools && yarn && popd

# set volume
VOLUME ${MAGENTO_ROOT}

# exposed ports
EXPOSE 80
EXPOSE 443

# switch to root:nginx user:group
USER root:nginx

ENTRYPOINT ["/usr/bin/tini", "--", "/docker-entrypoint.sh"]

CMD ["tail", "-f", "/etc/machine-id"]
