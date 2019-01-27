# Install Twity

## Requirements

- php 7.2 or higher
- MongoDb 4
- RabbitMQ 3

## Install

```bash
git clone https://github.com/julienj/twity.git
cd twity
cp .env.dist .env
```

## Configure

Edit `.env` file

### App


```dotenv
APP_ENV=prod
APP_SECRET=your-secret
```

### MongoDB

```dotenv
MONGODB_URL=mongodb://localhost:27017
MONGODB_DB=symfony
```

### RabbitMQ

```dotenv
MESSENGER_TRANSPORT_DSN=amqp://guest:guest@localhost:5672/%2f/messages
```

### Smtp

```dotenv
EMAIL_SENDER=no-reply@exemple.com
MAILER_URL=smtp://localhost:25?encryption=&auth_mode=
```

### Gitlab Oauth (optional)

```dotenv
GITLAB_DOMAINE=https://gitlab.exemple.com
GITLAB_CLIENT_ID=my-client-id
GITLAB_CLIENT_ID_SECRET=my-secret
```

## Run background jod

```bash
php bin/console messenger:consume-messages
```

Or use supervisord

## Configure crontab

Run this command every day for update provider

```bash
php /path/to/app/bin/console app:request-reload-providers
````

## Create Admin user

```bash
php bin/console app:create-user
```
Use `ROLE_ADMIN for create tou first admin user
