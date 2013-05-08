Conserva - Console tool for database backups
========================================

Console utility that allows you to save your database dumps. Many settings to 
configure. There are many different storage purposes (MySQL, PostgreSQL, Redis, 
Files) 

site: [conserva-backup.org](http://conserva-backup.org)

Requirements
-------------------------------

Conserva requires a PHP 5 interpreter (v5.3+)

How to use with config
-------------------------------

1. Download the latest version
```shell
    $ wget http://conserva-backup.org/conserva
```

2. Set executable
```shell
    $ chmod +x conserva
```

3. Run
```shell
    $ ./conserva create config
```
    To get the default config. In the current folder will be stored on the default configuration.

4. Open any config editor for editing.
```shell
    database.host     = 'localhost'
    database.username = 'root'
    database.password = '1'
```
    `database.*` - keys to access the database
```shell
    save_last = 30
```
    `save_last` - number of the last database dump (If 30, then the save 31 dump, dump the oldest will be removed)
```shell
    own_folder_for_db = true
```
    `own_folder_for_db` - When enabled, each database will be stored in a folder with the name of:
```shell
    ; run_folder /
    ;   / database_name1 /
    ;       / database_name1-2013040412.tar.gz
    ;       / database_name1-2013040416.tar.gz
    ;       / database_name1-2013040420.tar.gz
    ;       / ...
    ;   / database_name2 /
    ;       / database_name2-2013040412.tar.gz
    ;       / database_name2-2013040416.tar.gz
    ;       / database_name2-2013040420.tar.gz
    ;       / ...
```
```shell
    folder_prefix = db
```
    `folder_prefix` - option allows you to specify the name of the folder inside database directory (useful if the dump site, it is not just dump the database, but, for example, the dump dump redis DB + DB + Dump scripts)
```shell
    ; run_folder /
    ;   / database_name1 /
    ;       / db /
    ;           / database_name1-2013040412.tar.gz
    ;           / database_name1-2013040416.tar.gz
    ;           / database_name1-2013040420.tar.gz
    ;           / ...
    ;   / database_name2 /
    ;       / db /
    ;           / database_name2-2013040412.tar.gz
    ;           / database_name2-2013040416.tar.gz
    ;           / database_name2-2013040420.tar.gz
    ;           / ...
```
```shell
    backup_folder = .
```
    `backup_folder` - folder, where backups will saved

5. Add a cron job
```shell
    $ ./conserva mysql --config=/path_to/config.ini
```
