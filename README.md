# Laravel Admin

A boilerplate for Laravel based applications with an admin panel.

## Install

Install dependencies

```
composer install
yarn install
```

Set environment variables

```
cp .env.example .env
```

Setup database

```
php artisan key:generate
php artisan migrate
```

Generate routes manifest

```
php artisan app:routes
```

## Create views

```
php artisan admin:pages
```

## Create controllers

```
php artisan admin:controller
```

## Asset Bundling

Application

```
yarn dev
yarn build
```

Admin Panel

```
yarn dev:admin
yarn build:admin
```

## SSR

Build

```
yarn build:ssr
```

Start SSR server

```
php artisan inertia:start-ssr
```

## Local sharing

For LAN sharing, set `VITE_LOCAL` to `true` and `VITE_LOCAL_IP_ADDRESS` to your local IP address

```
VITE_LOCAL=true
VITE_LOCAL_IP_ADDRESS=192.168.1.92
```

Start the local server

```
php artisan serve --host=0.0.0.0
```

Start the Vite server

```
yarn dev:local
```
