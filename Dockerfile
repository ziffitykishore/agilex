FROM 754729414608.dkr.ecr.us-east-2.amazonaws.com/magento2:php7.1-nginx

ARG MAGE_MODE=development
ARG NODEJS_VERSION=v9.2.1
ARG MAGE_FRONTEND_THEMES="Magento/backend SomethingDigital/bryantpark"
ARG REVISION=v2.10.0

ENV "MAGENTO_ROOT" /var/www/vhosts/magento.test/current

# copy private key
COPY --chown=magento:magento files/vault_pass /home/magento/.vault_pass

# give sudo privilege to magento user
RUN echo "magento      ALL=(ALL)       NOPASSWD: ALL" > /etc/sudoers.d/magento

# clone repository
COPY --chown=magento:nginx ./ ${MAGENTO_ROOT}

# switch to user magento with nginx as group
USER magento:nginx

WORKDIR ${MAGENTO_ROOT}

# run prebuild play
COPY --chown=magento:magento files/vault_pass /home/magento/.vault_pass
RUN ansible-playbook provision.yml \
    --vault-id /home/magento/.vault_pass \
    -t prebuild
RUN rm "${MAGENTO_ROOT}/files" -rf

# run build play
RUN ansible-playbook provision.yml -t build

# set volume
VOLUME ${MAGENTO_ROOT}

# exposed ports
EXPOSE 80
EXPOSE 443

# switch to root:nginx user:group
USER root

ENTRYPOINT ["/usr/bin/tini", "--", "/docker-entrypoint.sh", "gosu", "magento:nginx"]

CMD ["tail", "-f", "/etc/machine-id"]
