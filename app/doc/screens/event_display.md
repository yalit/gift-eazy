# Objective

The objective of the page is to display the event details/information to the organizer once the event has been launched

## URl pattern

the url pattern is the following : domain.com/event/<token:optional>

if the user is logged in the url pattern is the following : domain.com/organizer/event/<token:optional>
## Security

You don't need to be logged in to access that page

## Content

The content of the page is the following:
- Event information 
  - name and date
- Organizer information
  - name and email
- Participants information => list with for each:
  - name and email
  - resend mail button (only if logged in)
    - allows to resend the mail originally sent
  - reading mail status (only if logged in)
  - clicking mail status (only if logged in)

