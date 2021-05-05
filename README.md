# Anaphora

The project is for sending e-mail with high deliverability.

[![Run Tests](https://github.com/gdegirmenci/anaphora/actions/workflows/run-tests.yml/badge.svg)](https://github.com/gdegirmenci/anaphora/actions/workflows/run-tests.yml)
[![StyleCI](https://github.styleci.io/repos/363748582/shield?branch=main&style=flat)](https://github.styleci.io/repos/363748582?branch=main)
[![ESLint Checker](https://github.com/gdegirmenci/anaphora/actions/workflows/run-eslint.yml/badge.svg)](https://github.com/gdegirmenci/anaphora/actions/workflows/run-eslint.yml)

## Install

Clone the project

```bash
git clone git@github.com:gdegirmenci/anaphora.git
```

Build docker images and up containers

```bash
docker-compose build && docker-compose up -d
```

Enter to the container

```bash
docker exec -it anaphora_php bash
```

Give necessary permission to storage

```bash
chmod -R 777 storage/
```

Copy .env.example as .env

```bash
cp .env.example .env
```

Install dependencies

```bash
composer install
npm ci
```

Generate application key

```bash
php artisan key:generate
```

Migrate schemas

```bash
php artisan migrate
```

Build assets

```bash
npm run dev
```

Start supervisor and work queue

```bash
service supervisor start
php artisan q:w
```

Add application URL to host file

```bash
127.0.0.1 anaphora.local
```

## Configure

To configure SendGrid and MailJet credentials, please update related fields from .env

```bash
SEND_GRID_API_URL=https://api.sendgrid.com/v3/mail/send
SEND_GRID_API_SECRET=

MAIL_JET_API_URL=https://api.mailjet.com/v3.1/send
MAIL_JET_API_SECRET=
```

To configure primary provider, please update related field from .env

```bash
PRIMARY_MAIL_PROVIDER=sendgrid # or mailjet
```

To configure threshold for failed emails, please update related fields from .env

```bash
CIRCUIT_BREAKER_THRESHOLD=3 # default is 3
```

## Usage

### **Vue Application**

To use application, please visit URL.

```bash
http://anaphora.local/
```

### **CLI**

If you want to use CLI instead of Vue.js application, then please enter to container and run following command.

```bash
$ docker exec -it anaphora_php bash
> php artisan campaign:send {payload: string}
```

Example command:

```bash
# should be string as minified JSON
$ docker exec -it anaphora_php bash
> php artisan campaign:send '{ "name": "Very Cool Campaign Name", "subject": "Subject", "from": { "email": "gdegirmenci0@icloud.com", "name": "Gökhan Değirmenci" }, "reply": { "email": "gdegirmenci0@icloud.com", "name": "Gökhan Değirmenci" }, "to": [ { "email": "recipient@mail.com", "name": "First Recipient" }, { "email": "recipient@mail.com", "name": "Second Recipient" } ], "template": "Mail content.", "type": "text" }'
```

### **REST API**

To create campaign, `type` is either `text` or `html`

```bash
## Create Campaign
curl -X "POST" "http://anaphora.local/api/campaigns/create" \
     -H 'Content-Type: application/json' \
     -d $'{
	"name": "Very Cool Campaign Name",
	"subject": "Subject",
  "from": {
    "email": "gdegirmenci0@icloud.com",
    "name": "Gökhan Değirmenci"
  },
  "reply": {
    "email": "gdegirmenci0@icloud.com",
    "name": "Gökhan Değirmenci"
  },
  "to": [
    {
      "email": "recipient@mail.com",
      "name": "First Recipient"
    },
    {
      "email": "recipient@mail.com",
      "name": "Second Recipient"
    }
  ],
  "template": "Mail content.",
	"type": "text"
}'
```

To list campaigns, `perPage` and `page` is optional

```bash
## Get Campaigns
curl -X "GET" "http://anaphora.local/api/campaigns/get" \
     -H 'Content-Type: application/json' \
     -d $'{
  "perPage": 10,
  "page": 1
}'
```

To get statistics and provider statuses

```bash
## Get Statistics and Provider Statuses
curl -X "GET" "http://anaphora.local/api/dashboard/get" \
     -H 'Content-Type: application/json'
```

## How & Why

### Circuit Breakers

Since one of our most important aim is sending e-mails with high deliverability, the project is working with a fallback mechanism, which is structured by Circuit Breaker design pattern. Thanks to this pattern, our service is detecting failures and deciding which provider it should use to send an e-mail.

Mainly, there are 3 statuses for circuits, `OPENED` `HALF-OPENED` and `CLOSED`.

- `OPENED` → Service is not responding at all.
- `HALF-OPENED` → Service is responding, but it gives error.
- `CLOSED` → Service is responding.

With keeping these status for each provider, the project is handling failures.

After exceeding failed requests for the provider, circuit is opening. By default, threshold is 3.

To close opened circuit, first of all provider should not give any failure for 5 minutes. Meanwhile, status will be opened.

After 10 minutes, if there would not be any failed request, status would be closed again.
```bash
# Opening circuit flow

            1 FAILED          3 FAILED  
CLOSED  →   HALF-OPENED   →   OPENED

--------------------------------------

# Closing circuit flow

5 MIN       5 MIN              
OPENED  →   HALF-OPENED   →   CLOSED
````

### Campaign Logs

The project is keeping log for each campaign and provider. From database, logs could be found at `campaign_logs` table. The relation is HasMany, means a campaign could have more than one log.

### Possible Scenarios

Let's assume some scenarios to understand fallback mechanism better.

```bash
## SendGrid Circuit: OPENED
## MailJet Circuit: HALF-OPENED
## MailJet Response: False (1st), True (2nd)
If circuit is not opened for MailJet, sending from MailJet for now.
```

```bash
## SendGrid Circuit: CLOSED
## MailJet Circuit: HALF-OPENED
## SendGrid Response: True
Sending from SendGrid for now.
```

```bash
## SendGrid Circuit: OPENED
## MailJet Circuit: OPENED
## SendGrid Response: N/A since there is no request
Sending from SendGrid for later.
```

```bash
## SendGrid Circuit: HALF-OPENED
## MailJet Circuit: HALF-OPENED
## SendGrid Response: False
## MailJet Response: False (1st), False (2nd)
If circuit is opened, sending from SendGrid for later.
```

### Provider Factory

Every provider should have an implementation. To choose provider there is a factory named `ProviderServiceFactory`. To add new provider, first it should be defined there.

After that, related service should be created under the folder `app\Services\Providers\`, with extending `BaseProviderService`

```php
<?php

namespace App\Services\Providers;

/**
 * Class FooProviderService
 * @package App\Services\Providers
 */
class FooProviderService extends BaseProvider
{
    /**
     * FooProviderService constructor.
     * @param CampaignRepository $campaignRepository
     * @param Email $email
     */
    public function __construct(CampaignRepository $campaignRepository, Email $email)
    {
        parent::__construct($campaignRepository, $email);
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        //
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        //
    }

    /**
     * @return Collection
     */
    public function getBody(): Collection
    {
        //
    }

    /**
     * @return string
     */
    public function getProvider(): string
    {
        // 
    }
}
```

### Vue.js & Vuetify

To create an user interface, the project is using [Vue.js](https://vuejs.org/) and [Vuetify](https://vuetifyjs.com/en/) libraries. Also for HTML/Markdown editor, [Vue2Editor](https://www.vue2editor.com/) is used.

The project is using [Vuex](https://vuex.vuejs.org/) to keep states, also fetching datas based on user actions, like clicking menu etc which is handling by [VueRouter](https://router.vuejs.org/).
