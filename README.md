# WordPress Skeleton

This repo is used as a base for setting up CI/CD using GitHub Actions for new or existing projects. It uses [rtCamp's GitHub Actions Library](https://github.com/rtCamp/github-actions-library).

`.github` folder in the repository contains the main CI/CD scripts.

## Usage

1. Setup your repo according to the skeleton strucutre of this repo. In case of fresh projects, you can start with a direct clone of this repo. For existing projects you can take the `.github` folder manually.

```bash
git clone --depth=1 git@github.com:rtCamp/wordpress-skeleton.git
```
2. Push your local repo to GitHub.
3. Update `hosts.yml` file to map GitHub branches to different server environment. Only the branches specified in `hosts.yml` will be deployed, rest will be filtered out.
4. Create [GitHub Secrets](https://developer.github.com/actions/creating-workflows/storing-secrets/) for [each GitHub Action](https://github.com/rtCamp/github-actions-library#list-of-github-actions) you are planning to use.
