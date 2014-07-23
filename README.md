# Shongo Authentication Utils

Shongo authentication and authorization is handled by two components:

* **Shongo AuthN Server** - provides federated authentication
* **Perun WS** - provides access to the Perun system, which is used as a user registratioo authority and a group management system

These systems require external applications like [Apache](http://httpd.apache.org/) and [Shibboleth SP](http://shibboleth.net/). This repo contains information, configuration samples, templates etc. which will help you to run those required applications.

## Requirements

Shongo AuthN Server:

* Apache 2.2.*
* Shibboleth SP 2.5.*

Perun WS:

* Apache 2.2.*

## Shibboleth configuration

### Variables

As a base for the main configuration you can use the sample included in this repo - `config/shibboleth2.xml`. You need to set the following variables used in the sample:

* `ENTITY_ID` - the entity ID of the SP which is registered in the federation
* `DS_FILTER` - base64-encoded custom filter settings for the eduID.cz discovery service - filters the IdPs, which will be provided for selection, see https://www.eduid.cz/en/tech/wayf-sp.
* `SUPPORT_EMAIL` - this email will be shown to users when an error occurs
* `SHONGO_AUTH_UTILS_DIR` - the full path of the directory where this repo is being installed

### Error handling

Custom error messages are configured in the `<Errors/>` element. You can set some variables, like `supportContact` and `styleSheet` as well as the location of the error messages templates. Our sample configuration uses templates from this repo (in the `templates/` directory) and a [Bootstrap](http://getbootstrap/) stylesheet from a CDN. 

The `accessError.html` template contains a link to a special "diagnostic" page, which logs all available data and presents the user with a reference string to use when communicating with support. The diagnostic page is a simple PHP script and it requires additional configuration in Apache, see below.

See the [official documentation](https://wiki.shibboleth.net/confluence/display/SHIB2/NativeSPErrors) for more information on customizing Shibboleth SP errors.

### Metadata providers

You need to configure metadata providers for the eduID.cz federation and the standalone Hostel IdP. The signatures of the both metadata files must be validated with the designated certificate. Make sure to download the metadata signing certificate, place it somewhere in your filesystem (for example in `/etc/ssl/certs`) and use it for signature validation.

Sample:
```xml
<!-- eduID.cz -->                                            
<MetadataProvider type="XML" uri="https://metadata.eduid.cz/entities/eduid+idp"
    backingFilePath="eduid+idp.xml" reloadInterval="3600">
    <MetadataFilter type="Signature" certificate="/etc/ssl/certs/metadata.eduid.cz.crt.pem"/>      
</MetadataProvider>
```

For more information see the `<MetadataProvider/>` elements in the sample and the [current metadata information](https://www.eduid.cz/cs/tech/summary).

### Certificate

Set the paths to the key and the certificate, used by the SP in its communication with the IdP:

```xml
<CredentialResolver type="File" 
            key="sp-key.pem" 
            certificate="sp-cert.pem"/>
```

## Apache configuration

### Variables

Use the `config/apache22.conf` sample as a base for you configuration. You need to replace the variables with the appropriate values. Then, place the configuration fragment into your virtual host configuration.

Variables:

* `SHONGO_AUTHN_SERVER_DIR` - the directory, where Shongo AuthN Server is installed
* `SHONGO_AUTH_UTILS_DIR` - the dorectory where this repo is installed
* `PERUN_WS_DIR` - the directory where Perun WS is installed

### Access control

The access control filter `acl/filter.xml` is used by Shibboleth SP, but it is specified in the Apache configuration for more flexibility. It can be changed without the need to restart the shibd daemon.

In case of an *access* error, the `template/accessError.html` page is shown to the user. Currently it also contains a link to a "diagnostics" page. To be able to show that page, you have to configure it in Apache - see the sample configuration. The page should be protected by Shibboleth, but without any access control.