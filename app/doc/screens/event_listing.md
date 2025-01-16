# Objective

The objective of the page is to list all the events and their status of one user

## URl pattern

the url pattern is the following : domain.com/organizer/event/list

## Security

You need to be logged in to access this page

## Content

The content of this page is the following:
- a button to create an event => redirects to the [creation page](./event_creation_page.md)
- table with a list of the events of which the user is the organizer
- For each event, the following is displayed:
  - name
  - date
  - status (launched or not)
  - if not launched: a link to the [edition page](./event_creation_page.md)
  - if launched a link to the [display page](./event_display.md)
  - a button to "cancel"/"stop" the event
    - "cancel" if the event is not launched
      - in the case of cancellation, a mail will be sent to the participant notifying the cancellation of the event
    - "stop" otherwise
  - a "clone" button
    - redirects to the [creation page](./event_creation_page.md) with a filled form already with the values of the cloned event

