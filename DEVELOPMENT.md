# 🛠️ Development Guide

Hướng dẫn thiết lập và phát triển dự án WEB-PLPT cục bộ.

## 📋 Requirements

- PHP >= 8.1
- Composer >= 2.0
- Node.js >= 16
- MySQL 5.7+ hoặc MariaDB 10.3+
- Git

## 🚀 Setup Development Environment

### Step 1: Clone Repository

```bash
git clone https://github.com/tangtrandinh814-maker/WEB-PLPT.git
cd WEB-PLPT
```

### Step 2: Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### Step 3: Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

Edit `.env` file:

```dotenv
# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=news_classifier_db
DB_USERNAME=root
DB_PASSWORD=

# AI Configuration
GEMINI_API_KEY=your_gemini_api_key_here
GEMINI_MODEL=gemini-pro

# App Configuration
APP_URL=http://localhost:8000
APP_DEBUG=true
```

### Step 4: Database Setup

```bash
# Create database (MySQL)
mysql -u root -e "CREATE DATABASE news_classifier_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php artisan migrate

# Seed database with test data
php artisan db:seed
```

Or in one command for fresh database:

```bash
php artisan migrate:fresh --seed
```

### Step 5: Start Development Servers

```bash
# Terminal 1 - Laravel Server (Port 8000)
php artisan serve

# Terminal 2 - Vite Dev Server (Port 5173)
npm run dev
```

Access the application at: **http://localhost:8000**

## 🔐 Test Credentials

```
Email: admin@news.com
Password: password
```

## 🏗️ Project Architecture

### Directory Structure

```
app/
├── Console/
│   └── Commands/              # Custom artisan commands
├── Exceptions/
│   └── Handler.php           # Exception handling
├── Http/
│   ├── Controllers/
│   │   ├── ArticleController.php       # Public article controller
│   │   └── Admin/
│   │       └── DashboardController.php # Admin panel
│   ├── Middleware/
│   │   ├── CheckAdminRole.php         # Admin role verification
│   │   └── [others]
│   ├── Requests/             # Form request validation
│   └── Kernel.php            # HTTP kernel
├── Models/
│   ├── User.php
│   ├── Article.php
│   ├── Category.php
│   ├── Source.php
│   └── ArticleView.php
├── Services/
│   ├── AIClassifierService.php    # Gemini API integration
│   └── NewsCrawlerService.php
├── Providers/
│   ├── AppServiceProvider.php
│   ├── RouteServiceProvider.php
│   └── [others]
└── [other folders]
```

## 🗄️ Database Schema

### Users
```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Categories
```sql
CREATE TABLE categories (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    icon VARCHAR(100),
    color VARCHAR(50),
    description TEXT,
    slug VARCHAR(255) UNIQUE,
    is_active BOOLEAN DEFAULT true,
    order INT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Articles
```sql
CREATE TABLE articles (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255),
    slug VARCHAR(255) UNIQUE,
    content LONGTEXT,
    summary TEXT,
    author VARCHAR(255),
    image_url VARCHAR(255),
    category_id BIGINT,
    source_id BIGINT,
    is_featured BOOLEAN DEFAULT false,
    is_published BOOLEAN DEFAULT false,
    ai_category VARCHAR(255),
    ai_confidence_score DECIMAL(5,2),
    views_count INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (source_id) REFERENCES sources(id)
);
```

### Sources
```sql
CREATE TABLE sources (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    url VARCHAR(255),
    rss_url VARCHAR(255),
    is_active BOOLEAN DEFAULT true,
    last_crawled_at TIMESTAMP NULL,
    crawl_frequency INT DEFAULT 24,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### ArticleViews
```sql
CREATE TABLE article_views (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    article_id BIGINT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id)
);
```

## 🧪 Testing

### Run Tests

```bash
# All tests
php artisan test

# Specific test file
php artisan test tests/Feature/ArticleControllerTest.php

# With coverage
php artisan test --coverage
```

### Write Tests

```php
// tests/Feature/ArticleTest.php
<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    public function testCanViewArticles()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function testAdminCanCreateArticle()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->post('/admin/articles', [
                'title' => 'Test Article',
                'content' => 'Test content',
                'category_id' => 1,
                'source_id' => 1,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('articles', ['title' => 'Test Article']);
    }
}
```

## 🔧 Useful Commands

### Database

```bash
# Fresh migration and seed
php artisan migrate:fresh --seed

# Rollback all migrations
php artisan migrate:rollback

# Rollback specific number of migrations
php artisan migrate:rollback --step=1

# Create new migration
php artisan make:migration create_table_name

# Create new seeder
php artisan make:seeder TableNameSeeder
```

### Code Generation

```bash
# Create new model (with migration)
php artisan make:model ModelName -m

# Create new controller
php artisan make:controller ControllerName

# Create new middleware
php artisan make:middleware MiddlewareName

# Create new service
php artisan make:class Services/ServiceName
```

### Cache & Config

```bash
# Clear all caches
php artisan cache:clear

# Clear configuration cache
php artisan config:clear

# Clear compiled views
php artisan view:clear

# Clear all caches and configs
php artisan cache:clear && php artisan config:clear && php artisan view:clear
```

## 🔌 API Development

### Making API Calls

```php
// Service example
use Illuminate\Support\Facades\Http;

class AIClassifierService
{
    public function classify($text)
    {
        $response = Http::post('https://api.example.com/classify', [
            'text' => $text,
            'api_key' => config('services.gemini.api_key')
        ]);

        return $response->json();
    }
}
```

### Testing API Endpoints

```php
// test/Feature/ApiTest.php
public function testArticleAPI()
{
    $response = $this->getJson('/api/articles');
    
    $response
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'content', 'created_at']
            ]
        ]);
}
```

## 🐛 Debugging

### Enable Debug Bar (Laravel Debugbar)

```bash
composer require barryvdh/laravel-debugbar --dev
```

### Log Debugging

```php
// In code
Log::debug('Debug message', ['key' => 'value']);
Log::info('Info message');
Log::warning('Warning message');
Log::error('Error message');

// Check logs
tail -f storage/logs/laravel.log
```

### Browser Console

```javascript
// Vue/JavaScript debugging
console.log('Debug message');
console.error('Error message');
```

## 📊 Performance Tips

### Query Optimization

```php
// ❌ Bad - N+1 query
$articles = Article::all();
foreach ($articles as $article) {
    echo $article->category->name; // Extra query for each article
}

// ✅ Good - Eager loading
$articles = Article::with('category')->get();
foreach ($articles as $article) {
    echo $article->category->name; // No extra queries
}
```

### Caching

```php
// Cache query result
$categories = Cache::remember('categories', 3600, function () {
    return Category::all();
});
```

## 🔐 Security Checklist

- [ ] Never commit `.env` file with real credentials
- [ ] Use HTTPS in production
- [ ] Validate all user inputs
- [ ] Use CSRF tokens in forms
- [ ] Hash sensitive data
- [ ] Use environment variables for secrets
- [ ] Update dependencies regularly
- [ ] Run security audit: `composer audit`

## 📝 Documentation

### Code Documentation

```php
/**
 * Create a new article
 *
 * @param \App\Http\Requests\StoreArticleRequest $request
 * @return \Illuminate\Http\RedirectResponse
 */
public function store(StoreArticleRequest $request)
{
    // Implementation
}
```

### Blade Template Comments

```blade
{{-- This is a comment in Blade --}}
<!-- This is an HTML comment -->
```

## 🆘 Troubleshooting

### "Class not found" Error

```bash
# Clear PHP autoloader
composer dump-autoload
```

### Database connection error

```bash
# Check .env DATABASE values
# Verify MySQL is running
# Check database exists

mysql -u root -e "CREATE DATABASE news_classifier_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### Permission denied in storage/

```bash
# Linux/Mac
chmod -R 775 storage bootstrap/cache

# Windows - Run as Administrator
```

### npm run dev not working

```bash
# Clear npm cache
npm cache clean --force

# Reinstall
rm package-lock.json
npm install
npm run dev
```

## 📚 Learning Resources

- [Laravel Documentation](https://laravel.com/docs)
- [PHP Documentation](https://www.php.net/manual/)
- [Bootstrap Documentation](https://getbootstrap.com/docs)
- [MySQL Documentation](https://dev.mysql.com/doc/)

## 🤝 Getting Help

- Check existing GitHub issues
- Create new issue with detailed description
- Contact team members
- Join team chat/Discord

---

**Happy coding! 🚀**
