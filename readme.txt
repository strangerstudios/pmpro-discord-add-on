=== Paid Memberships Pro - Discord Integration ===
Contributors: expresstechsoftware, strangerstudios
Tags: Discord, Talk, Video Chat, Hang Out, Friends, Memberships, discord role management
Requires at least: 5.2
Tested up to: 5.8
Requires PHP: 7.0
Stable tag: 1.0.1
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Discord integration for the Paid Memberships Pro plugin.

== Description ==

Seamlessly connect your WordPress site and Discord server. No more manually managing users in Discord. 

When new members are added to Paid Memberships Pro, they are also invited to your Discord Server. Their member role on the Discord Server will be set based on their PMPro membership level on your WordPress site. Members who change level or cancel on your site are automatically updated or removed from the Discord server.

[youtube https://youtu.be/v7lxB_Bvlv4]

This plugin provides the following features: 
1) Allow any member to connect their discord account with their Paid Memberships Pro membership account. 
2) Members will be assigned roles in discord as per their membership level.
3) Members roles can be changed/remove from the admin of the site.
4) Members roles will be updated when membership expires.
5) Members roles will be updated when membership is cancelled.
6) Admin can decide what default role to be given to all members upon connecting their discord to their membership account.
7) Admin can decide if membership should stay in their discord server when membership expires or is cancelled.
8) Admin can decide what default role to be assigned when membership is cancelled or expired.
9) Send a Direct message to discord members when their membership has expired. (Only works when "allow none member" is set to YES and Direct Message advanced setting is set ENABLED)
10) Send a Direct message to discord members when their membership is cancelled. (Only works when "allow none member" is set to YES and Direct Message advanced setting is set ENABLED)
11) Send membership expiration warnings by Direct Message when membership is about to expire (Default is to send 7 days before)

== Installation ==

= Download, Install and Activate! =
1. Upload the `pmpro-discord-add-on` directory to the `/wp-content/plugins/` directory of your site.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. The settings page is at Memberships --> Discord Settings in the WP dashboard.

= Complete the Initial Plugin Setup =
Our [Initial Setup Tutorial Video ](https://youtu.be/v7lxB_Bvlv4) will show you how to configure this plugin.

= More Installation and Setup Documentation =
* [Installation Video](https://youtu.be/v7lxB_Bvlv4)
* [Installation Docs](https://www.expresstechsoftwares.com/step-by-step-documentation-guide-on-how-to-connect-pmpro-and-discord-server-using-discord-addon/)

== Frequently Asked Questions ==
= I need help installing, configuring, or customizing the plugin. =
Please visit [our support site at https://www.expresstechsoftwares.com/step-by-step-documentation-guide-on-how-to-connect-pmpro-and-discord-server-using-discord-addon/](https://www.expresstechsoftwares.com/step-by-step-documentation-guide-on-how-to-connect-pmpro-and-discord-server-using-discord-addon/) for more documentation and our support forums.
= I'm getting an error in error Log 'Missing Access'. =
Please make sure your bot role has the highest priority among all other roles in your discord server roles settings. Please watch our video on YouTube: [Installation Video](https://youtu.be/v7lxB_Bvlv4?t=363)
= Role Settings is not appearing. =
1. Clear browser cache, to uninstall and install again.
2. Try the disabling your cache.
3. Try Disabling other plugins, there may be a conflict with another plugin.
= Members are not being added or updated spontaneously. =
1. Due to the nature of the Discord API, we have to use schedules to precisely control API calls. This is the cause of this delay.
= Some members are not getting their role and there is no error in the log. =
1. Sometimes discord API behaves weirdly. It is suggested to TRY again OR use another discord account.
= After expiry or member cancellation the roles are not being removed. =
1. We have seen cases where the Discord API returns "success" when attempting to update a user, but the update really fails. In this case, you may have to manually update that user.

== Screenshots ==
1. Install and activate the plugin and view the discord settings page inside Memberships
2. Map Discord roles and PMPRO levels.
3. Advanced settings.
4. Spot the Connect to Discord on your profile page.
