# Enhanced Features - Diva Ceramica

## New Features Added

### 1. Role-Based Access Control

#### Roles:
- **Admin**: Full access to all features, can view all clients from all reps
- **Rep (Sales Representative)**: Can only manage their own clients
- **Facturation**: Can manage invoices and view client data

#### Default Users (created by seeder):
- **Admin**: admin@divaceramica.com / password123
- **Rep**: rep@divaceramica.com / password123  
- **Facturation**: facturation@divaceramica.com / password123

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

### 4. Invoice Management System

#### Features:
- **Invoice Creation**: Add invoices with images
- **Status Tracking**: Draft, sent, paid, overdue, cancelled
- **Client Association**: Link invoices to specific clients
- **Image Upload**: Upload invoice images (max 10MB)
- **Multi-currency Support**: MAD, EUR, USD

#### Invoice Fields:
- Invoice number (unique)
- Invoice date
- Amount and currency
- Status
- Description
- Invoice image
- Client association

### 5. Enhanced Analytics

#### For Reps:
- Personal client analytics
- Source tracking
- City distribution
- Status breakdown

#### For Admins:
- Company-wide analytics
- Rep performance comparison
- Revenue tracking
- Client acquisition trends

## Database Schema Changes

### New Tables:
- `roles` - User roles
- `user_roles` - Many-to-many relationship between users and roles
- `invoices` - Invoice management

### Enhanced Tables:
- `clients` - Added 12 new fields for detailed client information

## Security Features

### Role-Based Access:
- Middleware protection for different roles
- Users can only access their own data (except admins)
- Invoice access based on role and ownership

### Data Protection:
- CSRF protection on all forms
- Input validation and sanitization
- File upload restrictions
- SQL injection prevention

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
- `GET /admin/dashboard` - Admin overview
- `GET /admin/clients` - All clients with filters
- `GET /admin/rep-performance` - Rep statistics

### Invoice Routes:
- `GET /invoices` - List invoices
- `POST /invoices` - Create invoice
- `GET /invoices/{id}` - View invoice
- `PUT /invoices/{id}` - Update invoice
- `DELETE /invoices/{id}` - Delete invoice

### Client Routes (Enhanced):
- `GET /clients` - List user's clients with filters
- `POST /clients` - Create detailed client
- `DELETE /clients/{id}` - Delete client

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
