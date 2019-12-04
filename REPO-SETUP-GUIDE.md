# Repo Setup Guide

## Adding description and what to add in it

GitHub repo description should have a short brief about the repo and site url if applicable.
![GitHub repo description and url](https://user-images.githubusercontent.com/42698168/70128800-8c724200-16a3-11ea-9c21-57c408cb86ec.png)

Example Description: A completely new look and branding of example.com along with Gutenberg, AMP, PWA, a PDF generator for case study.
Example Url: example.com

## Managing topics and what to add in topics

GitHub repo topics tags should contain the following:
1. Describe the technologies used in the project. (PHP, node, etc.)
2. Describe the hosting service and EasyEngine version if used. (AWS, EE4 | VIP-GO, etc.)

Guide on how to add topics: https://help.github.com/en/github/administering-a-repository/classifying-your-repository-with-topics

## Setting up branch protection rules

Branch protection rules should be added to the main branches of the project. It is also necessary to add branch protection rules when adding branch delete mechanism by default as described below, so that these default and main branches are not deleted. 

A good startpoint is to protect `master`, `staging`, `develop`, `qa`, `testing`.
Any branch other than protected branch should be created according to branch naming guidelines given in project readme.

Guide to enable branch protection: https://help.github.com/en/enterprise/2.16/admin/developer-workflow/configuring-protected-branches-and-required-status-checks#enabling-a-protected-branch-for-a-repository

## Setting up of deletion of merged branches

Branches other than protected branches should be removed as soon as their work is done. To setup automatic deletion of branches when they are merged go through the below guide. Please make sure to protect branches before setting this up. Otherwise it can lead to deletion of required branches.

Guide to setup auto deletion of merged branches: https://help.github.com/en/github/administering-a-repository/managing-the-automatic-deletion-of-branches

## How to configure and tweak GitHub actions

1. You need to add `VAULT_ADDR` and `VAULT_TOKEN` secrets to GitHub repository. Contact @sys if you don’t have these.
2. 

## Setting up slack notifications for the repo

GitHub slack integration is present in rtCamp slack. 

Guide to subscribe for GitHub notifications in slack channel: https://github.com/integrations/slack#subscribing-and-unsubscribing/

Ideally create a bot channel to subscribe the GitHub notifications of the project.

## Setting up labels for the repo
