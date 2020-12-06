## Social network for sharing photos

### Used stack:

- no frameworks and libs
- PHP/MySQL for backend
- HTML/CSS/JS for frontend
- Elasticmail for email notifications

### Features:
- Registration and authorization
- Capture image using webcam
- Upload picture from disk
- Draggable masks
- Comments
- Likes
- Pagination
- Email notifications
- Update user info
- Avatars
- Password restoration
- Hide/delele own photos
- Backend logger

Steps to run (apache webserver):
1. Clone repo
2. Soft link repo to *htdocs* folder
3. Register Elasticmail account
4. Add file *config/keys.php* with this lines:
```php
<?php
$ELASTIC_EMAIL_API_KEY = 'YOUR_KEY';
?>
```
5. Start mysql database and webserver
6. Go to *localhost/camagru*

### How it looks:

![Camagru screenshot 01](/screenshots/Screenshot_01.png)
![Camagru screenshot 02](/screenshots/Screenshot_02.png)
![Camagru screenshot 03](/screenshots/Screenshot_03.png)
![Camagru screenshot 04](/screenshots/Screenshot_04.png)
![Camagru screenshot 05](/screenshots/Screenshot_05.png)
![Camagru screenshot 06](/screenshots/Screenshot_06.png)

