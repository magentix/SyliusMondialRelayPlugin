<h1 align="center">
    <img src="doc/images/mondial-relay-sylius.png" alt="Mondial Relay for Sylius"/>
    <br />
    <a href="https://packagist.org/packages/magentix/sylius-mondial-relay-plugin" target="_blank">
        <img src="https://poser.pugx.org/magentix/sylius-mondial-relay-plugin/v/stable" />
    </a>
    <a href="https://packagist.org/packages/magentix/sylius-mondial-relay-plugin" target="_blank">
        <img src="https://poser.pugx.org/magentix/sylius-mondial-relay-plugin/downloads" />
    </a>
    <a href="https://packagist.org/packages/magentix/sylius-mondial-relay-plugin" target="_blank">
        <img src="https://poser.pugx.org/magentix/sylius-mondial-relay-plugin/license" />
    </a>
</h1>

# SyliusMondialRelayPlugin

This Plugin allows to add the Mondial Relay delivery method to Sylius.

## Features

* Mondial Relay Pick-up delivery up to 150kg (24R, 24L, DRI)

* Pick-up location in France, Belgium, Luxembourg, Germany and Spain

* On-map or on-list pick-up selection

* Configurable shipping rates based on weight

* Direct shipping management through Mondial Relay web services (shipments registration, shipping labels downloading)

## Overview

With over 40 million parcels delivered through its network of 6500 pick-up points in France (and more than 36000 in Europe) and home delivery services, Mondial Relay is a major actor of delivery to companies and individuals. Thousands of merchants use their services as well as the total control of the logistics process that Mondial Relay offers.

## Screenshot

![Alt text](doc/images/shipping.png "Mondial Relay Shipping Method")

## Installation

### Sylius >= 1.3.0

```bash
$ composer require magentix/sylius-mondial-relay-plugin:^1.3.0
```
    
Add the plugins to the `config/bundles.php` file:

```php
BitBag\SyliusShippingExportPlugin\BitBagSyliusShippingExportPlugin::class => ['all' => true],
Magentix\SyliusPickupPlugin\MagentixSyliusPickupPlugin::class => ['all' => true],
Magentix\SyliusMondialRelayPlugin\MagentixSyliusMondialRelayPlugin::class => ['all' => true],
```

Add the plugin's config by creating the file `config/packages/magentix_sylius_mondial_relay_plugin.yaml` with the following content:

```yaml
imports:
    - { resource: "@BitBagSyliusShippingExportPlugin/Resources/config/config.yml" }
    - { resource: "@MagentixSyliusPickupPlugin/Resources/config/config.yml" }
    - { resource: "@MagentixSyliusMondialRelayPlugin/Resources/config/config.yml" }
```
    
Add the plugin's routing by creating the file `config/routes/magentix_sylius_mondial_relay_plugin.yaml` with the following content:

```yaml
magentix_sylius_pickup_plugin:
    resource: "@MagentixSyliusPickupPlugin/Resources/config/routing.yml"
    
bitbag_shipping_export_plugin:
    resource: "@BitBagSyliusShippingExportPlugin/Resources/config/routing.yml"
    prefix: /admin
```

Finish the installation by updating the database schema and installing assets:

```bash
bin/console doctrine:migrations:diff
bin/console doctrine:migrations:migrate
bin/console assets:install
bin/console sylius:theme:assets:install
```

### Sylius < 1.3.0

```bash
$ composer require magentix/sylius-mondial-relay-plugin:^1.2.0
```

Add plugin dependencies to your `AppKernel.php` file:

```php
# app/AppKernel.php

public function registerBundles()
{
    $bundles = [
        ...
        new \BitBag\SyliusShippingExportPlugin\BitBagSyliusShippingExportPlugin(),
        new \Magentix\SyliusPickupPlugin\MagentixSyliusPickupPlugin(),
        new \Magentix\SyliusMondialRelayPlugin\MagentixSyliusMondialRelayPlugin(),
    ];
}
```

Import required config in your `app/config/config.yml` file:

```yaml
# app/config/config.yml

imports:
    ...
    - { resource: "@BitBagSyliusShippingExportPlugin/Resources/config/config.yml" }
    - { resource: "@MagentixSyliusPickupPlugin/Resources/config/config.yml" }
    - { resource: "@MagentixSyliusMondialRelayPlugin/Resources/config/config.yml" }
```
    
Import routing in your `app/config/routing.yml` file:

```yaml
# app/config/routing.yml
...

magentix_sylius_pickup_plugin:
    resource: "@MagentixSyliusPickupPlugin/Resources/config/routing.yml"
    
bitbag_shipping_export_plugin:
    resource: "@BitBagSyliusShippingExportPlugin/Resources/config/routing.yml"
    prefix: /admin
```

Deploy Assets:

```bash
php bin/console sylius:theme:assets:install
```

## Configuration

In *Shipping Method* section from admin, add and configure new Method with *Mondial Relay* Calculator.

In *Shipping Gateway* section from admin, add and configure new Gateway with *Mondial Relay* Shipping Method.

**API test**

* **API WSDL**: https://www.mondialrelay.fr/WebService/Web_Services.asmx?WSDL
* **API Company**: BDTEST13 (test mode)
* **API Reference**: 11 (test mode)
* **API Key**: PrivateK (test mode)