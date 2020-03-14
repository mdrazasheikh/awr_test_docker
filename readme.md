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


### Assumptions
* Vehicles are loaded via cron job once a day at 1AM
* Vehicles can be pulled manually via front API : `http://local.domain.com:6080/autotrustimporter/index`
* Only non reserved vehicle are shown in the listing
* Not all the options available with the APIs are shown to Customer
* Products are taken into account as simple products
* All the products are assigned to <code>Preowned vehicles</code> category. The landing page is not configured to show any products. Click on the <code>Preowned vehicles</code> to load the active vehicle list
 
## License

Each Magento source file included in this distribution is licensed under OSL 3.0 or the Magento Enterprise Edition (MEE) license.

[Open Software License (OSL 3.0)](https://opensource.org/licenses/osl-3.0.php).
Please see [LICENSE.txt](https://github.com/magento/magento2/blob/2.3-develop/LICENSE.txt) for the full text of the OSL 3.0 license or contact license@magentocommerce.com for a copy.

Subject to Licensee's payment of fees and compliance with the terms and conditions of the MEE License, the MEE License supersedes the OSL 3.0 license for each source file.
Please see LICENSE_EE.txt for the full text of the MEE License or visit https://magento.com/legal/terms/enterprise.