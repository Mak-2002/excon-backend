# Excon backend

This Laravel backend project serves as the backend API for a property sale, purchase, and rental app. It provides the necessary functionalities to manage property listings, user authentication, and property-related transactions.

## Table of Contents
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Database Migration and Seeding](#database-migration-and-seeding)
- [Authentication and Authorization](#authentication-and-authorization)
- [Contributing](#contributing)
- [Contact Information](#contact-information)

## Installation
1. Clone the repository: `git clone https://github.com/Mak-2002/excon-backend`
2. Set virtual server and database connection server specific to your operating system, such as [XAMPP](https://www.apachefriends.org/download.html) for Windows.
3. Install dependencies: `composer install`
4. Copy the `.env.example` file to `.env` and configure the database connection.
5. Generate the application key: `php artisan key:generate`
6. Run the database migrations: `php artisan migrate`
7. Start the development server: `php artisan serve`

## Configuration
- Configure the database connection in the `.env` file.
- OOP service server is not available in Syria, so you might use VPN for that.

## Usage
- The backend API provides endpoints for managing experts listing, reserving an appointment with an expert, and chating with him.

## Database Migration and Seeding
- To run the database migrations, use the command: `php artisan migrate`
- To seed the database with initial data, use the command: `php artisan db:seed`

## Authentication and Authorization
- User authentication implementation lacks comprehensive security measures, as it only mandates user login without additional layers of protection.

## Contributing
- Contributions, bug reports, and feature requests are welcome.

## Contact Information
- For any questions or inquiries, please contact Qusai Nasr via email at kossay.ar.en@gmail.com or through GitHub at [github.com/Mak-2002](https://github.com/Mak-2002).
