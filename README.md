# SimpleDI plugin for CakePHP

## Installation

You can install this plugin into your CakePHP application using [composer](https://getcomposer.org).

The recommended way to install composer packages is:

```sh
composer require passchn/cakephp-simple-di
```

Load the plugin:

```sh
bin/cake plugin load Passchn/SimpleDI
```

## Usage

In your `Application.php`:

```php
public function services(ContainerInterface $container): void
{
    Configure::load('app_di');
    
    DIManager::create($container)
        // to add individual services: 
        ->addServices(Configure::readOrFail('DI.services'))
        // to collect multiple services: 
        ->addModules(Configure::readOrFail('DI.modules')) 
        // a plugin can define multiple modules: 
        ->addPlugin(SomePlugin::class); 
}
```

Then, define Factories/Modules in your `app_di.php`:

```php
return [
    'DI' => [
        'services' => [
            NewsletterService::class => NewsletterServiceFactory::class,
            CheckoutService::class => CheckoutServiceFactory::class,
            PaymentService::class => fn () => new PaymentService(),
        ],
        'modules' => [
            MyModule::class,
        ],
    ],
];
```

Factories should be Invokables or class-strings of Invokables. 

You can then use the Service e.g. in Controller Actions:

```php
class ExamplesController {
    
    public function someAction(NewsletterService $service): Response 
    {
        $service->doSomething();
    }
}
```

If real DI is not possible, e.g. in ViewCells, you can use the `ServiceLocator` to receive the container or Services.

```php
\SimpleDI\Module\ServiceLocator\ServiceLocator::getContainer(); // the container instance
\SimpleDI\Module\ServiceLocator\ServiceLocator::get(NewsletterService::class); // the service
```

This only works if you loaded the plugin or registered the container yourself, e.g.:

```php
// in your Application::services()
ServiceLocator::setContainer($container);
```
