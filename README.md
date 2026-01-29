# PHP Developer Assignment – Product Inventory Management

## Overview

This project is a simple inventory management system built using **Laravel (Backend)** and **Vue 3 (Frontend)**.  
It displays a master product list and allows inventory updates via Excel file upload.

The system processes inventory changes asynchronously using **Laravel Queues**, supports **backend pagination**, and provides **RESTful APIs** for frontend consumption.

---

## Features

### Product Master List
- Display all products from `product_master_list`
- Backend-driven pagination
- Search filter by **Product ID** via API

### Product Status Upload
- Upload Excel file `product_status_list.xlsx`
- Inventory updates handled via **Laravel Queue**
- Business logic:
  - `Sold` → Deduct quantity
  - `Buy` → Add quantity
- Updated quantities reflected after processing

---

## Tech Stack

### Backend
- PHP **Laravel 10+**
- MySQL
- Laravel Queue (Database / Redis)
- RESTful API architecture

### Frontend
- Vue.js **Vue 3**
- Axios for API communication

### File Processing
- Excel parsing using `maatwebsite/excel`

---

## Database Design

### product_master_list

| Column       | Type        | Description |
|-------------|------------|-------------|
| id          | BIGINT     | Primary Key |
| product_id  | INT        | Product ID |
| type        | VARCHAR    | Product Type |
| brand       | VARCHAR    | Brand Name |
| model       | VARCHAR    | Model |
| capacity    | VARCHAR    | Capacity |
| quantity    | INT        | Stock Quantity |
| created_at | TIMESTAMP  | Auto |
| updated_at | TIMESTAMP  | Auto |

### product_status_list

| Column       | Type        | Description |
|-------------|------------|-------------|
| id          | BIGINT     | Primary Key |
| product_id  | INT        | Product ID |
| status      | ENUM       | Buy / Sold |
| quantity    | INT        | Quantity |
| created_at | TIMESTAMP  | Auto |

---

1. composer install
2. cp .env.example .env
2. ./vendor/bin/sail up -d
4. ./vendor/bin/sail artisan migrate
