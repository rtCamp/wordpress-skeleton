workflow "Deploy and Slack Notification" {
  on = "push"
  resolves = ["Slack Notification"]
}

action "Run phpcs inspection" {
  uses = "rtCamp/action-phpcs@master"
  secrets = ["USER_GITHUB_TOKEN"]
}

action "Deploy" {
  needs = ["Run phpcs inspection"]
  uses = "rtCamp/action-wordpress-deploy@master"
  secrets = ["VAULT_ADDR", "VAULT_TOKEN"]
}

action "Slack Notification" {
  needs = ["Deploy"]
  uses = "rtCamp/action-slack-notify@master"
  secrets = ["VAULT_ADDR", "VAULT_TOKEN"]
}
