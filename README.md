# Skarabee Weblink

PHP client for the [Skarabee](https://www.skarabee.com) Weblink. For detailed information about the available endpoints
and parameters, refer to the [official documentation](http://weblink.skarabee.com/v36/weblink.asmx).

## Installation

`composer require fw4/skarabee-weblink`

## Usage

```php
use Skarabee\Weblink\Client;

$client = new Client('username', 'password');
```

### Fetching a list of publications

Use the `getPublicationSummaries` method to get a list of published properties.

```php
$publications = $client->getPublicationSummaries();
```

It's possible to filter the list by change/creation date, type of property,
and/or shared status.

```php
use Skarabee\Weblink\Enums\PropertyType;

$modified_since = new DateTime('2021-01-01 12:00:00');
$property_types = [
    PropertyType::Transaction,
    PropertyType::Project,
];
$exclude_shared = true;

$publications = $client->getPublicationSummaries($modified_since, $property_types, $exclude_shared);
```

### Getting details about a publication

Use the `getPublication` method to get the data for a single publication.

```php
$publication = $client->getPublication($publication_id);
$bedrooms = $publication->property->numberOfBedrooms;
```

### Fetching a list of published projects

Use the `getProjectSummaries` method to get a list of published projects.

```php
$projects = $client->getProjectSummaries();
```

It's possible to filter the list by change/creation date and/or shared status.

```php
$modified_since = new DateTime('2021-01-01 12:00:00');
$exclude_shared = true;

$projects = $client->getProjectSummaries($modified_since, $exclude_shared);
```

### Fetching contact information

Use the `getContactInfo` method to get a list of contact information of the
agent.

```php
$info = $client->getContactInfo();
```

### Fetching a list of user accounts

Use the `getLogins` method to get a list of user accounts associated with the
agent.

```php
$users = $client->getLogins();
```

### Sending contact form input

Use the `insertContactMes` method to submit contact form input to Skarabee. The
method has no return value, but throws an `InvalidContactMeException` on error.

```php
$client->insertContactMes([
    'FirstName' => 'string',
    'LastName'  => 'string',
    'Comments'  => 'string',
    'Email'     => 'string',
]);
```

You can wrap multiple requests in a single array to batch submit data.

```php
$client->insertContactMes([$contact1, $contact2]);
```

### Updating publication status

Use the `feedback` method to submit updates about the publication's online
availability.

```php
use Skarabee\Weblink\Enums\FeedbackStatus;

$client->feedback([
    'PublicationID' => $publication_id,
    'Status'        => FeedbackStatus::Available,
    'URL'           => $property_url,
]);
```

You can wrap multiple requests in a single array to batch submit data.

```php
$client->feedback([$feedback1, $feedback2]);
```

## License

`fw4/skarabee-weblink` is licensed under the MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
