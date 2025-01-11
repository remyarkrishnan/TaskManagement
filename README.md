# Steps to follow

composer install

npm install

setup .env

php artisan migrate

php artisan serve

npm run dev

php artisan queue:work redis

Take : http://127.0.0.1:8000/

For user registration http://127.0.0.1:8000/register

For User Login : http://127.0.0.1:8000/login

Note : 2 user_types ( user_type = 1// admin user, user_type = 2 // normal user)

Admin user can create, edit, delete tasks. Normal user only can assingn/unassign, change status of the task

