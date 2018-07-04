# PhpSDGUI

The utility for generating new pages of the admin panel in `Simpla CMS`

#### Requirements
* Web server
* PHP 5.0.0+
* MySQL 4.0+
* Any browser

#### Setup
`Clone this repo to the web server (git clone https://github.com/Mishin870/PhpSDGUI.git)`
`Create new database`
`Import sdgui_backup.sql into your database`
`Change database name, host, user and password in db.php`
`Open index.php in your web browser`

#### Paths for generated filed
api => /api/%Name%.php
itemAdmin => /simpla/%name%Admin.php
itemsAdmin => /simpla/%name%sAdmin.php
itemTemplate => /simpla/design/html/%name%.tpl

#### Tasks
- [x] Generator core
- [x] Api file
- [x] ItemAdmin file
- [x] ItemsAdmin file
- [x] ItemTemplate file
- [ ] ItemsTemplate file
- [ ] Instructions for integrating