# 🤝 Contributing Guide

Cảm ơn bạn đã muốn contribute vào dự án! Hướng dẫn này sẽ giúp bạn bắt đầu.

## 🚀 Bắt đầu

### 1. Fork & Clone Repository

```bash
# Fork trên GitHub (click Fork button)
# Sau đó clone fork của bạn
git clone https://github.com/your-username/WEB-PLPT.git
cd WEB-PLPT

# Add upstream remote
git remote add upstream https://github.com/tangtrandinh814-maker/WEB-PLPT.git
```

### 2. Create Feature Branch

```bash
# Update main branch
git fetch upstream
git checkout main
git merge upstream/main

# Tạo feature branch
git checkout -b feature/description
# Hoặc fix branch
git checkout -b fix/description
```

**Branch naming convention:**
```
feature/new-feature-name      # Tính năng mới
fix/bug-description           # Sửa bug
docs/documentation-update     # Cập nhật documentation
refactor/code-improvement     # Refactor code
test/add-tests               # Thêm tests
```

## 💻 Development Workflow

### 1. Make Your Changes

```bash
# Edit files
# Run tests
php artisan test

# Check linting
```

### 2. Commit Your Work

```bash
# Stage changes
git add .

# Commit với meaningful message
git commit -m "feat: Add user authentication feature"
git commit -m "fix: Fix article sorting bug"
git commit -m "docs: Update API documentation"
```

**Commit message format:**
```
<type>: <subject>

<body>

<footer>
```

**Types:**
- `feat`: Thêm tính năng mới
- `fix`: Sửa bug
- `docs`: Cập nhật documentation
- `style`: Thay đổi formatting, whitespace
- `refactor`: Refactor code (không thay đổi functionality)
- `test`: Thêm hoặc update tests
- `chore`: Update dependencies, build scripts

**Subject:**
- Imperative mood ("add" not "added" or "adds")
- Không viết hoa chữ cái đầu
- Không có dấu chấm ở cuối

**Examples:**
```bash
git commit -m "feat: add user registration page"
git commit -m "fix: resolve article filtering issue"
git commit -m "refactor: simplify category selection logic"
```

### 3. Push to Your Fork

```bash
git push origin feature/description
```

### 4. Create Pull Request

1. Vào GitHub repository
2. Click "Compare & pull request"
3. Fill in PR description:

```markdown
## Description
Mô tả ngắn gọn về những gì PR làm

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Related Issues
Fixes #(issue number)

## How Has This Been Tested?
Mô tả cách bạn test changes

## Testing Instructions
Bước để test:
1. ...
2. ...
3. ...

## Screenshots (if applicable)
Thêm screenshots nếu có giao diện thay đổi

## Checklist
- [ ] Code follows style guidelines
- [ ] Self-review completed
- [ ] Comments added for complex sections
- [ ] Documentation updated
- [ ] No new warnings generated
- [ ] Tests added/updated
```

### 5. Address Code Review Feedback

1. Reviewers sẽ comment trên PR
2. Make changes locally
3. Commit changes (không cần squash)
4. Push to same branch

```bash
git add .
git commit -m "feat: address review feedback"
git push origin feature/description
```

### 6. Merge to Main

- Sau khi được approve
- Maintainer sẽ merge PR
- Branch sẽ tự động delete trên remote

## 📋 Code Style Guide

### Laravel Code

```php
// ✅ Good
public function getUserArticles(User $user): Collection
{
    return $user->articles()
        ->where('is_published', true)
        ->get();
}

// ❌ Bad
public function get_user_articles($user){
$result = $user->articles;
return $result;
}
```

### Blade Templates

```blade
{{-- ✅ Good --}}
@if ($user->isAdmin())
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
@endif

{{-- ❌ Bad --}}
@if ($user->role == 'admin')
    <a href="/admin">Dashboard</a>
@endif
```

### JavaScript/CSS

```javascript
// ✅ Good
const submitForm = () => {
    const data = {
        name: inputName.value,
        email: inputEmail.value
    };
    
    fetch('/api/users', {
        method: 'POST',
        body: JSON.stringify(data)
    });
};

// ❌ Bad
let submit = function(){
var d = {n:input1.value,e:input2.value};
fetch('/api/users',{method:'POST',body:JSON.stringify(d)});
};
```

## 🧪 Testing

### Run Tests

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter testUserCreation

# Run with coverage
php artisan test --coverage
```

### Write Tests

```php
// tests/Feature/ArticleTest.php
public function testCanCreateArticle(): void
{
    $user = User::factory()->create(['role' => 'admin']);
    
    $response = $this->actingAs($user)
        ->post('/admin/articles', [
            'title' => 'Test Article',
            'content' => 'Test content',
            'category_id' => 1
        ]);
    
    $response->assertRedirect();
    $this->assertDatabaseHas('articles', ['title' => 'Test Article']);
}
```

## 🔍 Before Submitting PR

- [ ] Code follows project style guidelines
- [ ] Self-review of own code
- [ ] Comments added for complex logic
- [ ] Documentation updated
- [ ] No console errors/warnings
- [ ] Tests added/updated
- [ ] Database migrations created (if needed)
- [ ] Environment variables documented

## ❓ Questions?

- 💬 Comment trên GitHub issue
- 📧 Contact maintainers
- 📚 Check documentation

## 📜 License

By contributing, you agree that your contributions will be licensed under the MIT License.

## Code of Conduct

### Our Pledge

We are committed to providing a welcoming and inspiring community for all.

### Examples of Behavior That Contributes

- Using welcoming and respectful language
- Being respectful of differing viewpoints
- Accepting constructive criticism
- Focusing on what is best for the community
- Showing empathy

### Examples of Unacceptable Behavior

- Insulting/derogatory comments
- Personal attacks
- Harassment
- Public or private attacks

---

**Happy contributing! 🎉**
