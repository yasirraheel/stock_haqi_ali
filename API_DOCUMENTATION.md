# API Documentation

## Overview
This document provides comprehensive information about all available API endpoints in the Android API Controller.

## Authentication
All API endpoints require authentication using the `APP_KEY` from your environment configuration.

### Authentication Methods
1. **Header Authentication (Recommended)**
   ```
   X-API-KEY: your_app_key_value
   ```

2. **Query Parameter Authentication**
   ```
   ?api_key=your_app_key_value
   ```

## Base URLs
- **Public API**: `/api/public/`
- **Main API**: `/api/v1/`

## Response Format
All API responses follow this general structure:
```json
{
    "VIDEO_STREAMING_APP" or "PHOTO_STREAMING_APP" or "AUDIO_STREAMING_APP": [response_data],
    "status_code": 200,
    "additional_fields": "..."
}
```

## Error Response Format
```json
{
    "error": "Error type",
    "message": "Error description",
    "status_code": 401
}
```

---

## Public API Endpoints

### 1. Get Random Videos
- **URL**: `POST /api/public/random_videos`
- **Description**: Retrieves a random video from the collection
- **Authentication**: Required (APP_KEY)

**Request Body** (Optional):
```json
{
    "data": "base64_encoded_json_data"
}
```

**Response**:
```json
{
    "VIDEO_STREAMING_APP": {
        "random_video": {
            "movie_id": 1,
            "movie_title": "Video Title",
            "video_slug": "video-slug",
            "movie_poster": "http://example.com/poster.jpg",
            "video_image": "http://example.com/image.jpg",
            "movie_duration": "2:30:00",
            "movie_access": 1,
            "content_rating": "PG-13",
            "video_description": "Video description",
            "release_date": "2024-01-01",
            "imdb_rating": 8.5,
            "views": 1000,
            "video_url": "http://example.com/video.mp4",
            "video_url_480": "http://example.com/video_480.mp4",
            "video_url_720": "http://example.com/video_720.mp4",
            "video_url_1080": "http://example.com/video_1080.mp4",
            "genres": [
                {"genre_id": 1, "genre_name": "Action"},
                {"genre_id": 2, "genre_name": "Drama"}
            ],
            "language_name": "English",
            "actors": [
                {
                    "actor_id": 1,
                    "actor_name": "Actor Name",
                    "actor_image": "http://example.com/actor.jpg"
                }
            ],
            "directors": [
                {
                    "director_id": 1,
                    "director_name": "Director Name",
                    "director_image": "http://example.com/director.jpg"
                }
            ],
            "license_price": 0,
            "is_premium": false
        }
    },
    "total_records": 100,
    "returned_records": 1,
    "status_code": 200
}
```

### 2. Get Random Audios
- **URL**: `POST /api/public/random_audios`
- **Description**: Retrieves a random audio from the collection
- **Authentication**: Required (APP_KEY)

**Response**:
```json
{
    "AUDIO_STREAMING_APP": {
        "random_audio": {
            "audio_id": 1,
            "title": "Audio Title",
            "description": "Audio description",
            "audio_url": "http://example.com/audio.mp3",
            "duration": "3:30",
            "file_size": "5MB",
            "format": "mp3",
            "bitrate": 320,
            "sample_rate": 44100,
            "genre": "Music",
            "mood": "Happy",
            "tags": "music,audio",
            "license_price": 0,
            "downloads_count": 10,
            "views_count": 50,
            "is_premium": "false"
        }
    },
    "total_records": 50,
    "returned_records": 1,
    "status_code": 200
}
```

### 3. Get Random Photos
- **URL**: `POST /api/public/random_photos`
- **Description**: Retrieves a random photo from the collection
- **Authentication**: Required (APP_KEY)

**Response**:
```json
{
    "PHOTO_STREAMING_APP": {
        "random_photo": {
            "photo_id": 1,
            "title": "Photo Title",
            "description": "Photo description",
            "image_url": "http://example.com/photo.jpg",
            "image_name": "photo.jpg",
            "category": "Nature",
            "tags": "nature,landscape",
            "keywords": "mountain,sky,landscape",
            "width": 1920,
            "height": 1080,
            "resolution": "1920x1080",
            "file_size": "2.5MB",
            "formatted_file_size": "2.5 MB",
            "dimensions": "1920x1080",
            "file_type": "jpg",
            "mime_type": "image/jpeg",
            "license_price": 0,
            "download_count": 5,
            "view_count": 25,
            "is_premium": "false",
            "camera_info": {
                "camera_make": "Canon",
                "camera_model": "EOS R5",
                "lens_model": "RF 24-70mm f/2.8L",
                "focal_length": "50mm",
                "aperture": "f/2.8",
                "shutter_speed": "1/125",
                "iso": 100,
                "date_taken": "2024-01-01"
            }
        }
    },
    "total_records": 200,
    "returned_records": 1,
    "status_code": 200
}
```

### 4. Get All Content
- **URL**: `POST /api/public/all_content`
- **Description**: Retrieves random video, audio, and photo in one response
- **Authentication**: Required (APP_KEY)

**Response**:
```json
{
    "ALL_CONTENT_APP": {
        "random_video": { /* video object */ },
        "random_audio": { /* audio object */ },
        "random_photo": { /* photo object */ }
    },
    "content_summary": {
        "videos_available": 100,
        "audios_available": 50,
        "photos_available": 200
    },
    "status_code": 200
}
```

### 5. Get Videos List
- **URL**: `POST /api/public/videos_list`
- **Description**: Retrieves a paginated list of videos
- **Authentication**: Required (APP_KEY)

**Request Body** (Optional):
```json
{
    "data": "base64_encoded_json_data_with_page_and_per_page"
}
```

**Response**:
```json
{
    "VIDEOS_LIST": [
        {
            "movie_id": 1,
            "movie_title": "Video Title",
            "movie_poster": "http://example.com/poster.jpg",
            "movie_duration": "2:30:00",
            "movie_access": 1,
            "content_rating": "PG-13",
            "video_description": "Description",
            "release_date": "2024-01-01",
            "imdb_rating": 8.5,
            "views": 1000,
            "video_url": "http://example.com/video.mp4",
            "genres": [{"genre_id": 1, "genre_name": "Action"}],
            "language_name": "English",
            "license_price": 0,
            "is_premium": false
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 10,
        "total_videos": 100,
        "total_pages": 10,
        "has_next_page": true,
        "has_prev_page": false
    },
    "status_code": 200
}
```

### 6. Get Audios List
- **URL**: `POST /api/public/audios_list`
- **Description**: Retrieves a paginated list of audios
- **Authentication**: Required (APP_KEY)

**Response**:
```json
{
    "AUDIOS_LIST": [
        {
            "audio_id": 1,
            "title": "Audio Title",
            "description": "Audio description",
            "audio_url": "http://example.com/audio.mp3",
            "duration": "3:30",
            "file_size": "5MB",
            "format": "mp3",
            "genre": "Music",
            "license_price": 0,
            "is_premium": "false"
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 10,
        "total_audios": 50,
        "total_pages": 5,
        "has_next_page": true,
        "has_prev_page": false
    },
    "status_code": 200
}
```

### 7. Get Photos List
- **URL**: `POST /api/public/photos_list`
- **Description**: Retrieves a paginated list of photos
- **Authentication**: Required (APP_KEY)

**Response**:
```json
{
    "PHOTOS_LIST": [
        {
            "photo_id": 1,
            "title": "Photo Title",
            "description": "Photo description",
            "image_url": "http://example.com/photo.jpg",
            "category": "Nature",
            "width": 1920,
            "height": 1080,
            "file_type": "jpg",
            "license_price": 0,
            "is_premium": "false"
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 10,
        "total_photos": 200,
        "total_pages": 20,
        "has_next_page": true,
        "has_prev_page": false
    },
    "status_code": 200
}
```

### 8. Get Genres
- **URL**: `GET /api/public/genres`
- **Description**: Retrieves list of available genres
- **Authentication**: Required (APP_KEY)

**Response**:
```json
{
    "VIDEO_STREAMING_APP": [
        {
            "genre_id": 1,
            "genre_name": "Action"
        },
        {
            "genre_id": 2,
            "genre_name": "Comedy"
        }
    ],
    "status_code": 200
}
```

---

## Main API Endpoints (v1)

### 1. API Index
- **URL**: `GET /api/v1/`
- **Description**: Basic API information
- **Authentication**: Required (APP_KEY)

**Response**:
```json
{
    "VIDEO_STREAMING_APP": [
        {
            "msg": "API",
            "success": "1"
        }
    ],
    "status_code": 200
}
```

### 2. App Details
- **URL**: `POST /api/v1/app_details`
- **Description**: Get application configuration details
- **Authentication**: Required (APP_KEY)

**Request Body**:
```json
{
    "data": "base64_encoded_json_data_with_user_id"
}
```

**Response**:
```json
{
    "VIDEO_STREAMING_APP": [
        {
            "app_package_name": "com.example.app",
            "app_name": "My App",
            "app_logo": "http://example.com/logo.png",
            "app_version": "1.0.0",
            "app_company": "My Company",
            "app_email": "support@example.com",
            "app_website": "https://example.com",
            "app_contact": "+1234567890",
            "app_about": "About the app",
            "app_privacy": "Privacy policy",
            "app_terms": "Terms of service",
            "ads_list": [
                {
                    "ad_id": 1,
                    "ads_name": "Banner Ad",
                    "ads_info": {},
                    "status": "true"
                }
            ],
            "app_update_hide_show": 1,
            "app_update_version_code": "1",
            "app_update_desc": "Update description",
            "app_update_link": "https://play.google.com/store/apps/details?id=com.example.app",
            "app_update_cancel_option": 1,
            "menu_shows": "true",
            "menu_movies": "true",
            "menu_sports": "false",
            "menu_livetv": "true",
            "success": "1"
        }
    ],
    "user_status": true,
    "status_code": 200
}
```

### 3. User Login
- **URL**: `POST /api/v1/login`
- **Description**: Authenticate user login
- **Authentication**: Required (APP_KEY)

**Request Body**:
```json
{
    "data": "base64_encoded_json_data_with_email_password_device_info"
}
```

**Response**:
```json
{
    "VIDEO_STREAMING_APP": [
        {
            "user_session_name": "session_id",
            "device_limit_reached": false,
            "user_id": 1,
            "name": "User Name",
            "email": "user@example.com",
            "phone": "+1234567890",
            "user_image": "http://example.com/profile.jpg",
            "msg": "Login successfully...",
            "success": "1"
        }
    ],
    "status_code": 200
}
```

### 4. User Signup
- **URL**: `POST /api/v1/signup`
- **Description**: Register new user
- **Authentication**: Required (APP_KEY)

**Request Body**:
```json
{
    "data": "base64_encoded_json_data_with_name_email_password"
}
```

**Response**:
```json
{
    "VIDEO_STREAMING_APP": [
        {
            "msg": "Account created successfully",
            "success": "1"
        }
    ],
    "status_code": 200
}
```

### 5. User Logout
- **URL**: `POST /api/v1/logout`
- **Description**: Logout user and clear session
- **Authentication**: Required (APP_KEY)

**Request Body**:
```json
{
    "data": "base64_encoded_json_data_with_user_id_and_session_name"
}
```

**Response**:
```json
{
    "VIDEO_STREAMING_APP": [
        {
            "msg": "Logout successfully",
            "success": "1"
        }
    ],
    "status_code": 200
}
```

### 6. Forgot Password
- **URL**: `POST /api/v1/forgot_password`
- **Description**: Send password reset email
- **Authentication**: Required (APP_KEY)

**Request Body**:
```json
{
    "data": "base64_encoded_json_data_with_email"
}
```

**Response**:
```json
{
    "VIDEO_STREAMING_APP": [
        {
            "msg": "We have e-mailed your password reset link!",
            "success": "1"
        }
    ],
    "status_code": 200
}
```

### 7. Get User Profile
- **URL**: `POST /api/v1/profile`
- **Description**: Get user profile information
- **Authentication**: Required (APP_KEY)

**Request Body**:
```json
{
    "data": "base64_encoded_json_data_with_user_id"
}
```

**Response**:
```json
{
    "VIDEO_STREAMING_APP": {
        "user_id": 1,
        "name": "User Name",
        "email": "user@example.com",
        "phone": "+1234567890",
        "paypal_email": "paypal@example.com",
        "user_address": "User Address",
        "user_image": "http://example.com/profile.jpg"
    },
    "status_code": 200
}
```

### 8. Update User Profile
- **URL**: `POST /api/v1/profile_update`
- **Description**: Update user profile information
- **Authentication**: Required (APP_KEY)

**Request Body**:
```json
{
    "data": "base64_encoded_json_data_with_user_profile_data",
    "user_image": "file_upload_optional"
}
```

**Response**:
```json
{
    "VIDEO_STREAMING_APP": [
        {
            "msg": "Successfully updated",
            "success": "1"
        }
    ],
    "profile_data": {
        "user_id": 1,
        "name": "Updated Name",
        "email": "updated@example.com",
        "phone": "+1234567890",
        "user_image": "http://example.com/new_profile.jpg"
    },
    "status_code": 200
}
```

### 9. Delete Account
- **URL**: `POST /api/v1/account_delete`
- **Description**: Delete user account
- **Authentication**: Required (APP_KEY)

**Request Body**:
```json
{
    "data": "base64_encoded_json_data_with_user_id_and_session_name"
}
```

**Response**:
```json
{
    "VIDEO_STREAMING_APP": [
        {
            "msg": "Account deleted successfully",
            "success": "1"
        }
    ],
    "status_code": 200
}
```

### 10. Check User Plan
- **URL**: `POST /api/v1/check_user_plan`
- **Description**: Check user subscription plan status
- **Authentication**: Required (APP_KEY)

**Request Body**:
```json
{
    "data": "base64_encoded_json_data_with_user_id"
}
```

**Response**:
```json
{
    "VIDEO_STREAMING_APP": [
        {
            "current_plan": "Premium Plan",
            "expired_on": 1735689600,
            "msg": "My Subscription",
            "success": "1"
        }
    ],
    "status_code": 200
}
```

### 11. Search Content
- **URL**: `POST /api/v1/search`
- **Description**: Search for videos, shows, sports, and live TV
- **Authentication**: Required (APP_KEY)

**Request Body**:
```json
{
    "data": "base64_encoded_json_data_with_search_text"
}
```

**Response**:
```json
{
    "VIDEO_STREAMING_APP": {
        "movies": [
            {
                "movie_id": 1,
                "movie_title": "Movie Title",
                "movie_poster": "http://example.com/poster.jpg",
                "movie_duration": "2:30:00",
                "movie_access": 1
            }
        ],
        "shows": [
            {
                "show_id": 1,
                "show_title": "Show Title",
                "show_poster": "http://example.com/show_poster.jpg",
                "show_access": 1
            }
        ],
        "sports": [
            {
                "sport_id": 1,
                "sport_title": "Sport Title",
                "sport_image": "http://example.com/sport.jpg",
                "sport_duration": "1:30:00",
                "sport_access": 1
            }
        ],
        "live_tv": [
            {
                "tv_id": 1,
                "tv_title": "Channel Name",
                "tv_logo": "http://example.com/channel_logo.jpg",
                "tv_access": 1
            }
        ]
    },
    "status_code": 200
}
```

### 12. Add/Edit Movie
- **URL**: `POST /api/v1/movies/add_edit_movie`
- **Description**: Add new movie or update existing movie
- **Authentication**: Required (APP_KEY)

**Request Body**:
```json
{
    "data": "base64_encoded_json_data_with_movie_data",
    "user_image": "file_upload_optional"
}
```

**Response**:
```json
{
    "status": true,
    "message": "Movie successfully added.",
    "data": {
        "id": 1,
        "video_title": "Movie Title",
        "video_description": "Movie description",
        "status": 0
    },
    "status_code": 200
}
```

### 13. Generate Movie Description
- **URL**: `POST /api/v1/movies/generate_description`
- **Description**: Generate AI-powered movie description based on title and metadata
- **Authentication**: Required (APP_KEY)

**Request Body** (JSON):
```json
{
    "title": "Movie Title (Required)",
    "genres": "Action,Drama,Thriller (Optional)",
    "actors": "Actor1,Actor2 (Optional)",
    "directors": "Director1,Director2 (Optional)"
}
```

**Response**:
```json
{
    "status": true,
    "message": "Description generated successfully",
    "description": "Generated movie description text here..."
}
```

**Error Response**:
```json
{
    "status": false,
    "message": "Movie title is required"
}
```

**App Implementation Flow**:
1. Call this endpoint before video upload to generate description
2. Display the generated description to user for verification/editing
3. User can modify the description if needed
4. Use the final description in the video upload request

---

## Data Encoding

Most endpoints require the request data to be encoded in a specific format using the `checkSignSalt` function. The data should be:

1. JSON encoded
2. URL encoded
3. Base64 encoded
4. Include `sign` and `salt` fields for validation

Example data structure before encoding:
```json
{
    "email": "user@example.com",
    "password": "password123",
    "sign": "md5_hash_of_key_and_salt",
    "salt": "random_salt_string"
}
```

## Error Codes

- **200**: Success
- **400**: Bad Request
- **401**: Unauthorized (Invalid or missing API key)
- **500**: Internal Server Error

## Rate Limiting

API endpoints may be subject to rate limiting. Check response headers for rate limit information.

## Support

For API support and questions, please contact the development team.
