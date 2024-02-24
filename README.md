# SimpleDI plugin for CakePHP

## Installation

You can install this plugin into your CakePHP application using [composer](https://getcomposer.org).

The recommended way to install composer packages is:

```
composer require your-name-here/simple-d-i
```

## Usage

In your `Application.php`: 

```php
public function services(ContainerInterface $container): void
{
    Configure::load('app_di');

    $di = new DIManager([
        ServiceFactoryManager::class => Configure::readOrFail('DI.services'),
    ]);

    $di->addDependencies($container);
}
```

Then, define Factories in your `app_di.php`: 

```php
return [
    'DI' => [
        'services' => [
            NewsletterService::class => NewsletterServiceFactory::class,
            CheckoutService::class => CheckoutServiceFactory::class,
            PaymentService::class => PaymentServiceFactory::class,
        ],
    ],
];
```

Services should be Invokables. It is best to implement `\SimpleDI\Module\DI\InvokableFactoryInterface`.

You can then use the Service e.g. in Controller Actions: 

```php
class ExamplesController {
    
    public function someAction(NewsletterService $service): Response 
    {
        $service->doSomething();
    }
}
```