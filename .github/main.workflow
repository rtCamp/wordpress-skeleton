workflow "Deploy and Slack Notification" {
  on = "push"
  resolves = ["Slack Notification"]
}

# Deploy Filter 
action "Correct branch filter" {
  uses = "rtCamp/github-actions-library/bin/filter@master"
  args = "branch master develop qa"
}

action "Run phpcs inspection" {
  needs = ["Correct branch filter"]
  uses = "rtCamp/github-actions-library/inspections/codesniffer@master"
}

action "White Screen Test" {
  needs = ["Run phpcs inspection"]
  uses = "rtCamp/github-actions-library/test/white-screen@master"
}

action "Deploy" {
  needs = ["White Screen Test"]
  uses = "rtCamp/github-actions-library/deploy@master"
  secrets = ["VAULT_ADDR", "VAULT_GITHUB_TOKEN"]
}

action "Slack Notification" {
  needs = ["Deploy"]
  uses = "rtCamp/github-actions-library/notification/vault-slack@master"
  secrets = ["VAULT_ADDR", "VAULT_GITHUB_TOKEN"]
}
