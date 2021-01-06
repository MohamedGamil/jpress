<?php

/**
 * Translations
 *
 * @since 0.2.5
 */


// NOTE: Prevent direct access
if (isset($data, $options) === false) exit;


// NOTE: Parsing the translations to be read in mobile application
$translationsKeys = array(
  'back', 'skip', 'done', 'contactUs', 'contactUsTitle', 'contactUsSubTitle', 'yourName', 'yourEmail', 'yourMessage', 'send', 'settings',
  'aboutUs', 'layout', 'textSize', 'aA', 'darkMode', 'rateApp', 'shareApp', 'privacyPolicy', 'termsAndConditions', 'poweredBy', 'logout',
  'relatedPosts', 'leaveComment', 'commentsCount', 'reply', 'replyTo', 'By', 'cancel', 'submit', 'comment', 'name', 'postComment',
  'postReply', 'lets', 'noFav', 'noPosts', 'mustNotBeEmpty', 'loadingMore', 'loadingMoreQuestions', 'someThingWentWrong', 'search',
  'noMore', 'removedToFav', 'addedToFav', 'typeToSearch', 'version', 'yourVersionUpToDate', 'yourVersionNotUpToDate', 'aboutApp',
  'noResults', 'errorPageTitle', 'retry', 'noInternet', 'checkInternet', 'noComments', 'seeMore', 'yes', 'next', 'By',
);

$options['translations'] = array();

foreach ( $translationsKeys as $key ) {
  if ( isset($data[ 'translate-' . $key ]) && empty($data[ 'translate-' . $key ]) === false ) {
    $options['translations'][$key] = $data[ 'translate-' . $key ];
  }
}
