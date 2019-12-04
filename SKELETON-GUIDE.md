# WordPress Skeleton

```bash
|-- .github
|   |
|   |-- ISSUE_TEMPLATE
|   |   |-- bug_report.md
|   |   `-- feature_request.md
|   |
|   |-- pull_request_template.md
|   |
|   |-- hosts.yml
|   `-- workflows
|       |-- deploy_on_push.yml
|       `-- phpcs_on_pull_request.yml
|       `-- plugin_update_every_week.yml
|       `-- repo_housekeeping.yml (future scope)
|
|-- .gitignore
|
|-- README.md
|-- REPO-SETUP-GUIDE.md
|-- SKELETON-GUIDE.md
|
|-- mu-plugins
|   |-- plugin-update-notification.php
|
|-- phpcs.xml
|
|-- plugins
|   `-- .gitkeep
|
|-- rt-config
|   `-- rt-config.php
|
`-- themes
|   `-- .gitkeep
|
|-- webroot-files
|   `-- .gitkeep
```

## Description of the skeleton structure

### .github

Contains issue template, PR template and GitHub actions.

1. `ISSUE_TEMPLATE` - Contains two main templates for creating issue in a repo. 
    i. `bug_report.md` - Standardized template to report a bug. Will contain all details and checklists to be added in issue.
    ii. `feature_request.md` - Standardized template to create a feature request.

2. `pull_request_template.md` - Standardized template for generating a PR. Many times PRs are created without any notes or issue references. This will take care to remind a dev about all the necessary things to write in description while opening a PR.

Both the templates aim to minimize the slack threads, calls and discussion due to lack of information in issues and PRs.

3. `hosts.yml` - Branch to server mapping file for [action-deploy-wordpress](https://github.com/rtCamp/action-deploy-wordpress/).

4. `workflows` - GitHub actions yml files.
    i. `deploy_on_push.yml` - Action to deploy site and send success slack notification. Based on [action-deploy-wordpress](https://github.com/rtCamp/action-deploy-wordpress/) and [action-slack-notify](https://github.com/rtCamp/action-slack-notify/)
    ii. `phpcs_on_pull_request.yml` - Action to run PHPCS checks on PRs. Based on [action-phpcs-code-review](https://github.com/rtCamp/action-phpcs-code-review/).
    iii. `plugin_update_every_week.yml` - Action to check for plugin updates every week and generate PR if update available. Based on [action-plugin-update](https://github.com/rtCamp/action-plugin-update/)
    iv. `repo_housekeeping.yml` - Future automation action to cleanup merged branches, report stale issues and stale PRs to PM, cleanup old repos of stale things.

### .gitignore

Specifies intentionally untracked files to ignore for WordPress development.

### README.md

Contains the standard readme with all the things from title, enviournment, setup, migration etc. all details covered that should ideally be present in a project readme.

### REPO-SETUP-GUIDE.md

The guide for repo setup and must TODOs. Should be referred while creating a repo from skeleton.

### SKELETON-GUIDE.md

The guide describing the skeleton repo structure and files.

### phpcs.xml

PHPCS Default ruleset Configuration File.

### rt-config

Folder containing `rt-config.php`.

1. `rt-config.php` - This file can be used similarly we are using `wp-config.php`. This file is loaded very early (immediately after `wp-config.php`).

### mu-plugins

Folder to keep WordPress mu-plugins.

1. `plugin-update-notification.php` - Display plugin update notification when `DISALLOW_FILE_MODS` constat set to true. Which is the case in wordpress-skeleton.

### plugins

Folder to keep WordPress mu-plugins.

### themes

Folder to keep WordPress themes.

### webroot-files

Files like `robots.txt`, that need to go to the webroot of the site should be kept here.