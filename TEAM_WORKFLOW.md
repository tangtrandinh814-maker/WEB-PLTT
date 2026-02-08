# 👥 Team Workflow Guide

Hướng dẫn và best practices cho làm việc nhóm trên dự án WEB-PLPT.

## 📋 Team Structure

- **Team Lead**: Quản lý project, code review, merge PR
- **Developers**: Develop features, fix bugs, write code
- **QA**: Test features, report bugs
- **Designers**: UI/UX design, frontend

## 🔀 Git Workflow - GitHub Flow

### Workflow Overview

```
main branch (protected)
    ↑
    │ (Pull Request)
    │
feature branch (developer working)
    ↑
    │ (Commit)
    │
Local Development
```

### Detailed Steps

#### 1. Setup Initial Repository

```bash
# Team Lead: Create repository
# Team Members: Clone repository
git clone https://github.com/tangtrandinh814-maker/WEB-PLPT.git
cd WEB-PLPT

# Configure git profile
git config user.name "Your Name"
git config user.email "your.email@example.com"
```

#### 2. Create Feature Branch

```bash
# Update your local main
git checkout main
git pull origin main

# Create feature branch
git checkout -b feature/descriptive-name

# Examples:
git checkout -b feature/add-article-search
git checkout -b feature/improve-ui-dashboard
git checkout -b fix/pagination-not-working
```

#### 3. Make Changes & Commit

```bash
# View changes
git status
git diff

# Stage changes
git add src/
git add resources/

# View staged changes
git diff --staged

# Commit with meaningful message
git commit -m "feat: implement article search functionality"
```

#### 4. Push to Remote

```bash
# First push (creates branch on remote)
git push -u origin feature/descriptive-name

# Subsequent pushes
git push origin feature/descriptive-name
```

#### 5. Create Pull Request

1. Go to GitHub repository
2. You'll see a "Compare & pull request" button
3. Click it
4. Fill in the PR template:

```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation

## Related Issues
Closes #123

## Testing Instructions
How to test the changes:
1. Navigate to...
2. Click on...
3. Verify that...

## Screenshots
[If applicable]

## Checklist
- [ ] Code follows style guidelines
- [ ] Self-review completed
- [ ] Tests added/updated
- [ ] Documentation updated
```

5. Request reviewers (click "Reviewers")
6. Submit PR

#### 6. Code Review

**For Reviewers:**
```
- Read the code carefully
- Run it locally to test
- Leave constructive comments
- Approve or request changes
```

**For PR Author:**
```
- Respond to comments
- Make requested changes
- Commit and push (don't squash yet)
- Wait for approval
```

#### 7. Merge to Main

1. Ensure all checks pass (CI/CD)
2. Ensure PR is approved
3. Team Lead merges PR (usually squash merge recommended)
4. Branch auto-deletes after merge

```bash
# Cleanup locally
git checkout main
git pull origin main
git branch -d feature/descriptive-name
```

## 📅 Development Schedule

### Daily Standup (Suggested 10 mins)

```
Each developer reports:
- ✅ What I did yesterday
- 🎯 What I'm doing today
- 🚧 Any blockers?
```

### Code Review Practice

- PR should be reviewed within 24 hours
- Minimum 1 approval before merge
- Team Lead final decision

## 🎯 Task Management

### Task Assignment

1. Create GitHub Issue for each task
2. Use labels: `bug`, `feature`, `documentation`, `help wanted`
3. Use milestones for sprints
4. Assign to developer

### Issue Template

```markdown
## Description
Clear description of the task

## Acceptance Criteria
- [ ] Requirement 1
- [ ] Requirement 2
- [ ] Requirement 3

## Resources
- Links to relevant documentation
- Related issues

## Tasks (for bugs)
- [ ] Reproduce the bug
- [ ] Identify root cause
- [ ] Fix the issue
- [ ] Write tests
```

### Issue Linking

```bash
# Link PR to issue
# In PR description:
Closes #123

# Or in commit message:
git commit -m "feat: add search feature

Closes #123"
```

## 🔒 Branch Protection Rules

**Recommended Branch Protection for `main`:**

- ✅ Require pull request reviews before merging
- ✅ Require status checks to pass
- ✅ Require branches to be up to date before merging
- ✅ Require code review from code owners
- ❌ Dismiss stale pull request approvals
- ✅ Require conversation resolution before merging
- ✅ Include administrators

## 🧪 Code Review Checklist

### Reviewer Checklist

- [ ] Code is clear and understandable
- [ ] No obvious logic errors
- [ ] Follows project conventions
- [ ] No unnecessary complexity
- [ ] Tests are included and pass
- [ ] Documentation is updated
- [ ] No hardcoded values
- [ ] Performance is acceptable
- [ ] Security considerations addressed

### Common Review Comments

```markdown
## Nice touches! A few things:

### 1. Style (nitpick)
The spacing here doesn't match our convention. 
See style guide: [link]

### 2. Potential bug
This could fail if $article is null.
Suggest adding null check:
---suggestion
if ($article === null) return;
---
```

## 🐛 Bug Report Template

When reporting a bug, include:

```markdown
## Summary
Brief description of bug

## Steps to Reproduce
1. Click...
2. Fill form with...
3. Submit button appears as...

## Expected Behavior
Button should be green

## Actual Behavior
Button is red

## Screenshots
[attachment]

## Environment
- Browser: Chrome 100
- OS: Windows 10
- Device: Desktop
```

## 📊 Git Best Practices

### Commit Messages

✅ **Good:**
```
feat: add user profile page
fix: resolve article deletion error
docs: update API documentation
refactor: simplify category filtering
test: add tests for article creation
```

❌ **Bad:**
```
update
fix bug
changes
asdf
WIP
```

### Commit Frequency

- ✅ Small, focused commits
- ✅ Logical units of work
- ❌ One huge commit with multiple features
- ❌ Too many micro commits

### Push Frequency

- ✅ Push daily at end of work
- ✅ Push before leaving
- ❌ Go days without pushing

## 🚫 What NOT to do

❌ **Never:**
```bash
# Force push to main
git push -f origin main

# Merge without review
# Merge with merge conflicts unresolved
# Commit secrets/passwords
# Commit node_modules, .env, vendor/
# Use git history rewrite on shared branches
```

## 🔧 Handling Merge Conflicts

### Conflict Example

```
<<<<<<< HEAD
    public function test() {
        return 'version A';
    }
=======
    public function test() {
        return 'version B';  
    }
>>>>>>> feature/new-feature
```

### Resolution Steps

```bash
# 1. See conflicted files
git status

# 2. Edit file and choose version
# Option A: Keep current (HEAD)
# Option B: Accept incoming (branch)
# Option C: Combine both

# 3. Remove conflict markers
# 4. Stage changes
git add filename

# 5. Complete merge
git commit -m "Merge: resolve conflicts from feature branch"
```

## 📞 Communication Channels

**GitHub Issues:** Technical discussions, bug reports
**GitHub Discussions:** General questions, ideas
**Email:** Urgent matters
**Team Chat:** Daily communication (Slack/Discord)
**Meetings:** Standups, planning sessions

## 📈 Performance Metrics

Track these metrics:

- PR merge time (target: < 24hrs)
- Review cycle time
- Test coverage
- Bug escape rate
- Code review effectiveness

## 🎓 Learning & Growth

- Share knowledge in team meetings
- Code reviews are teaching opportunities
- Suggest improvements, not criticisms
- Ask for help when stuck
- Mentor junior developers

## 🏁 Definition of Done (DoD)

Before marking PR as ready to merge:

- ✅ Code is written
- ✅ Code is reviewed
- ✅ Tests are written
- ✅ Tests pass
- ✅ Documentation is updated
- ✅ No merge conflicts
- ✅ Performance impact reviewed
- ✅ Accessibility checked (if UI)
- ✅ Security reviewed (if sensitive)

## 📋 Release Process

### Before Release

```bash
# Update version in package.json, composer.json
# Update CHANGELOG.md
git checkout -b release/v1.0.0

# Create release branch
git commit -m "chore: bump version to 1.0.0"
git push origin release/v1.0.0
```

### Release

```bash
# Create PR for release
# Team lead reviews and merges
# Create GitHub release tag
git tag -a v1.0.0 -m "Release version 1.0.0"
git push origin v1.0.0
```

## 🆘 Emergency Hotfix

```bash
# Create hotfix branch from main
git checkout -b hotfix/critical-bug main

# Fix and commit
git commit -m "fix: critical bug"

# Push and create PR
git push origin hotfix/critical-bug

# Merge to main and develop
# Merge back to develop branch too
```

---

**Remember: Communication and clarity are key to smooth teamwork! 🚀**
