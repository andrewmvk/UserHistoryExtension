# UserHistoryExtension
This is my first MediaWiki's Extension, its a implementation for other existed extension (UserPageViewTracker)

The UserHistory is my OWN extension, that just works with a custom UserPageViewTracker version.
My extension get the ideia in UserPageViewTracker and aply it futher...

The UPVT save the last time a user entered a page, if he visites once it will increment the amount of time of "Views" and replace the "Last" time he visites the page.
But this extension don't have all what we need, UPVT just shows the amount of and last time the user visited a page, but don't shows the FIRST time he visited it.
My extension complete the "missing part" of UPVT, it's a page that shows a table (like UPVT) with all every time THE USER visited any page with the date and time, 
IT'S LIKE A USER HISTORY.

With the two extension installed (UPVT custom version and UserHistory), you will be able to enter the UPVT's page and the "Last" will be clickable, it will redirects 
you to the UserHistory page with the User's ID that you clicked and shows his history. The table is responsive, so if he have more than 50 visites it will create another
page so you can acess that and see the the rest of the history.
