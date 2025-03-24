
# Development Environment – GEONERD

This project is optimized to run quickly on **WSL/Linux** using Docker,  
but it remains **100% compatible with Windows and macOS**, as long as Docker Desktop is installed.

---

## Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop)
- [Git](https://git-scm.com/)
- [Node.js](https://nodejs.org/)

---

## Running the project

1. Clone the repo:

```bash
git clone git@github.com:nathkeuss/GEONERD.git
cd your-project-folder
```

2. Start the containers:

```bash
docker compose up -d
```

3. Access the application:

- Symfony: [http://localhost:8000](http://localhost:8000)
- PhpMyAdmin: [http://localhost:8080](http://localhost:8080)

---

## Important to know

> The project has been migrated to a Linux (WSL) environment to improve Symfony performance locally.  
> If you are developing on Windows, Docker Desktop works perfectly **without needing WSL**,  
> but performance may be slightly lower.

---

## Development with Commitizen

The project uses [Commitizen](https://commitizen-tools.github.io/commitizen/) to standardize commit messages.

Once Node is installed, run:

```bash
npm install
npx cz
```

This will allow you to create clean and consistent commit messages according to the project’s conventions.

---

## Useful Commands

```bash
# Start the containers
docker compose up -d

# Stop the containers
docker compose down

# View the logs in real-time
docker compose logs -f

# Rebuild if necessary
docker compose build --no-cache
```

---

## GitHub Connection via SSH (recommended)

If you don't want to enter your password every time you `git push`, configure an SSH key:

```bash
ssh-keygen -t ed25519 -C "your@email.com"
eval "$(ssh-agent -s)"
ssh-add ~/.ssh/id_ed25519
cat ~/.ssh/id_ed25519.pub
```

Then paste the key into [https://github.com/settings/keys](https://github.com/settings/keys)

---

## Node & NVM Setup (WSL/Linux)

```bash
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash
source ~/.bashrc
nvm install --lts
nvm use --lts
```

---
