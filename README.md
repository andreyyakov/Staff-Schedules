# Requirements 

1. composer install
2. configure the .env and .env.testing files
3. php artisan migrate:refresh --seed

### Requirements for Unit test
This is for create testing environment and test table

1. php artisan config:cache --env=testing
2. php artisan migrate:refresh --seed
__Run Unit test - class method run example__
3. ./vendor/bin/phpunit --filter {testGetStaffInfoMethodResponseStaff}

>>>
If you want to switch to the local environment run the  __php artisan config:cache__ 

>>>
I created the class ScheduledController that contains all the logic code, I also created a UI so that you can easily see the results. And also created the SchedulesTest class, where tests for SchedulesController were written (You can see it in tests/Unit/SchedulesTest.php)
