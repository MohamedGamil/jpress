<?php

/**
 * Translations Tab
 *
 * @since 0.2.6
 */


// NOTE: Prevent direct access
defined( 'ABSPATH' ) || exit;


// NOTE: Translations Page
$settings->open_tab_item('translations');

$translations_section	=	$settings->add_section( array(
  'name' => 'Tanslations',
  'id' => 'section-general-header',
  'options' => array(
    'toggle' => true,
  )
));

$translations_section->add_field(array(
  'name' => 'Back',
  'default' => "Back",
  'id' => 'translate-back',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'SKIP',
  'default' => "SKIP",
  'id' => 'translate-skip',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Done',
  'default' => "Done",
  'id' => 'translate-done',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Contact Us',
  'default' => "Contact Us",
  'id' => 'translate-contactUs',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => "Contact Us",
  'default' => "Contact Us",
  'id' => 'translate-contactUsTitle',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Contact us for more information.',
  'default' => "Contact us for more information.",
  'id' => 'translate-contactUsSubTitle',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Your Name',
  'default' => "Your Name",
  'id' => 'translate-yourName',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Your Email',
  'default' => "Your Email",
  'id' => 'translate-yourEmail',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Your Message',
  'default' => "Your Message",
  'id' => 'translate-yourMessage',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Send',
  'default' => "Send",
  'id' => 'translate-send',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Settings',
  'default' => "Settings",
  'id' => 'translate-settings',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'About Us',
  'default' => "About Us",
  'id' => 'translate-aboutUs',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Layout',
  'default' => "Layout",
  'id' => 'translate-layout',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Text Size',
  'default' => "Text Size",
  'id' => 'translate-textSize',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Aa',
  'default' => "Aa",
  'id' => 'translate-aA',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Dark Mode',
  'default' => "Dark Mode",
  'id' => 'translate-darkMode',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Rate this app',
  'default' => "Rate this app",
  'id' => 'translate-rateApp',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Share the app',
  'default' => "Share the app",
  'id' => 'translate-shareApp',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Privacy policy',
  'default' => "Privacy policy",
  'id' => 'translate-privacyPolicy',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Terms and Conditions',
  'default' => "Terms and Conditions",
  'id' => 'translate-termsAndConditions',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Powered by',
  'default' => "Powered by",
  'id' => 'translate-poweredBy',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Logout',
  'default' => "Logout",
  'id' => 'translate-logout',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'RELATED POSTS',
  'default' => "RELATED POSTS",
  'id' => 'translate-relatedPosts',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'LEAVE A COMMENT',
  'default' => "LEAVE A COMMENT",
  'id' => 'translate-leaveComment',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'COMMENTS',
  'default' => "COMMENTS",
  'id' => 'translate-commentsCount',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Reply',
  'default' => "Reply",
  'id' => 'translate-reply',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Reply to',
  'default' => "Reply to",
  'id' => 'translate-replyTo',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'By',
  'default' => "By",
  'id' => 'translate-By',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Cancel',
  'default' => "Cancel",
  'id' => 'translate-cancel',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Submit',
  'default' => "Submit",
  'id' => 'translate-submit',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Comment',
  'default' => "Comment",
  'id' => 'translate-comment',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Name',
  'default' => "Name",
  'id' => 'translate-name',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Post Comment',
  'default' => "Post Comment",
  'id' => 'translate-postComment',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Post Reply',
  'default' => "Post Reply",
  'id' => 'translate-postReply',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => "Let's go",
  'default' => "Let's go",
  'id' => 'translate-lets',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'No Favorites Yet',
  'default' => "No Favorites Yet",
  'id' => 'translate-noFav',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'No Posts Found',
  'default' => "No Posts Found",
  'id' => 'translate-noPosts',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => ' must not be empty',
  'default' => " must not be empty",
  'id' => 'translate-mustNotBeEmpty',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Loading more...',
  'default' => "Loading more...",
  'id' => 'translate-loadingMore',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Load more',
  'default' => "Load more",
  'id' => 'translate-loadingMoreQuestions',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Something went wrong',
  'default' => "Something went wrong",
  'id' => 'translate-someThingWentWrong',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Search',
  'default' => "Search",
  'id' => 'translate-search',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'No more items',
  'default' => "No more items",
  'id' => 'translate-noMore',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Removed from favorites',
  'default' => "Removed from favorites",
  'id' => 'translate-removedToFav',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Added to favorites',
  'default' => "Added to favorites",
  'id' => 'translate-addedToFav',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Type to search',
  'default' => "Type to search",
  'id' => 'translate-typeToSearch',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Version ',
  'default' => "Version ",
  'id' => 'translate-version',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Your app version is up to date ',
  'default' => "Your app version is up to date ",
  'id' => 'translate-yourVersionUpToDate',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Update the latest version ',
  'default' => "Update the latest version ",
  'id' => 'translate-yourVersionNotUpToDate',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'About app',
  'default' => "About app",
  'id' => 'translate-aboutApp',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'No Results',
  'default' => "No Result",
  'id' => 'translate-noResults',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Error Page Title',
  'default' => "Oops",
  'id' => 'translate-errorPageTitle',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Retry',
  'default' => "Retry",
  'id' => 'translate-retry',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'No Internet Connection!',
  'default' => "No Internet Connection!",
  'id' => 'translate-noInternet',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Please check your internet connection and try again',
  'default' => "Please check your internet connection and try again",
  'id' => 'translate-checkInternet',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'No Comments',
  'default' => "No Comments",
  'id' => 'translate-noComments',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'See All',
  'default' => "See All",
  'id' => 'translate-seeMore',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Yes',
  'default' => "Yes",
  'id' => 'translate-yes',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'Next',
  'default' => "Next",
  'id' => 'translate-next',
  'type' => 'text',
  'grid' => '6-of-6',
));

$translations_section->add_field(array(
  'name' => 'By',
  'default' => "By",
  'id' => 'translate-By',
  'type' => 'text',
  'grid' => '6-of-6',
));

$settings->close_tab_item('translations');
