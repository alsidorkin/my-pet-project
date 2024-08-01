 Getting Started

To run locally, you'll need a local web server with PHP support. You can use a local Symfony server or Docker.

 Local Setup

1. Clone this repo into your local server webroot (e.g., htdocs).
2. Launch a terminal app and change to the newly cloned folder.
3. Download dependencies with Composer using the following command:

    ```bash
    composer install
    ```

4. To obtain your API keys, follow these steps:

 Get Started with News API

    1. Create a free News API user account (if you don't have one).
    2. Login to the [News API developer center](https://newsapi.org/account).
    3. Copy the API key and add it to `.env` in the line:

        ```env
        NEWS_API_KEY=39**********009
        ```

     Get Started with OpenWeatherMap

    1. Create a free OpenWeatherMap account (if you don't have one).
    2. Login to the [OpenWeatherMap API keys page](https://home.openweathermap.org/api_keys).
    3. Copy the API key and add it to `.env` in the line:

        ```env
        OPENWEATHERMAP_API_KEY=39**********009
        ```

     Get Started with Holiday API

    1. Create a free Holiday API user account (if you don't have one).
    2. Login to the [Holiday API dashboard](https://calendarific.com/account/dashboard).
    3. Copy the API key and add it to `.env` in the line:

        ```env
        CALENDARIFIC_API_KEY=39**********009
        ```

     Get Started with Todoist

    1. Create a free Todoist account (if you don't have one).
    2. Login to the [Todoist developer console](https://developer.todoist.com/appconsole.html).
    3. Click the "New Application" link.
    4. Enter the name of your application.
    5. Enter the redirect URI (your callback URL - for example, `http://localhost:8000/callback.php`).
    6. Agree to the terms and click “Create an application”.
    7. Click the "Create Secret" button.
    8. Copy your Client ID and Secret and save them for future use.
    9. Set up API keys. You need to update your code in the `.env` file.

     ```env
    TODOIST_CLIENT_ID=421************1e
    TODOIST_CLIENT_SECRET=827**********9c07c
     ```
 Docker Setup
To set up the project using Docker, follow these steps:

1. Install Docker and Docker Compose

   Make sure Docker and Docker Compose are installed on your machine. You can download and install them from the [official Docker website](https://www.docker.com/get-started).

2. Clone this repo

   If you haven't already, clone the repository into your local machine:

    ```bash
    git clone <URL of your repository>
    cd <name of your project folder>
    ```

3. Create and configure the `.env` file

   Create a `.env` file in the root directory of your project if it doesn't exist. Insert the necessary environment variables. Example `.env` file:


4. Build Docker images

    Build the Docker images for your project:

    ```bash
    docker-compose build
    ```

5. Start the containers

    Start the containers in the background:

    ```bash
    docker-compose up -d
    ```

6. Run database migrations

    If your project requires database migrations, execute them:

    ```bash
    docker-compose exec app php bin/console doctrine:migrations:migrate
    ```

    Replace `app` with the name of your main application container if it's different.

7. Check the application

    Open your web browser and navigate to [http://localhost:8000](http://localhost:8000) (or another port if configured differently) to ensure the application is running.

8. Stop the containers

    To stop and remove containers, use:

    ```bash
    docker-compose down
    ```

 Additional Information

- Docker Documentation: [Docker Docs](https://docs.docker.com/)
- Docker Compose Documentation: [Docker Compose Docs](https://docs.docker.com/compose/)


Setting Up Cron Jobs

1. Install and Configure Cron

   Make sure `cron` is installed and running on your system:

   - Linux/Unix: Typically installed by default. Check with:

     ```bash
     sudo service cron status
     ```

     Install if not present:

     ```bash
     sudo apt-get install cron
     ```

   - macOS: `cron` is built into the system. Use `crontab` for scheduling tasks.

   - Windows: Use Task Scheduler for similar functionality as `cron`.

2. Access Your Server via SSH

   If you're managing `cron` on a remote server, ensure you have SSH access:

   ```bash
   ssh user@your-server-address


 Comment API

This API allows you to manage comments in the application. You can perform CRUD operations (Create, Read, Update, Delete) on comments.

 Authentication

Authentication is handled using JWT (JSON Web Token). To interact with the protected endpoints, you need to obtain a token by making a POST request to `/api/login_check`.

 Obtain JWT Token

POST `/api/login_check`

Request Body:
json
{
    "username": "your_email@example.com",
    "password": "your_password"
}

Response:
json
{
    "token": "your_jwt_token"
}
Include this token in the Authorization header of your requests using the Bearer scheme.


Endpoints

1. Create a Comment
POST /api/comment

Request Body:

json
{
    "content": "This is a comment content"
}
Response:

json
{
    "id": 1,
    "content": "This is a comment content",
    "author": "your_email@example.com",
    "createdAt": "2024-07-31T12:34:56+00:00"
}

2. Retrieve All Comments

GET /api/comment

Response:

json
[
    {
        "id": 1,
        "content": "This is a comment content",
        "author": "your_email@example.com",
        "createdAt": "2024-07-31T12:34:56+00:00"
    },

]


3. Retrieve a Single Comment

GET /api/comment/{id}

Response:

json
{
    "id": 1,
    "content": "This is a comment content",
    "author": "your_email@example.com",
    "createdAt": "2024-07-31T12:34:56+00:00"
}

4. Update a Comment

PUT /api/comment/{id}

Request Body:

json
{
    "content": "Updated comment content"
}


Response:

json
{
    "id": 1,
    "content": "Updated comment content",
    "author": "your_email@example.com",
    "createdAt": "2024-07-31T12:34:56+00:00"
}

5. Delete a Comment

DELETE /api/comment/{id}

Response:

json
{
    "message": "Comment deleted successfully"
}


Error Handling

All responses include a standard error structure in case of errors.

Error Response Example:

json
{
    "type": "https://tools.ietf.org/html/rfc2616#section-10",
    "title": "An error occurred",
    "status": 400,
    "detail": "Error message here"
}
Notes
Ensure you include the JWT token in the Authorization header for all protected endpoints.
The createdAt field is automatically set to the current date and time when a comment is created.
For security reasons, make sure to handle tokens securely and avoid exposing them in client-side code.


