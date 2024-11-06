# Nurse Project  🏥
#####  Participants:

- Eric Baena Canto
- Guillem Sánchez Oliveras
- Victor Manuel Lucumi Grueso
- Joel Mesas Hontoria

## Description

The propouse of the project is to manage and controll a group of nurses from a hospital. We manage their information and save it to follow up about their updates. In this project we apply a CRUD for the doctors where you can **create**, **read**, **update** and **delete** them. Also, we work with a cloud database where we save all the information.

## Installation
### :exclamation: Pre requeriments :exclamation:
***Make sure you have the following tools installed:***

<img width="15" src="https://skillicons.dev/icons?i=symfony"/> **Symfony :arrow_right: 7.1.5** <br>
**Composer :arrow_right: 2.7.9** <br>
<img width="15" src="https://skillicons.dev/icons?i=php"/> **PHP :arrow_right: 8.1** <br>
<img width="15" src="https://skillicons.dev/icons?i=git"/> **Git** <br> <br>
 
### :rocket: Installation steps :rocket:
- **Clone the repository**

```bash
git clone https://github.com/EricB-59/NurseProject_M12
cd NurseProject_M12
```

<br>

- **Install dependencies**

```bash
composer install
```

<br>

- **Configure enveiroment variables**

Create a copy of the `.env` file and rename it `.env.local`. Then, edit the variables based on your local settings (such as database access).

```bash
cp .env .env.local
```

<br>

- **Configure database**
Make sure the database connection details in `.env.local` are correct and then create the database:

```bash
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
```

<br>

 -  **Throw the development server**
If you had installed CLI of Symfony, you can throw the server with:

```bash
symfony serve
```

<br>

- **Access the project**
Open the browser and search `http://localhost:8000`

<br>

***Additional comands***
- Execute migrations: `php bin/console doctrine:migrations:migrate`
- Install assets: `php bin/console assets:install`
