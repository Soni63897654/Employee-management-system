# Employee Management System


A **Laravel 12-based Employee Management System** that allows you to manage employees dynamically with departments and managers. Built with **AJAX** for smooth updates and **Bootstrap 5** for a responsive design.

---

## 🔹 Features

- List all employees with department & manager info  
- Add, edit, delete employees dynamically  
- Dynamic search and filtering  
- Responsive user interface (Bootstrap 5)  
- Database migrations & seeders for easy setup  
- Proper validation & error handling  

---

## Technology Stack

| Component | Technology |
|-----------|------------|
| Backend | Laravel 12 |
| Frontend | Blade Templates, HTML, CSS, Bootstrap 5 |
| Database | MySQL / MariaDB |
| AJAX | Dynamic CRUD operations |
| Version Control | GitHub |
| PHP | 8.2.12 |

---

##  Installation Instructions


### 1️ Clone Repository
```bash
git clone https://github.com/Soni63897654/Employee-management-system.git
cd Employee-management-system

# Install Dependencies
    
composer install
npm install
npm run dev

#Configure Environment
copy .env.example .env
php artisan key:generate


# Configure Environment

php artisan migrate

# Seed the Database

php artisan db:seed --class=DepartmentSeeder
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=EmployeeSeeder


#  Contact

GitHub: Soni63897654
Email:  soni638976@gmail.com