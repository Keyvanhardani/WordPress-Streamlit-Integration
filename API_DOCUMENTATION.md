# API Documentation

## Authentication Endpoints

### POST /wp-json/jwt-auth/v1/token
Authenticate user and retrieve JWT token.

**Request:**
```json
{
    "username": "user@example.com",
    "password": "secure_password"
}
```

**Response:**
```json
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "user_email": "user@example.com",
    "user_nicename": "username",
    "user_display_name": "Display Name"
}
```

### POST /wp-json/jwt-auth/v1/token/validate
Validate existing JWT token.

**Headers:**
```
Authorization: Bearer your-jwt-token
```

## User Management

### GET /wp-json/wp/v2/users/me
Get current user information.

**Response:**
```json
{
    "id": 1,
    "username": "user",
    "email": "user@example.com",
    "roles": ["editor"],
    "capabilities": ["read", "edit_posts"]
}
```

## Error Handling

All endpoints return standardized error responses:

```json
{
    "code": "error_code",
    "message": "Human readable error message",
    "status": 400
}
```
