# ActionEaseKit Overview

**ActionEaseKit** is a Symfony library designed to help developers rapidly create actions and provide convenient services. It simplifies the development process by streamlining action creation and enhancing access to Symfony's functionality.

## Key Features

1. **Fast action creation:** Simplify the process of creating actions in Symfony with predefined service architecture.
2. **Service integration:** Provides additional services and abstract classes to standardize actions, enhancing code reusability and maintainability.
3. **JSON EntityData support:** Offers tools to easily work with JSON data within database columns, making it convenient to manage complex data structures.

## Prerequisites

- **Symfony version:** 6.0 or later
- **PHP version:** 8.1 or later

Ensure you have these installed before proceeding with the library setup.

## Installation
To start using ActionEaseKit, install it via Composer:

```bash
composer require fanatov37/actioneasekit
```

# Usage

## Example of Action

Define your controller that extends the base `AbstractRequestController` provided by the library:

```php
use ActionEaseKit\Base\Controller\AbstractRequestController;

class RequestController extends AbstractRequestController
{
    public function __construct(
        RequestActionService $requestActionService
        // add more action services...
    )
    {
        parent::__construct($requestActionService);
    }
}
```

### Adding Route

You need to define a route for the action. Here's an example of how to add a route in your Symfony application:
```yaml
api_request:
    path: /api/request
    controller: App\Controller\RequestController::indexAction
```
or in Controller
```php
#[Route('/api/request', name: 'api_request')]
class RequestController extends AbstractRequestController
{}
```

### Example of Action Service

Create a service that extends the `AbstractActionService`. You can define various actions within this service. Use role attributes if needed for specific actions.
```php
use ActionEaseKit\Base\Service\AbstractActionService;
use ActionEaseKit\Attributes\RequiresRole;

class RequestService extends AbstractActionService
{
    public function firstAction(array $args): array
    {
        return $args;
    }

    #[RequiresRole(['ROLE_ADMIN', 'ROLE_USER'])]
    public function secondAction(int $id, string $name): array
    {
        return [
            'id' => $id,
            'name' => $name,
            'email' => $this->getUser()->getUserIdentifier(),
            'roles' => $this->getUser()->getRoles()
        ];
    }
}
```


### POST Request Example

All POST requests will be made to the `/api/request` endpoint, as specified in the route configuration.

When making a request, you need to specify:
1. **service**: The name of the service that you want to call (e.g., `RequestService`).
2. **action**: The specific action within the service that you want to execute (e.g., `firstAction` or `secondAction`).
3. **arguments**: The arguments required for the action, passed as an associative array.


Here’s how you can send a POST request to trigger your actions:

#### First Action:

```json
{
    "service": "RequestService",
    "action": "firstAction",
    "arguments": [{"id": 1, "name": "myName"}]
}
```

#### Second Action:

```json
{
    "service": "RequestService",
    "action": "secondAction",
    "arguments": {"id": 1, "name": "myName"}
}
```



## Example of Usage: IndicatorEntity (Quick Data Access from JSON Columns)

This section demonstrates how to quickly access JSON data stored in columns using the `IndicatorEntity`. Let's assume we have an entity `User` that includes two JSON columns: `data` and `activity_log`.

#### Example Entity: User

In this example, the `User` entity extends `IndicatorEntity` to leverage quick access to JSON fields, like user profiles or activity logs, using simple getter methods.
Trait DataPropertyTrait has default data json column


```php
#[ORM\Table(name: 'users')]
class User extends IndicatorEntity implements PasswordAuthenticatedUserInterface, UserInterface
{
    use DataPropertyTrait;

    public const ACTIVITY_LOG_PROPERTY_NAME = 'activityLog';

    // define your json fields
    protected const INDICATOR_DATA = [
        'profile' => [
            "first_name",
            "last_name",
            "bio",
            "date_of_birth",
            "location",
            "interests"
        ],
        'settings' => [
            'notifications' => ['email', 'sms'],
            'privacy' => ['profile_visible', 'last_seen_visible']
        ]
    ];

    protected const INDICATOR_ACTIVITYLOG = [
        "activity",
        "timestamp",
        "ip_address",
        "location",
        "device",
        "browser",
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    // define more json fields
    #[ORM\Column(name: "activity_log", type: "json", nullable: true, options: ["jsonb" => true])]
    protected ?array $activityLog = null;
}
```

### JSON Data Structure

- **data Column**: Contains user profile and settings.
- **activity_log Column**: Tracks user activities, such as logins or password changes.

#### Example of `data` Column
```json
{
  "profile": {
    "first_name": "Jane",
    "last_name": "Smith",
    "bio": "Passionate about technology and design.",
    "date_of_birth": "1992-05-30",
    "location": "San Francisco, USA",
    "interests": [
      "design",
      "photography",
      "traveling"
    ]
  },
  "settings": {
    "notifications": {
      "email": true,
      "sms": true
    },
    "privacy": {
      "profile_visible": false,
      "last_seen_visible": true
    }
  }
}
```

#### Example of `activity_log` Column
```json
{
  "device": "MacBook Pro",
  "browser": "Chrome",
  "activity": "password_change",
  "location": "Tirana, Albania",
  "timestamp": "2024-10-22T16:45:00Z",
  "ip_address": "192.168.1.15"
}
```

### Quick Access to JSON Data
Here’s an example of how to quickly access data from JSON columns using the `getIndicator()` method.
```php
    #[RequiresRole(['ROLE_ADMIN'])]
    public function secondAction(int $id, string $name) : array
    {
        /** @var User $user */
        $user = $this->getUser();

        //get from data columns by default. from trait DataPropertyTrait
        $location = $user->getIndicator('profile:location');
        $notifications = $user->getIndicator('settings:notifications');
        $notificationsSMS = $user->getIndicator('settings:notifications:sms');


        // change property for get data from activity_log columns
        $user->setCurrentPropertyName(User::ACTIVITY_LOG_PROPERTY_NAME);
        $activityLogDevice = $user->getIndicator('device');

        return [
            'id' => $id,
            'name' => $name,
            'email' => $this->getUser()->getUserIdentifier(),
            'roles' => $this->getUser()->getRoles(),
            'location' => $location,
            'notification' => $notifications,
            'notification_sms' => $notificationsSMS,
            'device' => $activityLogDevice
        ];
    }
```

### Explanation of the Example:

#### Accessing Data from `data` Column:

- By default, the `getIndicator()` method fetches data from the `data` column.
    - `profile:location` fetches the user's location (e.g., "San Francisco, USA").
    - `settings:notifications:sms` checks whether SMS notifications are enabled.

#### Switching to `activity_log` Column:

- To access the `activity_log` column, the `setCurrentPropertyName()` method is used to point to `activityLog`.
- The `getIndicator()` method then fetches data such as the device used during the latest activity.

This method greatly simplifies data retrieval from complex JSON columns, allowing quick and flexible access to nested JSON structures.
