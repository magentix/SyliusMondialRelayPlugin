## Installation
```bash
$ composer require magentix/mondial-relay-plugin
```

Add plugin dependencies to your AppKernel.php file:
```php
public function registerBundles()
{
    $bundles = [
        ...
        new \MagentixMondialRelayPlugin\MagentixMondialRelayPlugin(),
    ];
}
```

Import required config in your `app/config/config.yml` file:

```yaml
# app/config/config.yml

imports:
    ...   
    - { resource: "@MagentixMondialRelayPlugin/Resources/config/config.yml" }