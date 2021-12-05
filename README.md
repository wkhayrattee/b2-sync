# b2-sync #
**Contributors:** [wkhayrattee](https://profiles.wordpress.org/wkhayrattee/)  
**Tags:** backblaze, backblaze b2,sync,backup  
**Requires at least:** 4.7  
**Tested up to:** 5.8.2  
**Stable tag:** 1.2.0  
**Requires PHP:** 7.2  
**License:** GPLv2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html  

![b2-sync banner](https://ik.imagekit.io/wkhayrattee/b2sync/banner-1544x500_Hd3BLN-Sz8.png?updatedAt=1638126306180)

A WordPress plugin for Backblaze b2 cloud to sync assets files from wp-content/uploads onto a Backblaze B2 bucket

## Description ##

This plugin allows you to sync your wp-content/uploads folder onto your backblaze b2 bucket.

It will also automatically sync all your media whenever you:
- create a new post or page and add a new media
- edit an existing post or page to add or update a media file

NOTE:
It will not get triggered if you upload a media on the media library page.
Fortunately, after you upload a media like this, you can directly trigger a sync to backblaze by using the action button that we put at your disposal.

## REQUIREMENTS ##

This plugin relies and assumes that you have *rclone* installed on your server.
Refer to this guide on how to install rclone: [https://rclone.org/downloads/](https://rclone.org/downloads/)

## TODO in next phase ##

- Identify an appropriate **hook** when a media is uploaded via the ***WordPress Media Library*** so that we can trigger the sync for this action as well
- Tell the sync process to ignore certain type of files
- The log file messages are not pretty - create a custom approach for that instead of relying on `monolog/monolog`
- [done] Show a real-time verbose mode of the sync when triggering the sync manual using the action button?

## Installation ##

1) Install & activate the plugin

2) Fill in your Backblaze B2 Bucket credentials on the admin page "B2-Sync" menu


## Contributing ##

The best way to contribute to the development of this plugin is by participating on the GitHub project:

[https://github.com/wkhayrattee/b2-sync](https://github.com/wkhayrattee/b2-sync)

There are many ways you can contribute:

* Raise an issue if you found one
* Create/send us a Pull Request with your bug fixes and/or new features
* Provide us with your feedback and/or suggestions for any improvement or enhancement
* Translation - this is an area we are yet to do

## Attributions ##
* **Storyset** for the illustrations

## Changelog ##

= 1.2.0 (December 5, 2021) =
* FIX: the process was blocking with wait(), let us try to make it async as getting real-time output and being async is a challenge
* Enhancement: display all lines in the log henceforth so that user can see what is happening from start to end

= 1.1.0 (December 4, 2021) =
* New Feature: see real-time output on the log screen
* Enhancement: Allow the system to check if a sync process is already running so tha we prevent triggering any duplicate sync process

= 1.0.0 (November 28, 2021) =
* Initial release onto WordPress.org plugin repo with the initial code from phase 1 of this plugin

= 0.1.0 (November 20, 2021) =
* Initial commit of working code for the benefit of everyone who needs this plugin
