# README #

Welcome to the Open Source repository of the e-Proficiency Testing (ePT) software

### How do I get set up? ###

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

### Who do I talk to? ###

* You can reach us at brichards@systemone.id
