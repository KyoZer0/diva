# Enhanced Features - Diva Ceramica CRM

## Overview
Diva Ceramica is a modern, fully-featured Customer Relationship Management (CRM) system built with Laravel and Tailwind CSS. It provides powerful tools for managing clients, tracking sales activities, and analyzing business performance.

## Features at a Glance

### 🎨 Modern UI/UX
- Beautiful, responsive design with gradient accents
- Smooth animations and transitions
- Mobile-friendly interface
- Dark mode support in components
- Interactive data visualizations

### 1. Role-Based Access Control

#### Roles:
- **Admin**: Full access to all features, can view all clients from all reps, and access system-wide analytics
- **Rep (Sales Representative)**: Can manage their own clients, track activities, and view personal analytics

#### Default Users (created by seeder):
- **Admins**: 
  - mery@divaceramica.com / password123
  - mlhlou@divaceramica.com / password123
  - it@divaceramica.com / password123
- **Reps**: 
  - sekkat@divaceramica.com / password123
  - khalid@divaceramica.com / password123
  - yousef@divaceramica.com / password123
  - yassir@divaceramica.com / password123
  - hatim@divaceramica.com / password123
  - oumaima@divaceramica.com / password123

### 2. Enhanced Client Management

#### New Client Fields:
- **Client Type**: Individual or Company
- **Company Information**: Company name, contact person (for companies)
- **Detailed Contact**: Separate phone and email fields
- **Complete Address**: Address, city, postal code, country
- **Business Information**: Status (lead, prospect, customer, inactive), budget range
- **Tracking**: Last contact date, detailed notes

#### Client Status System:
- **Lead**: New potential client
- **Prospect**: Qualified lead showing interest
- **Customer**: Active paying client
- **Inactive**: No longer active

### 3. Admin Dashboard

#### Features:
- **Overview Statistics**: Total clients, reps, invoices, revenue
- **All Clients View**: See clients from all reps with filtering
- **Rep Performance**: Individual rep statistics and performance
- **Quick Actions**: Easy navigation to key features

#### Filtering Options:
- Filter by rep
- Filter by client status
- Filter by client type (individual/company)

### 4. Advanced Search & Filtering

#### Features:
- **Multi-criteria Search**: Search by name, email, phone, company, city
- **Filter Options**: 
  - Status (visited, purchased, follow_up, prospect, inactive)
  - Client type (particulier, professionnel)
  - City
  - Source
- **Real-time Filtering**: Instant results as you filter
- **Reset Filters**: Quick reset to default view

### 5. Data Export

#### CSV Export:
- Export all client data to CSV
- Excel-compatible formatting
- UTF-8 BOM for international characters
- Includes all client fields and metadata
- Timestamped filenames

### 6. Enhanced Client Details Page

#### Features:
- **Beautiful Header**: Gradient background with client avatar
- **Quick Actions**: Call, Email, WhatsApp buttons
- **Stats Dashboard**: Key metrics at a glance
- **Contact Information**: Organized contact details with icons
- **Products Interest**: Visual display of interested products
- **Notes Section**: Rich text notes with highlighting
- **Activity Timeline**: Visual timeline of client interactions
- **Business Details**: Advisor info, quote requests, etc.
- **Quick Summary Card**: Essential info in sidebar

### 7. Enhanced Analytics Dashboard

#### For Reps:
- **Personal Analytics**: 
  - Total clients count
  - New clients (30 days)
  - Clients with quotes
- **Visual Charts**:
  - Sources of acquisition with percentages
  - Products interest analysis
  - Top cities distribution
  - Client type breakdown
- **Interactive Design**: Animated progress bars and cards

#### For Admins:
- **Company-wide Analytics**:
  - All metrics from rep view
  - Rep performance comparison
  - Client acquisition trends
  - Status distribution
- **Rep Performance Cards**:
  - Individual rep statistics
  - Recent activity tracking
  - Comparative analysis

## Database Schema

### Tables:
- `users` - System users (admins and reps)
- `roles` - User roles
- `user_roles` - Many-to-many relationship between users and roles
- `clients` - Complete client information with extensive fields

### Client Fields:
- Basic: full_name, client_type, company_name
- Contact: phone, email, city
- Business: source, products, conseiller, devis_demande
- Tracking: status, notes, last_contact_date
- Metadata: created_at, updated_at

## Security Features

### Role-Based Access:
- Middleware protection for different roles
- Reps can only access their own clients
- Admins have full system access
- Route protection with role checking

### Data Protection:
- CSRF protection on all forms
- Comprehensive input validation
- SQL injection prevention via Eloquent ORM
- Secure password hashing
- XSS protection

## File Structure

```
diva-ceramica/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdminController.php
│   │   │   ├── InvoiceController.php
│   │   │   └── ClientController.php (enhanced)
│   │   └── Middleware/
│   │       ├── CheckRole.php
│   │       └── CheckAdminOrOwner.php
│   ├── Models/
│   │   ├── Role.php
│   │   ├── Invoice.php
│   │   ├── Client.php (enhanced)
│   │   └── User.php (enhanced)
│   └── Policies/
│       └── ClientPolicy.php
├── database/
│   ├── migrations/
│   │   ├── create_roles_table.php
│   │   ├── enhance_clients_table.php
│   │   └── create_invoices_table.php
│   └── seeders/
│       └── RoleSeeder.php
├── resources/views/
│   ├── admin/
│   │   ├── dashboard.blade.php
│   │   └── clients.blade.php
│   ├── invoices/
│   │   ├── index.blade.php
│   │   └── create.blade.php
│   └── clients/
│       └── index.blade.php (enhanced)
└── storage/app/public/invoices/ (for invoice images)
```

## Setup Instructions

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Run Seeders
```bash
php artisan db:seed
```

### 3. Create Storage Link
```bash
php artisan storage:link
```

### 4. Set Permissions (Linux/Mac)
```bash
chmod -R 755 storage/
chmod -R 755 public/storage/
```

## Usage Examples

### Admin Workflow:
1. Login as admin@divaceramica.com
2. View admin dashboard for overview
3. Check all clients from all reps
4. Monitor rep performance
5. Manage invoices

### Rep Workflow:
1. Login as rep@divaceramica.com
2. Add detailed client information
3. Track client status and preferences
4. View personal analytics

### Facturation Workflow:
1. Login as facturation@divaceramica.com
2. Create invoices for clients
3. Upload invoice images
4. Track payment status
5. Generate reports

## API Endpoints

### Admin Routes:
- `GET /admin/dashboard` - Admin overview with stats
- `GET /admin/clients` - All clients with advanced filters
- `GET /admin/rep-performance` - Rep performance metrics
- `GET /admin/analytics` - Company-wide analytics

### Client Routes:
- `GET /clients` - List user's clients with advanced search & filters
- `GET /clients/export` - Export clients to CSV
- `GET /clients/create` - Create new client form
- `POST /clients` - Store new client
- `GET /clients/{id}` - View client details
- `DELETE /clients/{id}` - Delete client

### Analytics Routes:
- `GET /analytics` - Rep analytics dashboard (for reps)
- `GET /admin/analytics` - Admin analytics dashboard

## Future Enhancements

### Potential Additions:
- Email notifications for invoice status changes
- PDF invoice generation
- Advanced reporting and charts
- Client communication history
- Product catalog integration
- Payment tracking
- Automated follow-up reminders
- Export functionality (CSV, PDF)
- Mobile app integration

