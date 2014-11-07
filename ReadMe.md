# Tweet classifier

Crowdsourced tweet classifier that auto-replies to tweets from people mentioning terms like “bus”, “commute”, “subway”, etc. with a link to a personalized mini-questionnaire which asks them to classify their tweet based on our classification system.

Written in PHP for Heroku.

**Steps to complete**:
1. Set up Twitter account
2. Register App
3. Use search API to look for Tweets with our search terms within the last *n* minutes via [https://dev.twitter.com/rest/public/search](https://dev.twitter.com/rest/public/search) (we need to somehow trigger this automatically every *n* minute)
4. Parse the response, and for every tweet:
5. Reply to the tweet via [https://dev.twitter.com/rest/reference/post/statuses/update](https://dev.twitter.com/rest/reference/post/statuses/update). The response needs to contain a custom URL a la http://ourapp.com/classify.php?tweetid=1234
6. If the user clicks on the link, show a page with the original tweet (via [https://dev.twitter.com/rest/reference/get/statuses/show/%3Aid](https://dev.twitter.com/rest/reference/get/statuses/show/%3Aid)), a dropdown menu with our different classes, and a submit button. The bottom of the page should have a short text about who we are and why we do this. This page needs to be responsive to make sure it looks good on any device (use bootstrap?)
7. When the user submits the classification, the form sends back the tweet ID and the classification to the server.
8. The server uses the ID to fetch the tweet once again, and stores the columns we are interested in to PostGres: ID, username, text, timestamp, lat/lon (if available) – anything missing?