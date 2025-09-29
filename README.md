# Stock Haqi Ali - Video Streaming Platform

A comprehensive Laravel-based video streaming platform with multi-content support, subscription management, and mobile API integration.

## 🚀 Features

### Core Content Management
- **Movies Management** - Upload, manage, and stream movies with multiple quality options
- **TV Shows & Series** - Complete series management with seasons and episodes
- **Sports Videos** - Sports content with category management
- **Live TV** - Live television streaming with channel management
- **Photos & Audio** - Media library with photo and audio content support

### User Management
- **Multi-tier User System** - Admin, Sub-Admin, and regular users
- **User Authentication** - Login, registration, password reset
- **Social Login** - Google and Facebook integration
- **User Profiles** - Complete profile management with device tracking
- **Device Management** - Track and manage user devices with remote logout

### Subscription & Payment
- **Subscription Plans** - Flexible subscription management
- **Multiple Payment Gateways**:
  - PayPal
  - Stripe
  - Razorpay
  - Paystack
  - PayU Money
  - Instamojo
  - Mollie
  - Flutterwave
  - Paytm
  - Cashfree
  - CoinGate
  - Bank Transfer
- **Coupon System** - Discount codes and promotional offers
- **Transaction Management** - Complete payment tracking and reporting

### Content Features
- **Multi-quality Streaming** - 480p, 720p, 1080p support
- **Subtitle Support** - Multiple subtitle languages
- **Download Options** - Offline content download
- **Watchlist** - Save favorite content for later
- **Recently Watched** - Track viewing history
- **Like/Unlike System** - User engagement features
- **Search & Filter** - Advanced search with genre and language filters
- **AI-Powered Thumbnails** - Google Gemini AI for custom thumbnails

### Admin Features
- **Comprehensive Dashboard** - Analytics and statistics
- **Content Management** - Full CRUD operations for all content types
- **User Management** - User administration and monitoring
- **Settings Management** - Extensive configuration options
- **Email Management** - SMTP configuration and email templates
- **Backup System** - Automated backup functionality
- **SEO Management** - Meta tags, sitemaps, and SEO optimization

### Mobile API
- **RESTful API** - Complete mobile app support
- **Android Integration** - Native Android app compatibility
- **Push Notifications** - OneSignal integration
- **Device Tracking** - Mobile device management

### Technical Features
- **Multi-language Support** - Internationalization ready
- **Responsive Design** - Mobile-first approach
- **SEO Optimized** - Search engine friendly
- **Security** - CSRF protection, input validation
- **Caching** - Performance optimization
- **File Management** - Secure file upload and storage

## 🛠 Technology Stack

### Backend
- **Laravel 10.x** - PHP Framework
- **PHP 8.1+** - Server-side language
- **MySQL** - Database
- **Laravel Sanctum** - API authentication

### Frontend
- **Blade Templates** - Server-side rendering
- **Bootstrap** - CSS framework
- **jQuery** - JavaScript library
- **Vite** - Asset bundling

### Payment Integration
- **PayPal REST API SDK**
- **Stripe PHP SDK**
- **Razorpay PHP SDK**
- **Paystack PHP SDK**
- **PayU Money Integration**
- **Instamojo API**
- **Mollie API**
- **Flutterwave API**
- **Paytm SDK**
- **Cashfree API**
- **CoinGate API**

### Additional Services
- **Google Gemini AI** - Thumbnail generation
- **OneSignal** - Push notifications
- **Google reCAPTCHA** - Security
- **Intervention Image** - Image processing
- **Laravel Excel** - Data export
- **Laravel Backup** - Automated backups

## 📋 Requirements

- PHP >= 8.1
- MySQL >= 5.7
- Composer
- Node.js & NPM
- Web Server (Apache/Nginx)

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/yasirraheel/stock_haqi_ali.git
cd stock_haqi_ali
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Setup
```bash
# Create database
mysql -u root -p
CREATE DATABASE stock_haqi_ali;

# Run migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed
```

### 5. Storage Setup
```bash
php artisan storage:link
chmod -R 755 storage
chmod -R 755 public/upload
```

### 6. Build Assets
```bash
npm run build
```

### 7. Configure Environment Variables
Update your `.env` file with:
```env
APP_NAME="Stock Haqi Ali"
APP_ENV=production
APP_KEY=base64:your_generated_key
APP_DEBUG=false
APP_URL=http://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stock_haqi_ali
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Payment Gateways
PAYPAL_CLIENT_ID=your_paypal_client_id
PAYPAL_CLIENT_SECRET=your_paypal_secret
STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret

# AI Services
GEMINI_API_KEY=your_gemini_api_key

# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
```

## 🔧 Configuration

### Admin Access
- Default admin credentials are set during installation
- Access admin panel at: `http://your-domain.com/admin`

### Content Management
1. **Movies**: Add movies with multiple quality options
2. **Series**: Create series with seasons and episodes
3. **Sports**: Upload sports content with categories
4. **Live TV**: Configure live TV channels
5. **Photos/Audio**: Manage media library

### Payment Setup
1. Configure payment gateways in admin panel
2. Set up subscription plans
3. Configure pricing and billing

### AI Thumbnails
1. Get Gemini API key from Google AI Studio
2. Add to `.env` file
3. Thumbnails will be auto-generated for new content

## 📱 Mobile API

The platform includes a comprehensive REST API for mobile applications:

### Authentication
- `POST /api/v1/login` - User login
- `POST /api/v1/signup` - User registration
- `POST /api/v1/logout` - User logout

### Content
- `POST /api/v1/movies` - Get movies list
- `POST /api/v1/shows` - Get TV shows
- `POST /api/v1/sports` - Get sports content
- `POST /api/v1/livetv` - Get live TV channels

### User Features
- `POST /api/v1/dashboard` - User dashboard
- `POST /api/v1/profile` - User profile
- `POST /api/v1/watchlist_add` - Add to watchlist
- `POST /api/v1/my_watchlist` - Get watchlist

## 🎨 Customization

### Themes
- Modify views in `resources/views/`
- Update CSS in `public/site_assets/css/`
- Customize JavaScript in `public/site_assets/js/`

### Content Types
- Add new content types by extending existing models
- Create new controllers following Laravel conventions
- Update routes in `routes/web.php` and `routes/api.php`

## 🔒 Security

- CSRF protection enabled
- Input validation and sanitization
- SQL injection prevention
- XSS protection
- Secure file uploads
- User authentication and authorization

## 📊 Analytics

The admin dashboard provides:
- User statistics
- Content analytics
- Revenue tracking
- Transaction reports
- Device usage statistics

## 🚀 Deployment

### Production Setup
1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false`
3. Configure proper database credentials
4. Set up SSL certificate
5. Configure web server (Apache/Nginx)
6. Set up cron jobs for scheduled tasks

### Server Requirements
- PHP 8.1+
- MySQL 5.7+
- 2GB+ RAM
- 20GB+ storage
- SSL certificate

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

For support and questions:
- Create an issue on GitHub
- Check the documentation
- Review the code comments

## 🔄 Updates

### Recent Updates
- AI-powered thumbnail generation with Google Gemini
- Enhanced mobile API
- Improved security features
- Better performance optimization
- Updated payment gateways

### Version History
- v1.0.0 - Initial release
- v1.1.0 - Added AI thumbnails
- v1.2.0 - Enhanced mobile support
- v1.3.0 - Security improvements

## 📞 Contact

- **Developer**: Yasir Raheel
- **GitHub**: [@yasirraheel](https://github.com/yasirraheel)
- **Repository**: [stock_haqi_ali](https://github.com/yasirraheel/stock_haqi_ali)

---

**Note**: This is a comprehensive video streaming platform. Make sure to configure all payment gateways and services according to your requirements before going live.
