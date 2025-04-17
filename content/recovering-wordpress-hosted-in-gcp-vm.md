---
title: "Recovering a WordPress Site from a Dead GCP VM"
date: 2025-04-17
author: "Your Name"
categories: ["WordPress", "DevOps", "GCP", "Recovery"]
tags: ["wordpress", "gcp", "vm", "database", "recovery", "mysql"]
description: "A step-by-step guide on how to recover a WordPress site from an inaccessible Google Cloud Platform VM using disk cloning and database recovery techniques."
draft: false
---

# Recovering a WordPress Site from a Dead GCP VM

We've all been there - a production VM suddenly becomes inaccessible, and with it, an entire WordPress site disappears. In this post, I'll walk through a recent recovery process I had to perform when a client's WordPress site went offline due to VM issues on Google Cloud Platform.

## The Problem

My client reached out with this urgent issue:

> I need help to recover and restore a WordPress website hosted on a Google Cloud Platform VM.
>
> Context:
> The original VM hosting the WordPress site is inaccessible via SSH — the issue appears to be related to corrupted SSH metadata, broken keys, or instance-level misconfiguration.
>
> Console and serial access have also failed, and the VM cannot be accessed or repaired easily in its current state.
>
> What I've Already Done:
> I have cloned the boot disk from the original VM and attached it to a new rescue VM.
>
> I've confirmed that the full WordPress directory is intact — including:
>
> wp-config.php
>
> /wp-content/ (themes, plugins, media)
>
> I can browse the file system and have full access to the cloned disk.
>
> The WordPress database (MySQL) is also present on the cloned disk at /var/lib/mysql/wordpress, but it may require recovery.
>
> The current environment is running MySQL 8.0, but the original disk was on MySQL 5.7, and it was not cleanly shut down — which has created compatibility issues with redo logs.
>
> What I Need:
> Restore the full WordPress site to a new clean GCP VM, using the cloned disk data.
>
> Dump and import the existing MySQL wordpress database safely, likely by:
>
> Spinning up a temporary MySQL 5.7 environment
>
> Exporting the DB from the cloned disk
>
> Importing it into the new server (which can be MySQL 8+)
>
> Ensure the site works, loads as expected, and I can log into wp-admin
>
> Ensure I have SSH access and a working LAMP/LEMP environment
>
> Possibly upgrade PHP or install extensions if needed
>
> Set up Let's Encrypt SSL again if needed (optional but ideal)
>
> Platform details:
> GCP VM, Debian/Ubuntu (standard WordPress image)
>
> WordPress + MySQL (no Cloud SQL)
>
> Apache2, PHP (version may need upgrade)

## The Solution

Here was the solution that we came up with:

> ## Recovery Plan
>
> Let's break this down into the following steps:
>
> 1. Mount and access the cloned disk data
> 2. Recover the MySQL database
> 3. Set up a new VM with proper LAMP stack
> 4. Restore WordPress files
> 5. Configure WordPress
> 6. Set up SSL with Let's Encrypt
>
> ### Step 1: Mount and Access the Cloned Disk
>
> First, let's make sure the cloned disk is properly mounted on your rescue VM:
>
> ```bash
> # List available disks
> sudo lsblk
>
> # Create a mount point if not already done
> sudo mkdir -p /mnt/cloned-disk
>
> # Mount the cloned disk (assuming it's sdb1 - adjust as needed)
> sudo mount /dev/sdb1 /mnt/cloned-disk
>
> # Verify the WordPress files are accessible
> ls -la /mnt/cloned-disk/var/www/html
> ```
>
> ### Step 2: Recover the MySQL Database
>
> Since you're dealing with MySQL version compatibility issues (5.7 to 8.0), let's use a temporary MySQL 5.7 container to recover the database:
>
> ```bash
> # Install Docker if not already installed
> sudo apt-get update
> sudo apt-get install -y docker.io
>
> # Run a MySQL 5.7 container with the cloned MySQL data directory mounted
> sudo docker run --name mysql57recovery -e MYSQL_ALLOW_EMPTY_PASSWORD=yes \
>   -v /mnt/cloned-disk/var/lib/mysql:/var/lib/mysql \
>   -d mysql:5.7
>
> # Wait for the container to start and recover the database
> sleep 10
>
> # Export the WordPress database from the container
> sudo docker exec mysql57recovery mysqldump --no-tablespaces wordpress > wordpress_backup.sql
>
> # Stop and remove the recovery container
> sudo docker stop mysql57recovery
> sudo docker rm mysql57recovery
> ```
>
> ### Step 3: Set Up a New VM with LAMP Stack
>
> Now, let's set up a fresh VM with LAMP:
>
> ```bash
> # Update system
> sudo apt-get update
> sudo apt-get upgrade -y
>
> # Install Apache, MySQL 8, PHP and required extensions
> sudo apt-get install -y apache2 mysql-server php php-mysql php-curl php-gd php-mbstring php-xml php-xmlrpc php-soap php-intl php-zip
>
> # Start and enable services
> sudo systemctl start apache2
> sudo systemctl enable apache2
> sudo systemctl start mysql
> sudo systemctl enable mysql
>
> # Secure MySQL installation
> sudo mysql_secure_installation
>
> # Create WordPress database and user
> sudo mysql -e "CREATE DATABASE wordpress;"
> sudo mysql -e "CREATE USER 'wpuser'@'localhost' IDENTIFIED BY 'your_strong_password';"
> sudo mysql -e "GRANT ALL PRIVILEGES ON wordpress.* TO 'wpuser'@'localhost';"
> sudo mysql -e "FLUSH PRIVILEGES;"
>
> # Import the WordPress database
> sudo mysql wordpress < wordpress_backup.sql
> ```
>
> ### Step 4: Restore WordPress Files
>
> Copy the WordPress files from the cloned disk to the new web server:
>
> ```bash
> # Create web directory if it doesn't exist
> sudo mkdir -p /var/www/html
>
> # Copy WordPress files
> sudo cp -r /mnt/cloned-disk/var/www/html/* /var/www/html/
>
> # Fix permissions
> sudo chown -R www-data:www-data /var/www/html/
> sudo find /var/www/html/ -type d -exec chmod 755 {} \;
> sudo find /var/www/html/ -type f -exec chmod 644 {} \;
> ```
>
> ### Step 5: Configure WordPress
>
> Update the wp-config.php file to match the new database credentials:
>
> ```bash
> # Backup the original config
> sudo cp /var/www/html/wp-config.php /var/www/html/wp-config.php.bak
>
> # Update database credentials in wp-config.php
> sudo sed -i "s/define('DB_USER', '.*')/define('DB_USER', 'wpuser')/" /var/www/html/wp-config.php
> sudo sed -i "s/define('DB_PASSWORD', '.*')/define('DB_PASSWORD', 'your_strong_password')/" /var/www/html/wp-config.php
> ```
>
> ### Step 6: Set Up SSL with Let's Encrypt
>
> Install and configure Let's Encrypt:
>
> ```bash
> # Install Certbot
> sudo apt-get install -y certbot python3-certbot-apache
>
> # Get SSL certificate (replace example.com with your domain)
> sudo certbot --apache -d example.com -d www.example.com
> ```
>
> ## Additional Considerations
>
> 1. **Update WordPress Site URL**: If your domain has changed, you'll need to update the site URL in the database:
>
> ```bash
> sudo mysql -e "USE wordpress; UPDATE wp_options SET option_value = 'https://yournewdomain.com' WHERE option_name = 'siteurl' OR option_name = 'home';"
> ```
>
> 2. **Check PHP Version**: Ensure your PHP version is compatible with your WordPress installation:
>
> ```bash
> php -v
> ```
>
> 3. **Test WordPress Admin Access**: Visit yourdomain.com/wp-admin to ensure you can log in.
>
> 4. **Database Configuration Check**: Make sure the database name in wp-config.php matches what you've set up:
>
> ```bash
> grep DB_NAME /var/www/html/wp-config.php
> ```


## Key Takeaways

When recovering a WordPress site from a failed VM, remember these critical steps:

1. **Always create disk snapshots/clones** before attempting any recovery work
2. **Handle database version compatibility** carefully - especially when dealing with unclean shutdowns
3. **Use Docker containers** for temporary recovery environments when needed
4. **Check and update WordPress config files** to match the new environment
5. **Test functionality thoroughly** before considering the recovery complete

By following this systematic approach, you can recover a WordPress site even when the original VM is completely inaccessible. While stressful, these situations are recoverable with the right steps and a bit of patience.

Have you ever had to recover a WordPress site from a dead VM? Share your experiences in the comments!