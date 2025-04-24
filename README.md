# WeTransferStyle API

A WeTransfer-style file upload and sharing system built with Laravel. This API allows users to upload files, receive shareable download links, and set expiry settings for the uploaded files.

## Features

- **File Upload**: Upload up to 5 files (max 100MB each) of specific types (jpg, png, pdf, docx, zip)
- **Download Links**: Receive shareable download links for uploaded files
- **Expiry Settings**: Set how long the download links should remain active
- **Auto-Deletion**: Expired files are automatically deleted by a scheduled command
- **Email Notifications**: Optional email notifications when files are uploaded
- **Password Protection**: Optional password protection for downloads
- **File Statistics**: View statistics about uploaded files (downloads, size, expiry)

## Requirements

- PHP 8.1 or higher
- Laravel 10.x
- MySQL/PostgreSQL
- Composer

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/gilbertozioma/WeTransferStyle.git
   cd laravel-file-upload-api
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Create and configure your environment file:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure your database in the `.env` file:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=wetransferstlye
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. Run migrations:
   ```bash
   php artisan migrate
   ```

6. Set up email configuration for notifications (Sorry, I can't disclose my credentials):
   ```
   MAIL_MAILER=smtp
   MAIL_HOST=your-smtp-host
   MAIL_PORT=587
   MAIL_USERNAME=your-username
   MAIL_PASSWORD=your-password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=your-email@example.com
   MAIL_FROM_NAME="${APP_NAME}"
   ```

7. Set up the task scheduler for file cleanup:
   Add this Cron entry to your server to run the Laravel scheduler:
   ```
   * * * * * cd /project-path && php artisan schedule:run >> /dev/null 2>&1
   ```

8. Run the application:
   ```bash
   php artisan serve
   ```

## API Endpoints

### Upload Files

**POST /api/upload**

Upload files and receive a download link.

**Parameters (multipart/form-data):**
- `files[]` - Array of files (required)
- `expires_in` - Number of days until the link expires (optional, default: 1)
- `email_to_notify` - Email to notify when files are uploaded (optional)
- `password` - Password to protect downloads (optional)

**Response:**
```json
{
  "success": true,
  "download_link": "https://domain.com/api/download/{token}"
}
```

### Download Files

**GET /api/download/{token}**

Download uploaded files using the token.

**Parameters (query):**
- `password` - Password if the download is protected
- `file_id` - File ID if multiple files were uploaded

**Response:**
- File download stream, or
- JSON error if token is invalid, expired, or password is required

### Get File Statistics

**GET /api/uploads/stats/{token}**

Get statistics about uploaded files.

**Parameters (query):**
- `password` - Password if the download is protected

**Response:**
```json
{
    "success": true,
    "expires_at": "2025-04-25T02:39:47+00:00",
    "expires_in": 66412,
    "has_password": false,
    "total_files": 2,
    "total_size": 1064565,
    "total_downloads": 0,
    "files": [
      {
         "id": 1,
         "filename": "Screenshot_7-4-2025_122537_127.0.0.2.jpg",
         "size": 958422,
         "mime_type": "image/jpeg",
         "downloads": 0
      },
      {
         "id": 2,
         "filename": "Screenshot_29-3-2025_201912_pitch.jpeg",
         "size": 106143,
         "mime_type": "image/jpeg",
         "downloads": 0
      }
   ]
}
```

## Command Line Tools

### Clean Expired Uploads

Run the command to delete expired files and records:
```bash
php artisan clean:expired-uploads
```

This command is scheduled to run daily at midnight automatically.

## Postman Collection

Import the included Postman collection to test the API endpoints:

[Download Postman Collection](./public/postman_collection/wetransferstyle.json)

## Customization

Customizable settings in the `.env` file:

```
UPLOAD_MAX_FILE_SIZE=100
UPLOAD_MAX_FILES=5
UPLOAD_DEFAULT_EXPIRY=1
UPLOAD_MAX_EXPIRY=30
UPLOAD_STORAGE_DISK=local
UPLOAD_DIRECTORY=uploads
```