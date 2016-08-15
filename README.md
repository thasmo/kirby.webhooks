# Webhooks-Plugin for Kirby
> Push Kirby hook events to HTTP endpoints.

[![GitHub Release](https://img.shields.io/github/release/thasmo/kirby.webhooks.svg)](https://github.com/thasmo/kirby.webhooks/releases/latest)
[![License](https://img.shields.io/github/license/thasmo/kirby.webhooks.svg)](https://github.com/thasmo/kirby.webhooks/blob/develop/LICENSE.md)

---

## About

The Kirby webhooks-plugin will send a `POST` request to the configured HTTP endpoints
holding a `application/json` body with the hook name, the user-data of the user who
triggered the hook and the actual page data including a diff with the old page data.

* `hook` holds the name of the triggered hook.
* `host` holds the hostname from where the request originated.
* `user` holds the data of the user who triggered the hook.
* `data` holds the new page data.
* `diff` holds the page's old page data which changed.

**Example of the Request-Body** (shortened)
```json
{
    "hook": "panel.page.update",
    "host": "127.0.0.1",
    "user": {
        "username": "thasmo",
        "email": "hi@thasmo.com",
        "language": "en",
        "role": "admin",
        "firstname": "Thasmo",
        "lastname":" Deinhamer",
        "history": []
    },
    "data":{
        "id": "page/subpage",
        "title": "Subpage",
        "content": {
            "name": "New name!"
        }
    },
    "diff": {
        "content": {
            "name": "Old name."
        }
    }
}
```

## Installation

Choose your preferred installation method below and enable the plugin in the configuration.

### Kirby CLI
```sh
kirby plugin:install thasmo/kirby.webhooks
```

### Composer
```json
{
    "name": "my-kirby-installation",
    "require": {
        "mnsami/composer-custom-directory-installer": "1.0.*",
        "thasmo/kirby.webhooks": "0.1.0"
    },
    "repositories": [
        {
            "type": "package",
            "package": {
                "name": "thasmo/kirby.webhooks",
                "version": "0.1.0",
                "source": {
                    "type": "git",
                    "url": "https://github.com/thasmo/kirby.webhooks.git",
                    "reference": "v0.1.0"
                }
            }
        }
    ],
    "extra": {
        "installer-paths": {
            "./site/plugins/webhooks": ["thasmo/kirby.webhooks"]
        }
    }
}
```

### Manual
Download [the latest release](https://github.com/thasmo/kirby.webhooks/releases/latest) and unpack it to `site/plugins/webhooks`.

## Usage

### Configuration

**webhooks** _required_, default: `false`  
Enable the webhooks-plugin.
```php
c::set('webhooks', true);
```

**webhooks.endpoints** _required_ default: `null`  
Define HTTP endpoints.
```php
c::set('webhooks.endpoints', [
    'http://domain-1.com/all-events/',
    'http://domain-2.com/page-events-only/' => ['panel.page'],
    'http://domain-3.com/user-events-only/' => ['panel.user'],
]);
```

**webhooks.blacklist** _optional_, default: `['password', 'secret']`  
Set property-names which should not be passed to the endpoints.
```php
c::set('webhooks.blacklist', ['password', 'secret', 'email']);
```

---

[![forthebadge](http://forthebadge.com/images/badges/built-with-love.svg)](http://forthebadge.com)
