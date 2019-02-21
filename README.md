# GitHub Actions - WordPress Skeleton
    
## Project short intro
This project is used as a base for setting up CI/CD for new or existing projects.

## Setup CI/CD
To setup CI/CD in your project follow these steps:

1. Download the required configuration files.

```bash
# Method - 1
git clone --depth=1 git@github.com:rtCamp/github-actions-wordpress-skeleton.git .github && rm -rf .github/.git

# Method -2
latest_release=$(curl -s "https://api.github.com/repos/easyengine/easyengine/releases/latest" | grep '"tag_name":' | sed -E 's/.*"([^"]+)".*/\1/') && \
curl "https://github.com/EasyEngine/easyengine/archive/$latest_release.zip" -Lso gh-actions.zip && \
unzip gh-actions.zip && \
mv github-actions-wordpress-skeleton-$latest_release .github
```

2. Update `hosts.yml`
    1. Update branch blocks with required branches and corresponding server details.
    2. Update `ci_script_options` as per project needs.
        1. Setting `vip: true` will enable cloning of mu-plugins.
        2. `wp-version` can be set to `latest` for latest released version. Or it can be pinned to a specified by setting a value like: `5.0.3`.

3. Update `Correct branch filter` in `main.workflow`, set the branches according to the deployment needs.


## Status of CI/CD, test coverage, code-review level
[![Build Status][build-badge]][build]

## Any other meta
