# compasia-server

## Overview

This project is a simple inventory management system built using **Laravel (Backend)** integrated with external project **Vue 3 (Frontend)** - [Repository](https://github.com/nblkmal/compasia-hub-frontend).  
It displays a master product list and allows inventory updates via Excel file upload.

The system processes inventory changes asynchronously using **Laravel Queues**, supports **backend pagination**, and provides **RESTful APIs** for frontend consumption.

---

## Features

### Product Master List
- Display all products from `product_master_list`
- Backend-driven pagination
- Search filter by **Product ID** via API

### Product Status Upload
- Upload Excel file located at `public/product_status_list.xlsx`
- Inventory updates handled via **Laravel Queue** powered by **Maatwebsite/Excel** for Excel file parsing
- Business logic:
  - `Sold` → Deduct quantity
  - `Buy` → Add quantity

__Note:__ Based on the given excel file, there is no column for `quantity` indicating amount of items to be added or deducted, so we will assume every row is a single product and update the quantity accordingly
- Updated quantities reflected after processing

__Extra:__ Product log table `product_log` is created to track inventory changes

---

## Tech Stack

### Prerequisites
- Docker for Sail
- Composer

### Backend
- PHP **Laravel 12**
- MySQL
- Laravel Queue (Redis)
- RESTful API architecture
- Implemented API Resources for cleaner and structured API responses
- Laravel Sail for docker environment
- Laravel Unit Test with PHPUnit
- Laravel Seeder for initial dataset
- Laravel Event Broadcast with Pusher

### File Processing
- Excel parsing using `maatwebsite/excel`

---

## Setup Instructions

```sh
1. run composer install
2. cp .env.example .env
p/s: value for Pusher is intentionally predefined for quicker setup

3. run ./vendor/bin/sail up -d
p/s: If you are using windows, make sure WSL is working properly

4. run ./vendor/bin/sail artisan migrate:fresh --seed
5. run ./vendor/bin/sail artisan queue:work
6. Browse http://laravel.test/api/health for quick health check. It should return alived
```

## Windows Users (WSL + Docker + Laravel Sail)

If you are using Windows, Laravel Sail must be run inside WSL (Ubuntu).
Running Sail directly from PowerShell or CMD is not supported.

Prerequisites

Make sure you have:

WSL 2 installed

Ubuntu installed via WSL

Docker Desktop for Windows

Docker Desktop configured to use WSL 2

## Unit Test
If just want to make sure the feature is working, run the test

```sh
./vendor/bin/sail artisan test

   PASS  Tests\Feature\ProductControllerTest
  ✓ index returns paginated products                                               1.24s  
  ✓ index can search products                                                      0.03s  
  ✓ upload file successfully                                                       0.03s  
  ✓ upload file validation fails when file is missing                              0.02s  
  ✓ upload file validation fails with invalid file type                            0.01s  
  ✓ logs returns paginated logs                                                    0.03s  

  Tests:    6 passed (80 assertions)
  Duration: 1.52s
```