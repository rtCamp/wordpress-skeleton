# GitHub Actions - WordPress Skeleton
    
## Project short intro
This project is used as a base for setting up CI/CD using GitHub actions for new or existing projects.

## Setup CI/CD
To setup CI/CD in your project follow these steps:

1. Download the required configuration files.

```bash
git clone --depth=1 git@github.com:rtCamp/github-actions-wordpress-skeleton.git
```

2. Update `hosts.yml`
    1. Update branch blocks with required branches and corresponding server details.
    2. Update `ci_script_options` as per project needs.
        1. Setting `vip: true` will enable cloning of mu-plugins.
        2. `wp-version` can be set to `latest` for latest released version. Or it can be pinned to a specified by setting a value like: `5.0.3`.

3. Update `Correct branch filter` in `main.workflow`, set the branches according to the deployment needs.

4. Update `SLACK_CHANNEL` value in `main.workflow`, set it to the channel in which you want build slack notifications.

**Note: If site has not been created with `--public-dir=current` EasyEngine flag, then only steps 5 and 6 are required.**

5. Update nginx webroot of the site to point to `/var/www/htdocs/current` .

6. Move `wp-config.php` inside `htdocs` folder.
```bash
mv /opt/easyengine/example.com/app/wp-config.php /opt/easyengine/example.com/app/htdocs/wp-config.php 
```

## Customize the actions

[GitHub actions library](https://github.com/rtCamp/github-actions-library) has the flexibility of customization to accomodate various scenarios.

To customize any particular file in the action, follow the folder structure of the library inside `.github` folder. File inside `.github` folder will override the default file in the action.

Example: If `deploy.php` needs to be customised, then in the library the file is present in `deploy` folder. So, placing it in location `.github/deploy/deploy.php` will override the one in action.

## FAQs

1. How to configure custom `deploy.php`?
A: You can take a reference of [this deploy.php](https://github.com/rtCamp/github-actions-library/blob/master/deploy/deploy.php) and create similar `deploy.php` with additional configurations as per need and place it in location `.github/deploy/deploy.php`.

2. How to run `composer install` for plugins in CI/CD setup?
A: You can update the `deply.php` as stated above, and add a task to run `composer install`. Or you can override `deploy.sh` by placing it location `.github/deploy/deploy.sh` and add `composer install` line [here](https://github.com/rtCamp/github-actions-library/blob/7af8f915bfa9263e2241d8f0db42ab05804ec5a2/deploy/deploy.sh#L55).

3. How to change phpcs inspections standards?
A: Take [this phpcs.sh](https://raw.githubusercontent.com/rtCamp/github-actions-library/7af8f915bfa9263e2241d8f0db42ab05804ec5a2/inspections/codesniffer/phpcs.sh) as the base file and place it in location `.github/inspections/codesniffer/phpcs.sh`. Then, update [following lines](https://github.com/rtCamp/github-actions-library/blob/7af8f915bfa9263e2241d8f0db42ab05804ec5a2/inspections/codesniffer/phpcs.sh#L19-L24) to install standards according to the need of project.

4. How to setup git repo for mu-plugins cloning for vip site?
A: By default, if `vip: true` is setup in `hosts.yml`, then https://github.com/Automattic/vip-mu-plugins-public repo is cloned. If any other repo is required, then it can be setup as [env variable](https://github.com/rtCamp/github-actions-library/tree/7af8f915bfa9263e2241d8f0db42ab05804ec5a2/deploy#environment-variables-that-can-be-setup-in-the-github-actions) in action `Deploy` in `main.workflow` file. 
