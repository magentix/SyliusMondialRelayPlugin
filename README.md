## Notes

* This Plugin allows to add the Mondial Relay delivery method.

## Screenshot

![Alt text](doc/images/shipping.png "Mondial Relay Shipping Method")

## Installation

```bash
$ composer require magentix/mondial-relay-plugin
```

Add plugin dependencies to your `AppKernel.php` file:

```php
# app/AppKernel.php

public function registerBundles()
{
    $bundles = [
        ...
        new \MagentixPickupPlugin\MagentixPickupPlugin(),
        new \MagentixMondialRelayPlugin\MagentixMondialRelayPlugin(),
    ];
}
```

Import required config in your `app/config/config.yml` file:

```yaml
# app/config/config.yml

imports:
    ...
    - { resource: "@MagentixPickupPlugin/Resources/config/config.yml" }
    - { resource: "@MagentixMondialRelayPlugin/Resources/config/config.yml" }
```
    
Import routing in your `app/config/routing.yml` file:

```yaml
# app/config/routing.yml
...

magentix_pickup_plugin:
    resource: "@MagentixPickupPlugin/Resources/config/routing.yml"
```

Deploy Assets:

```bash
php bin/console sylius:theme:assets:install
```

Finally, in *Shipping Method* section from admin, add new Method with *Mondial Relay* Calculator.

## Configuration

* Shipping charges
  * **Calculator**: Mondial Relay
      * **API WSDL**: https://www.mondialrelay.fr/WebService/Web_Services.asmx?WSDL
      * **API Company**: BDTEST13 (test mode)
      * **API Reference**: 11 (test mode)
      * **API Key**: PrivateK (test mode)