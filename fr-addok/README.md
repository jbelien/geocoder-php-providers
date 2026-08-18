# 🇫🇷 Geocoder PHP "Addok" provider

> [Geocoder PHP](https://github.com/geocoder-php/Geocoder) is a PHP library which helps you build geo-aware applications by providing a powerful abstraction layer for geocoding manipulations.

This is the "Addok" provider for the [Geocoder PHP](https://github.com/geocoder-php/Geocoder).

![Latest Stable Version](https://poser.pugx.org/jbelien/geocoder-php-addok-provider/v)
![Total Downloads](https://poser.pugx.org/jbelien/geocoder-php-addok-provider/downloads)
![Monthly Downloads](https://poser.pugx.org/jbelien/geocoder-php-addok-provider/d/monthly)
![License](https://poser.pugx.org/jbelien/geocoder-php-addok-provider/license)

## Information

- **Coverage:** France
- **API:** <https://adresse.data.gouv.fr/api>

## Install

```bash
composer require jbelien/geocoder-php-addok-provider
```

## Usage

See [Geocoder PHP README file](https://github.com/geocoder-php/Geocoder/blob/master/README.md).

This provider can be used with any [Addok](https://github.com/addok/addok) server.

This provider provides [BAN (*Base Adresse Nationale*)](https://adresse.data.gouv.fr/) with the function `Addok::withBANServer()`.
