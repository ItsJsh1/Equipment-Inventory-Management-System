# EIMS - Equipment Inventory Management System

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Tailwind_CSS-4.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white" alt="Alpine.js">
</p>

## 📋 Project Description

**EIMS (Equipment Inventory Management System)** is a comprehensive web-based application designed to streamline and automate the management of equipment and assets within an organization. The system provides a centralized platform for tracking equipment lifecycle from acquisition to disposal, managing borrowings, scheduling maintenance, and generating insightful reports.

This system is ideal for:
- **Educational Institutions** - Managing laboratory equipment, computers, and learning materials
- **Corporate Offices** - Tracking office equipment, IT assets, and furniture
- **Government Agencies** - Maintaining accountability for public assets
- **Healthcare Facilities** - Managing medical equipment and devices
- **Manufacturing Companies** - Tracking tools, machinery, and production equipment

---

## ✨ Key Features

### 🔐 User Management & Authentication
- Secure login and registration system
- Role-based access control (Super Admin, Admin, Staff)
- User account activation/deactivation
- Password management

### 📦 Equipment Management
- Complete equipment inventory tracking
- Auto-generated equipment codes (customizable prefix)
- Equipment categorization by brand, category, and location
- Track equipment status (Available, In Use, Under Maintenance, Disposed)
- Track equipment condition (New, Good, Fair, Poor)
- Image upload for equipment
- Equipment specifications and warranty tracking
- Export equipment data to Excel

### 🔄 Transaction Management
- **Incoming Transactions** - Record new equipment acquisitions
- **Outgoing Transactions** - Track equipment transfers or releases
- Transaction approval workflow (Pending → Approved → Completed)
- Auto-generated transaction codes with date format (PREFIX-YYYY-MM-DD-XXXX)
- Link transactions to departments and personnel
- Export transactions to Excel

### 📋 Borrowing Management
- Equipment borrowing requests and tracking
- Expected and actual return date monitoring
- **Overdue borrowing alerts** - Track items not returned on time
- Borrower information (internal employees or external persons)
- Return processing with condition assessment
- Export borrowing records to Excel

### 🔧 Maintenance Management
- Schedule preventive maintenance
- Track maintenance history per equipment
- Maintenance workflow (Scheduled → In Progress → Completed)
- Record maintenance costs and service providers
- Export maintenance records to Excel

### 🗑️ Disposal Management
- Equipment disposal requests
- **Single and Bulk disposal** - Dispose multiple items at once
- Disposal approval workflow (Pending → Approved → Completed)
- Automatic equipment status update upon disposal completion
- Export disposal records to Excel

### 📊 Dashboard & Analytics
- Real-time statistics overview
- Total equipment count with status breakdown
- Active borrowings and overdue alerts
- Pending transactions count
- Ongoing maintenance tracking
- Recent activities feed
- Category distribution chart

### 📈 Reports Module
- Equipment inventory reports
- Transaction reports (filtered by type, date range)
- Borrowing reports
- Maintenance reports
- Disposal reports
- **Export to Excel and PDF**

### 📝 Audit Trail
- Complete activity logging
- Track all create, update, and delete operations
- User action accountability
- Filterable by date range and action type

### ⚙️ System Settings
- Customizable code prefixes (Equipment, Transaction, Borrowing, Maintenance, Disposal)
- Organization settings
- System configuration management

### 🌓 User Experience
- **Dark mode support** - Toggle between light and dark themes
- **Responsive design** - Works on desktop, tablet, and mobile
- Clean and modern UI with Tailwind CSS
- Interactive components with Alpine.js

---

## 🏗️ System Architecture

### Main Modules

| Module | Description |
|--------|-------------|
| **Dashboard** | Overview statistics and recent activities |
| **Equipment** | Core equipment inventory management |
| **Transactions** | Incoming and outgoing equipment transactions |
| **Borrowings** | Equipment lending and return tracking |
| **Maintenances** | Maintenance scheduling and history |
| **Disposals** | Equipment disposal workflow |
| **Master Data** | Brands, Categories, Departments, Locations |
| **Users** | User account management |
| **Reports** | Data reporting and exports |
| **Audit Trail** | System activity logs |
| **Settings** | System configuration |

### Data Models

```
Equipment
├── Brand
├── Category
├── Location
├── Transactions (1:N)
├── Borrowings (1:N)
├── Maintenances (1:N)
└── Disposals (1:N)

User
├── Roles (via Spatie Permission)
├── Created Equipment
├── Created Transactions
└── Activity Logs

Department
├── Transactions
└── Borrowings
```

---

## 🔄 Project Flow

### Equipment Lifecycle Flow

```
1. ACQUISITION
   └── Create equipment record OR
   └── Create incoming transaction → Approve → Complete → Equipment created

2. ACTIVE USE
   ├── Borrowing: Request → Borrow → Return
   ├── Maintenance: Schedule → Start → Complete
   └── Transaction: Transfer between locations/departments

3. DISPOSAL
   └── Request disposal → Approve → Complete → Equipment marked as disposed
```

### Transaction Workflow

```
┌─────────────┐     ┌──────────────┐     ┌─────────────┐
│   PENDING   │ ──► │   APPROVED   │ ──► │  COMPLETED  │
└─────────────┘     └──────────────┘     └─────────────┘
     │                                          │
     │  (Admin/Super Admin approves)            │
     │                                          │
     └── Equipment status updated ◄─────────────┘
```

### Borrowing Workflow

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│  BORROWED   │ ──► │   OVERDUE   │ ──► │  RETURNED   │
└─────────────┘     └─────────────┘     └─────────────┘
       │                  │                    │
       │                  │                    │
       └── Expected date passes ───────────────┘
                                               │
                              Equipment status: Available
```

---

## 🛠️ Technology Stack

| Layer | Technology |
|-------|------------|
| **Backend Framework** | Laravel 12.x |
| **PHP Version** | PHP 8.2+ |
| **Database** | MySQL |
| **Frontend CSS** | Tailwind CSS 4.x |
| **Frontend JS** | Alpine.js 3.x |
| **Authentication** | Laravel Breeze |
| **Authorization** | Spatie Laravel Permission |
| **Activity Logging** | Spatie Laravel Activitylog |
| **Excel Export** | Maatwebsite Excel |
| **PDF Export** | Barryvdh Laravel DomPDF |
| **Build Tool** | Vite |

---

## 📁 Project Structure

```
eims/
├── app/
│   ├── Exports/           # Excel export classes
│   ├── Http/
│   │   └── Controllers/   # Application controllers
│   ├── Models/            # Eloquent models
│   └── Providers/         # Service providers
├── config/                # Configuration files
├── database/
│   ├── factories/         # Model factories
│   ├── migrations/        # Database migrations
│   └── seeders/           # Database seeders
├── public/                # Public assets
├── resources/
│   ├── css/               # Stylesheets
│   ├── js/                # JavaScript files
│   └── views/             # Blade templates
│       ├── layouts/       # Layout templates
│       ├── dashboard.blade.php
│       ├── equipment/
│       ├── transactions/
│       ├── borrowings/
│       ├── maintenances/
│       ├── disposals/
│       ├── users/
│       ├── reports/
│       ├── audit-trail/
│       └── settings/
├── routes/
│   └── web.php            # Web routes
├── storage/               # Application storage
└── tests/                 # Test files
```

---

## 🚀 Installation & Setup

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL Database

### Installation Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd eims
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   
   Edit `.env` file with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=eims_db
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

6. **Run migrations and seeders**
   ```bash
   php artisan migrate --seed
   ```

7. **Build assets**
   ```bash
   npm run build
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

9. **Access the application**
   
   Open your browser and navigate to: `http://localhost:8000`

---

## 👥 User Roles & Permissions

| Role | Permissions |
|------|-------------|
| **Super Admin** | Full system access, user management, settings, all approvals |
| **Admin** | Equipment management, transaction approvals, reports access |
| **Staff** | Create requests, view assigned equipment, limited reports |

---

## 📱 Screenshots

> *Add screenshots of your application here*

- Dashboard
- Equipment List
- Transaction Management
- Borrowing Management
- Reports
- Settings

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 📞 Support

For support and inquiries, please contact the development team.

---

<p align="center">
  <strong>EIMS - Equipment Inventory Management System</strong><br>
  Built with ❤️ using Laravel
</p>
