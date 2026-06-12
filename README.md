# Softgigs API Documentation

This project uses Laravel 11 and Sanctum for authentication. All endpoints are prefixed with `/api`.
Responses use a standardized JSON format.

---

## Base URL
`http://localhost:8000/api`

---

## 1. Authentication Routes

### Register a new user
**Endpoint:** `POST /register`
**Visibility:** Public

**Request Body (JSON):**
```json
{
  "name": "Jane Doe",
  "email": "jane@example.com",
  "password": "password123",
  "phone": "1234567890",
  "role": "admin" 
}
```
*Note: `role` can be `admin` or `user`.*

### Login
**Endpoint:** `POST /login`
**Visibility:** Public

**Request Body (JSON):**
```json
{
  "email": "jane@example.com",
  "password": "password123"
}
```
**Response:**
Returns the user object along with a `token`. You must use this token in the `Authorization` header as a Bearer token for protected routes.

---

## 2. Protected User Routes
*Requires header: `Authorization: Bearer {your_token}`*

### Get Current Profile
**Endpoint:** `GET /profile`
Returns the currently authenticated user.

### Upload Avatar
**Endpoint:** `POST /user/avatar`
**Content-Type:** `multipart/form-data`

**Request Body:**
- `avatar`: (File) The image file (jpeg, png, jpg, gif) Max size: 2MB.

### Logout
**Endpoint:** `POST /logout`
Invalidates the current token.

---

## 3. Public Listing Routes (Jobs)

### Get All Listings
**Endpoint:** `GET /listings`
**Visibility:** Public

**Query Parameters (Optional):**
- `search` (string): Search by title, tags, company, or date.
- `limit` or `per_page` (int): Number of items per page (default: 15).
- `page` (int): The page number.

**Example Request:**
`GET /listings?search=Remote&limit=10&page=1`

---

## 4. Protected Admin Listing Routes
*Requires header: `Authorization: Bearer {your_token}`*
*Requires User Role: `admin`*

### Create a Listing
**Endpoint:** `POST /listings`

**Request Body (JSON):**
```json
{
  "title": "Senior AI Native Developer",
  "tags": "Remote",
  "company": "Tech Corp",
  "location": "New York, NY",
  "email": "apply@techcorp.com",
  "website": "https://techcorp.com",
  "description": "We are looking for an experienced developer...",
  "date": "2024-12-01"
}
```
*Note: `tags` must be a valid Enum value (e.g. `Full Time`, `Part Time`, `Contract`, `Freelance`, `Remote`, `Internship`). `date` must be `Y-m-d`.*

### Update a Listing
**Endpoint:** `PUT /listings/{listing_id}`

**Request Body (JSON):**
Pass only the fields you wish to update.
```json
{
  "title": "Lead Laravel Developer"
}
```
*Note: You can only update listings that were created by your admin account.*

### Delete a Listing
**Endpoint:** `DELETE /listings/{listing_id}`
*Note: You can only delete listings that were created by your admin account.*
