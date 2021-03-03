# Project Name / sitename.tld

Project description and short intro goes here.

## Environments

| Environment | Branch  | URL                          | Hostname            |
|-------------|---------|------------------------------|---------------------|
| Production  | main  | https://example.com          | prod.example.com    |
| Staging     | staging | https://staging.example.com  | staging.example.com |
| Development | develop | https://dev.example.com      | dev.example.com     |

## Maintainer

### rtCamp Maintainers:

| Name                    | Github Username   |
|-------------------------|-------------------|
| [Name](mailto:email-id) |  @github_username |

### Client Representative: (if any)

| Name                    | Github Username   |
|-------------------------|-------------------|
| [Name](mailto:email-id) |  @github_username |

## Development Workflow

### Default Branch

`main`

### Branch naming convention

- For bug - `fix/issue-name` For example, `fix/phpcs-errors`
- For feature - `feature/issue-name` For example, `feature/add-plugin`

### Pull Request and issue notes

- Title should be same as Issue title. Also add issue number before title. For example, `GH-3 Setup initial theme`.
- Add proper description.
- Assign reviewer.
- Create draft pull request for work in-progress PR and don't add `WIP:` in PR title.
- PR should have one approval.

### Testing

List down tests created for the project and details on how to execute them locally.

- PHP Unit tests if any.
- Behat tests if any.
- GitHub actions/travis/circleci etc. CI test cases if any.

### Linting

Notes about linting to be followed in project.

## Project Management tool details

Add details about JIRA / ActiveCollab / GitHub project in use.

## Env/project specific Customization

Example: Plugin settings that need to be updated in dev/staging sites.

| Title                                                     | Name                         | Disable on dev site? | Notes                   |
|-----------------------------------------------------------|------------------------------|----------------------|-------------------------|
| Jetpack by WordPress.com                                  | jetpack                      | No                   | Enable Jetpack Dev Mode |
| AWS SES wp_mail drop-in                                   | aws-ses-wp-mail              | No                   | Check that `AWS_SES_MAIL_*` constants are not present in wp-config     |

## Repo integrations

Add details of apps and integrations installed for the repo.

## Meta

### Repo Setup Guide

If you are setting up the repo, read: [REPO-SETUP-GUIDE.md](./REPO-SETUP-GUIDE.md)

### Skeleton Guide

Please read the skeleton repo guide to understand the structure of repo: [SKELETON-GUIDE.md](./SKELETON-GUIDE.md)
