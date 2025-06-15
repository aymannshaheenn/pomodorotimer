# pomodoro Timer

**Pomodoro** is a comprehensive and user-friendly task and productivity management system based on the **Pomodoro Technique**. This web application was developed as part of a **Database Systems course project**, featuring a clean and functional interface, goal tracking, session logging, and database integration.

---

## 📌 Project Summary

This project demonstrates the integration of **front-end technologies**, **PHP**, and **MySQL** to create a fully functional Pomodoro-based productivity system. Users can manage tasks, track timed work sessions, and monitor goal progress—all stored and retrieved through a relational database structure.

---

## 🛠️ Tech Stack

- **Frontend**: HTML5, CSS3, JavaScript (vanilla)
- **Backend**: PHP 8
- **Database**: MySQL (InnoDB engine with relational design)
- **Environment**: XAMPP / Apache server

---

## 💡 Features

| Feature | Description |
|--------|-------------|
| 👤 **User Authentication** | Sign up and log in securely to manage personal tasks and sessions. |
| 📋 **Task Management** | Add, update, and delete tasks with priority levels and session targets. |
| ⏳ **Pomodoro Timer** | Start work sessions with optional short or long breaks, and track remaining sessions per task. |
| 🎯 **Goal Progress Tracking** | Define personal goals and monitor achievement progress through a visual progress bar. |
| 📜 **Session Logging** | Every work/break session is saved in the database with task and timestamp information. |
| 🌙 **Dark Mode Support** | Toggle between light and dark themes for a comfortable UI experience. |
| 🌸 **Stylized Interface** | Pink floral theme with a notepad-style layout focused on clarity and aesthetics. |

---

## 🧩 Database Design

### 📁 Database Name: `pomodoro_task_manager`

#### 🔐 `users`
- `user_id` (PK)
- `username`
- `password_hash`
- `created_at`

#### 📋 `tasks`
- `task_id` (PK)
- `user_id` (FK)
- `title`
- `priority` (`High`, `Medium`, `Low`)
- `sessions` (target session count)
- `notes`

#### ⏱️ `sessions`
- `session_id` (PK)
- `user_id` (FK)
- `task_id` (FK)
- `session_type` (`Work`, `Short Break`, `Long Break`)
- `duration`
- `custom_work_duration`
- `custom_break_duration`
- `timestamp`

#### 📜 `history`
- `history_id` (PK)
- `user_id` (FK)
- `session_id` (FK)
- `task_id` (FK)
- `task_name`
- `session_type`
- `duration`
- `timestamp`

#### 🎯 `goals`
- `goal_id` (PK)
- `user_id` (FK)
- `goal_text`
- `is_achieved` (Boolean)
- `created_at`
- `achieved_at` (nullable)

---
