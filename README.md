For installation
- Change your DB_ROOT_PASSWORD, APP_PORT and MYSQL_PORT from .env file
- If you change anything on .env file you must change moodle400/config.php file with updated APP_PORT, DB_ROOT_PASSWORD
- Run ```docker compose up -d ```
- Change the permission of newly created moodledata directory
```
sudo chmod 0777 moodledata/
```