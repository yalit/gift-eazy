# Objective

The event creation/edit page allows for a signed-in user to create an event and/or adds participants

## URl pattern

the url pattern is the following : 
  - domain.com/event/creation => creation page
  - domain.com/event/edit/<event_token> => edit page

If the user is logged the pattern is the following:
  - domain.com/organizer/event/creation => creation page
  - domain.com/organizer/event/edit/<event_token> => edit page

## Security

You don't need to be logged in to access that page

## Content

The screen should contain the following elements:
- a form to create/update the event
  - if the page is the creation one, the form is empty
  - if the page is the edit one, the form is filled with the data of the event stored in the db
- a button to save the event as it is
  - this should trigger a mail if it's the first time the form is saved sending to the creator the dedicated link to the creation of the event
- a start button to actively launch the event 
  - this should trigger an email to each participant
  - this should trigger an email to the event creator with the status update

## Form 
The form contains the following elements:
- [event](../process/event.md) characteristics with dedicated label and inputs
- checkbox requesting if the organizer should be a participant (ticked by default) => if yes, the organizer is automatically to the list of participant
- a list of participants. For each participant the following elements are to be visible:
  - name and email
  - list of non-wanted target recipients (only if logged and _only if paying membership ???_) => list is based on the other participants provided in the list by name
  - icon to remove the participant
  - the names should be unique for an event
- (_maybe in a next stage or in a paying version_ and definitely only if logged in : link a previous event so that we can identify pairs of gift_giver-recipient)
- a save button
  - the button saves the event in the backend
  - if it's the first time that the event is saved, then an email is triggered with the url of the event creation page with the unique token for the event
  - the user is redirected to the url with the token
- a launch the event button
  - the button starts the event by doing the following things:
    - generates the pairs gift_giver-recipient
    - pushing the event into the status "Ongoing" => it's then not modifiable anymore
    - sending a mail to each participant with the url of its [participant page](./event_recipient_page.md)
    - sending a mail to the organizer with the url of the [event display page](./event_display.md)
    - redirects the user to the [event display page](./event_display.md)
