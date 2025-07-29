# Contact Management API Documentation

This document provides comprehensive testing instructions for the Contact Management API endpoints.

## Table of Contents
- [Base URL](#base-url)
- [Authentication](#authentication)
- [API Endpoints](#api-endpoints)
  
## Base URL

```
http://127.0.0.1:8000/api
```

## Authentication

The API uses Laravel Sanctum for authentication. Most endpoints require a Bearer token.

### Getting a Token

1. **Login** to get an access token
2. **Include the token** in the `Authorization` header for subsequent requests
3. **Logout** to revoke the token

## API Endpoints

### 1. Authentication Endpoints

#### POST /api/login
Authenticate user and get access token.

**Request Body:**
```json
{
    "email": "test@example.com",
    "password": "password"
}
```

**Response:**
```json
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "Test User",
        "email": "test@example.com"
    },
    "token": "1|abc123def456..."
}
```

#### POST /api/logout
Logout and revoke current token.

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "message": "Logged out successfully"
}
```

#### GET /api/user
Get current authenticated user information.

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "user": {
        "id": 1,
        "name": "Test User",
        "email": "test@example.com"
    }
}
```

### 2. Contact Management Endpoints

All contact endpoints require authentication.

#### GET /api/contacts
Get paginated list of contacts with optional search and filtering.

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `search` (optional): Search in first_name, last_name, email, company
- `company` (optional): Filter by company name
- `sort_by` (optional): Sort field (default: created_at)
- `sort_order` (optional): asc or desc (default: desc)
- `per_page` (optional): Items per page (default: 15)
- `page` (optional): Page number (default: 1)

**Example Request:**
```
GET /api/contacts?search=john&company=tech&sort_by=first_name&sort_order=asc&per_page=10&page=1
```

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "first_name": "Ahmad",
            "last_name": "Lufi",
            "full_name": "Ahmad Lufi",
            "email": "ahmad.lufi@example.com",
            "phone": "+1234567890",
            "company": "Halo Ahmad",
            "address": "Indonesia",
            "birth_date": "1990-01-15",
            "notes": "Software developer",
            "created_at": "2025-01-01 10:00:00",
            "updated_at": "2025-01-01 10:00:00"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 50
    }
}
```

#### POST /api/contacts
Create a new contact.

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "first_name": "Ahmad",
    "last_name": "Lufi",
    "email": "ahmad.lufi@example.com",
    "phone": "+1234567890",
    "company": "Halo Ahmad",
    "address": "Indonesia",
    "birth_date": "1990-01-15",
    "notes": "Software developer"
}
```

**Response:**
```json
{
    "message": "Contact created successfully",
    "data": {
        "id": 51,
        "first_name": "Aahmad Lufi",
        "last_name": "Lufi",
        "full_name": "Ahmad Lufi",
        "email": "ahmad.lufi@example.com",
        "phone": "+1234567890",
        "company": "Halo Ahmad",
        "address": "Indonesia",
        "birth_date": "1990-01-15",
        "notes": "Software developer",
        "created_at": "2025-01-01 12:00:00",
        "updated_at": "2025-01-01 12:00:00"
    }
}
```

#### GET /api/contacts/{id}
Get a specific contact by ID.

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "data": {
        "id": 1,
        "first_name": "Ahmad",
        "last_name": "Lufi",
        "full_name": "Ahmad Lufi",
        "email": "ahmad.lufi@example.com",
        "phone": "+1234567890",
        "company": "Halo Ahmad",
        "address": "Indonesia",
        "birth_date": "1990-01-15",
        "notes": "Software developer",
        "created_at": "2025-01-01 10:00:00",
        "updated_at": "2025-01-01 10:00:00"
    }
}
```

#### PUT /api/contacts/{id}
Update an existing contact.

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "first_name": "Ahmad",
    "last_name": "Lufi Updated",
    "email": "ahmad.lufi.updated@example.com",
    "phone": "+1234567890",
    "company": "Updated Halo Ahmad",
    "address": "Indonesia",
    "birth_date": "1990-01-15",
    "notes": "Software Engineer 2"
}
```

**Response:**
```json
{
    "message": "Contact updated successfully",
    "data": {
        "id": 1,
        "first_name": "Ahmad",
        "last_name": "Lufi Updated",
        "full_name": "Ahmad Lufi Updated",
        "email": "ahmad.lufi.updated@example.com",
        "phone": "+1234567890",
        "company": "Updated Halo Ahmad",
        "address": "Indonesia",
        "birth_date": "1990-01-15",
        "notes": "Software Engineer 2",
        "created_at": "2025-01-01 10:00:00",
        "updated_at": "2025-01-01 13:00:00"
    }
}
```

#### DELETE /api/contacts/{id}
Delete a contact.

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "message": "Contact deleted successfully"
}
```
