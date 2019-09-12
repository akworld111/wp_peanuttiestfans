=== WP Foto Vote ===
Requires at least: 3.8
Tested up to: 5.2.1
Stable tag: 3.8
Requires PHP: 5.5

Just another photo contest plugin. Simple but flexible.

== Changelog ==

How to update: http://wp-vote.net/doc/updating-plugin/

/* VER 2.3.14 - 22/07/2019 */

- Importer fix
- Gallery addon - lightbox on single page
- Addon Base - updater fix (changed slug to avoid conflicts with MyCred, etc)

/* VER 2.3.13 - 26/05/2018 */

- [fix] Fixed issue with FB sharing counter

/* VER 2.3.12 - 21/02/2018 */

- [fix] Fixed issue with "reCaptcha" in the Subscribe modal

/* VER 2.3.11 - 21/02/2018 */

- [fix] {competitor_meta_*} tags is not working in emails

/* VER 2.3.10 - 21/02/2018 */

- [fix] Rating summary is not saved when editing competitor (for "Rate summary" voting type)

/* VER 2.3.09 - 25/12/2018 */

- [fix!] During saving a settings all translation is reset
- [tweak] Wrong sorting for a leaders block with a "Rating summary" voting type
- [tweak] Corrected Final Countdown wrong timezone using

/* VER 2.3.08 - 13/12/2018 */

- {r371}[fix!] Updated Notification library for fix bug with a WP 5.0 + Yoast SEO plugin
- {r370}[tweak] Ultimate Member support (author avatar + author link)
- {r369}[tweak] Tweaks for Voting modal, Subscribe process and upload redirect
- {r368}[tweak] Added "reCaptcha" option to the Subscribe modal
- {r367}[tweak] Small upload fixes
- {r366}[tweak] Added option to remove all plugin data during deactivation

/* VER 2.3.07 - 27/11/2018 */

- {r365}[tweak] Added "Back to contest" link into the Single View of the "Default" skin

/* VER 2.3.06 - 03/11/2018 */

- {r363}[fix] Fix for Voting Additional security "FB Share"
- {r362}[fix] Fixed issue with a Simple FB login and Video contest addon
- {r361}[fix] Fixes for "Rate summary" voting type - restored correct sorting order for "Popular" and votes display in lightbox
- {r360}[fix] Small customizer fix (could not work with some themes)

/* VER 2.3.05 - 08/10/2018 */

- {r358}[fix] Fixes for Email Verification process with using link
- {r357}[fix] Fixes for VK login with enabled VK social counter
- {r356}[new] Now possible to select voting icon in customizer (gallery, single view, winners, leaders, lightbox)
- {r355}[new] Now possible to select icon for user voted photos (in gallery, single view, lightbox)

/* VER 2.3.04 - 20/09/2018 */

- {r352}[new] Auto-infinite scroll
- {r351}[new] New skin - Hermes with a customizer support
- {r350}[tweak] Gallery addon tweak - better lightbox support

/* VER 2.3.03 - 15/09/2018 */

- {r346}[tweak] {admin_comment} notification tag isn't added to email content
- {r345}[new] New voting type: Rating summary (entry votes count = sum of all stars instead of average)

/* VER 2.3.02 - 02/09/2018 */

- {r342}[tweak] Small Rating mode tweaks
- {r341}[fix] Not possible to disable Privacy modal before vote
- {r340}[fix] Not possible to upload image in IE 11

/* VER 2.3.01 - 31/07/2018 */

- {r330}[fix] Fixed voting issue if photo uploaded by anonymous user and option "Restrict vote for user own photos" is ON
- {r331}[fix] Fixed issue with Multi-upload and Limitation by image dimensions (if one image is wrong - all picked images are reset)
- {r332}[fix] Added possibility to edit "Default" order type title

/* VER 2.3.00 - 28/07/2018 */

- {r320}[fix] Integrated with Wordpress customizer (for now supports Toolbar customizing, some Skins, etc)
- {r321}[tweak] Fix: photos counter in contests lists counts photos in Moderation
- {r322}[tweak] Fix: if upload allowed only for registered users - wrong message was displayed (that user do not have enough rights)
- {r323}[update] Rewritten "Simple social login" to keep your users data protected and increase voting security during Social login (now available only FB, VK and Google+ networks)
- {r324}[fix] Fixed issue with thumbnail in leaders block
- {r325}[tweak] GDPR option: Agree with Privacy Policy before Vote
- {r326}[tweak] GDPR option: Erase votes log in 7,14,30 days
- {r327}[tweak] GDPR option: Erase uploader's ip in 7,30 days
- {r328}[tweak] GDPR option: Reminder that necessary clear Subscribers list

/* VER 2.2.814 - 24/07/2018 */

- {r312}[fix] Fix for Upload in Safari (if submitted form with empty file inputs - upload fails)

/* VER 2.2.813 - 26/05/2018 */

- {r310}[tweak] Categories improvements: added Categories to CSV export + speed optimizations

/* VER 2.2.811 - 28/04/2018 */

- {r307}[fix] Error in settings for WP versions < 4.9

/* VER 2.2.810 - 27/04/2018 */

- {r305}[fix] Social Icons can't be hidden via Settings in New Year and Default (single view) skins

/* VER 2.2.809 - 25/04/2018 */

- {r300}[new] WhatsApp share icon (only on Tablet and Mobile)
- {r301}[new] Notice about security vulnerability in "Simple Social login" voting security option: https://www.facebook.com/WordpressPhotoContestApp/posts/957098634449453
- {r302}[new] Updated French translations (thanks to Pierre Alloueteau)

/* VER 2.2.808 - 16/04/2018 */

- {r290}[tweak] Added meta tags support to "Lightbox title template" (like {meta_phone})
- {r291}[new] Added custom Javascript fields to settings (global, only gallery, only single, only upload page)
- {r292}[tweak] Migrated from local Codemirror library (code highlight for CSS & JS) to WP Core version (require 4.9+)
- {r293}[tweak] Small images Lazy load tweak

/* VER 2.2.807 - 11/04/2018 */

- {r281}[new] Fixed bug with Comments editing in admin (comments content changes wasn't saved)
- {r282}[fix] Fixed issue with Winners pick & Rating stars mode
- {r283}[fix] Fixed issue with voting in MagnificPopup lightbox
- {r284}[tweak] Save to votes log casted "rating" for voting type "Rate Stars" and display in list
- {r285}[tweak] Improved deleting votes for voting type "Rate Stars"

/* VER 2.2.806 - 07/04/2018 */

- {r280}[new] Added feature to submit current translation for make them available for other users
- {r279}[new] Added option to decrease JPEG image quality on upload
- {r278}[fix] Fixed issue with Pinterest & Flickr gallery templates & lightbox
- {r277}[fix] Categories: Decrease category items counter when competitor deleted
- {r276}[fix] Categories: Fixed issue that happens with Category Filter if site used custom database prefix (not "wp_")

/* VER 2.2.805 - 27/03/2018 */

- {r275}[fix] Fixed bug that happens during installing new plugins (added in 2.2.804)

/* VER 2.2.804 - 21/03/2018 */

- {r271}[tweak] Small Voting Modal tweak to fix issues with some themes (fo example "advanced-newspaper")
- {r272}[fix] Fixed bugs with "Email Subscribe" voting security - verify email not sent + wrong email content

/* VER 2.2.803 - 05/03/2018 */

- {r270}[tweak] Small improvements for Single View "Open Graph" tags

/* VER 2.2.801 - 10/02/2018 */

- {r263}[new] During creating contest now possible create any of public custom post type (before only Page or Post)
- {r264}[new] New shortcode "fv_single_vote_button" for display only Vote button
- {r265}[tweak] During cloning contest also clone Categories
- {r266}[fix] Fixed "Night votes %" on "Votes analytic page"
- {r267}[new] Upload progress % as button background
- {r268}[new] Added support for Local Video files: display in gallery + lightbox via Magnific Popup

/* VER 2.2.800 - 25/11/2017 */

- {r244}[tweak] Changed design of admin tabs (Settings, Translation pages)
- {r245}[tweak] Voting: added extra option to limit voting by User ID + by User Role.
- {r246}[tweak] Voting: "Subscribe form" security type separated to 2 "Subscribe form" and "Subscribe form for not Authorized"
- {r247}[removed] Removed deprecated addon "Agree with rules"
- {r248}[tweak] Integrated "Gallery Addon" to plugin Core.
- {r249}[tweak] Rewritten "Gallery Addon": dynamic count of gallery items, better admin interface
- {r250}[tweak] Renamed some folders with UpperCase chars to avoid problems with Litespeed servers
- {r251}[tweak] Auto-attach competitor to user when editing Competitor Email
- {r252}[fix] Fixed {admin_comment} and {competitor_meta_*} tags in Notification emails
- {r253}[new] Added new contest options that allows override global settings: Hide votes, Allow upload only logged in users and Image size limit
- {r254}[tweak] Make Leaders skin "Block 2" responsive
- {r255}[new] Upload: added extra option to limit upload by User Role
- {r256}[tweak] Voting: added extra option to limit total votes per contest (for example 3 votes per day but not more than 15 per all contest)
- {r257}[new] Conditional tags in Single and List "photo description template": [IF {name}]name is {name}[ELSE]no name[ENDIF]
- {r258}[new] Fixed incompatibility with Subscribe modal and MagnificPopup lightbox
- {r259}[new] Upload form CSS tweaks
- {r260}[new] Error alert to user when Voting or Upload request fails (like 500, 404 error codes)
- {r261}[new] !! Complete Categories support - https://www.facebook.com/WordpressPhotoContestApp/posts/908612135964770

/* VER 2.2.708 - 14/11/2017 */

- {r240}[new] New"voting security" - Math Captcha
- {r241}[new] WPML tweaks
- {r242}[new] Updated "punycode" library
- {r243}[tweak] Slightly improved "Multi Adding photos form" (now requests send synchronously, to avoid possible server locking for too many onetime requests)

/* VER 2.2.705 - 14/10/2017 */

- {r236}[new] last_by_voting_date_end
- {r235}[new] Added new parameter to Competitor - Order position, that allows set Competitor position
- {r236}[tweak] Improved Competitor form
- {r237}[new] Added Filter by status feature in contests List (Published, Finished, Archived)
- {r238}[tweak] Thumbnails size params for Contests List shortcode moved from Settings to Shortcode params

/* VER 2.2.704 - 03/10/2017 */

- {r231}[tweak] Improved Countdown - now you can pass parameter "count_to" to shortcode: [fv_countdown count_to="upload,voting"] for specify date to countdown (total 4 mode: 1 - if voting active, 2 - if voting will be active, 1 - if upload active, 2 - if upload will be active )
- {r232}[new] New shortcode "[fv_winners contest_id=X]"
- {r233}[new] Option to display Author avatar (instead of icon) from Wordpress (or Buddypress, if installed)
- {r234}[new] New Tab "Details" for display contest "Description & rules"

/* VER 2.2.703 - 30/09/2017 */

- {r229}[fix] Restore possibility to edit Competitor status (Publish, Draft, On Moderation)
- {r230}[fix] Minor fixes for Competitors list to avoid issues with module "ModPagespeed"

/* VER 2.2.702 - 26/09/2017 */

- {r228}[fix] Fixed: "After vote heart mark changes to a check mark, but after a while it returns to the heart mark"

/* VER 2.2.701 - 22/09/2017 */

- {r227}[fix] Small fix in ModelCompetitors::findByMeta()

/* VER 2.2.700 - 21/09/2017 */

- {r221}[tweak] Re-writed thumbnail retrieving process to allow "S3", "Instagram" and "Video contest" addons correct work together
- {r222}[tweak] Option to "Restrict users to vote for own photos"
- {r223}[tweak] Small "pinterest" skin tweaks - moved hover overlay animation from JS to CSS
- {r224}[removed] Disabled Google+ sharing counter due to removing "Shares counter" by Google https://warfareplugins.com/google-plus-share-counts/
- {r225}[fix] Fixed small bugs with Countdown display

/* VER 2.2.608 - 30/08/2017 */

- {r213}[fix] Small ORM fix, to avoid issue that sometime happens when used JOIN's
- {r214}[update] Updated "Notifications" plugin to version 3.1.1 [https://github.com/Kubitomakita/Notification/tree/3.1.1]
- {r215}[tweak] Reduced sql queries count made by "Notifications" plugin with using cache
- {r216}[fix] Mail to admin about new competitor is not send
- {r217}[tweak] Added "hide_toolbar" shortcode param to allow hide toolbar in specified contest
- {r218}[tweak] Ability to set separate "required capability" for Moderate new entries to grant access only for "Moderation" page
- {r219}[fix] Fixed issue with AWS S3 addon thumbnails retrieving
- {r220}[tweak] Ability to disable "evercookie" library with <code>define("FV_DISABLE_EVERCOOKIE", 1);</code>

/* VER 2.2.607 - 26/08/2017 */

- {r212}[fix] Hide Contest leaders, if zero entries

/* VER 2.2.606 - 23/08/2017 */

- {r209}[new] Ability to clone "Upload Form"
- {r210}[fix] Fixed bug in Chrome with Sharing Dialog buttons color
- {r211}[new] Added Brazil translation (thanks to Roberto Augusto)

/* VER 2.2.605 - 13/08/2017 */

- {r207}[fix] Fixed possible issue with Notifications Emails, if in Contest settings isn't set "Related page"
- {r208}[new] Added new tab in contest Settings: "Description & rules" where you can add some details about contest, that will be displayed before contest gallery

/* VER 2.2.604 - 3/08/2017 */

- {r203}[tweak] Added ability search by "Votes count" & "Competitor ID" in admin Competitors list
- {r204}[tweak] Small tweaks for "Fraud score" calculating
- {r205}[tweak] Added 'status_not' param to Contests list shortcode

/* VER 2.2.603 - 31/07/2017 */

- {r201}[tweak] Performance improvements for page "Votes analytic", that allows remove "max 3000 votes" limit and display whole data
- {r202}[tweak] Added a few more fraud checks for "Refer" param

/* VER 2.2.602 - 30/07/2017 */

- {r199}[tweak] Added more cheating indicators - https://monosnap.com/file/nbhLBKdOxQFdvvfaCwS0k2V23SEkVg
- {r200}[fix] Fixed TOR browser detection

/* VER 2.2.601 - 15/07/2017 */

- {r197}[tweak] Lightbox Evolution tweaks: added Facebook Video support (with possible issues) + make all Video played via HTTPS
- {r198}[fix] Fixed Contest "Upload" status, if contest is finished

/* VER 2.2.600 - 21/06/2017 */

- {r190}[new] Complete rewritten competitors list: better performance, improved search, more "Bulk actions"
- {r191}[tweak] Responsive tweaks for Contest "settings" & "competitors" tabs
- {r192}[new] Complete rewritten notifications (about upload, etc)
#####TEST##### - {r193}[tweak] Small WPML tweak, to send emails in current user language
- {r194}[new] Option to Automatic fix image orientation (based on EXIF tags)
- {r195}[tweak] Small improvements for export Votes and Search

/* VER 2.2.513 - 07/05/2017 */

- {r184}[fix] OG meta fixes + Avada theme compatibility fix

/* VER 2.2.512 - 07/05/2017 */

- {r183}[fix] Small issue with icons at contest list shortcode

/* VER 2.2.511 - 07/05/2017 */

- {r185}[fix] Voting Security "FB Share" compatibility fix with latest FB updates (https://developers.facebook.com/docs/sharing/reference/feed-dialog#response)

/* VER 2.2.510 - 22/04/2017 */

- {r182}[fix] Fixed php issue that can down contest page on some hostings

/* VER 2.2.509 - 22/04/2017 */

- {r180}[tweak] Contest Settings API tweaks
- {r181}[tweak] PHP 5.2 fixes

/* VER 2.2.508 - 20/04/2017 */

- {r178}[new] New constant "FV_ADMIN__COMPETITORS_LIST__FETCH_USER_EMAIL" that force select user_email from wp_users table (for export & admin competitors list)

/* VER 2.2.507 - 19/04/2017 */

- {r175}[tweak] New filter, that allows change max votes count per user
- {r176}[tweak] New filter, that allows change max uploads count per user
- {r177}[tweak] Added admin notice, if something happens with "Single contest view page" (deleted, trashed)

/* VER 2.2.506 - 14/04/2017 */

- {r173}[tweak] Small ModelCompetitors update

/* VER 2.2.505 - 11/04/2017 */

- {r171}[fix] Fixed bug with new abstract classes (they can contain itself object instead of raw results)
- {r170}[fix] Fixed error that can down page with contest shortcode

/* VER 2.2.504 - 27/03/2017 */

- {r164}[tweak] Performance optimizations for Single View + extra optimizations for Pinterest & Selfie skins
- {r162}[new] Added admin notice about too small "Maximum File Upload Size", to avoid problems with images upload
- {r163}[new] Added "Instagram" social login type

/* VER 2.2.503 - 27/03/2017 */

- {r160}[new] !! Winners Feature !! - auto pick winners, manual pick, admin notify and a lot more features

- {r154}[new] New Leaders Skin "Poll"
- {r155}[new] New possible show 1-10 Leaders (before 1-4)
- {r156}[new] New Contests List Skin "Grid"
- {r157}[new] A lot updated "Contests List" shortcode
- {r158}[new] Skin "Like" improvements (js & css)
- {r159}[new] Widgets improvements - added "winners" order type for display Winners when contest ends
- {r161}[new] New Widget type - "global list" to display competitors from all contests

/* VER 2.2.502 - 13/03/2017 */

- {r145}[tweak] Possible set "Comma-separated list of email addresses" Settings => Upload notify =>  "Email to notify me on upload:" field
- {r146}[tweak] Pinterest "Single View" fix for not clickable "Next/Prev" buttons on mobile
- {r147}[new] All Skins functions migrated to skin.php (from "theme.php")
- {r148}[new] Cookies not used anymore
- {r149}[new] "User country" cache moved to Session from Cookies
- {r150}[new] New Skin "Red"
- {r151}[new] New Order type "pseudo-random"
- {r152}[new] More actions in Pinterest "single_list.php" (for make it more customizable)
- {r153}[fix] Admin Security Fixes

/* VER 2.2.501 - 04/03/2017 */

- {r142}[tweak] Contest lists shortcode tweaks
- {r143}[new] New method - FV_Templater::locateCustomInTheme() for locate addons templates in theme folder

/* VER 2.2.500 - 03/03/2017 */

- {r133}[tweak] Small tweaks "Addons" page Tabs + Responsive fixes for Tabs on "Translations" page
- {r134}[fix] Small upload fix
- {r135}[fix] Flickr && infinite pagination Fix
- {r136}[tweak] FastVoting compatibility set to WP 4.7.2

---- DEV ----
- {r137}[new] Abstract wrappers for Contest & Competitors
- {r138}[new] New Skins base class and new file skin.php
- {r139}[!new] Now possible rewrite template files with placing file in wp theme directory ""/wp-foto-vote/", for example "/wp-content/themes/hueman/wp-foto-vote/pinterest/list_item.php"

/* VER 2.2.418 - 21/02/2017 */

- {r132}[tweak] Added more filters for Leaders shortcode
- {r131}[tweak] WPML addon tweaks

/* VER 2.2.417 - 20/02/2017 */

- {r130}[tweak] Now possible filter widget's thumbnails params (size/cropping)

/* VER 2.2.416 - 15/02/2017 */

- {r128}[tweak] Small compatibility tweak for "HotOrNot" addon.
- {r129}[tweak] Added "stars" & "Rating:" strings to translation (used on "Rating mode")

---- DEV ----
- {r130}[tweak] ModelMeta->increaseOrInsert() + "updateOrInsert" methods
- {r131}[tweak] Possible add custom class to FV contest container (now used in HotOrNot addon for add custom class, if pair voting active)

/* VER 2.2.415 - 14/02/2017 */

- {r126}[tweak] Rating tweak
- {r127}[fix] On public upload fields now cropped to max field length (name = 255, description = 500, full_description = 1255)

/* VER 2.2.414 - 3/02/2017 */

- {r125}[fix] Some fixes in FvAddonBase class ("admin_init" now running with priority 9, else Instagram addon settings does not saved)

/* VER 2.2.413 - 27/01/2017 */

- {r124}[fix] Evercookie Lib fixes (some assets not loading after {r109})

/* VER 2.2.412 - 25/01/2017 */

- {r120}[fix] Bug with styles for main Image on Single View
- {r121}[fix] Other (flush rewrite rules, etc)
- {r122}[new] Added Mailchimp addon auto-updater
- {r123}[tweak] New Addons Updater Url

/* VER 2.2.411 - 23/01/2017 */

- {r117}[tweak] Optimized querying values for option "Page, where contest are placed" (for sites with small PHP memory values)
- {r118}[new] Now addons can require minimal "WP Foto Vote" plugin version, for avoid troubles with older versions
- {r119}[fix] Fixed FB sharing without FB APP ID on mobile devices

/* VER 2.2.410 - 21/01/2017 */

- {r115}[fix] Voting Security Type "IP+cookies+evercookie + Facebook Share" work incorrect with FB Andorid App (and may be iOS)
- {r116}[tweak] Better Upload logging, if "Debug Upload" option is on

/* VER 2.2.409 - 20/01/2017 */

- {r112}[fix] "Seconds" label in "Final Countdown" do not translates
- {r113}[fix] Bug with a styles if place Upload form at separate page
- {r114}[new] Added "Get Help" page

/* VER 2.2.408 - 17/01/2017 */

- {r109}[tweak] Renamed Evercookie lib path to avoid blocking in Opera
- {r110}[tweak] Added Votes count beside Photo ID/Name https://yadi.sk/i/kiayrQ1o39VG8V
- {r111}[tweak] Some code improvements to avoid Select2 library conflicts with older versions (loaded by from WooCommerce and other plugins)

/* VER 2.2.407 - 16/01/2017 */

- {r100}[!fix] FastVoting compatibility fix with a WP 4.7.1
- {r101}[fix] After edit & save competitor Meta is not updated in a Competitors table [https://yadi.sk/i/V1w3MUQQ39GdAB]
- {r102}[tweak] Different messages on deleting competitor (if Enabled/Disabled deleting photo from hosting)
- {r103}[tweak] Updated "DataTables jQuery plugin" from version 1.10.12 to 1.10.13 [https://datatables.net/]
- {r104}[tweak] Updated "DataTables jQuery :: Select extension" from version 1.2.0 to 1.2.1 [https://datatables.net/extensions/select/]
- {r105}[tweak] Now possible Hard Set reCAPTCHA language code (js filter "fv/public/reCAPTCHA/lang-code")
- {r106}[fix] Now on exporting Votes Log selected Contest are applied to results (before was exported all results)
- {r107}[tweak] Admin notice, if no Page/Post selected for a contest
- {r108}[fix] {r90} - setting not saved for use HTML in emails

/* VER 2.2.406 - 6/01/2017 */

- [tweak] Small code optimizations (for add support "Pay For Action" >= 0.9 && "User Manage Lite" >= 0.2)
- [tweak] Leaders & Forms shortcodes moved to separate files
- [tweak] Updater url switched to https
- [tweak] Main JS & CSS assets are register globally, so can be easy enqueued at another places

/* VER 2.2.405 - 26/12/2016 */

- [fix] Flickr template: gallery layout fix
- [tweak] Escaped More images "Alt" attribute at a Single View

/* VER 2.2.404 - 18/12/2016 */

- [tweak] Small improvements on Single View page on mobile (Pinterest and Modern Azure themes), example - https://yadi.sk/i/KdbO__9Z33i5nV
- [fix] Pinterest template: if contest voting is inactive, in *mobile* voting button still shows

/* VER 2.2.403 - 12/12/2016 */

- [fix] Fixed issue that can broken emails notify & PFA addon

/* VER 2.2.402 - 11/12/2016 */

- [tweak] Added a few more logic to detect upload error when image is added to Media Library but not added to contest

/* VER 2.2.401 - 1/12/2016 */

- {r90}[tweak] In translations emails html allowed from now

/* VER 2.2.400 - 1/12/2016 */

- [new] !! Added public search option (in Toolbar)
- [new] In settings possible hide from Toolbar "Order" & "Search" blocks
- [tweak] Public CSS moved SCSS
- [new] On contest manage page Config & Competitors separated to tabs
- [new] Added "Contest Stats" tab
- [tweak] Small tweaks in Flickr & New Year themes

/* VER 2.2.375 - 23/11/2016 */

- [tweak] Added shortcodes support in Upload Form require login message > https://yadi.sk/i/v_p8amEHzHsY2

/* VER 2.2.374 - 22/11/2016 */

- [tweak] To dropdown pages list added with status "draft" and "private"
- [tweak] Increased circle spinner rotation time (on voting, upload) to avoid user panic when it stopped after 2s spinning

/* VER 2.2.373 - 10/11/2016 */

- [tweak] improved remove_emoji funcion
- [new] In contestants list added link to editing Attachment page

/* VER 2.2.372 - 26/10/2016 */

- [new] Video output support for Single View

/* VER 2.2.371 - 26/10/2016 */

- [fix] Fixed small issue with getting thumbnails speed troubles if Settings thumbs Height or Width = 0
- [fix] Count photos on moderation at dashboard widget "At a glance"

/* VER 2.2.370 - 21/10/2016 */

- [new] Now possible edit "contestant" email (that user entered on upload) in admin
- [new] New Form field type - Phone
- [new] New Form field option - width (100%, 75%, 50%, etc)
- [new] New Form field type - "Rules checkbox" > as replacement "Agree with Rules" addon
- [new] Added "Fail votes" (if user already voted, etc) counter
- [new] Voting: Better TOR detection + extra field `is_tor`
- [tweak] checking 404 status for single Photo View page
- [fix] Fixed non-critical (but unpleasant) vulnerability, that theoretically allow logged in user without enough permissions change forms
- [fix] Fixed non-critical (but unpleasant) vulnerability, that allows delete all contests data, forms, Debug log for any Logged in user
- [tweak] on public upload by admin photo will be not automatically published
- [tweak] Removed Delete actions from Contests list to avoid casual deleting
- [tweak] on rotating image all thumbs will be deleted, for not leave not used thumbs
- [tweak] On Contest settings & Plugin Settings pages "Pages & Posts list" lazy loaded by user query for reduce SQL queries count
- [tweak] Decreased queries count in admin editing contest & public contest images list (up to 3x)

/* VER 2.2.363 - 8/10/2016 */

##test - [tweak] No need try refresh AJAX votes count - If no contest photos shows on page
- [tweak] updated RU video instruction links
- [fix] Photo Meta splashes issue fix (like "asds\\\'da")
- [fix] Form Builder splashes issue fix (form can't be edited after paste splash into Label or other field)
- [tweak] Removed Admin notice about empty contest, if Upload form shows
- [tweak] now possible copy contest shortcode on iOS (from "readonly" copy not worked)
- [fix] Fixed tabs show/hide "icon" in contest setting

/* VER 2.2.361 - 17/09/2016 */

- [fix] When plugin or addon was activated user get "The plugin generated 299 characters of unexpected output ***"
- [tweak] Not Fast AJAX will work just if current WP version tested with it
- [tweak] Export Contestants & Meta fields small fix
- [tweak] Crop contestant data (name, description, etc) to avoid Out of max length problems
- [fix] Don't possible make field not required

/* VER 2.2.360 - 16/09/2016 */

- [new] Cloudflare Rocket Loader support (need enable in settings)

/* VER 2.2.350 - 12/09/2016 */

- [new] *Settings*, *Addons* and *Forms editor* now can be edited by just one user at one time
- [tweak] Added multiply geodecoding services (IP => Country) for avoid bans
- [tweak] At forms list shows Contests where it used

/* VER 2.2.342 - 09/09/2016 */

- [fix] AJAX pagination & countdown fix (after go to another page Countdown stop working)

/* VER 2.2.341 - 08/09/2016 */

- [fix] After Enabling multiupload not possible Disable it
- [fix] Save reCAPTHCA result to session for 30 minutes (at voting) not work
- [fix] Form with Image but without any data fields (text, etc) can't be submitted
- [tweak] Fast AJAX & WP 4.6 small compatibility fixes

/* VER 2.2.340 - 02/09/2016 */

- [fix] Ajax update votes count if enabled Cache work incorrect with "Rating mode" voting type
- [fix] At "Single photo View" on Last/First photo in contest removed Next/Prev link to empty page
- [fix] At "Single photo View" nav links includes not published photos and user see error when open it (link to not published photo)

/* VER 2.2.330 - 29/08/2016 */

- [new] ## Multi forms support ##
- [tweak] When enabled option "Hide votes count" ajax get votes if enabled Cache returns 0 for all votes

/* VER 2.2.320 - 28/08/2016 */

- [tweak] Now Success/Error upload messages shows in default popup + small translations tweaks.

/* VER 2.2.311 - 26/08/2016 */

- [fix] Social sharing fix
- [fix] When enabled option "Hide votes count" on Single photo page count still shows
- [fix] Compatibility updates at some templates

/* VER 2.2.310 - 26/07/2016 */

- [fix] Text leaders link
- [new] Date field in Form builder
- [new] Show to (all, logged in, not logged in user) field option in Form builder
- [new] Real IP detection with CloudClare service

/* VER 2.2.300 - 19/07/2016 */

- [tweak] License details moved to separate page & some other license improvements

/* VER 2.2.208 - 16/07/2016 */

- [new] Mass actions with photos on Moderation page & Photos List

/* VER 2.2.207 - 04/07/2016 */

- [!fix!] Meta: on deleting Contestant deletes all Contestants meta from this Contest
- [fix] SHARING & contestant `social_description` field (it does not used correct)
- [tweak] MailChimp addon integration

/* VER 2.2.206 - 22/06/2016 */

- [fix] remove Emoji from texts on upload photo
- [new] ##Photos meta##
- [tweak] Updated Russian translation
- [tweak] Updated "Automatic Plugin Updater" library
- [tweak] On multi-adding photos "Image title" from Media library automatically sets and "Photo name"
- [fix] Beauty theme fixes

/* VER 2.2.205 - ?/05/2016 */

- [tweak] jQuery Select2 library in admin (contest editing & votes log)
- [tweak] Multiply contests at one page improvements (Voting, Ajax pagination, Sharing)

- [new] Now possible disable/enable FB & WP comments for Single Photo page
- [new] Redirect to page after success upload
- [new] Now possible select Facebook Sharing Dialog type
- [new] Addons automatic updater

- [fix] Toolbars are works independent
- [fix] Fixed bug with showing not Published photos at Single page

/* VER 2.2.200 - 20/03/2016 */

- [tweak] text mistakes in admin
- [tweak] new notify Mails body tags
- [tweak] little Spam score calc improvements
- [tweak] added Spam score details (Score reason)
- [tweak] added some missed public texts to admin Translator
- [new] Field save form in Form builder (like "{value} years old" except simply "{value}").
- [fix] FB social shares counter fixes

/* VER 2.2.123 - 08/01/2016 */

- [new] "Single photo page" changes:
  - possible select "Single photo" Theme independently
  - meta tags managing (single photo page Meta title & Meta description & Page Heading)
  - nice urls, like http://www.site.com/contest-photo/253/

- [tweak] upload form Responsive fixes
- [fix] If on page placed 2 upload form with email field user can't submit photo in second form
- [removed] Removed option "Limit upload by cookie"
- Like theme fix

/* VER 2.2.123 - 08/01/2016 */

- [improvement] FB login fix + added min user age (like 13+,18+,21+)
- [fix] voting bug fix (from 2.2.122)

/* VER 2.2.122 - 18/12/2015 */

- [new] New Addons API v2 - reduced memory & resourses usage(in some cases plugin memory usage descreased into 40%)
- [improvement] Removed parameter 'contest_id' from full contestant url
- [improvement] Chars counter in photo editing form
- [improvement] Allowed html in photo Description & Full description
- [fix] imageLightbox & ajax pagination fix
- [fix] Bug with retrieving user country
- [fix] Compatibility fix with Wp 4.4
- [fix] Removed Twitter shares counter, because Twitter partial disabled this feature
- [new] "Agree with rules" addon now integrated into package
- [new] Recoding user "Display size" on voting
- [removed] Removed Share via Email option

/* VER 2.2.120 - 18/11/2015 */

- [new] Social counter
- [new] You can limit upload photo dimensions (like photo must be bigger that 1024 * 768 px)

/* VER 2.2.111 - -/09/2015 */

- [new] A lot little fixes in themes, added Lazy load in Pinterest and Flickr, now grid in this themes generates a lot faster
- [improvement] Rewritten generating thumbnails from BFI_thumb to more stable and faster https://github.com/gambitph/WP-OTF-Regenerate-Thumbnails
- [new] Integrated Jetpack Photon module direct support (now it used by default, if enabled) - https://jetpack.me/support/photon/
- [improvement] Minified JS files
- [fix] Security fixes in `Cookie + Social Login` voting type
- [fix] Fallback, if jQuery(document).ready() not works because of JS errors

/* VER 2.2.110 - 21/09/2015 */

- [new] #2 new voting security types - "IP+cookies+evercookie + Recaptcha" and "cookies+evercookie + Recaptcha"
- [new] #Integrated BFI thumb library (https://github.com/bfintal/bfi_thumb)
- [new] #Added ability change Toolbar background, text and other colors
- [new] #Added ability to user upload from public more than 1(up to 10) photos for one upload action
- [new] #New pagination modes - "Ajax" and "Infinity loading"
- [improvement] Rewritten some moments in "Like" skin
- [new] Added `voting chart` with votes per day for Contest or Photo (in `Analytic` tab)
- [improvement] After upload message shows as overflow in form.

- [fix] In default English messages text removed some mistakes @@Thanks to Richard Hellier
- [fix] Now possible hide warning about "Using cache"
- [fix] Added some responsive styles from upload form
- [fix] Little memory optimization
- [new] Some styles optimization (minimized some styles files)
- [new] Now possible disable addons support for little decrease memory usage
- [new] Added new options for customization Contest list shortcode - `contest block size` and `thumb size`
- [new] Added Alphabetical photos order [A-Z or Z-A]
- [fix] Now possible export max 5000 records
- [fix] Added indexes for some table fields
- [fix] Added fallback in FormBuilder page, if jQuery.ready() not works
- [new] Added debug options for Voting and Upload (save to log information about process)

/* VER 2.2.105f - 21/08/2015 */

- [fix] Fixed issue with Wordpress 4.3 and editing contestant popup
- [fix] Added responsive css to Toolbar
- [fix] Fixed email validation in upload form
- [improvement] Updated Redux Framework

/* VER 2.2.104 - -/06/2015 */

- Some fixes
- [new] Added support Cloudinary.com

/* VER 2.2.103 - 08/06/2015 */

- [improvement] ImageLightbox fix in IE8 and some other
- [improvement] Admin page editing contest rewrite to Bootstrap css and Bootstrap modal
- [improvement] Changes in photo editing form - removed field Additional, added Description, Full description, Social description
- [improvement] Some rewrite Like theme
- [improvement] Into Votes log added field `User ID`
- [fix] Email title - photo deleted
- [fix] Bug with 24hFonce voting type
- [fix] Bug with voting security - users with some mobile browsers can vote more that once
- [new] ** Integrated new Form Builder **
- [new] Ability to create custom upload form styles
- [new] Added detailed environment information for Debug
- [new] Email share ability with ReCaptcha security
- [new] Added button `reset all votes` in contest edit page
- [new] Added toolbar
- [new] Anti fraud system in Beta
- [new] Lazy Load for images  (Not works in Pinterest, Flikr and Fashion theme)
- [new] Cache plugins support (ajax reload votes after cached page loading)
- [new] Added simple login form, if user need be Logged in for upload
- [improvement] Pagination changes: now possible set any photos per page number and some changes to more compatibility for support pagination in posts

/* VER 2.2.101 - 04/04/2015 */

- [improvement] Lightbox closing fixes in mobile
- [improvement] Removed some unused functions
- [improvement] Rewrite some upload form parts for allow place many forms in one page
- [new] Shortcode to show Contest leaders in any page
- [new] Shortcode to show Countdown in any page
- [improvement] Countdown fixes (more correct show leave time)
- [new] Ability use custom countdown
- [new] Addons support
- [new] Into export data added custom upload fields

/* VER 2.2.083 - 01/03/2015 */

- [fix] Little translations fixes and rewrited some JS code
- [new] In translations notify mail body now are textareas with multiline supports
- [improvement] Added css code editor in settings
- [new] Integrated new lightbox and added ability to simply integrate new lightboxes
- [new] Change user capability to manage contest

/* VER 2.2.082 */

- [new] New theme - flickr
- [new] Integrated new WP_image_uploader in admin
- [new] Multi adding photos in admin
- [new] Select Facebook SDK loading position - in head or footer
- [improvement] some responsive styles for vote modal
- [fix] Some problems with voting frequency `24 hours`

/* VER 2.2.081 */

- [new] Rotate images on admin
- [new] Added setting for select delimiter in CSV file
- [improvement] Recoded export data to CSV functions
- [fix] error on deleting photo in `Moderation` page
- [improvement] Removed some old translation JS files
- [new] Popup messages in admin on actions (add, delete, save)

/* VER 2.2.073, 2.2.08 */

- [new] New sharing and vote box
- [new] New pagination styles
- [new] Limit image size on upload photos
- [new] Set, from email send notify to users about photo uploaded, etc (early wordpress sended from `wordpress@domain.com`)
- [new] Email validation with javascript in upload form
- [new] Ability to set different date ranges for upload photos and voting
- [new] More precise designation of dates with hours and minutes
- [new] !Ability to simply create custom themes without plugin code changing, that allows update plugin in future without problems
- [new] Notices in admin, when "vote log" row is deleted
- [fix] Count votes in log when uses filter by contest or photo
- [fix] Upload form shortcode have some troubles
- [fix] When user get error on upload photo, form not reset
- [fix] When selected contest "Security type" as "Default + Facebook Sharing" now shows share not runs automatically,
        for exclude block share window for browser

/* VER 2.2.071 */

- [new] Export contest data into CSV (photos list with emails, names, votes count, etc)
- [new] Upload form shortcode
- [new] Pre or after upload photo moderation
- [improvement] social autorization without ip checks
- [new (test)] Map, with votes by country (filtrable by contest and photo)
- [fix] error on deleting vote from log

/* VER 2.2.06 */

** Fixed bugs
- image resize doe's not work
- problems with upload photo in admin

** Added
- social authorization
- filter in @votes log@ by photo ID
- countdown  timer
- facebook share improvement
- ability, to change notify messages, when photo uploaded, approved, deleted

/* VER 2.2.05 */

** Fixed bugs

** Added
- custom css code in admin
- widget Gallery type
- change lightbox theme in constest settings
- change photos order in constest settings
- new year theme

== Description ==

This plugin allows you simply create photo contest in you site.

http://wp-vote.net/instructions/
