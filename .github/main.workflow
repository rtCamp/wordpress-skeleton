workflow "Deploy and Slack Notification" {
  on = "push"
  resolves = ["Slack Notification"]
}

action "Run phpcs inspection" {
  uses = "rtCamp/action-phpcs-code-review@master"
  secrets = ["GH_BOT_TOKEN"]
  args = ["WordPress,WordPress-Core,WordPress-Docs"]
}

action "Deploy" {
  needs = ["Run phpcs inspection"]
  uses = "rtCamp/action-deploy-wordpress@master"
  secrets = ["VAULT_ADDR", "VAULT_TOKEN"]
}

action "Slack Notification" {
  needs = ["Deploy"]
  uses = "rtCamp/action-slack-notify@master"
  secrets = ["VAULT_ADDR", "VAULT_TOKEN"]
}
