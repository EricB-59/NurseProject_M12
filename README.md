# Nurse Project  🏥
#####  Participants:

- Eric Baena Canto :man:
- Guillem Sánchez Oliveras :man:
- Victor Manuel Lucumi Grueso :man:
- Joel Mesas Hontoria :man:

## Description 📰

The propouse of the project is to manage and controll a group of nurses from a hospital. We manage their information and save it to follow up about their updates. In this project we apply a CRUD for the doctors where you can **create**, **read**, **update** and **delete** them. Also, we work with a cloud database where we save all the information.

## Installation 🖥️
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

<br>

## API examples
### :ambulance: C R U D :ambulance:
To execute the following API's we are going to use PostMan to send the requests.

- **Create** :pencil:<br>

![67e0b710-8bdd-440a-8273-de7f377e035c](https://github.com/user-attachments/assets/15d16f2d-e43a-400d-83e5-f916da657ccd)

- **Read** 📖<br>

![1d31da71-0f44-47d0-87ea-0e2182a4f82c](https://github.com/user-attachments/assets/681310e5-1f40-4a74-8e26-bce42e4fbae2)

###### GetAll
![75221843-6238-4e59-9e5c-abad89429ad6](https://github.com/user-attachments/assets/4d1b3346-fe5e-4ee2-a026-cac3b909603d)
###### Login
![19e0c664-d89c-4433-91e3-fd5aa12c62dd](https://github.com/user-attachments/assets/94928626-73a2-4d13-b645-61e725f22835)
###### FindName
![86a05349-6ec9-4a35-90ee-1e5982322be8](https://github.com/user-attachments/assets/dc303416-b0c8-4384-b918-649e3dffc68a)

- **Update** :recycle:<br>

![2e0c96f0-5cbc-4810-8241-63951539fdd9](https://github.com/user-attachments/assets/8a5761d1-2cc6-4750-83c7-2727e1077fcf)

- **Delete** ✂️:<br>

![d7c4853d-a074-42c4-848b-ae15abc9551e](https://github.com/user-attachments/assets/2d7b29f3-1292-4947-9980-9e9d96459a1b)





