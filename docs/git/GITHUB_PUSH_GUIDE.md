# GitHub Push Guide for StudentHub

Step-by-step instructions to initialize, stage, commit, and push this repository to GitHub.

---

## 1. Prepare Git Remote

If you haven't created a GitHub repository yet:
1. Open [github.com/new](https://github.com/new).
2. Set the repository name (e.g. `Student-Portal-25CS045` or `StudentHub`).
3. Set visibility to **Public** or **Private**.
4. Leave "Add a README", ".gitignore", and "License" unchecked (we already have clean files prepared).
5. Click **Create repository**.
6. Copy the remote URL (e.g., `https://github.com/<your-username>/Student-Portal-25CS045.git`).

---

## 2. Terminal Commands to Push

Open PowerShell or Command Prompt in `C:\xampp\htdocs\Student-Portal-25CS045-main`:

```bash
# 1. Check current git status
git status

# 2. Stage all organized files, documentation, and folders
git add .

# 3. Commit with a clear, descriptive message
git commit -m "feat: complete UI design system, official CHARUSAT timetable & structured folder layout"

# 4. Set default branch to main
git branch -M main

# 5. Add your GitHub remote (replace with your actual repository URL)
git remote add origin https://github.com/<your-username>/Student-Portal-25CS045.git

# 6. Push to GitHub
git push -u origin main
```

---

## 3. What Git Tracks vs Ignores

The `.gitignore` file has been configured to ensure:
- **Tracked**: All PHP source code, modules (`student/`, `admin/`, `api/`, `pages/`), styles (`css/`), scripts (`js/`), database schemas (`database/`, `migrations/`), university logos (`images/`), comprehensive documentation (`docs/`), and academic practicals (`practicals/`).
- **Ignored**: Temporary session dumps, IDE configurations (`.vscode`, `.idea`), OS artifacts (`Thumbs.db`, `.DS_Store`), and large binary temporary files.


