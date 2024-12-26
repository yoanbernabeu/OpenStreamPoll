# Installation

This page details the different methods for installing OpenStreamPoll.

## Prerequisites

Before starting the installation, make sure you have:

- PHP 8.3 or higher
- Composer
- Symfony CLI
- SQLite
- Make
- Docker (recommended)

## 1. Production Deployment with Make (Recommended)

### Quick Steps

```bash
# Clone the repository
git clone https://github.com/yoanbernabeu/OpenStreamPoll.git
cd OpenStreamPoll

# Launch deployment
make deploy
```

### Process Details

The `make deploy` command will automatically:

1. Create a `compose.prod.yaml` file from the template
2. Ask for your domain name
3. Start Docker containers
4. Create and configure the database
5. Create the administrator account

## 2. Manual Production Deployment

If you prefer to control each step:

```bash
# Clone the repository
git clone https://github.com/yoanbernabeu/OpenStreamPoll.git
cd OpenStreamPoll

# Create production configuration
cp compose.yaml compose.prod.yaml

# Configure domain in compose.prod.yaml
# SERVER_NAME=yourdomain.com (use :80 without SSL)

# Start services
docker compose -f compose.prod.yaml up -d

# Create database
docker compose -f compose.prod.yaml exec openstreampoll php bin/console doctrine:database:create

# Run migrations
docker compose -f compose.prod.yaml exec openstreampoll php bin/console doctrine:migrations:migrate --no-interaction

# Create admin user
docker compose -f compose.prod.yaml exec openstreampoll php bin/console app:create-user <username> <password>
```

## 3. Development Installation

For local development with hot-reload:

```bash
# Initial installation
make first-install

# Normal startup
make start

# Useful commands
make stop           # Stop server
make reset-db       # Reset database
make tests         # Run tests
make before-commit # Pre-commit checks
```

## 4. Quick Local Testing with Docker

For rapid testing:

```bash
# Start container
docker run -d -p 80:80 \
  -e SERVER_NAME=:80 \
  -e APP_ENV=prod \
  --name openstreampoll \
  yoanbernabeu/openstreampoll:latest

# Database setup
docker exec openstreampoll php bin/console doctrine:database:create
docker exec openstreampoll php bin/console doctrine:migrations:migrate --no-interaction
docker exec openstreampoll php bin/console app:create-user <username> <password>
```

The application will be accessible at `http://localhost`

## Troubleshooting

### Common Errors

1. **Permission Issues**
   - Check write permissions on the `var/` directory
   - Use `chmod -R 777 var/` if needed

2. **Database Errors**
   - Verify SQLite is installed
   - Ensure database directory is writable

3. **Docker Container Issues**
   - Check logs with `docker compose logs`
   - Ensure ports are not already in use

### Support

If you encounter problems:

1. Check [GitHub issues](https://github.com/yoanbernabeu/OpenStreamPoll/issues)
2. Create a new issue if needed
3. For security concerns, contact the maintainer directly
