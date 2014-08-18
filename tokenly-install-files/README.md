#Tokenly Installation Instructions

Steps to install system:

* Download source, extract to desired directory. The "www" folder should be your public web directory, with data & scripts folders etc. being one level up.
* Copy files from "tokenly-install-files" into your base installation directory (add *conf* folder and merge *www* folder)
* Fill out information (database credentials, installation path) in *conf/config.php* and *conf/api.php*
* Import *data/tokenly-clean.sql* into your newly created database
* In your web browser, navigate to the "install" folder that you copied from *tokenly-install-files*. Complete the form to register website and root user in the database.
* After installation script runs, delete the "install" folder from your public web directory.
* All done!



