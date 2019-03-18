# GitHub Actions - WordPress Skeleton
    
## Project short intro
This project is used as a base for setting up CI/CD using GitHub actions for new or existing projects. It uses [GitHub Actions Library](https://github.com/rtCamp/github-actions-library) for actions inside the workflow. `.github` folder in the repository contains the main CI/CD scripts.

## Setup CI/CD
To setup CI/CD in your project follow these steps:

0. If you're creating a site through EasyEngine v4, add flag `--public-dir=current` in creation for proper configuration and then delete the current folder (`rm -r /opt/easyengine/sites/example.com/app/htdocs/current`), it will get created by deployer in CI/CD.

1. Setup your repo according to the skeleton strucutre of this repo. In case of fresh projects, you can start with a direct clone of this repo. For existing projects you can take the `.github` folder.

```bash
git clone --depth=1 git@github.com:rtCamp/github-actions-wordpress-skeleton.git
```

2. Update `hosts.yml`
    1. Update branch blocks with required branches and corresponding server details.
    2. Update `ci_script_options` as per project needs.
        1. Setting `vip: true` will enable cloning of mu-plugins.
        2. `wp-version` can be set to `latest` for latest released version. Or it can be pinned to a specified by setting a value like: `5.0.3`.
        3. Setup `slack-channel`, with the channel name you want to send notification to. If left empty, it will send slack notification to default webhook channel.

3. Update [GitHub secret](https://developer.github.com/actions/creating-workflows/storing-secrets/) and add `VAULT_ADDR` and `VAULT_TOKEN` secret. For more information on how to setup vault and what should be the vaule of these secret variables check [vault for deployment](https://github.com/rtCamp/action-wordpress-deploy#vault) and [vault for slack notification](https://github.com/rtCamp/action-slack-notify#additional-vault-support).

In case vault is not being used, you can also use `SSH_PRIVATE_KEY` [for deployment](https://github.com/rtCamp/action-wordpress-deploy#installation) and `SLACK_WEBHOOK` [for slack notification](https://github.com/rtCamp/action-slack-notify#installation) instead of `VAULT_ADDR` and `VAULT_TOKEN` secrets.

**Note: Steps 4 and 5 are required, only if the site has not been created with `--public-dir=current` EasyEngine flag**

4. Update nginx webroot of the site to point to `/var/www/htdocs/current`. 
If you are using EasyEngine v4 then:
    1. Update the file: `/opt/easyengine/sites/example.com/config/nginx/conf.d/main.conf` and replace `/var/www/htdocs` with `/var/www/htdocs/current`
    2. Run `ee site reload example.com`.

5. Move `wp-config.php` inside `htdocs` folder.
```bash
mv /opt/easyengine/sites/example.com/app/wp-config.php /opt/easyengine/sites/example.com/app/htdocs/wp-config.php 
```

## FAQs

**Q:** How to configure custom `deploy.php`?

**A:** Read the documentation on how to customize [deploy GitHub action](https://github.com/rtCamp/action-wordpress-deploy#customize-the-action).

----

**Q:** How to run `composer install` for plugins in CI/CD setup?

**A:** You can update the `deply.php` [as stated above](https://github.com/rtCamp/action-wordpress-deploy#customize-the-action), and add a task to run `composer install`. Or you can override `deploy.sh` by placing it location `.github/deploy/deploy.sh` and add `composer install` line [here](https://github.com/rtCamp/action-wordpress-deploy/blob/d07e406998515955b83fea87f7ed635647187489/deploy.sh#L85).

----

**Q:** How to change phpcs inspections standards?

**A:** Create [phpcs.xml](https://github.com/rtCamp/github-actions-wordpress-skeleton/blob/master/phpcs.xml) with the standards and rulesets that you want the project to follow and put it in the root of your repo.

----

**Q:** How to setup git repo for mu-plugins cloning for vip site?

**A:** By default, if `vip: true` is setup in [hosts.yml](https://github.com/rtCamp/github-actions-wordpress-skeleton/blob/c642c5076fe3ece90be9135e2e7373b8a77c0862/.github/hosts.yml#L43), then https://github.com/Automattic/vip-mu-plugins-public repo is cloned. If any other repo is required, then it can be setup as [env variable](https://github.com/rtCamp/action-wordpress-deploy#environment-variables) in action `Deploy` in `main.workflow` file. 

----
