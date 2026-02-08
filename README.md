# 🗞️ Tin Tức AI - News Classification System

Hệ thống phân loại tin tức tự động sử dụng AI (Gemini API). Được xây dựng với Laravel 10 và Bootstrap 5.

## ✨ Tính năng chính

- 📰 **Quản lý bài viết** - Tạo, sửa, xóa bài viết
- 📁 **Quản lý danh mục** - Tổ chức bài viết theo chủ đề
- 📡 **Quản lý nguồn tin** - Kết nối với các nguồn tin
- 🤖 **AI Classification** - Phân loại bài viết tự động sử dụng Gemini API
- 👥 **Phân quyền Admin/User** - Kiểm soát truy cập dựa trên role
- 🔍 **Tìm kiếm & Lọc** - Tìm kiếm bài viết theo danh mục và từ khóa
- 📊 **Dashboard** - Thống kê chi tiết với bảng điều khiển admin
- 👀 **View Tracking** - Theo dõi lượt xem bài viết

## 🛠️ Stack Công nghệ

| Công nghệ | Phiên bản |
|-----------|----------|
| Laravel | 10.x |
| PHP | >= 8.1 |
| MySQL | 5.7+ |
| Bootstrap | 5 |
| Vite | 5.x |
| Node.js | 16+ |

## 🚀 Cài đặt nhanh

### 1. Clone & Setup
\`\`\`bash
git clone https://github.com/tangtrandinh814-maker/WEB-PLTT.git
cd WEB-PLPT

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate
\`\`\`

### 2. Cấu hình Database
Edit file \`.env\`:
\`\`\`
DB_DATABASE=news_classifier_db
DB_USERNAME=root
DB_PASSWORD=
GEMINI_API_KEY=your_gemini_key
\`\`\`

### 3. Migration & Seeding
\`\`\`bash
php artisan migrate:fresh --seed
\`\`\`

### 4. Start Development
\`\`\`bash
# Terminal 1 - Laravel Server
php artisan serve

# Terminal 2 - Vite Dev Server
npm run dev
\`\`\`

**Truy cập**: http://localhost:8000

## 👤 Test Credentials

\`\`\`
Email: admin@news.com
Password: password
Role: Admin
\`\`\`

## 👥 Teamwork Guide

### Git Workflow cho Team

#### 1️⃣ Tạo feature branch
\`\`\`bash
git checkout -b feature/tên-feature
# Ví dụ: 
git checkout -b feature/add-sorting
git checkout -b feature/improve-performance
\`\`\`

#### 2️⃣ Commit với convention
\`\`\`bash
git add .
git commit -m "feat: description"
git commit -m "fix: description"
git commit -m "docs: description"
\`\`\`

**Commit Convention:**
- \`feat:\` Thêm tính năng mới
- \`fix:\` Sửa bug
- \`docs:\` Cập nhật documentation
- \`style:\` Thay đổi formatting
- \`refactor:\` Refactor code
- \`test:\` Thêm tests
- \`chore:\` Cập nhật dependencies

#### 3️⃣ Push & Create Pull Request
\`\`\`bash
git push origin feature/tên-feature
\`\`\`
- Vào GitHub repository
- Click "Compare & pull request"
- Mô tả chi tiết thay đổi (What, Why, How)
- Assign reviewers
- Request review từ team

#### 4️⃣ Code Review & Merge
- Team members review & comment
- Fix feedback từ reviewers
- Sau khi được approve: Click "Merge pull request"
- Delete branch nếu không dùng nữa

#### 5️⃣ Update local repo
\`\`\`bash
git checkout main
git pull origin main
\`\`\`

### Best Practices

✅ **DO:**
- Tạo branch riêng cho mỗi tính năng
- Commit nhỏ, có ý nghĩa
- Push thường xuyên
- Request review trước khi merge
- Update main branch trước khi tạo feature branch mới

❌ **DON'T:**
- Commit trực tiếp vào main
- Push code chưa test
- Merge mà không review
- Để branch cũ không dùng trên remote
- Dùng generic commit messages như "update", "fix"

## 📂 Project Structure

\`\`\`
WEB-PLPT/
├── app/
│   ├── Http/Controllers/
│   ├── Models/
│   ├── Services/
│   └── Http/Middleware/
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/views/
│   ├── admin/
│   ├── articles/
│   └── auth/
├── routes/
├── config/
└── storage/
\`\`\`

## 🔐 Security Features

- ✅ CSRF protection
- ✅ SQL injection prevention
- ✅ Role-based access control (Admin/User)
- ✅ Password hashing (bcrypt)
- ✅ Middleware authentication

## 📞 Communication

- 💬 Discuss issues trên GitHub
- 📧 Email team lead nếu cần help
- 📋 Update status trên PR

## 📄 License

MIT License - Open source for educational purposes

---

**Ready to collaborate! Let's build something amazing together!** 🚀
