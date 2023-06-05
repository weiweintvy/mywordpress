=== Contact Form 7 Multi-Step Forms ===
Contributors: webheadllc
Donate Link: https://webheadcoder.com/donate-cf7-multi-step-forms
Tags: contact form 7, multistep form, form, multiple pages, contact, multi, step
Requires at least: 4.7
Tested up to: 6.2
Stable tag: 4.2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Enables the Contact Form 7 plugin to create multi-page, multi-step forms.

== Description ==

I needed a contact form that spanned across multiple pages and in the end would send an email with all the info collected.  This plugin adds onto the popular Contact Form 7 plugin to do just that.

Sample of this working is at [https://webheadcoder.com/contact-form-7-multi-step-form/](https://webheadcoder.com/contact-form-7-multi-step-form/)

Requires the [Contact Form 7 plugin](https://wordpress.org/plugins/contact-form-7/), version 4.8 or above, by Takayuki Miyoshi.

**Usage**

1. Create one page or post for each step in your multi-step form process.  If you have 3 steps, create 3 pages/posts.  You will need the urls to these when creating your forms.

2. Create a Contact Form 7 form.

3. Place your cursor at the end of the form.

4. On the "Form" tab of the Contact Form 7 form, click on the button named "multistep".

5. In the window that pops up, check the checkbox next to "First Step" if this is the first step of your multi step forms.  If this is your last step in the multi step forms, check the "Last Step" checkbox.  All other checkboxes are optional. 

6. The Next Page URL is the url that contains your next form.  If this form is the last step, you can leave the URL field blank.

7.  Click "Insert Tag"

8.  Save your completed form and place the form's shortcode into the appropriate Page/Post you created in step 1.

9.  Repeat for **each form** in your multi-step form process.  

10.  On the last step, you probably would want to send an email.  Make sure to check the "Send Email" checkbox in step 5.  On the Mail Tab, simply enter the mail-tags as you normally would.  For example if your first form has the field `your-email` you can include `[your-email]` in the Mail tab on your last form.  Note:  CF7 will see this as an error because `your-email` may not be displayed on the current form.  You can safely ignore this error.


**Multistep Tag Options**

* **Name** - The name of this multistep form-tag.  This is required, but is currently not being used.

* **First Step** - Besides marking the first step of your multistep forms, this allows any form to act as the first step and show when no previous data has ben submitted.  This is useful when you want some users to skip the first step.

* **Last Step** - Besides marking the last step of you multistep forms, this clears the data from user's browsers.  Once they submit this form they won't see their data populating the forms anymore.

* **Send Email** - If this is checked the form will send an email like a normal Contact Form 7 submission.

* **Skip Save** - If you use Flamingo or CFDB7 to save submissions to the database this prevents saving this form submission.

* **Next Page URL** - This is the URL your users will go to after the form is submitted.

**Additional Tags**

`[multiform "your-name"]`
The `multiform` form-tag can be used to display a field from a previous step.  Replace `your-name` with the name of your field.  This is only for use on the Form tab, this tag will not work in the Mail tab.  

`[previous "Go Back"]`
The `previous` form-tag can be used to display a button to go to a previous step.  Replace `Go Back` with text you want to show in the button.


**Messages Tab**
When a visitor to your site visits the 4th step in your multi step form without filling out the 1st step, the message "Please fill out the form on the previous page." will be displayed.  You can change this on each form in the Messages tab.  


**What this plugin DOES NOT do:**  

* This plugin does not support file uploads on every form.  If you need to use file uploads make sure to place it on the last step.  

* This plugin currently does not support "pipes" in the select field.  See https://contactform7.com/selectable-recipient-with-pipes/ for more on what "pipes" is on the Contact Form 7 site.  

* This plugin does not load another form on the same page.  It only works when the forms are on separate pages.  Many have asked to make it load via ajax so all forms can reside on one page.  This plugin does not support that.

**PRO Version**
If you expect to have a lot of data submitted through your multi-step forms, the Pro version may be able to help you better.  The PRO version uses Session Storage so it is able to handle roughly 1,000 times more data for your multiple forms.  In total it can handle about 5MB vs 4KB in the free version.  **Currently the Pro version REQUIRES the WordPress REST API and Contact Form 7 AJAX Submission to be enabled.**   

Another feature the Pro version offers is the ability to skip steps with the "Contact Form 7 - Conditional Fields plugin".  [Learn more here.](https://webheadcoder.com/contact-form-7-multi-step-forms/#pro)

== Frequently Asked Questions ==

= The Next button doesn't show up =
Like all Contact Form 7 forms, you still need to add a button to submit the form.  Use the normal submit button with any label you want like so `[submit "Next"]`.

The `multistep` form tag is a hidden field and tries not to add any spacing to your form.  In this effort, anything directly after this tag may be hidden.  To prevent this, add a carriage return after the `multistep` form tag, or just follow the directions and place the form tag at the end of the form.

= I keep getting the "Please fill out the form on the previous page" message.  What's wrong? =

It could be one of these reasons:

1. Your Caching system is not allowing cookies to be set in a normal way.  No workarounds or fixes are planned at this time.  You will need to turn off caching for cookies named cf7*.
2. Your protocol or domain is not the same on all pages.  Each page that holds a form needs to have the same protocol and domain.  If your first page uses https like https://webheadcoder.com, your second page cannot be http:// or a subdomain of that.
3.  Make sure your first form has the first_step attribute in the multistep form-tag, like:  `[multistep multistep-123 first_step "/your-next-url/"]`

= Why are no values being passed from one form to the next form? =

If your form reloads the page after hitting the submit button, you either disabled the WordPress REST API or javascript for Contact Form 7 isn't working correctly.  Please see the Contact Form 7's troubleshooting page for more information:
[https://contactform7.com/why-isnt-my-ajax-contact-form-working-correctly/](https://contactform7.com/why-isnt-my-ajax-contact-form-working-correctly/)


= How can I show a summary of what the user entered or show fields from previous steps? =

`[multiform "your-name"]`  
The multiform form-tag can be used to display a field from a previous step.  Replace `your-name` with the name of your field.

= My form values aren't being sent in the email.  I get [multiform "your-name"] instead of the actual person's name. =

The multiform form-tag should only be used on the Form tab.  On the Mail tab follow the instructions from the Contact Fom 7 documentation.  So if you wanted to show the `your-name` field, type `[your-name]`.

It's also important that the last form has the multistep form-tag.  

= Can I have an email sent on the first step of the multi-step forms? =

Yes, you can.  Make sure to check the "Send Email" checkbox or have the send_email attribute in the multistep form-tag like:  `[multistep multistep-123 first_step send_email "/your-next-url/"]`.  

= My forms are not working as expected.  What's wrong? =

- Make sure you have the `multistep` tag on each and every form.

- It is very common for other plugins to have javascript errors which can prevent this plugin from running properly.  Deactivate all other plugins and try again.

= Why "place your cursor at the end of the form" before inserting the multistep tag? =

The `multistep` form tag is a hidden field and tries not to add any spacing to your form.  In this effort, anything directly after this tag may be hidden.  To prevent this, add a carriage return after the `multistep` form tag, or just follow the directions and place the form tag at the end of the form.

= How do I get Flamingo or CFDB7 to not save every form? =
Make sure to check the "Skip Save" checkbox or have the skip_save attribute in the multistep form-tag like: `[multistep multistep-123 skip_save "/your-next-url/"]`.  

= When checkbox fields are left unchecked they appear as [field-name] in the email.  How do I resolve this? =
When checkboxes are not checked they aren't submitted through the form so the last step of the form doesn't know the unchecked checkbox field exists.  To get around this issue add a hidden form tag like `[hidden field-name]` to the last step.  This way the last step will either submit the previously set value or a blank value.

== Changelog ==

= 4.2.1 =
* fixed PHP warning.  
* updated checkboxes to trigger the checked event when form is repopulated.  

= 4.2 =
* fixed multiform tags for CF7 5.7.3.  
* changed "form field" tag generator name to "multiform".  
* updated Freemius.  

= 4.1.92 =
* fixed values not saving across steps on Safari browsers.  

= 4.1.91 =
* updated Freemius to v2.4.3.  

= 4.1.9 =
* updated logo images to be within the plugin to comply with WordPress requirements.  

= 4.1.8 =
* security update:  HTML is now escaped when being output using the multiform tag.  Additional sanitization changes.  

= 4.1.7 =
* fixed next url to support external urls.  

= 4.1.6 =
* fixed conditional fields not trigerring after form population.  
* fixed 500 error due to conflict with Conditional Fields plugin expecting an array in cookie values.  

= 4.1.5 =
* fixed prev button not showing up when next url from previous form has a querystring in it.  
* fixed issue with cookie being set on non-multistep forms.  
* PRO: added compatibility for repeaters in Conditional Fields Pro.  

= 4.1.4 =
* fixed error when caching is enabled.  

= 4.1.2 =
* updated version to bust cache.  

= 4.1.1 =
* updated for CF7 5.4.
* updated freemius.  

= 4.1 =
* added sanitization similar to the core CF7 plugin.  

= 4.0.9 =
* updated freemius.  

= 4.0.8 =
* fixed values being saved when form submission is invalid.  

= 4.0.7 =
* fixed success message showing when not on the last step due to a change in Contact Form 7 v5.2.1.  
* added Skip Save for Advanced Contact form 7 DB plugin.  Thanks to @undersound.   

= 4.0.6 =
* PRO: fixed fields not going through when form ids were not in the right order.  

= 4.0.5 =
* PRO: fixed checkboxes not being passed on to next form.  

= 4.0.4 =
* PRO: fixed fields showing up out of order when viewed in Flamingo (part 2).  

= 4.0.3 =
* PRO: fixed fields showing up out of order when viewed in Flamingo.  

= 4.0.2 =
* fixed get_magic_quotes_gpc() deprecated warning when running PHP 7.4.  
* fixed slashes appearing in free version
* fixed previous button not showing when the multiform tag's next url doesn't match the page url because of a trailing slash.
* added a filter to fallback to sessions.  

= 4.0.1 =
* fixed issue where the multistep cookie was being set on non multistep forms.  

= 4.0 =
In Version 4.0 the format of the multistep form-tag has changed dramatically.  The old format is backwards compatible and will still work until January 2021.  Beyond that the old format is not guaranteed to work with newer versions.  More Info:  [https://webheadcoder.com/contact-form-7-multi-step-forms-update-4-0/](https://webheadcoder.com/contact-form-7-multi-step-forms-update-4-0/)

* added new multiform form-tag format to allow for options to send email and not save to database.  
* added customizable error on the Messages tab.  
* added admin notice to notify user of large form submissions.
* PRO: added compatibility to skip steps with the CF7 Conditional Fields plugin.  

= 3.2 =
* added review notice to get to know how users like this plugin.  
* fixed WP warning when CF7 is not installed.  
* updated freemius.

= 3.1.2 =
* added ability to skip over steps if it was previously submitted.  

= 3.1.1 =
* updated freemius.  

= 3.1 =
* fixed issue where CF7 MSM files still loaded even when WPCF7_LOAD_JS is set to false.  
* fixed success message not showing for forms with a wrapping inner element.
* fixed multi-select population.  
* updated how select is set so it can trigger javascript changes.  

= 3.0.9 =
* fixed issue where WPCF7_LOAD_JS is set to false and resulted in 302 error.  thanks to @zetoun17.
* security fix  

= 3.0.8 =
* added missing freemius files  

= 3.0.7 =
* updated freemius

= 3.0.6 =
* PRO: fixed "Cannot use a scalar value as an array" warning when CF7 Conditional Fields plugin is active.  

= 3.0.5 =
* PRO: fixed compatibility with Contact Form 7 Conditional Fields plugin to only show group that is supposed to show.  

= 3.0.4 =
* deprecated wpcf7_form_field_value filters.  
* added cf7msm_form_field_value filters.  

= 3.0.3 =
* PRO: fixed conditional fields (from the Conditional Fields for Contact Form 7 plugin) not showing in email.  

= 3.0.2 =
* fixed quotes in values causing errors.  
* added plugin action links.  

= 3.0.1 =
* fixed session storage not clearing after final step was submitted.  
* fixed form not hiding after final step was submitted.  Thanks to @tschodde.  

= 3.0 =
* changed internal field names to be prefixed with cf7msm.  
* added PRO version to handle long forms.  
* fixed minor issues.  

= 2.26 =  
* updated i18n code.  

= 2.25 =  
**Contact From 7 version 4.8 or above is required for this version**.  
* fixed incompatible JSON_UNESCAPED_UNICODE for PHP versions < 5.4.  

= 2.24 =  
**Contact From 7 version 4.8 or above is required for this version**.  
* fixed not redirecting to next step on older iPad browsers.  
* fixed illegal offset exception warning.  
* added JSON_UNESCAPED_UNICODE for czech language.  

= 2.23 =  
**Contact From 7 version 4.8 or above is required for this version**.  
* fixed back button on firefox.  
* fixed url not displaying correctly when it has the & symbol.  

= 2.22 =  
**Contact From 7 version 4.8 or above is required for this version**.  
* fixed back button going back more than one step.  

= 2.21 =  
**Contact From 7 version 4.8 or above is required for this version**.  
* fixed an issue where a notice occurred when using scan_form_tags on servers that displayed PHP notices.  


= 2.2 =  
**Contact From 7 version 4.8 or above is required for this version**.  
* fixed back button not working when using with Contact Form 7 version 4.8.  
* fixed fields from previous steps not showing up when using with Contact Form 7 version 4.8.  
Thanks to @eddraw, updated deprecated functions.  

= 2.1 =  
* Use Contact Form 7's built-in hidden form tag if version 4.6 or above is present.  

= 2.0.9 =  
* fixed issue where using the `[multiform]` tag causes the field to blank out and not show in emails on certain servers.  


= 2.0.8 =  
* added field_name and value to wpcf7_form_field_value filter.  


= 2.0.7 =
* fixed calls to deprecated CF7 functions.
* Increased minimum WP version to match CF7's specs.  


= 2.0.6 =
* Thanks to @eddraw for the updates!  
* added translation: add pot file.  
* fixed translation: use the name of the plugin as textdomain and load it.  


= 2.0.5 =
* added form id to wh_hide_cf7_step_message filter.  


= 2.0.4 =
* fixed plugin conflict.  


= 2.0.3 =
* fixed issue where server variables may not be defined.  added some support for strings to be translatable.  


= 2.0.2 = 
* Fix previous button not showing class attribute.  


= 2.0.1 = 
* Minor fix to detecting if previous form was filled.  


= 2.0 = 
* Added Form Tags to Form Tag Generator.  No more needing to update the Additional Settings tab.  
* Added error alert when form is too large.  
* Fixed Deprecated: preg_replace() error message.  
* Fixed certain instances where the "Please fill out the form on the previous page" messages displayed unexpectedly.
* Fixed issue where it was possible to type in the url of the next step after receiving validation errors on the current step.  


= 1.6 =
* Added support for when contact form 7 ajax is disabled.

= 1.5 =
* Added support for free_text in checkboxes and radio buttons.

= 1.4.4 =
* fix empty checkboxes causing javascript error when going back.

= 1.4.3 =
* fix exclusive checkboxes not saving on back.  added version to javascript.

= 1.4.2 =
* fix radio button not saving on back. make sure its the last step before clearing cookies.

= 1.4.1 =
* Fixed bug where tapping the Submit button on the final step submits form even with validation errors.

= 1.4 =
* Updated to be compatible with Contact Form 7 version 3.9.

= 1.3.6 =
* Updated readme to be more readable.
* Fixed issue for servers with magic quotes turned off.  Fixes "Please fill out the form on the previous page" error.

= 1.3.5 =
* Fix:  Also detect contact-form-7-3rd-party-integration/hidden.php so no conflicts arise if both are activated.

= 1.3.4 =
* Fix:  Better detection of contact-form-7-modules plugin so no conflicts arise if both are activated.

= 1.3.3 =
* Fixed back button functionality.

= 1.3.2 =
* Some people are having trouble with cookies.  added 'cf7msm_force_session' filter to force to use session.

= 1.3.1 =
* Added checks to prevent errors when contact form 7 is not installed.

= 1.3 =
* Confused with the version numbers.  apparently 1.02 is greater than 1.1?

= 1.1 =
* renamed all function names to be more consistent.
* use cookies before falling back to session.
* added back shortcode so users can go back to previous step.

= 1.02 =
* updated version numbers.

= 1.01 =
* updated readme.

= 1.0 =
* Initial release.

