QwikClock
=========

QwikClock is an employee time tracking app.

https://netsyms.biz/apps/qwikclock

There are two applications with the name QwikClock.  This one integrates with 
the Business Apps, the other one (QwikClock Enterprise) is a standalone system. 
Eventually, this application will have all the features of QwikClock Enterprise, 
and Enterprise will be discontinued.

Features
--------

**Shift Assignment**  
Ensure 100% shift coverage and prevent early clock-ins by assigning shifts to employees.

**Powerful Reports**  
Generate reports in as little as three clicks. Filter by employee and date range so you only see what you need. Import into LibreOffice, Excel, and most other software.

**Server Time**  
QwikClock synchronizes to the server's clock so your punches are always accurate.

**Mobile-ready**  
Employees can clock in and out from a remote job site with their phones, or check their work schedule from home.


Installing
----------

0. Follow the installation directions for [AccountHub](https://source.netsyms.com/Business/AccountHub), then download this app somewhere.
1. Copy `settings.template.php` to `settings.php`
2. Import `database.sql` into your database server
3. Edit `settings.php` and fill in your DB info
4. Set the location of the AccountHub API in `settings.php` (see "PORTAL_API") and enter an API key ("PORTAL_KEY")
5. Set the location of the AccountHub home page ("PORTAL_URL")
6. Set the URL of this app ("URL")
7. Run `composer install` (or `composer.phar install`) to install dependency libraries.