# README #

Welcome to the Open Source repository of the e-Proficiency Testing (ePT) software

### How do I get set up? ###
#### Prerequisites
- Ubuntu 18.04
- Internet facing, static IP address with domain name

#### Provision Server
##### Install Apache 2 Web Server, PHP 5 & Certbot
```
sudo apt update
sudo apt upgrade -y
sudo apt install apache2
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update
sudo apt install php5.6 mysql-client php5.6-mysql php5.6-gd software-properties-common -y
sudo add-apt-repository universe
sudo add-apt-repository ppa:certbot/certbot
sudo apt-get update
sudo apt-get install certbot python3-certbot-apache

##### Allow Web Tarffic to the Server
sudo ufw allow 'Apache'
```

#### Deploy Source Code & Configuration
```
sudo mkdir /var/www/ept-staging.systemone.id
sudo chown -R $USER:$USER /var/www/ept-staging.systemone.id
sudo chmod -R 777 /var/www/ept-staging.systemone.id

ssh-keygen -t rsa
cat .ssh/id_rsa.pub
# Add contents of ~/.ssh/id_rsa.pub to GitHub SSH keys
git config --global user.email "<your GitHub email address>"
git config --global user.name "<your full name>"
cd /var/www/ept-staging.systemone.id
git clone git@github.com:SystemOneId/ept.git .
vi application/configs/application.ini
# Edit with correct email, URL and database configuration values
vi application/configs/config.ini
# Edit with correct Google API Key
sudo vi /etc/apache2/sites-available/ept-staging.systemone.id.conf
sudo a2ensite ept-staging.systemone.id.conf
sudo a2dissite 000-default.conf
sudo apache2ctl configtest
sudo a2enmod rewrite
sudo systemctl restart apache2

# Create the upload directories
mkdir /var/www/ept-staging.systemone.id/public/uploads/document -p
mkdir /var/www/ept-staging.systemone.id/public/uploads/generated-reports
mkdir /var/www/ept-staging.systemone.id/public/uploads/reports
sudo chmod -R 777 /var/www/ept-staging.systemone.id/public/uploads
```

#### If Not a Staging Environment Edit .htaccess & .htpasswd to Protect Site Using Basic Auth
```
vi public/.htaccess
# Remove the following Lines
AuthType Basic
AuthName "ePT Test Environment Login"
AuthUserFile /var/www/ept-staging.systemone.id/.htpasswd
require valid-user
```
Otherwise, the Username & Password for getting into a site with this style of Basic Auth are
- Username: epttester
- Password: Q7znFkzMUSCA1r4A

#### Setup SSL Certificate
```
sudo certbot --apache
# Follow the wizard
tar -zcvf ~/etc-letsencrypt.tar.gz /etc/letsencrypt
# copy ~/etc-letsencrypt.tar.gz to the relevant sub-directory on ePT google drive directory https://drive.google.com/drive/u/1/folders/1CAYmMOAKExvfctmwJ62MpfLLCpJnEeq7

sudo systemctl restart apache2
```


* [Download the ePT Source Code](https://github.com/SystemOne/ept/releases) and put it into your server's root folder (www or htdocs).
* Create a database and [import the sql file that you can find in the downloads section of this repository](https://github.com/SystemOne/ept/releases)
* Modify the config file (application/configs/application.ini) and update the database parameters
* Create a virtual host pointing to the public folder of the source code

#### Setup Cron Jobs on Server ####
`
$ sudo crontab -u www-data -e
`

enter the following jobs
```
*/2 * * * * php -f /var/www/ept-2.4/cron/SendMobilePushNotifications.php
*/2 * * * * php -f /var/www/ept-2.4/cron/SendMailAlerts.php
```

#### Renew SSL Certificate
The SSL certificate for the website was essued by [Let's Encrypt](https://letsencrypt.org) and requested using [certbot](https://certbot.eff.org/lets-encrypt/ubuntubionic-apache)

There should be a cron job or service that automatically renews the SSL certificate every 3 months but if it expires, it can be manually renewed by following these steps:
1. Ensure that the let's encrypt config is present on the server in /etc/letsencrypt. If it isn't restore it from the relevant `etc-letsencrypt.tar.gz` archive in the [ePT Google Drive](https://drive.google.com/drive/u/1/folders/1CAYmMOAKExvfctmwJ62MpfLLCpJnEeq7)
2. Run the [certbot client](https://certbot.eff.org/lets-encrypt/ubuntubionic-apache) `$ sudo certbot renew`

---

## Dev Tools

### Docker
The docker environment has been provisioned for local development machines according to the current production environment. To use this environment,

1. Ensure that [Docker Engine](https://docs.docker.com/engine/) and [Docker Compose](https://docs.docker.com/compose/) is correctly installed and working,

2. Copy `docker/.env` to `docker/.env.local` and set variables accordingly
```sh
DATABASE_PASS="a strong password"
GOOGLE_MAPS_KEY="key from Google (optional)"
```
3. Ensure that the following ports are available on your localhost,

    * `8000` nginx - The web application gateway
    * `8001` phpMyAdmin - A web GUI for the local database
    * `8002` Mailhog - An email testing client for outbound emails


4. Navigate to the `docker` folder and run:
```sh
docker-compose --env-file=.env.local up
```
5. During the first build, Docker will create an initial sample database and then run the stack.

6. To restore the sample database,
    1. Stop the database container,
    2. Delete the `docker/var/mysql` directory,
    3. Start the container again (this may take a couple minutes).

You can find relevant logs in the `docker/var` directory. Inspect containers and the `./docker/docker-compose.yml` file for more information.

### Postman

A [Postman](https://www.postman.com/) collection can be found in `ePT.postman_collection.json`. The collection contains requests that are generally cumbersome to develop/debug through the ePT interface such as PDF generation and API calls etc.

Import the collection JSON file into a workspace on Postman, and configure the collection variables as follows;

| Variable | Current Value | Description |
| :--- | :--- | :--- |
| host | http://localhost:8000 | The local / staging / production host as `protocol://host:port` |
| sessionid | `<sessid>` | Your session id after you have logged in to the ePT interface, <br><br>_Use the network inspector in Chrome to extract the `sessid` portion of the `Cookie: PHPSESSID=<sessid>` request header after you have logged in_ |

You should now be able to use Postman to debug respective endpoints. If you receive a `500 Internal Server Error` response, the chances are the `sessionid` parameter is incorrect or needs to be renewed.

_Do not store secrets in the "Initial Value" of collection variables as these values are sharable. Use "Current Value" only unless you intend to share the values with others._

---

### Who do I talk to? ###

* You can reach us at support@systemone.id
