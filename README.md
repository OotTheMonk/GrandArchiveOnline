# GrandArchiveOnline
<p align="center">
## New docker way to run Clarent.

### Prerequisites:
 - Docker

Easier to do on *nix OS than on MS OS.

First clone the repo:
```
git clone https://github.com/OotTheMonk/GrandArchiveOnline.git
```
Into the directory we go:
```
cd GrandArchiveOnline
```
Run the script to do the setup and start the docker containers
```
bash start.sh
```

- NOTE: If you're on windows, the newline characters might mess up this script. It's only two lines so you can just run them manually in Windows Powershell or git Bash:
```
cp -n HostFiles/RedirectorTemplate.php HostFiles/Redirector.php
docker compose up -d
```

You'll then be able to access your local dev at http://localhost:8080/GrandArchiveOnline/MainMenu.php , play a game or two against yourself and have a play about.

The containers are running in detached (background) mode. To stop them:
```
bash stop.sh
```
or
```
docker compose down
```

### What do the scripts do?
- As noted in the [Quickstart Guide](https://docs.google.com/document/d/1qVlTrst58iZ_6xD9PkxIgZUiSKzV-S4eTJmK32qzaP0/edit) below you need a unique Redirector file setup. You can customise it, it's required, but it's not checked into the repo. So the script makes a copy of the Redirector file before launching the containers.
- The stop script just stops the containers by calling `docker compose down`
- After the first time making the Redirector file, you can bring the docker containers up and down using `docker compose up` and `docker compose down` and whatever else you're used to.
## Disclaimer

To Be Added