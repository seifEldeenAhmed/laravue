# Laravue Blog

A modern blog application built with Laravel 12 and Vue 3, featuring comprehensive user management, content creation, and administrative functionality.

## Installation Guide

Follow these steps to get the application running on your local environment.

### Prerequisites

Ensure you have the following software installed on your system:

-   PHP 8.2 or higher
-   Composer
-   Node.js 18 or higher
-   npm or yarn
-   MySQL (for production) or SQLite (for development and testing)

### Installation Steps

#### 1. Clone the Repository

```bash
git clone https://github.com/seifEldeenAhmed/laravue.git
cd laravue-blog
```

#### 2. Install PHP Dependencies

```bash
composer install
```

#### 3. Install JavaScript Dependencies

```bash
npm install
```

#### 4. Environment Configuration

Copy the example environment file and generate an application key:

```bash
cp .env.example .env
php artisan key:generate
```

#### 5. Database Setup

Choose one of the following database configurations:

**Option A: MySQL Database**

Edit your `.env` file with your MySQL credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravue_blog
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

**Option B: SQLite Database (Recommended for Development)**

Edit your `.env` file:

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

Create the SQLite database file:

```bash
touch database/database.sqlite
```

#### 6. Database Migration and Seeding

Run the following commands to set up your database structure and populate it with sample data:

```bash
php artisan migrate
php artisan db:seed
```

#### 7. Create Storage Link

Create a symbolic link from the public storage directory to the storage directory:

```bash
php artisan storage:link
```

#### 8. Build Frontend Assets

For development:

```bash
npm run dev
```

For production:

```bash
npm run build
```

### Running the Application

#### Development Mode

Start the Laravel development server:

```bash
php artisan serve
```

In a separate terminal, start the Vite development server:

```bash
npm run dev
```

Access the application at `http://localhost:8000`

### Default User Accounts

After running the database seeders, you can log in using these default accounts:

**Regular User:**

-   Email: `test@example.com`
-   Password: `password`

**Administrator:**

-   Email: `admin@example.com`
-   Password: `adminpassword`

#### Production Mode

Build the production assets:

```bash
npm run build
```

Configure your web server to serve the application from the `public` directory.

## Features Overview

### User Management System

The application provides a complete user authentication and management system. Users can register for new accounts, log in securely, and manage their personal profiles. The system includes email verification and password reset functionality.

### Content Management

Users can create, edit, and delete blog posts through an intuitive interface. The content management system supports rich text editing and includes features for organizing and categorizing posts. All content is automatically saved with timestamps and author attribution.

### Administrative Controls

The application includes a comprehensive admin panel with enhanced permissions. Administrators can manage all user accounts, moderate content, and access detailed analytics about site usage. The admin interface provides tools for bulk operations and content moderation.

### Security Features

Security is implemented through Laravel Sanctum, providing robust API token-based authentication. All user sessions are securely managed, and the application includes protection against common web vulnerabilities such as CSRF attacks and SQL injection.

### Modern Frontend Experience

The Vue 3 frontend delivers a responsive, single-page application experience. The interface is built with Tailwind CSS, ensuring consistent styling across all devices. The application includes smooth transitions, real-time updates, and an intuitive user interface.

### Database Flexibility

The application supports both MySQL for production environments and SQLite for development and testing. This flexibility allows for easy development setup while maintaining production reliability. Database migrations ensure consistent schema management across environments.

## Technical Architecture

This application follows a decoupled architecture where Laravel serves as a pure API backend and Vue operates as an independent single-page application. Communication between the frontend and backend occurs through RESTful API endpoints using Axios for HTTP requests. This architecture provides flexibility and scalability while maintaining clear separation of concerns.

## Testing

The application includes comprehensive test coverage using PHPUnit. Tests are configured to run with SQLite for speed and isolation:

```bash
php artisan test
```

Run specific test suites:

```bash
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit
```

## Contributing

Contributions are welcome. Please ensure all tests pass before submitting pull requests. Follow the existing code style and include appropriate test coverage for new features.

## License

This project is open-sourced software licensed under the MIT license.

```bash
composer install
```

### Step 3: Install the JavaScript Stuff

```bash
npm install
```

### Step 4: Set Up Your Environment

```bash
# Copy the example environment file
cp .env.example .env

# Generate a shiny new application key
php artisan key:generate
```

### Step 5: Choose Your Database Adventure

You've got two options here, and both are totally fine:

#### Option A: I Want to Use MySQL (The Classic Choice)

Edit your `.env` file and update these lines:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravue_blog
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Just replace `your_username` and `your_password` with your actual MySQL credentials.

#### Option B: Keep It Simple with SQLite (Recommended for Getting Started)

Edit your `.env` file:

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

Then create the database file:

```bash
touch database/database.sqlite
```

_Pro tip: SQLite is perfect for development and testing - no setup required!_

### 6. Run Migrations and Seeders

```bash
# Run database migrations
php artisan migrate

# Seed the database with sample data
php artisan db:seed
```

### 7. Build Frontend Assets

```bash
# For development
npm run dev

# For production
npm run build
```

## 🏃‍♂️ Running the Application

### Development Mode

1. **Start the Laravel development server:**

```bash
php artisan serve
```

2. **Start the Vite development server (in another terminal):**

```bash
npm run dev
```

3. **Access the application:**
    - Frontend: http://localhost:8000
    - API: http://localhost:8000/api

### Production Mode

1. **Build frontend assets:**

```bash
npm run build
```

2. **Configure your web server** to point to the `public` directory

## 🧪 Testing

The application includes comprehensive tests that run on SQLite for speed and isolation.

### Run All Tests

```bash
php artisan test
```

### Run Specific Test Suite

```bash
# Run feature tests
php artisan test --testsuite=Feature

# Run unit tests
php artisan test --testsuite=Unit

# Run specific test class
php artisan test --filter=PostControllerTest
```

### Test Configuration

Tests are automatically configured to use:

-   SQLite in-memory database (`:memory:`)
-   Array cache driver
-   Sync queue driver

## 📁 Project Structure

```
laravue-blog/
├── app/
│   ├── Enums/           # Enumerations (PostStatus)
│   ├── Http/
│   │   ├── Controllers/ # API Controllers
│   │   ├── Middleware/  # Custom middleware
│   │   ├── Requests/    # Form request classes
│   │   └── Resources/   # API resources
│   ├── Models/          # Eloquent models
│   ├── Policies/        # Authorization policies
│   └── Services/        # Business logic services
├── database/
│   ├── factories/       # Model factories
│   ├── migrations/      # Database migrations
│   └── seeders/         # Database seeders
├── resources/
│   ├── js/             # Vue.js SPA application
│   │   ├── components/ # Vue components
│   │   ├── layouts/    # Layout components
│   │   ├── Pages/      # Vue pages/views
│   │   ├── routers/    # Vue Router configuration
│   │   └── stores/     # Pinia stores
│   └── css/            # Stylesheets
└── tests/
    ├── Feature/        # Feature tests
    └── Unit/           # Unit tests
```

## 🔧 Configuration

### Database Switching

The application supports both MySQL and SQLite. To switch databases:

1. **Update `.env` file** with your preferred database configuration
2. **Run migrations:**

```bash
php artisan migrate:fresh --seed
```

### Cache and Queue

For production, configure proper cache and queue drivers in `.env`:

```env
CACHE_STORE=redis
QUEUE_CONNECTION=redis
```

## 🚦 API Routes

The application provides RESTful API endpoints:

-   `GET /api/posts` - List posts
-   `POST /api/posts` - Create post
-   `GET /api/posts/{id}` - Show post
-   `PUT /api/posts/{id}` - Update post
-   `DELETE /api/posts/{id}` - Delete post

Authentication required for most endpoints using Sanctum tokens.

## 🏗 Architecture

This application follows a **decoupled SPA architecture**:

-   **Laravel Backend**: Serves as a pure API backend with routes under `/api/*`
-   **Vue Frontend**: Independent Single Page Application (SPA)
-   **Communication**: Frontend communicates with backend via Axios HTTP requests
-   **Authentication**: Token-based using Laravel Sanctum
-   **Routing**: Vue Router handles client-side routing, Laravel handles API routes

## 🧑‍💻 Development Commands

```bash
# Clear application cache
php artisan cache:clear

# Clear route cache
php artisan route:clear

# Clear configuration cache
php artisan config:clear

```

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

**Made with ❤️ using Laravel and Vue.js**
