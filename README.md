# Laravel Model Typescript Transformer

This package generates TypeScript definitions for Laravel Eloquent models
using [spatie/laravel-typescript-transformer](https://github.com/spatie/laravel-typescript-transformer).

## Installation

Install via Composer:

```bash
composer require nolanos/laravel-model-typescript-transformer
```

Update your `config/typescript-transformer.php` config file by...
* Appending `ModelTypeScriptCollector` to the `collectors` array.
* Appending `ModelTransformer` to the `transformers` array.

```php
    'collectors' => [
        // ...
        Nolanos\LaravelModelTypescriptTransformer\ModelTypeScriptCollector::class,
    ],
    'transformers' => [
        // Etc....
        Nolanos\LaravelModelTypescriptTransformer\ModelTransformer::class,
    ],
```

## Usage

Generate TypeScript definitions by running:

```bash
php artisan typescript:generate
```

## How it works

### Identifying Properties
The `ModelTransformer` identifies the properties of the given `Model` by looking at the columns in the database table, and then
filters columns out based on the `$hidden` and `$visible` properties of the model.

### Mapping Database Types to TypeScript Types
Property types are determined by mapping the database column type to a TypeScript type. The following mappings are used:

| Typescript Type | Database Type                                                                                                                                  |
|-----------------|------------------------------------------------------------------------------------------------------------------------------------------------|
| string          | uuid, string, text, varchar, character varying, date, datetime, timestamp, timestamp without time zone, bpchar, timestamptz, time, bytea, blob |
| number          | integer, bigint, int2, int4, int8, float, double, decimal, float8, numeric                                                                     |
| boolean         | boolean, bool                                                                                                                                  |


Note: Unknown column types will be mapped to `unknown` and are followed by a comment stating the db type.

### Nullable Types

If a column is nullable, the TypeScript type will be suffixed with `| null`.


## Example

Let's look at a simple of example of a user model in a PostgreSQL database.

```sql
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL ,
    password VARCHAR(255),
    active BOOLEAN NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

The `User` model might look like this:

```php
class User extends Model
{
    protected $hidden = ['password'];
}
````

Will generate the following TypeScript definition:

```typescript
declare namespace App.Models {
  export interface User {
    id: number
    name: string
    email: string
    active: boolean
    created_at: string | null
    updated_at: string | null
  }
}
```

## Limitations 

This package has some limitations. 

Take a look at the [issues](https://github.com/nolanos/laravel-model-typescript-transfomer/issues) to see what's missing.

# Development

### Setup

```bash
git clone git@github.com:nolanos/laravel-model-typescript-transfomer.git

cd laravel-model-typescript-transformer

composer install
```

### Running Tests

```bash

composer test
```

### Publishing new Versions

To publish a new version of the package, you need to create a new tag and push it to the repository.

```bash
git tag vx.x.x
git push origin vx.x.x
```

Go to [Packagist](https://packagist.org/packages/nolanos/laravel-model-typescript-transformer) and click on "Update" to
update the package.