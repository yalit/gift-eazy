## Definition

An event is the representation of a moment where a group of people will exchange gifts together. It has a name and a date and an organizer.

## Structure

An event has the following characteristics:
- a name
- a date (optional)
- an organizer
  - name and email
- a maximum spent amount (optional)
- a theme (optional)
- an image (optional)
- a small description (optional)

## Process

An event can be in 4 different statuses:
- draft : by default at creation
- launched : 
  - starting from that time the event can't be modified anymore
- cancelled : 
  - participant page not shown anymore
  - updates not possible anymore
- closed :
  - automatically 10 days after the date of the event, the event is closed
  - participant page not shown anymore
  - updates not possible anymore

## Security

Users do not need to be logged in to create an event. 
A dedicated token will be created for each event for it to be viewable directly also providing tokens for each of the participant page


