## Customization

Before using this application in a production manner, the views and outbound emails should be customized to represent your company.

These PHP files are parsed with Laravel's Blade Template engine.  For more information about that, see [Blade Templates](https://laravel.com/docs/5.1/blade).

### Views

The HTML presentation logic for the application can be found in the **resources/views** directory.  To see the HTML presented to a user when they are creating an instance for instance, look at **resources/views/instance/create.blade.php**.

StratoSTACK uses the Bootstrap CSS framework and a number of custom stylesheets located in **public/css**.

The application's logo can be found at ****public/img/logo.png****.

If you wish to change the overall site layout, the template can be found at **resources/views/app.blade.php**.

### Outbound Emails

Outbound emails can be found in: **resources/views/emails**

**emailConfirm.blade.php**  
Email confirmation message, sent to a new user when they sign up.

**newinstance.blade.php**  
Sent to users when they create a new instance.  It contains name, IP and password if applicable.

**password.blade.php**  
Password reset email that gives users a link to reset their password when requested.

**password_changed.blade.php**  
When a user changes their password through the Portal, this email is sent to them.

**voucher.blade.php**  
This email notifies someone that a user has issued them a voucher code.

**welcome.blade.php**  
Currently unused.