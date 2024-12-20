# OpenStreamPoll

OpenStreamPoll is an open-source live polling platform designed specifically for streamers. It empowers creators to engage their audience with instant polls, real-time results, and seamless integration with popular streaming tools.

---

## Features

- **Instant Poll Creation:** Set up live polls effortlessly with multiple customizable options.
- **Real-Time Results:** Display dynamic updates to keep your audience engaged.
- **OBS Integration:** Designed to work flawlessly with OBS for professional streams.
- **Anti-Cheat Protection:** Ensures fair and authentic voting.
- **Mobile-Friendly Interface:** Optimized for all devices, making participation easy.
- **Docker-Ready Deployment:** Simplifies setup and scalability.

---

## Prerequisites for Local Development

To get started locally, ensure you have the following installed:

- PHP 8.3 or higher
- Composer
- Symfony CLI
- SQLite
- Make
- Docker

---

## Local Development Setup

Follow these steps to run the project locally:

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/yoanbernabeu/OpenStreamPoll.git
   cd OpenStreamPoll
   ```

2. **Initial Installation:**
   ```bash
   make first-install
   ```

3. **Launch the Development Server:**
   ```bash
   make start
   ```
   The application will be accessible at: `http://localhost:8000` (or an alternative port if 8000 is unavailable).

4. **Create an Admin User:**
   ```bash
   symfony console app:create-user <username> <password>
   ```

5. **Access the Admin Interface:**
   Navigate to: `http://localhost:8000/admin`

---

## Production Deployment

> **Note:** Secure your server before deploying this application to production. If you are not familiar with server security best practices, consider using a managed hosting provider.

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/yoanbernabeu/OpenStreamPoll.git
   cd OpenStreamPoll
   ```

2. **Start the Application:**
   ```bash
   make deploy
   ```

---

## Usage Guide

1. **Create a Poll:** Use the admin interface to set up a new poll.
2. **Share the Link:** Distribute the poll's public URL to your audience.
3. **Monitor Results:** View live updates to track audience responses.
4. **Stream Results:** Seamlessly display live results during your stream.

---

## Contributing

Currently, contributions are not being accepted as this is a personal project shared for public use. While PRs are welcome, they may not be actively managed at this time.

---

## License

OpenStreamPoll is open-source and distributed under the [MIT License](LICENSE).