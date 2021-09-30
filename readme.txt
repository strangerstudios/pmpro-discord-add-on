=== Connect Paid Memberships Pro to Discord ===
Contributors: expresstechsoftware, strangerstudios
Tags: Discord, Talk, Video Chat, Hang Out, Friends, Meberships, discord role management
Requires at least: 4.7
Tested up to: 5.8
Requires PHP: 7.0
Stable tag: 1.0.1
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This add-on enables connecting your PMPRO enabled website to your discord server. Now you can add/remove PMPRO members directly to your discord server roles, assign roles according to your member levels, unassign roles when they expire, change role when they change membership.

👉 Please please please give us your love, please always ask in support if you are stuck in setting up the plugin or facing an issue we will prmptly help you sort out any issue you may be facing.
👉 If you love this plugin, please rate us 5 star rating.
👉 We welcome donations to keep us developing awesome free plugins, our Paypal is: business@expresstechsoftwares.com


== Description ==
= The official PMPRO Discord AddOn enables connecting your PaidMebershipPro members to connect to your discord online community, with the server roles assigned to members as per their membership level. =

Very simple setup and intutive User interface to Manage Member Role inside Discord.

[youtube https://youtu.be/v7lxB_Bvlv4]

This plugin provides the following features: 
1) Allow any member to connect their discord account with their PaidMebershipPro membership account. 
2) Members will be assigned roles in discord as per their membership level.
3) Members roles can be changed/remove from the admin of the site.
4) Members roles will be updated when membership expires.
5) Members roles will be updated when membership cancelled.
6) Admin can decide what default role to be given to all members upon connecting their discord to their membership account.
7) Admin can decide if membership should stay in their discord server when membership expires or cancelled.
8) Admin can decide what default role to be assigned when membership cancelled or expire.
9) Admin can change role by changing the membership by editng user insider WP Manage user.
10) Send a Direct message to discord members when their membership has expired. (Only work when allow none member is set to YES and Direct Message advanced setting is set ENABLED)
11) Send a Direct message to discord members when their membership is cancelled. (Only work when allow none member is set to YES and Direct Message advanced setting is set ENABLED)
12) Send membership expiration warnings Direct Message when membership is about to expire (Default 7 days before)


[View all Screenshots](https://www.expresstechsoftwares.com/pmpro-official-discord-add-on/)

== Installation ==

= Download, Install and Activate! =
1. Go to Plugins > Add New to find and install PMPRO discord Addon.
2. Or, download the latest version of the plugin, then go to Plugins > Add New and click the "Upload Plugin" button to upload your .zip file.
3. Activate the plugin.

= Complete the Initial Plugin Setup =
Go to Memberships > Discord Settings in the WordPress admin to begin setup. Our [Initial Setup Tutorial Video ](https://youtu.be/v7lxB_Bvlv4) will show you how to configure

= More Installation and Setup Documentation =
* [Installation Video](https://youtu.be/v7lxB_Bvlv4)
* [Installation Docs](https://www.expresstechsoftwares.com/step-by-step-documentation-guide-on-how-to-connect-pmpro-and-discord-server-using-discord-addon/)


== Frequently Asked Questions ==
= I need help installing, configuring, or customizing the plugin. =
Please visit [our support site at https://www.expresstechsoftwares.com/step-by-step-documentation-guide-on-how-to-connect-pmpro-and-discord-server-using-discord-addon/](https://www.expresstechsoftwares.com/step-by-step-documentation-guide-on-how-to-connect-pmpro-and-discord-server-using-discord-addon/) for more documentation and our support forums.
= I'm getting an error in error Log 'Missing Access'
Please make sure your bot role has the highest priority among all other roles in your discord server roles settings. please watch video on youtube how to do it. [Installation Video](https://youtu.be/v7lxB_Bvlv4?t=363)
= Role Settings is not appearing.
1. Clear browser cache, to uninstall and install again.
2. Try the disabling cache
3. Try Disabling other plugins, there may be any conflict with another plugin.
= Members are not being added spontaneously.
1. Due to the nature of Discord API, we have to use schedules to precisely control API calls, that why actions are delayed. 
= Member roles are not being assigned spontaneously.
1. Due to the nature of Discord API, we have to use schedules to precisely control API calls, that why actions are delayed. 
= Some members are not getting their role and there is no error in the log.
1. Sometimes discord API behaves weirdly, It is suggested to TRY again OR use another discord account.
= After expiry or member cancellation the roles are not being removed
1. It is seen in discord API that it return SUCCESS but does not work sometimes. It is suggested to manually adjust roles via PMPPRO->Members table.

== Screenshots ==
1. Install and activate the plugin and view the discord settings page inside Memberships
2. Map Discord roles and PMPRO levels.
3. Advanced settings.
4. Spot the Connect to Discord on your profile page.
