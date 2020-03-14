## Installation instructions
[Reference Material](https://www.magemodule.com/all-things-magento/magento-2-tutorials/docker-magento-2-development/)

This document explains the steps to setup this magento installation with docker (linux/MAC OS).

## Requirements:
* Docker

## Instructions
* Checkout the code to/your/local/path
* RUN <code>sudo -- sh -c "echo '127.0.0.1 local.domain.com' >> /etc/hosts"</code>
* cd to your checked out code path (to/your/local/path)
* Make sure the ports 6080 & 5080 is available as our application and phpmyadmin will be exposed on this port on the host machine
* RUN <code>docker-compose up -d --build</code>
* Load phpmyadmin using <code>http://127.0.0.1:5080/</code>
* RUN <code>docker exec -it web bash</code>
* RUN <code>cd /app</code>
* RUN <code>composer install</code>
* RUN the installer next. Below
```
php bin/magento setup:install \
--admin-firstname=admin \
--admin-lastname=admin \
--admin-email=mdrazasheikh@gmail.com \
--admin-user=admin \
--admin-password='admin123' \
--base-url=http://local.domain.com:6080 \
--base-url-secure=https://local.domain.com:6080 \
--backend-frontname=admin \
--db-host=mysql \
--db-name=magento \
--db-user=root \
--db-password=root \
--use-rewrites=1 \
--language=en_US \
--currency=AED \
--timezone=Asia/Dubai \
--use-secure-admin=0 \
--admin-use-security-key=1 \
--session-save=files
```
* RUN `php src/bin/magento -f setup:static-content:deploy`
* Once finished move contents of `social_images` to `src/pub/static/frontend/Magento/luma/en_US/Magento_Catalog/images/`
* Open your browser. visit http://local.domain.com:6080/admin
* Login with `admin:admin123`
* Go product categories. Create a category under default root category. Rename this to `Preowned Vehicles`. Rename the default root category to some meaningful name.
* All the products that are imported will be assigned to category ids 3(Currently hardcoded for simplicity).
* Create a folder by name tmp on path pub/media if not already exists. This is required currently to load media from external url.
* Crons are configured to run everyday at 1AM to fetch new products.
* For now initiate product fetch by calling the front controller. http://local.domain.com:6080/autotrustimporter/index. This might take sometime to finish.
* Once finished all products will be loaded in admin panel.
* Visit the frontend website to view these products.
* Note that `reserved` products will not be shown on the frontend website
