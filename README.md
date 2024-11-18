# Backend Laravel Developer Challenge: News Aggregator API

   ## The application is containerized using Docker. It can be easily deployed using docker-compose.

   ## Prerequisites
        Ensure you have the following installed on your machine:
            -   Docker
            -   Docker Compose

   ## Clone the Repository and the working directory to 'news-aggregator-api' by running the below   two commands:
        git clone https://github.com/tholscoa/news-aggregator-api.git
        cd news-aggregator-api

   ## Setup the Environment by running the below command:
        cp .env.example .env

   ## Deploy the Application and DB using Docker Compose by Running the command in each steps below:
        Step 1: Build the images:
            docker-compose build
        Step 2: Start the Containers:
            docker-compose up -d
        Step 3: Run migrations:
            docker-compose exec app php artisan migrate
        Step 4: Run Seed:
            docker-compose exec app php artisan db:seed
        Step 5: Generate API documentation:
            docker-compose exec app php artisan l5-swagger:generate 

   ## Accessing the Application
    Once all steps are completed successfully:

    - Application URL: http://localhost:8000
    - API Documentation: http://localhost:8000/api/documentation

