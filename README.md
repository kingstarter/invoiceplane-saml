# InvoicePlane SAML Authentication Plugin

This package is planned as a plugin to integrate SAML to [InvoicePlane 1 & 2](https://github.com/InvoicePlane/InvoicePlane). Using the package let InvoicePlane act as an SAML SP (Service Provider). An IDP integration is currently not planned. As during package development only InvoicePlane 1 is available, all further configurations are IP1 specific. 

## Description

SAML generally implements SingleSignOn (SSO) and SingleLogout (SLO) between different web applications. This package allows the implementation of SingleSignOn. Though configurations of SingleLogout are available, the SLO part has not been tested and might therefore not work correctly. 

Automatic account creation has been added as well. When signing in via the IDP with an unknown account an adminstator user is created using both the given name and email and some standard configurations. 

## Installation and configuration

The package should be installed using ```composer```: 

``` 
composer require "kingstarter/laravel-saml":"dev-master"
```

Basically all that has to be done is calling the install script and configuring both the SP and the IDP for SAML authentication. The plugin install script will modify InvoicePlane to support SAML:

```
cd {ip-root-path}/
perl vendor/kingstarter/invoiceplane-saml/install-plugin.pl -v
```

### Configuring InvoicePlane as SP

After running the install script the general settings have been appended by a SAML entry. Login to InvoicePlane, go within system settings to the Saml settings. There are some sample settings given. Upon saving all settings will be added to the database. A metadata file is not needed.

### Configuring the IDP

As ```Entity ID / Issuer``` the page address can be used, e.g. ```https://invoiceplane.example.com```. The SAML-Response consumer URL (SP login destination) is the samlauth endpoint:

```
https://invoiceplane.example.com/index.php/sessions/samlauth
```
 
### Certificates

Certificates can be added using the integration settings. Alternatively they should be placed within the `/var/ip_certs` directory.

- SP Crt: `/var/ip_certs/ip-sp.crt`
- SP Key: `/var/ip_certs/ip-sp.crt`
- IDP Crt: Stored within database using the configuration page.

## Troubleshooting

### Note for CSRF protection

Within ```application/config/config.php``` the samlauth API endpoint needs to be added within the ```csrf_exclude_uris``` array (should be done automatically by the install script). In case IP is blocking with an 403 forbidden error it might be necessary to check the config entry:

```
$config['csrf_exclude_uris'] = array(
    'sessions/samlauth'
); 
```

## Contributors
* The package is redesigned using a fork of [Trajches Kanboard SAMLAuth Plugin](https://github.com/steve-ks/SamlAuth) as model.
