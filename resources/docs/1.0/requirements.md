# Requirements

---

- [PHP Version](#php-version)
- [MySQL](#mysql)
- [PHP Extensions](#php-extensions)
- [PHP Functions](#php-functions)
- [PHP Memory Limit](#php-memory-limit)
- [Browser Support](#browser-support)
- [Cron Job](#cronjob)
- [Domain Name](#domain-name)

<a name="php-version"></a>
## PHP Version
Adamasanya minimum required PHP version: >=8.2

<a name="mysql"></a>
## MySQL
Adamasanya requires MySQL version: >= 5.6 <br>
Recommended MySQL version: >=5.7.

<a name="php-extensions"></a>
## PHP Extensions

- bcmath
- ctype
- mbstring
- openssl
- pdo
- tokenizer
- cURL
- iconv
- gd
- fileinfo
- dom

<h4>Recommended PHP Extensions</h4>
The extensions listed below are not required during installation, however, if you want to use specific features after installation, you will need to enable them, for example the "zip" extension gives you the ability to perform 1 click update and apply patches.

- zip
- imap

<a name="php-functions"></a>
## PHP Functions
The below functions are listed as required PHP functions because on some PHP builds they are disabled by default, if that's the case, you should consult with your hosting provider to enable them or if you are managing the server, perform a research on how to enable PHP functions based on your server environment.

- symlink
- tmpfile
- ignore_user_abort
- fpassthru
- highlight_file

<a name="php-memory-limit"></a>
## PHP Memory Limit

Adamasanya requires at least 128 MB PHP memory limit.
<a name="browser-support"></a>
## Browser Support

Adamasanya supports the most recent versions of the following browsers:

- Google Chrome
- Apple Safari
- Microsoft Edge
- Mozilla Firefox

<a name="cronjob"></a>
## Cronjob

cron is a Linux utility that schedules a command or script on your server to run automatically at a specified time and date. A cron job is the scheduled task itself. Cron jobs can be very useful to automate repetitive tasks.

Your web server must support configuring cron jobs, in order, Adamasanya features to work properly, after installation, please make sure to check the cron job guide to get familiar on how to configure the cron job required by the application.

See detailed cron job setup guide.

<a name="domain-name"></a>
## Domain Name

Your server where you will be installing Adamasanya must have domain name, you can't use the server IP-address to access your Adamasanya installation, it must be accessed via a domain name.